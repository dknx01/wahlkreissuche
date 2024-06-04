<?php

declare(strict_types=1);

namespace App\UnitTests\Security;

use App\Security\VerifyEmailHelper;
use App\Security\VerifyEmailQueryUtility;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\ExpiredSignatureException;
use SymfonyCasts\Bundle\VerifyEmail\Exception\WrongEmailVerifyException;
use SymfonyCasts\Bundle\VerifyEmail\Generator\VerifyEmailTokenGenerator;

class VerifyEmailHelperTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|UrlGeneratorInterface $router;
    private ObjectProphecy|UriSigner $uriSigner;
    private ObjectProphecy|VerifyEmailQueryUtility $queryUtility;
    private ObjectProphecy|VerifyEmailTokenGenerator $tokenGenerator;
    private int $lifetime = 5;

    protected function setUp(): void
    {
        $this->router = $this->prophesize(UrlGeneratorInterface::class);
        $this->uriSigner = $this->prophesize(UriSigner::class);
        $this->queryUtility = $this->prophesize(VerifyEmailQueryUtility::class);
        $this->tokenGenerator = new VerifyEmailTokenGenerator('123');
    }

    public function testGenerateSignature(): void
    {
        $this->router->generate('foo', Argument::type('array'), UrlGeneratorInterface::ABSOLUTE_URL)->willReturn('http://www.kent-wehner.org/login');
        $this->uriSigner->sign(Argument::type('string'))->willReturn('http://www.kent-wehner.org/login?token=123456asdbt');

        $signatureComponents = $this->getHelper()->generateSignature(
            'foo',
            '5104c7f6-489f-43b8-bdc8-c393db58d751',
            'frances.daugherty@example.com',
        );
        $this->assertEquals('http://www.kent-wehner.org/login?token=123456asdbt', $signatureComponents->getSignedUrl());
    }

    public function testValidateEmailConfirmation(): void
    {
        $token = 'CwyWEYCrT7tTZnstFFwoTJSwwWbtrYjuLZygEibsAMs=';
        $url = 'http://www.kent-wehner.org/login?token=' . $token;
        $this->queryUtility->getExpiryTimestamp($url)
            ->willReturn(time() + 50);
        $this->queryUtility->getTokenFromQuery($url)
            ->willReturn($token);
        $this->tokenGenerator = $this->prophesize(VerifyEmailTokenGenerator::class);
        $this->tokenGenerator->createToken(
            '5104c7f6-489f-43b8-bdc8-c393db58d751',
            'frances.daugherty@example.com'
        )->willReturn('CwyWEYCrT7tTZnstFFwoTJSwwWbtrYjuLZygEibsAMs=');

        $this->getHelper()->validateEmailConfirmation(
            $url,
            '5104c7f6-489f-43b8-bdc8-c393db58d751',
            'frances.daugherty@example.com',
        );
        $this->assertTrue(true);
    }

    public function testValidateEmailConfirmationWithExpiredSignatureException(): void
    {
        $this->expectException(ExpiredSignatureException::class);
        $token = 'CwyWEYCrT7tTZnstFFwoTJSwwWbtrYjuLZygEibsAMs=';
        $url = 'http://www.kent-wehner.org/login?token=' . $token;
        $this->queryUtility->getExpiryTimestamp($url)
            ->willReturn(time() - 50);

        $this->getHelper()->validateEmailConfirmation(
            $url,
            '5104c7f6-489f-43b8-bdc8-c393db58d751',
            'frances.daugherty@example.com',
        );
    }

    public function testValidateEmailConfirmationWithWrongEmailVerifyException(): void
    {
        $this->expectException(WrongEmailVerifyException::class);
        $token = 'something_wrong';
        $url = 'http://www.kent-wehner.org/login?token=' . $token;
        $this->queryUtility->getExpiryTimestamp($url)
            ->willReturn(time() + 50);
        $this->queryUtility->getTokenFromQuery($url)
            ->willReturn('AnotherToken');

        $this->getHelper()->validateEmailConfirmation(
            $url,
            '5104c7f6-489f-43b8-bdc8-c393db58d751',
            'frances.daugherty@example.com',
        );
    }

    private function getHelper(): VerifyEmailHelper
    {
        return new VerifyEmailHelper(
            $this->router->reveal(),
            $this->uriSigner->reveal(),
            $this->queryUtility->reveal(),
            $this->tokenGenerator instanceof ObjectProphecy ? $this->tokenGenerator->reveal() : new VerifyEmailTokenGenerator('01GFK5NNMKT5R5QVS3R6EE8WD2'),
            $this->lifetime
        );
    }
}
