<?php

declare(strict_types=1);

namespace App\UnitTests\Security\LoginLink;

use App\Entity\User;
use App\Security\LoginLink\ExpiredSignatureStorage;
use App\Security\LoginLink\LoginLinkItem;
use App\Security\LoginLink\SignatureHasher;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\Signature\Exception\ExpiredSignatureException;
use Symfony\Component\Security\Core\Signature\Exception\InvalidSignatureException;

class SignatureHasherTest extends TestCase
{
    use ProphecyTrait;

    private ExpiredSignatureStorage $storage;
    private ArrayAdapter $cache;
    private string $hash = 'cle417nfe00017s13i2zrj3e5';

    protected function setUp(): void
    {
        $this->cache = new ArrayAdapter();
        $this->storage = new ExpiredSignatureStorage($this->cache, 2);
    }

    public function testVerifySignatureHashWithOldRequest(): void
    {
        $this->expectException(ExpiredSignatureException::class);
        $this->expectExceptionMessage('Signature has expired.');

        $signatureHasher = new SignatureHasher(new PropertyAccessor(), [], '', $this->storage, 5);
        $signatureHasher->verifySignatureHash(new User(), time() - 100, $this->hash);
    }

    public function testVerifySignatureHashWithOldRequestAndCacheContent(): void
    {
        $this->expectException(ExpiredSignatureException::class);
        $this->expectExceptionMessage('Signature has expired.');
        $this->storage->save($this->hash, (new \DateTime())->modify('-1 month')->getTimestamp());

        $signatureHasher = new SignatureHasher(new PropertyAccessor(), [], '', $this->storage, 5);
        $signatureHasher->verifySignatureHash(new User(), time() - 100, $this->hash);
        $this->assertFalse($this->storage->has($this->hash));
    }

    public function testVerifySignatureHashWithUnknownSignature(): void
    {
        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Unknown signature.');

        $signatureHasher = new SignatureHasher(new PropertyAccessor(), [], '', $this->storage, 5);
        $signatureHasher->verifySignatureHash(new User(), time() + 100, $this->hash);
    }

    public function testVerifySignatureHashWithInvalidSignature(): void
    {
        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Invalid signature');

        $item = $this->cache->getItem(rawurlencode($this->hash));

        if (!$item->isHit()) {
            $expiredAt = (new \DateTime())->modify('+5 seconds')
                ->getTimestamp();
            $item->expiresAfter($expiredAt);
            $item->set(new LoginLinkItem($expiredAt, 'cle41i0gi00027s137mmaze3l', 0));
        }

        $this->cache->save($item);

        $signatureHasher = new SignatureHasher(new PropertyAccessor(), [], '', $this->storage, 5);
        $signatureHasher->verifySignatureHash(new User(), time() + 100, $this->hash);

        $this->assertFalse($this->cache->hasItem($this->hash));
    }

    public function testVerifySignatureHashWithSignatureCountExceeded(): void
    {
        $this->expectException(ExpiredSignatureException::class);
        $this->expectExceptionMessage('Signature can only be used "5" times.');

        $item = $this->cache->getItem(rawurlencode($this->hash));

        if (!$item->isHit()) {
            $expiredAt = (new \DateTime())->modify('+5 seconds')
                ->getTimestamp();
            $item->expiresAfter($expiredAt);
            $item->set(new LoginLinkItem($expiredAt, $this->hash, 5));
        }

        $this->cache->save($item);

        $signatureHasher = new SignatureHasher(new PropertyAccessor(), [], '', $this->storage, 5);
        $signatureHasher->verifySignatureHash(new User(), time() + 100, $this->hash);

        $this->assertFalse($this->cache->hasItem($this->hash));
    }

    public function testVerifySignatureHashSuccessful(): void
    {
        $item = $this->cache->getItem(rawurlencode($this->hash));

        if (!$item->isHit()) {
            $expiredAt = (new \DateTime())->modify('+5 seconds')
                ->getTimestamp();
            $item->expiresAfter($expiredAt);
            $item->set(new LoginLinkItem($expiredAt, $this->hash, 0));
        }

        $this->cache->save($item);

        $signatureHasher = new SignatureHasher(new PropertyAccessor(), [], '', $this->storage, 5);
        $signatureHasher->verifySignatureHash(new User(), time() + 100, $this->hash);

        $this->asserttrue($this->cache->hasItem($this->hash));
    }

    public function testComputeSignatureHash(): void
    {
        $signatureHasher = new SignatureHasher(new PropertyAccessor(), ['id', 'registeredAt'], '', $this->storage, 5);
        $user = new User();
        $user->setId(Uuid::uuid4());
        $user->setUsername('foo');
        $user->setRegisteredAt(\DateTimeImmutable::createFromInterface((new \DateTime())->modify('-1 day')));
        $this->assertNotEmpty($signatureHasher->computeSignatureHash($user, 2));
    }

    public function testComputeSignatureHashWithInvalidField(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The property path "roles" on the user object "App\Entity\User" must return a value that can be cast to a string, but "array" was returned.');

        $signatureHasher = new SignatureHasher(new PropertyAccessor(), ['id', 'registeredAt', 'roles'], '', $this->storage, 5);
        $user = new User();
        $user->setId(Uuid::uuid4());
        $user->setUsername('foo');
        $user->setRoles(['bla']);
        $user->setRegisteredAt(\DateTimeImmutable::createFromInterface((new \DateTime())->modify('-1 day')));
        $signatureHasher->computeSignatureHash($user, 2);
    }
}
