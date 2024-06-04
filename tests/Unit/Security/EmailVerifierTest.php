<?php

declare(strict_types=1);

namespace App\UnitTests\Security;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifierTest extends TestCase
{
    use ProphecyTrait;
    private EntityManagerInterface|ObjectProphecy $em;
    private VerifyEmailHelperInterface|ObjectProphecy $emailHelper;
    private MailerInterface|ObjectProphecy $mailer;

    protected function setUp(): void
    {
        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->emailHelper = $this->prophesize(VerifyEmailHelperInterface::class);
        $this->mailer = $this->prophesize(MailerInterface::class);
    }

    public function testSendEmailConfirmation(): void
    {
        $userId = '4b174637-16d9-401f-a596-3914f70820ce';
        $userEmail = 'deangelo.marvin@example.com';
        $user = new User();
        $user->setId(Uuid::fromString($userId));
        $user->setEmail($userEmail);

        $emailTemplate = new TemplatedEmail();
        $route = 'foo';

        $signatureComponents = new VerifyEmailSignatureComponents(
            new \DateTimeImmutable('+1 hour'),
            'foo?token=blabla',
            time() + 1000
        );
        $this->emailHelper->generateSignature(
            $route,
            $userId,
            $userEmail,
            ['id' => $user->getId()]
        )->willReturn($signatureComponents);

        $this->getVerifier()->sendEmailConfirmation($route, $user, $emailTemplate);
        $this->mailer->send($emailTemplate)->shouldHaveBeenCalledOnce();

        $this->assertEquals('foo?token=blabla', $emailTemplate->getContext()['signedUrl']);
        $this->assertEquals('%count% minute|%count% minutes', $emailTemplate->getContext()['expiresAtMessageKey']);
    }

    public function testHandleEmailConfirmation(): void
    {
        $request = new Request(
            server: [
                'SERVER_NAME' => 'hudson.biz',
                'HTTPS' => 'off',
            ]
        );
        $userId = '4b174637-16d9-401f-a596-3914f70820ce';
        $userEmail = 'deangelo.marvin@example.com';
        $user = new User();
        $user->setId(Uuid::fromString($userId));
        $user->setEmail($userEmail);

        $this->emailHelper->validateEmailConfirmation($request->getUri(), $userId, $userEmail)->shouldBeCalledOnce();
        $this->em->persist($user)->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();

        $this->getVerifier()->handleEmailConfirmation($request, $user);

        $this->assertTrue($user->isVerified());
    }

    private function getVerifier(): EmailVerifier
    {
        return new EmailVerifier(
            $this->emailHelper->reveal(),
            $this->mailer->reveal(),
            $this->em->reveal()
        );
    }
}
