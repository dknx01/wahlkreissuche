<?php

namespace App\Security\LoginLink;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Security\Core\Signature\Exception\InvalidSignatureException;

/**
 * @author Ryan Weaver <ryan@symfonycasts.com>
 */
final class ExpiredSignatureStorage
{
    public function __construct(private CacheItemPoolInterface $cache, private int $lifetime)
    {
    }

    public function countUsages(string $hash): int
    {
        $key = rawurlencode($hash);
        if (!$this->cache->hasItem($key)) {
            return 0;
        }

        return $this->cache->getItem($key)->get()->getUsage();
    }

    public function incrementUsages(string $hash): void
    {
        $item = $this->cache->getItem(rawurlencode($hash));

        if (!$item->isHit()) {
            $loginLinkItem = new LoginLinkItem($this->lifetime, $hash, 0);
            $item->expiresAfter($this->lifetime);
        } else {
            /** @var LoginLinkItem $loginLinkItem */
            $loginLinkItem = $item->get();
            $loginLinkItem->setUsage($loginLinkItem->getUsage() + 1);
        }
        $item->set($loginLinkItem);
        $this->cache->save($item);
    }

    public function save(string $hash, int $expiredAt): void
    {
        $item = $this->cache->getItem(rawurlencode($hash));

        if (!$item->isHit()) {
            $item->expiresAfter($expiredAt);
            $item->set(new LoginLinkItem($expiredAt, $hash, 0));
        }

        $this->cache->save($item);
    }

    public function delete(string $hash): void
    {
        $this->cache->deleteItem(rawurlencode($hash));
    }

    public function has(string $hash): bool
    {
        return $this->cache->getItem(rawurlencode($hash))->isHit();
    }

    public function get(string $hash): LoginLinkItem
    {
        if (!$this->has($hash)) {
            throw new InvalidSignatureException('Unknown signature');
        }

        return $this->cache->getItem(rawurlencode($hash))->get();
    }
}
