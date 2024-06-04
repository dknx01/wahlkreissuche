<?php

namespace App\UnitTests\Twig;

use App\Service\Tracking\Matomo;
use App\Twig\TrackingExtension;
use PHPUnit\Framework\TestCase;

class TrackingExtensionTest extends TestCase
{
    public function testGetFunctions(): void
    {
        $extension = new TrackingExtension();

        $functions = $extension->getFunctions();

        $this->assertCount(1, $functions);
        $this->assertSame('tracking', $functions[0]->getName());
        $this->assertEquals([Matomo::class, 'track'], $functions[0]->getCallable());
    }
}
