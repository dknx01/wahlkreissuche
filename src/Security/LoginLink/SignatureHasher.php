<?php

namespace App\Security\LoginLink;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Security\Core\Signature\Exception\ExpiredSignatureException;
use Symfony\Component\Security\Core\Signature\Exception\InvalidSignatureException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Creates and validates secure hashes used in login links and remember-me cookies.
 */
class SignatureHasher
{
    /**
     * @param string[] $signatureProperties
     */
    public function __construct(
        private PropertyAccessorInterface $propertyAccessor,
        private array $signatureProperties,
        private string $secret,
        private ExpiredSignatureStorage $expiredSignaturesStorage,
        private int $maxUses
    ) {
    }

    /**
     * Verifies the hash using the provided user and expire time.
     *
     * @param int    $expires the expiry time as a unix timestamp
     * @param string $hash    the plaintext hash provided by the request
     *
     * @throws InvalidSignatureException If the signature does not match the provided parameters
     * @throws ExpiredSignatureException If the signature is no longer valid
     */
    public function verifySignatureHash(UserInterface $user, int $expires, string $hash): void
    {
        if ($expires < time()) {
            $this->expiredSignaturesStorage->delete($hash);
            throw new ExpiredSignatureException('Signature has expired.');
        }

        if (!$this->expiredSignaturesStorage->has($hash)) {
            throw new InvalidSignatureException('Unknown signature.');
        }

        $loginLinkItem = $this->expiredSignaturesStorage->get($hash);
        if ($loginLinkItem->getHash() !== $hash) {
            $this->expiredSignaturesStorage->delete($hash);
            throw new InvalidSignatureException('Invalid signature');
        }

        if ($this->expiredSignaturesStorage->countUsages($hash) >= $this->maxUses) {
            $this->expiredSignaturesStorage->delete($hash);
            throw new ExpiredSignatureException(sprintf('Signature can only be used "%d" times.', $this->maxUses));
        }

        $this->expiredSignaturesStorage->incrementUsages($hash);
    }

    /**
     * Computes the secure hash for the provided user and expire time.
     *
     * @param int $expires the expiry time as a unix timestamp
     */
    public function computeSignatureHash(UserInterface $user, int $expires): string
    {
        $signatureFields = [
            base64_encode($user->getUserIdentifier()),
            $expires,
        ];

        foreach ($this->signatureProperties as $property) {
            $value = $this->propertyAccessor->getValue($user, $property) ?? '';
            if ($value instanceof \DateTimeInterface) {
                $value = $value->format('c');
            }

            if (!is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
                throw new \InvalidArgumentException(sprintf('The property path "%s" on the user object "%s" must return a value that can be cast to a string, but "%s" was returned.', $property, \get_class($user), get_debug_type($value)));
            }
            $signatureFields[] = base64_encode($value);
        }

        return base64_encode(hash_hmac('sha256', implode(':', $signatureFields), $this->secret));
    }
}
