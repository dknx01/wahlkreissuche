<?php

declare(strict_types=1);

namespace App\UnitTests\ElectionPoster;

use App\Entity\ElectionPoster;
use App\Entity\Wahllokal;
use App\ElectionPoster\Map;
use App\Repository\ElectionPosterRepository;
use App\Repository\WahllokalRepository;
use App\Repository\WishElectionPosterRepository;
use App\Tests\Builder\ElectionPosterBuilder;
use App\Tests\Builder\WishElectionPosterBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class MapTest extends TestCase
{
    use ProphecyTrait;

    public function testFindAllActivePostersInState(): void
    {
        $electionPosterRepo = $this->prophesize(ElectionPosterRepository::class);
        $cities = [
            ['city' => 'Castlelobruxo'],
            ['city' => 'The Briar Patch'],
        ];
        $electionPosterRepo->findAllCitiesByState('Berlin')
            ->willReturn($cities);
        $electionPoster1 = new ElectionPoster(
            'Worf',
            new \DateTime('27-01-2022'),
            new ElectionPoster\Address(51.12324, 13.9865, 'Mann Mountains 648, 87991 East Jeffryland', null, 'East Jeffryland', 'Indiana'),
            'I\'ve just had an unhappy love affair, so I don\'t see why anybody else should have a good time.',
        );
        $electionPoster2 = new ElectionPoster(
            'Bowerick Wowbagger',
            new \DateTime('27-01-2022'),
            new ElectionPoster\Address(51.234, 13.23425, 'Mann Mountains 352, 87991 Muellermouth', null, 'East Jeffryland', 'Indiana'),
            'Never laugh at live dragons, Bilbo you fool!',
        );
        $electionPosterRepo->findAllActiveByStateAndCity('Castlelobruxo', 'Berlin')
            ->willReturn([$electionPoster1]);
        $electionPosterRepo->findAllActiveByStateAndCity('The Briar Patch', 'Berlin')
            ->willReturn([$electionPoster2]);

        $wahllokalRepo = $this->prophesize(WahllokalRepository::class);

        $wishElectionPosterRepo = $this->prophesize(WishElectionPosterRepository::class);
        $map = new Map(
            $electionPosterRepo->reveal(),
            $wahllokalRepo->reveal(),
            $wishElectionPosterRepo->reveal(),
        );

        $result = $map->findAllActivePostersInState('Berlin');
        $this->assertArrayHasKey('Castlelobruxo', $result);
        $this->assertArrayHasKey('The Briar Patch', $result);
        $this->assertEquals($electionPoster1, $result['Castlelobruxo'][0]);
        $this->assertEquals($electionPoster2, $result['The Briar Patch'][0]);
    }

    public function testFindAllActivePostersInDistricts(): void
    {
        $electionPosterRepo = $this->prophesize(ElectionPosterRepository::class);
        $cities = [
            ['district' => 'Castlelobruxo'],
            ['district' => 'The Briar Patch'],
        ];
        $electionPosterRepo->findAllDistrictsByCityAndState('Berlin', 'Berlin')
            ->willReturn($cities);

        $electionPoster1 = new ElectionPoster(
            'Worf',
            new \DateTime('27-01-2022'),
            new ElectionPoster\Address(51.12324, 13.9865, 'Mann Mountains 648, 87991 East Jeffryland', null, 'East Jeffryland', 'Indiana'),
            'I\'ve just had an unhappy love affair, so I don\'t see why anybody else should have a good time.',
        );
        $electionPoster2 = new ElectionPoster(
            'Bowerick Wowbagger',
            new \DateTime('27-01-2022'),
            new ElectionPoster\Address(51.234, 13.23425, 'Mann Mountains 352, 87991 Muellermouth', null, 'East Jeffryland', 'Indiana'),
            'Never laugh at live dragons, Bilbo you fool!',
        );
        $electionPosterRepo->findAllActiveByDistrictAndCityAndState('Castlelobruxo', 'Berlin', 'Berlin')
            ->willReturn([$electionPoster1]);
        $electionPosterRepo->findAllActiveByDistrictAndCityAndState('The Briar Patch', 'Berlin', 'Berlin')
            ->willReturn([$electionPoster2]);

        $wahllokalRepo = $this->prophesize(WahllokalRepository::class);

        $wishElectionPosterRepo = $this->prophesize(WishElectionPosterRepository::class);
        $map = new Map($electionPosterRepo->reveal(), $wahllokalRepo->reveal(), $wishElectionPosterRepo->reveal());

        $result = $map->findAllActivePostersInDistricts('Berlin', 'Berlin');
        $this->assertArrayHasKey('Castlelobruxo', $result);
        $this->assertArrayHasKey('The Briar Patch', $result);
        $this->assertEquals($electionPoster1, $result['Castlelobruxo'][0]);
        $this->assertEquals($electionPoster2, $result['The Briar Patch'][0]);
    }

    public function testFindAllWahllokale(): void
    {
        $electionPosterRepo = $this->prophesize(ElectionPosterRepository::class);
        $wahllokalRepo = $this->prophesize(WahllokalRepository::class);

        $wahllokal = new Wahllokal();
        $wahllokal->setAdress('406 Cora Run, 16510 Angellachester');
        $wahllokal->setDescription('42');
        $wahllokal->setLatitude('51.3245465');
        $wahllokal->setLongitude('13.3245465');
        $wahllokal->setDistrict('port');
        $wahllokale = [$wahllokal];
        $wahllokalRepo->findAll()->willReturn($wahllokale);

        $wishElectionPosterRepo = $this->prophesize(WishElectionPosterRepository::class);
        $map = new Map($electionPosterRepo->reveal(), $wahllokalRepo->reveal(), $wishElectionPosterRepo->reveal());

        $result = $map->findAllWahllokale();
        $this->assertSame($wahllokal, $result[0]);
    }

    public function testFindAllActivePostersInDistrictsWithWishes(): void
    {
        $electionPosterRepo = $this->prophesize(ElectionPosterRepository::class);
        $wahllokalRepo = $this->prophesize(WahllokalRepository::class);
        $wishElectionPosterRepo = $this->prophesize(WishElectionPosterRepository::class);

        $districts = [
            [
                'district' => 'District 1',
            ],
        ];

        $electionPosterRepo->findAllDistrictsByCityAndState('Foo', 'Town')
            ->willReturn(
                $districts
            );

        $electionPoster = (new ElectionPosterBuilder())->get();
        $electionPosterRepo->findAllActiveByDistrictAndCityAndState(
            'District 1',
            'Town',
            'Foo'
        )->willReturn([$electionPoster]);

        $wishElectionPoster = (new WishElectionPosterBuilder())->get();
        $wishElectionPosterRepo->findAllActiveByStateAndCity('Town', 'Foo')
            ->willReturn([$wishElectionPoster]);

        $map = new Map($electionPosterRepo->reveal(), $wahllokalRepo->reveal(), $wishElectionPosterRepo->reveal());

        $result = $map->findAllActivePostersInDistrictsWithWishes('Foo', 'Town');

        $this->assertCount(2, $result);
        $this->assertArrayHasKey('District 1', $result);
        $this->assertSame([$electionPoster], $result['District 1']);
        $this->assertArrayHasKey('Wunschorte', $result);
        $this->assertSame([$wishElectionPoster], $result['Wunschorte']);
    }

    public function testFindAllWahllokaleInCity(): void
    {
        $electionPosterRepo = $this->prophesize(ElectionPosterRepository::class);
        $wahllokalRepo = $this->prophesize(WahllokalRepository::class);
        $wishElectionPosterRepo = $this->prophesize(WishElectionPosterRepository::class);
        $wahllokale = [
            new Wahllokal(),
        ];
        $wahllokalRepo->findBy(['city' => 'Jenastad'])->shouldBeCalledOnce()->willReturn($wahllokale);

        $map = new Map($electionPosterRepo->reveal(), $wahllokalRepo->reveal(), $wishElectionPosterRepo->reveal());
        $this->assertSame($wahllokale, $map->findAllWahllokaleInCity('Jenastad'));
    }
}
