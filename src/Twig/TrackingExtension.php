<?php

namespace App\Twig;

use App\Service\Tracking\Matomo;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TrackingExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('tracking', [Matomo::class, 'track']),
        ];
    }
}
