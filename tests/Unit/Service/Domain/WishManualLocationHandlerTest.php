<?php

namespace App\UnitTests\Service\Domain;

use App\Repository\WishElectionPosterRepository;
use App\Service\Domain\WishManualLocationHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class WishManualLocationHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testGetCityChoicesWithSubmitted(): void
    {
        $repo = $this->prophesize(WishElectionPosterRepository::class);
        $repo->findAllCities()->shouldBeCalledTimes(2)->willReturn(
            [
                'South Jacqulyn',
                'New Altonside',
            ]
        );
        $handler = new WishManualLocationHandler(
            $repo->reveal(),
        );
        $handler->setCity('Flourish & Blotts');

        $this->assertEquals(
            [
                'South Jacqulyn' => 'South Jacqulyn',
                'New Altonside' => 'New Altonside',
                'Flourish & Blotts' => 'Flourish & Blotts',
            ],
            $handler->getCityChoices()
        );
    }

    public function testGetCityChoices(): void
    {
        $repo = $this->prophesize(WishElectionPosterRepository::class);
        $repo->findAllCities()->shouldBeCalledTimes(2)->willReturn(
            [
                'South Jacqulyn',
                'New Altonside',
            ]
        );
        $handler = new WishManualLocationHandler(
            $repo->reveal(),
        );

        $this->assertEquals(
            [
                'South Jacqulyn' => 'South Jacqulyn',
                'New Altonside' => 'New Altonside',
            ],
            $handler->getCityChoices()
        );
    }

    public function testGetDistrictChoicesWithSubmitted(): void
    {
        $repo = $this->prophesize(WishElectionPosterRepository::class);
        $repo->findAllDistricts()->willReturn(
            ['Western Spiral Arm', 'Oglaroon']
        );
        $handler = new WishManualLocationHandler(
            $repo->reveal(),
        );
        $handler->setDistrict('Bournemouth');

        $this->assertEquals(
            [
                'Western Spiral Arm' => 'Western Spiral Arm',
                'Oglaroon' => 'Oglaroon',
                'Bournemouth' => 'Bournemouth',
            ],
            $handler->getDistrictChoices()
        );
    }

    public function testGetDistrictChoices(): void
    {
        $repo = $this->prophesize(WishElectionPosterRepository::class);
        $repo->findAllDistricts()->willReturn(
            ['Western Spiral Arm', 'Oglaroon']
        );
        $handler = new WishManualLocationHandler(
            $repo->reveal(),
        );

        $this->assertEquals(
            [
                'Western Spiral Arm' => 'Western Spiral Arm',
                'Oglaroon' => 'Oglaroon',
            ],
            $handler->getDistrictChoices()
        );
    }
}
