<?php

namespace App\UnitTests\EventListener;

use App\EventListener\MobileDetectListener;
use App\Kernel;
use Detection\MobileDetect;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class MobileDetectListenerTest extends TestCase
{
    private ObjectProphecy|Kernel $kernel;

    protected function setUp(): void
    {
        $this->kernel = $this->prophesize(Kernel::class);
    }

    use ProphecyTrait;

    #[TestWith([null, ['_route' => '_wdt'], []])]
    #[TestWith([MobileDetectListener::DESKTOP, ['_route' => 'foo'], ['HTTP_USER-AGENT' => 'Chrome']])]
    #[TestWith([MobileDetectListener::Mobile, ['_route' => 'foo'], ['HTTP_USER-AGENT' => 'ONEPLUS']])]
    #[TestWith([MobileDetectListener::TABLET, ['_route' => 'foo'], ['HTTP_USER-AGENT' => 'iPad']])]
    #[TestWith([MobileDetectListener::DESKTOP, ['_route' => 'foo'], ['HTTP_USER-AGENT' => 'ACMEPAD']])]
    #[TestWith([MobileDetectListener::TABLET, ['_route' => 'foo'], ['HTTP_USER-AGENT' => 'ACMETablet']])]
    public function testListener(null|string $expected, array $attributes, array $header): void
    {
        $mobileDetectListener = new MobileDetectListener(
            new MobileDetect()
        );
        $request = new Request(
            attributes: $attributes,
            server: $header
        );
        $event = new RequestEvent(
            $this->kernel->reveal(),
            $request,
            null
        );
        $mobileDetectListener($event);
        $this->assertSame($expected, $event->getRequest()->attributes->get('isMobile'));
    }
}
