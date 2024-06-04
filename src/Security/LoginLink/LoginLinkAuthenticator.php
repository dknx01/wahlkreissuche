<?php

namespace App\Security\LoginLink;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\InteractiveAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\LoginLink\Exception\InvalidLoginLinkAuthenticationException;
use Symfony\Component\Security\Http\LoginLink\Exception\InvalidLoginLinkExceptionInterface;

/**
 * @method TokenInterface createToken(Passport $passport, string $firewallName)
 */
class LoginLinkAuthenticator extends AbstractAuthenticator implements InteractiveAuthenticatorInterface
{
    /**
     * @var array<string, string|bool>
     */
    private array $options;

    /**
     * @param array<string, string|bool> $options
     */
    public function __construct(
        private LoginLinkHandler $loginLinkHandler,
        private HttpUtils $httpUtils,
        private AuthenticationSuccessHandlerInterface $successHandler,
        private AuthenticationFailureHandlerInterface $failureHandler,
        array $options
    ) {
        $this->options = $options + ['check_post_only' => false];
    }

    public function supports(Request $request): ?bool
    {
        return $this->httpUtils->checkRequestPath($request, $this->options['check_route']);
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->get('?user');
        if (!$username) {
            $username = $request->get('user');
        }
        if (!$username) {
            throw new InvalidLoginLinkAuthenticationException('Missing user from link.');
        }

        return new SelfValidatingPassport(
            new UserBadge($username, function () use ($request) {
                try {
                    $user = $this->loginLinkHandler->consumeLoginLink($request);
                } catch (InvalidLoginLinkExceptionInterface $e) {
                    throw new InvalidLoginLinkAuthenticationException('Login link could not be validated.', 0, $e);
                }

                return $user;
            }),
            [new RememberMeBadge()]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return $this->successHandler->onAuthenticationSuccess($request, $token);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return $this->failureHandler->onAuthenticationFailure($request, $exception);
    }

    public function isInteractive(): bool
    {
        return true;
    }
}
