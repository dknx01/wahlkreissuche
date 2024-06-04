<?php

namespace App\Security\LoginLink;

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
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class LoginLinkHandler implements LoginLinkHandlerInterface
{
    /** @var array<string, int|string|null> */
    private array $options;

    /**
     * @param array<string, int|string|null> $options
     */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private UserProviderInterface $userProvider,
        private SignatureHasher $signatureHashUtil,
        private ExpiredSignatureStorage $expiredSignatureStorage,
        array $options
    ) {
        $this->options = array_merge([
            'route_name' => null,
            'lifetime' => 600,
        ], $options);
    }

    public function createLoginLink(UserInterface $user, ?Request $request = null, ?int $lifetime = null): LoginLinkDetails
    {
        $expires = time() + $this->options['lifetime'];
        $expiresAt = new \DateTimeImmutable('@' . $expires);

        $parameters = [
            'user' => $user->getUserIdentifier(),
            'expires' => $expires,
            'hash' => $this->signatureHashUtil->computeSignatureHash($user, $expires),
        ];
        $this->expiredSignatureStorage->save($parameters['hash'], $expires);

        if ($request) {
            $currentRequestContext = $this->urlGenerator->getContext();
            $this->urlGenerator->setContext(
                (new RequestContext())
                    ->fromRequest($request)
                    ->setParameter('_locale', $request->getLocale())
            );
        }

        try {
            $url = $this->urlGenerator->generate(
                $this->options['route_name'],
                $parameters,
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        } finally {
            if ($request) {
                $this->urlGenerator->setContext($currentRequestContext);
            }
        }

        return new LoginLinkDetails($url, $expiresAt);
    }

    public function consumeLoginLink(Request $request): UserInterface
    {
        $userIdentifier = $request->get('?user');
        if (!$userIdentifier) {
            $userIdentifier = $request->get('user');
        }
        try {
            $user = $this->userProvider->loadUserByIdentifier($userIdentifier);
        } catch (UserNotFoundException $exception) {
            throw new InvalidLoginLinkException('User not found.', 0, $exception);
        }

        $hash = $request->get('hash');
        $expires = $request->get('expires');

        try {
            $this->signatureHashUtil->verifySignatureHash($user, $expires, $hash);
        } catch (ExpiredSignatureException $e) {
            throw new ExpiredLoginLinkException(ucfirst(str_ireplace('signature', 'login link', $e->getMessage())), 0, $e);
        } catch (InvalidSignatureException $e) {
            throw new InvalidLoginLinkException(ucfirst(str_ireplace('signature', 'login link', $e->getMessage())), 0, $e);
        }

        return $user;
    }
}
