<?php

namespace App\EventListener;

use Detection\MobileDetect;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener]
class MobileDetectListener
{
    public const DESKTOP = 'desktop';
    public const Mobile = 'mobile';
    public const TABLET = 'tablet';

    public function __construct(
        private readonly MobileDetect $mobileDetect,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if ($event->getRequest()->attributes->get('_route') !== '_wdt') {
            $this->mobileDetect->setUserAgent($event->getRequest()->headers->get('user-agent'));
            $event->getRequest()->attributes->set('isMobile', $this->getMobile());
        }
    }

    private function getMobile(): string
    {
        if ($this->mobileDetect->isMobile()) {
            return $this->mobileDetect->isTablet() ? self::TABLET : self::Mobile;
        }

        return self::DESKTOP;
    }
}
