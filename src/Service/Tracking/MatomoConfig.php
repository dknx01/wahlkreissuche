<?php

namespace App\Service\Tracking;

class MatomoConfig
{
    public function __construct(
        private bool $enabled,
        private string $env,
        private string $cookieDomain,
        private string $matomoDomain,
        private string $trackerUrl,
        private string $siteId,
        private string $noScriptImage,
        private bool $serverSide,
    ) {
    }

    public function getCookieDomain(): string
    {
        return $this->cookieDomain;
    }

    public function getMatomoDomain(): string
    {
        return $this->matomoDomain;
    }

    public function getTrackerUrl(): string
    {
        return $this->trackerUrl;
    }

    public function getSiteId(): string
    {
        return $this->siteId;
    }

    public function getNoScriptImage(): string
    {
        return $this->noScriptImage;
    }

    public function isEnabled(): bool
    {
        return $this->enabled && ($this->env === 'prod');
    }

    public function isServerSide(): bool
    {
        return $this->serverSide;
    }
}
