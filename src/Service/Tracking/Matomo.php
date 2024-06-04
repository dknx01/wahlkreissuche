<?php

namespace App\Service\Tracking;

use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\RuntimeExtensionInterface;

class Matomo implements RuntimeExtensionInterface
{
    public function __construct(private \MatomoTracker $matomoTracker, private string $env, private bool $enabled)
    {
    }

    public function track(Request $request, string $pageTitle): void
    {
        if ($this->env !== 'prod' || !$this->enabled) {
            return;
        }
        $this->matomoTracker->setIp($request->getClientIp());
        $this->matomoTracker->setUserAgent($request->headers->get('user-agent'));
        $this->matomoTracker->setBrowserLanguage($request->getLanguages()[0]);
        $this->matomoTracker->setUrl($request->getUri());
        $this->matomoTracker->setUrlReferrer($request->headers->get('referer', ''));
        // Sends Tracker request via http
        $this->matomoTracker->doTrackPageView($pageTitle);
    }
}
