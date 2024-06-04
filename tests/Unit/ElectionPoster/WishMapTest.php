<?php

declare(strict_types=1);

namespace App\UnitTests\ElectionPoster;

use App\ElectionPoster\WishMap;
use App\Entity\Wahllokal;
use App\Repository\WahllokalRepository;
use App\Repository\WishElectionPosterRepository;
use App\Tests\Builder\WishElectionPosterBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class WishMapTest extends TestCase
{
    use ProphecyTrait;

    public function testFindAllWahllokale(): void
    {
        $wahllokalRepo = $this->prophesize(WahllokalRepository::class);
        $wishElectionPosterRepo = $this->prophesize(WishElectionPosterRepository::class);
        $wahllokale = [
            new Wahllokal(),
        ];
        $wahllokalRepo->findAll()->shouldBeCalledOnce()->willReturn($wahllokale);

        $wishMap = new WishMap($wishElectionPosterRepo->reveal(), $wahllokalRepo->reveal());
        $this->assertSame($wahllokale, $wishMap->findAllWahllokale());
    }

    public function testFindAllActivePostersInState(): void
    {
        $state = 'Berlin';
        $wahllokalRepo = $this->prophesize(WahllokalRepository::class);
        $wishElectionPosterRepo = $this->prophesize(WishElectionPosterRepository::class);
        $wishElectionPosterRepo->findAllCitiesByState($state)->shouldBeCalledOnce()->willReturn([['city' => 'Berlin']]);
        $wishPosters = [
            (new WishElectionPosterBuilder())->get(),
        ];
        $wishElectionPosterRepo->findAllActiveByStateAndCity('Berlin', $state)->shouldBeCalledOnce()->willReturn($wishPosters);

        $wishMap = new WishMap($wishElectionPosterRepo->reveal(), $wahllokalRepo->reveal());
        $this->assertSame(
            [
                'Berlin' => $wishPosters,
            ],
            $wishMap->findAllActivePostersInState($state)
        );
    }

    public function testFindAllActivePostersInDistricts(): void
    {
        $state = 'Berlin';
        $city = 'Goyetteberg';
        $wahllokalRepo = $this->prophesize(WahllokalRepository::class);
        $wishElectionPosterRepo = $this->prophesize(WishElectionPosterRepository::class);
        $wishElectionPosterRepo->findAllDistrictsByCityAndState($state, $city)->shouldBeCalledOnce()->willReturn([['district' => 'Port']]);
        $wishPosters = [
            (new WishElectionPosterBuilder())->get(),
        ];
        $wishElectionPosterRepo->findAllActiveByDistrictAndCityAndState('Port', $city, $state)
            ->shouldBeCalledOnce()->willReturn($wishPosters);

        $wishMap = new WishMap($wishElectionPosterRepo->reveal(), $wahllokalRepo->reveal());
        $this->assertSame(
            [
                'Port' => $wishPosters,
            ],
            $wishMap->findAllActivePostersInDistricts($state, $city)
        );
    }
}
