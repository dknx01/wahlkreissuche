<?php

declare(strict_types=1);

namespace App\UnitTests\Security\LoginLink;

use App\Entity\User;
use App\Security\LoginLink\LoginLinkAuthenticator;
use App\Security\LoginLink\LoginLinkHandler;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\LoginLink\Exception\ExpiredLoginLinkException;
use Symfony\Component\Security\Http\LoginLink\Exception\InvalidLoginLinkAuthenticationException;

class LoginLinkAuthenticatorTest extends TestCase
{
    use ProphecyTrait;

    public function testOnAuthenticationFailure(): void
    {
        $request = new Request();
        $exception = new AuthenticationException();
        $authenticationFailureHandler = $this->prophesize(AuthenticationFailureHandlerInterface::class);
        $authenticationFailureHandler->onAuthenticationFailure($request, $exception)
            ->shouldBeCalledOnce()
            ->willReturn(new Response());
        $authenticator = new LoginLinkAuthenticator(
            $this->prophesize(LoginLinkHandler::class)->reveal(),
            new HttpUtils(),
            $this->prophesize(AuthenticationSuccessHandlerInterface::class)->reveal(),
            $authenticationFailureHandler->reveal(),
            []
        );
        $authenticator->onAuthenticationFailure($request, $exception);
    }

    public function testIsInteractive(): void
    {
        $authenticator = new LoginLinkAuthenticator(
            $this->prophesize(LoginLinkHandler::class)->reveal(),
            new HttpUtils(),
            $this->prophesize(AuthenticationSuccessHandlerInterface::class)->reveal(),
            $this->prophesize(AuthenticationFailureHandlerInterface::class)->reveal(),
            []
        );
        $this->assertTrue($authenticator->isInteractive());
    }

    #[TestWith([true, true])]
    #[TestWith([false, false])]
    public function testSupports(?bool $expected, ?bool $checkResult): void
    {
        $httpUtils = $this->prophesize(HttpUtils::class);
        $request = new Request();
        $httpUtils->checkRequestPath($request, 'check/route')->willReturn($checkResult);
        $authenticator = new LoginLinkAuthenticator(
            $this->prophesize(LoginLinkHandler::class)->reveal(),
            $httpUtils->reveal(),
            $this->prophesize(AuthenticationSuccessHandlerInterface::class)->reveal(),
            $this->prophesize(AuthenticationFailureHandlerInterface::class)->reveal(),
            ['check_route' => 'check/route']
        );
        $this->assertEquals($expected, $authenticator->supports($request));
    }

    public function testAuthenticateInvalidUserWithoutQuestionMarkInUrl(): void
    {
        $this->expectException(InvalidLoginLinkAuthenticationException::class);
        $this->expectExceptionMessage('Missing user from link.');

        $authenticator = new LoginLinkAuthenticator(
            $this->prophesize(LoginLinkHandler::class)->reveal(),
            new HttpUtils(),
            $this->prophesize(AuthenticationSuccessHandlerInterface::class)->reveal(),
            $this->prophesize(AuthenticationFailureHandlerInterface::class)->reveal(),
            ['check_route' => 'check/route']
        );
        $request = new Request();
        $authenticator->authenticate($request);
    }

    public function testAuthenticateWithInvalidLoginLinkAuthenticationException(): void
    {
        $this->expectException(InvalidLoginLinkAuthenticationException::class);
        $this->expectExceptionMessage('Login link could not be validated.');
        $request = new Request(['?user' => 'foo']);
        $exception = new ExpiredLoginLinkException();

        $loginLinkHandler = $this->prophesize(LoginLinkHandler::class);
        $loginLinkHandler->consumeLoginLink($request)
            ->shouldBeCalledOnce()
            ->willThrow($exception);
        $authenticator = new LoginLinkAuthenticator(
            $loginLinkHandler->reveal(),
            new HttpUtils(),
            $this->prophesize(AuthenticationSuccessHandlerInterface::class)->reveal(),
            $this->prophesize(AuthenticationFailureHandlerInterface::class)->reveal(),
            ['check_route' => 'check/route']
        );

        $authenticator->authenticate($request)->getUser();
    }

    public function testAuthenticateSuccessful(): void
    {
        $request = new Request(['user' => 'foo']);

        $loginLinkHandler = $this->prophesize(LoginLinkHandler::class);
        $loginLinkHandler->consumeLoginLink($request)
            ->shouldBeCalledOnce()
            ->willReturn(new User());
        $authenticator = new LoginLinkAuthenticator(
            $loginLinkHandler->reveal(),
            new HttpUtils(),
            $this->prophesize(AuthenticationSuccessHandlerInterface::class)->reveal(),
            $this->prophesize(AuthenticationFailureHandlerInterface::class)->reveal(),
            ['check_route' => 'check/route']
        );

        $passport = $authenticator->authenticate($request);
        $this->assertInstanceOf(User::class, $passport->getUser());
        $this->assertTrue($passport->hasBadge(UserBadge::class));
        $this->assertTrue($passport->hasBadge(RememberMeBadge::class));
    }

    public function testOnAuthenticationSuccess(): void
    {
        $request = new Request();
        $token = $this->prophesize(TokenInterface::class)->reveal();
        $authenticationSuccessHandler = $this->prophesize(AuthenticationSuccessHandlerInterface::class);
        $authenticationSuccessHandler->onAuthenticationSuccess($request, $token)
            ->shouldBeCalledOnce()
            ->willReturn(new Response());
        $authenticator = new LoginLinkAuthenticator(
            $this->prophesize(LoginLinkHandler::class)->reveal(),
            new HttpUtils(),
            $authenticationSuccessHandler->reveal(),
            $this->prophesize(AuthenticationFailureHandlerInterface::class)->reveal(),
            []
        );
        $authenticator->onAuthenticationSuccess($request, $token, 'foo');
    }
}
