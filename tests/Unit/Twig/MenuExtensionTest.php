<?php

namespace App\UnitTests\Twig;

use App\Twig\Menu\MenuRuntime;
use App\Twig\MenuExtension;
use PHPUnit\Framework\TestCase;

class MenuExtensionTest extends TestCase
{
    private static array $expectedExtentions = [
        'poster_states' => [MenuRuntime::class, 'getPosterStates'],
        'poster_states_with_berlin' => [MenuRuntime::class, 'getPosterStatesWithBerlin'],
        'state_center' => [MenuRuntime::class, 'getStateCenter'],
        'poster_city_district' => [MenuRuntime::class, 'getPosterCityDistrict'],
        'wish_poster_states' => [MenuRuntime::class, 'getWishPosterStates'],
        'ltw_states' => [MenuRuntime::class, 'getLtwStates'],
        'isMobile' => [MenuRuntime::class, 'isMobile'],
    ];

    public function testGetFunctions(): void
    {
        $extension = new MenuExtension();

        foreach ($extension->getFunctions() as $function) {
            $this->assertArrayHasKey($function->getName(), self::$expectedExtentions);
            $this->assertEquals(self::$expectedExtentions[$function->getName()], $function->getCallable());
        }
    }
}
