<?php

declare(strict_types=1);

namespace App\UnitTests\Security\LoginLink;

use App\Security\LoginLink\ExpiredSignatureStorage;
use App\Security\LoginLink\LoginLinkItem;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Security\Core\Signature\Exception\InvalidSignatureException;

class ExpiredSignatureStorageTest extends TestCase
{
    use ProphecyTrait;

    public function testSave(): void
    {
        $lifetime = 1;
        $cache = new ArrayAdapter();
        $hash = '/foo/blblbl&expired=6021643';

        $storage = new ExpiredSignatureStorage($cache, $lifetime);
        $storage->save($hash, (new \DateTime('+1 sec'))->getTimestamp());

        $this->assertTrue($storage->has($hash));
    }

    public function testGetInvalidHash(): void
    {
        $this->expectException(InvalidSignatureException::class);

        $cache = new ArrayAdapter();
        $hash = '/foo/blblbl&expired=6021643';

        $storage = new ExpiredSignatureStorage($cache, 1);
        $storage->get($hash);
    }

    public function testGet(): void
    {
        $cache = new ArrayAdapter();
        $hash = '/foo/blblbl&expired=6021643';

        $storage = new ExpiredSignatureStorage($cache, 1);
        $item = $cache->getItem(rawurlencode($hash));
        $loginLinkItem = new LoginLinkItem(2, $hash, 0);
        $item->expiresAfter(2);
        $item->set($loginLinkItem);
        $cache->save($item);

        $this->assertEquals($loginLinkItem, $storage->get($hash));
    }

    public function testCountUsagesForNewItem(): void
    {
        $lifetime = 2;
        $cache = new ArrayAdapter();
        $storage = new ExpiredSignatureStorage($cache, $lifetime);
        $this->assertEquals(0, $storage->countUsages('01GS5MAFGW2SPG0VSCK6KMEW1E'));
    }

    public function testCountUsagesForCachedItem(): void
    {
        $lifetime = 2;
        $cache = new ArrayAdapter();
        $hash = '/foo/bar&expired=6021643';
        $item = $cache->getItem(rawurlencode($hash));
        $loginLinkItem = new LoginLinkItem($lifetime, $hash, 5);
        $item->expiresAfter($lifetime);
        $item->set($loginLinkItem);
        $cache->save($item);

        $storage = new ExpiredSignatureStorage($cache, $lifetime);
        $this->assertEquals(5, $storage->countUsages($hash));
    }

    public function testDelete(): void
    {
        $lifetime = 2;
        $cache = new ArrayAdapter();
        $hash = '/foo/bar&expired=6021643';
        $item = $cache->getItem(rawurlencode($hash));
        $loginLinkItem = new LoginLinkItem($lifetime, $hash, 5);
        $item->expiresAfter($lifetime);
        $item->set($loginLinkItem);
        $cache->save($item);

        $storage = new ExpiredSignatureStorage($cache, $lifetime);
        $storage->delete($hash);
        $this->assertFalse($storage->has($hash));
    }

    public function testIncrementUsagesNewItem(): void
    {
        $cache = new ArrayAdapter();
        $lifetime = 2;

        $hash = '/foo/cle2yjihz00007s137w3kut6w&expired=6021643';

        $storage = new ExpiredSignatureStorage($cache, $lifetime);
        $storage->incrementUsages($hash);

        $this->assertTrue($storage->has($hash));
    }

    public function testIncrementUsagesExistingItem(): void
    {
        $cache = new ArrayAdapter();
        $lifetime = 2;

        $hash = '/foo/blbldfsfdsfs&expired=6021643';
        $item = $cache->getItem(rawurlencode($hash));
        $loginLinkItem = new LoginLinkItem($lifetime, $hash, 5);
        $item->expiresAfter($lifetime);
        $item->set($loginLinkItem);
        $cache->save($item);

        $storage = new ExpiredSignatureStorage($cache, $lifetime);
        $storage->incrementUsages($hash);

        $loginLinkItem1 = $storage->get($hash);
        $this->assertEquals(6, $loginLinkItem1->getUsage());
        $this->assertEquals(2, $loginLinkItem1->getExpires());
        $this->assertNotEmpty($loginLinkItem1->getHash());
    }
}
