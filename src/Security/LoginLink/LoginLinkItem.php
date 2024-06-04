<?php

namespace App\Security\LoginLink;

class LoginLinkItem
{
    public function __construct(private int $expires, private string $hash, private int $usage)
    {
    }

    public function getExpires(): int
    {
        return $this->expires;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getUsage(): int
    {
        return $this->usage;
    }

    public function setUsage(int $usage): LoginLinkItem
    {
        $this->usage = $usage;

        return $this;
    }
}
