<?php

namespace App\UnitTests\Service\Tracking;

use App\Service\Tracking\Matomo;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\Request;

class MatomoTest extends TestCase
{
    use ProphecyTrait;

    public function testTrackForNonProdEnv(): void
    {
        $matomoTracker = $this->prophesize(\MatomoTracker::class);
        $matomoTracker->doTrackPageView(Argument::any())->shouldNotBeCalled();
        $matomo = new Matomo(
            $matomoTracker->reveal(),
            'dev',
            true
        );
        $matomo->track(new Request(), 'test');
    }

    public function testTrackForProdEnv(): void
    {
        $request = new Request(
            server: [
                'REMOTE_ADDR' => '21.35.182.135',
                'HTTP_USER_AGENT' => 'Foo',
                'HTTP_ACCEPT_LANGUAGE' => 'de-DE',
            ],
        );
        $matomoTracker = $this->prophesize(\MatomoTracker::class);
        $matomoTracker->setIp('21.35.182.135')->shouldBeCalled();
        $matomoTracker->setUserAgent('Foo')->shouldBeCalled();
        $matomoTracker->setBrowserLanguage('de_DE')->shouldBeCalled();
        $matomoTracker->setUrl(Argument::type('string'))->shouldBeCalled();
        $matomoTracker->setUrlReferrer(Argument::type('string'))->shouldBeCalled();
        $matomoTracker->doTrackPageView(Argument::any())->shouldBeCalled();
        $matomo = new Matomo(
            $matomoTracker->reveal(),
            'prod',
            true
        );
        $matomo->track($request, 'test');
    }
}
