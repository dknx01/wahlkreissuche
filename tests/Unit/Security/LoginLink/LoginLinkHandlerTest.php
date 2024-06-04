<?php

declare(strict_types=1);

namespace App\UnitTests\Security\LoginLink;

use App\Entity\User;
use App\Security\LoginLink\ExpiredSignatureStorage;
use App\Security\LoginLink\LoginLinkHandler;
use App\Security\LoginLink\SignatureHasher;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Signature\Exception\ExpiredSignatureException;
use Symfony\Component\Security\Core\Signature\Exception\InvalidSignatureException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\LoginLink\Exception\ExpiredLoginLinkException;
use Symfony\Component\Security\Http\LoginLink\Exception\InvalidLoginLinkException;

class LoginLinkHandlerTest extends TestCase
{
    use ProphecyTrait;

    #[TestWith(['?user'])]
    #[TestWith(['user'])]
    public function testConsumeLoginLinkWithRequestParameter(string $requestKey): void
    {
        $options = [
            'liftime' => 300,
            'route_name' => 'login_link_route',
        ];
        $signatureHasher = $this->prophesize(SignatureHasher::class);
        $storage = new ExpiredSignatureStorage(new ArrayAdapter(), 2);
        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $user = new User();
        $userProvider->loadUserByIdentifier('foo')->shouldBeCalledOnce()->willReturn($user);

        $request = new Request(
            query: [
                $requestKey => 'foo',
                'hash' => '01GS7GNZNEJZKXTXMX88NVE9CA',
                'expires' => 123456789,
            ]
        );
        $signatureHasher->verifySignatureHash($user, 123456789, '01GS7GNZNEJZKXTXMX88NVE9CA')->shouldBeCalledOnce();

        $handler = new LoginLinkHandler($urlGenerator->reveal(), $userProvider->reveal(), $signatureHasher->reveal(), $storage, $options);
        $this->assertSame($user, $handler->consumeLoginLink($request));
    }

    public function testConsumeLoginLinkWithUserNotFoundException(): void
    {
        $this->expectException(InvalidLoginLinkException::class);
        $this->expectExceptionMessage('User not found.');
        $options = [
            'liftime' => 300,
            'route_name' => 'login_link_route',
        ];
        $signatureHasher = $this->prophesize(SignatureHasher::class);
        $storage = new ExpiredSignatureStorage(new ArrayAdapter(), 2);
        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $userProvider->loadUserByIdentifier('foo')->shouldBeCalledOnce()->willThrow(new UserNotFoundException());

        $request = new Request(
            query: [
                'user' => 'foo',
                'hash' => '01GS7GNZNEJZKXTXMX88NVE9CA',
                'expires' => 123456789,
            ]
        );

        $handler = new LoginLinkHandler($urlGenerator->reveal(), $userProvider->reveal(), $signatureHasher->reveal(), $storage, $options);
        $handler->consumeLoginLink($request);
    }

    #[TestWith([new ExpiredSignatureException(), ExpiredLoginLinkException::class])]
    #[TestWith([new InvalidSignatureException(), InvalidLoginLinkException::class])]
    public function testConsumeLoginLinkWithInvalidSignatureHash(\Throwable $exception, string $expectedException): void
    {
        $this->expectException($expectedException);
        $options = [
            'liftime' => 300,
            'route_name' => 'login_link_route',
        ];
        $signatureHasher = $this->prophesize(SignatureHasher::class);
        $storage = new ExpiredSignatureStorage(new ArrayAdapter(), 2);
        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $user = new User();
        $userProvider->loadUserByIdentifier('foo')->shouldBeCalledOnce()->willReturn($user);
        $signatureHasher->verifySignatureHash($user, 123456789, '01GS7GNZNEJZKXTXMX88NVE9CA')->willThrow($exception);

        $request = new Request(
            query: [
                'user' => 'foo',
                'hash' => '01GS7GNZNEJZKXTXMX88NVE9CA',
                'expires' => 123456789,
            ]
        );

        $handler = new LoginLinkHandler($urlGenerator->reveal(), $userProvider->reveal(), $signatureHasher->reveal(), $storage, $options);
        $handler->consumeLoginLink($request);
    }

    public function testCreateLoginLinkWithoutRequest(): void
    {
        $options = [
            'liftime' => 300,
            'route_name' => 'login_link_route',
        ];
        $signatureHasher = $this->prophesize(SignatureHasher::class);
        $storage = new ExpiredSignatureStorage(new ArrayAdapter(), 2);
        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $user = $this->prophesize(UserInterface::class);
        $userProvider = $this->prophesize(UserProviderInterface::class);

        $signatureHasher->computeSignatureHash($user->reveal(), Argument::type('int'))->shouldBeCalledOnce()->willReturn('01GS7DNF3T07D4X2P4MSSYVXM1');
        $urlGenerator->generate($options['route_name'], Argument::type('array'), UrlGeneratorInterface::ABSOLUTE_URL)
            ->shouldBeCalledOnce()->willReturn('www.kasie-lynch.example/natus_ea');
        $user->getUserIdentifier()->shouldBeCalledOnce()->willReturn('Lou');

        $handler = new LoginLinkHandler($urlGenerator->reveal(), $userProvider->reveal(), $signatureHasher->reveal(), $storage, $options);

        $loginLinkDetails = $handler->createLoginLink($user->reveal());
        $this->assertTrue($storage->has('01GS7DNF3T07D4X2P4MSSYVXM1'));
        $this->assertEquals('www.kasie-lynch.example/natus_ea', $loginLinkDetails->getUrl());
    }

    public function testCreateLoginLinkWithRequest(): void
    {
        $options = [
            'liftime' => 300,
            'route_name' => 'login_link_route',
        ];
        $signatureHasher = $this->prophesize(SignatureHasher::class);
        $storage = new ExpiredSignatureStorage(new ArrayAdapter(), 2);
        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $user = $this->prophesize(UserInterface::class);
        $userProvider = $this->prophesize(UserProviderInterface::class);

        $signatureHasher->computeSignatureHash($user->reveal(), Argument::type('int'))->shouldBeCalledOnce()->willReturn('01GS7DNF3T07D4X2P4MSSYVXM1');
        $urlGenerator->generate($options['route_name'], Argument::type('array'), UrlGeneratorInterface::ABSOLUTE_URL)
            ->shouldBeCalledOnce()->willReturn('www.kasie-lynch.example/natus_ea');
        $urlGenerator->getContext()->shouldBeCalledOnce()->willReturn(new RequestContext());
        $urlGenerator->setContext(Argument::type(RequestContext::class))->shouldBeCalledTimes(2);
        $user->getUserIdentifier()->shouldBeCalledOnce()->willReturn('Lou');

        $handler = new LoginLinkHandler($urlGenerator->reveal(), $userProvider->reveal(), $signatureHasher->reveal(), $storage, $options);

        $request = new Request();
        $request->setDefaultLocale('de_de');
        $loginLinkDetails = $handler->createLoginLink($user->reveal(), $request);
        $this->assertTrue($storage->has('01GS7DNF3T07D4X2P4MSSYVXM1'));
        $this->assertEquals('www.kasie-lynch.example/natus_ea', $loginLinkDetails->getUrl());
    }
}
