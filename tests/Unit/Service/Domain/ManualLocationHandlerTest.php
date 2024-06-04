<?php

namespace App\UnitTests\Service\Domain;

use App\Repository\ElectionPosterRepository;
use App\Repository\WishElectionPosterRepository;
use App\Service\Domain\ManualLocationHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ManualLocationHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testGetCityChoicesWithSubmitted(): void
    {
        $repo = $this->prophesize(ElectionPosterRepository::class);
        $repo->findAllCities()->shouldBeCalledTimes(2)->willReturn(
            [
                'South Jacqulyn',
                'New Altonside',
            ]
        );
        $handler = new ManualLocationHandler(
            $repo->reveal(),
            $this->prophesize(WishElectionPosterRepository::class)->reveal()
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
        $repo = $this->prophesize(ElectionPosterRepository::class);
        $repo->findAllCities()->shouldBeCalledTimes(2)->willReturn(
            [
                'South Jacqulyn',
                'New Altonside',
            ]
        );
        $handler = new ManualLocationHandler(
            $repo->reveal(),
            $this->prophesize(WishElectionPosterRepository::class)->reveal()
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
        $repo = $this->prophesize(ElectionPosterRepository::class);
        $repo->findAllDistricts()->willReturn(
            ['Western Spiral Arm', 'Oglaroon']
        );
        $handler = new ManualLocationHandler(
            $repo->reveal(),
            $this->prophesize(WishElectionPosterRepository::class)->reveal()
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
        $repo = $this->prophesize(ElectionPosterRepository::class);
        $repo->findAllDistricts()->willReturn(
            ['Western Spiral Arm', 'Oglaroon']
        );
        $handler = new ManualLocationHandler(
            $repo->reveal(),
            $this->prophesize(WishElectionPosterRepository::class)->reveal()
        );

        $this->assertEquals(
            [
                'Western Spiral Arm' => 'Western Spiral Arm',
                'Oglaroon' => 'Oglaroon',
            ],
            $handler->getDistrictChoices()
        );
    }

    public function testGetCityChoicesForWishes(): void
    {
        $repo = $this->prophesize(WishElectionPosterRepository::class);
        $repo->findAllCities()->shouldBeCalledTimes(2)->willReturn(
            ['Western Spiral Arm', 'Oglaroon']
        );
        $handler = new ManualLocationHandler(
            $this->prophesize(ElectionPosterRepository::class)->reveal(),
            $repo->reveal()
        );
        $handler->setCity('Foo');

        $this->assertEquals(
            [
                'Western Spiral Arm' => 'Western Spiral Arm',
                'Oglaroon' => 'Oglaroon',
                'Foo' => 'Foo',
            ],
            $handler->getCityChoicesForWishes()
        );
    }

    public function testGetDistrictChoicesWishes(): void
    {
        $repo = $this->prophesize(WishElectionPosterRepository::class);
        $repo->findAllDistricts()->shouldBeCalledOnce()->willReturn(
            ['Western Spiral Arm', 'Oglaroon']
        );
        $handler = new ManualLocationHandler(
            $this->prophesize(ElectionPosterRepository::class)->reveal(),
            $repo->reveal()
        );
        $handler->setDistrict('Foo');

        $this->assertEquals(
            [
                'Western Spiral Arm' => 'Western Spiral Arm',
                'Oglaroon' => 'Oglaroon',
                'Foo' => 'Foo',
            ],
            $handler->getDistrictChoicesWishes()
        );
    }
}
