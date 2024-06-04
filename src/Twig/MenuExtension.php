<?php

declare(strict_types=1);

namespace App\Twig;

use App\Twig\Menu\MenuRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('poster_states', [MenuRuntime::class, 'getPosterStates']),
            new TwigFunction('wish_poster_states', [MenuRuntime::class, 'getWishPosterStates']),
            new TwigFunction('poster_states_with_berlin', [MenuRuntime::class, 'getPosterStatesWithBerlin']),
            new TwigFunction('state_center', [MenuRuntime::class, 'getStateCenter']),
            new TwigFunction('poster_city_district', [MenuRuntime::class, 'getPosterCityDistrict']),
            new TwigFunction('ltw_states', [MenuRuntime::class, 'getLtwStates']),
            new TwigFunction('isMobile', [MenuRuntime::class, 'isMobile']),
        ];
    }
}
