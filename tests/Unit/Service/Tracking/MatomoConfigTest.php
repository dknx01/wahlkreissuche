<?php

namespace App\UnitTests\Service\Tracking;

use App\Service\Tracking\MatomoConfig;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MatomoConfigTest extends TestCase
{
    public function testConfigNonProd(): void
    {
        $config = new MatomoConfig(
            false,
            'foo',
            'http://example.test',
            'https://matomo.test',
            '?tracking',
            '1',
            'foo.png',
            true
        );

        $this->assertFalse($config->isEnabled());
        $this->assertEquals('http://example.test', $config->getCookieDomain());
        $this->assertEquals('https://matomo.test', $config->getMatomoDomain());
        $this->assertEquals('?tracking', $config->getTrackerUrl());
        $this->assertEquals('1', $config->getSiteId());
        $this->assertEquals('foo.png', $config->getNoScriptImage());
        $this->assertTrue($config->isServerSide());
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function testConfigProd(bool $enabled): void
    {
        $config = new MatomoConfig(
            $enabled,
            'prod',
            'http://example.test',
            'https://matomo.test',
            '?tracking',
            '1',
            'foo.png',
            true
        );

        $this->assertEquals($enabled, $config->isEnabled());
        $this->assertEquals('http://example.test', $config->getCookieDomain());
        $this->assertEquals('https://matomo.test', $config->getMatomoDomain());
        $this->assertEquals('?tracking', $config->getTrackerUrl());
        $this->assertEquals('1', $config->getSiteId());
        $this->assertEquals('foo.png', $config->getNoScriptImage());
        $this->assertTrue($config->isServerSide());
    }
}
