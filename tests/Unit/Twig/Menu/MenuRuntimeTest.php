<?php

namespace App\UnitTests\Twig\Menu;

use App\EventListener\MobileDetectListener;
use App\Options\BtwKreise;
use App\Repository\ElectionPosterRepository;
use App\Repository\WahlkreisRepository;
use App\Repository\WishElectionPosterRepository;
use App\Twig\Menu\MenuRuntime;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuRuntimeTest extends TestCase
{
    use ProphecyTrait;

    private ElectionPosterRepository|ObjectProphecy $repo;
    private WishElectionPosterRepository|ObjectProphecy $wishRepo;
    private WahlkreisRepository|ObjectProphecy $wahlkreisRepo;

    protected function setUp(): void
    {
        $this->repo = $this->prophesize(ElectionPosterRepository::class);
        $this->wishRepo = $this->prophesize(WishElectionPosterRepository::class);
        $this->wahlkreisRepo = $this->prophesize(WahlkreisRepository::class);
    }

    public function testGetStateCenter(): void
    {
        $runtime = new MenuRuntime(
            $this->repo->reveal(),
            $this->wishRepo->reveal(),
            $this->wahlkreisRepo->reveal(),
            new RequestStack()
        );
        $this->assertEquals(BtwKreise::getConfig('Berlin'), $runtime->getStateCenter('Berlin'));
    }

    public function testGetPosterCityDistrict(): void
    {
        $result = [
            [
                'address.city' => 'town1',
                'address.district' => 'district1',
            ],
            [
                'address.city' => 'town2',
                'address.district' => 'district2',
            ],
            [
                'address.city' => 'town1',
                'address.district' => '',
            ],
        ];
        $this->repo->findAllDistrictsAndCities()->willReturn($result);
        $runtime = new MenuRuntime(
            $this->repo->reveal(),
            $this->wishRepo->reveal(),
            $this->wahlkreisRepo->reveal(),
            new RequestStack()
        );
        $expected = [
            'town1' => ['district1'],
            'town2' => ['district2'],
        ];
        $this->assertEquals($expected, $runtime->getPosterCityDistrict());
    }

    public function testGetPosterStatesWithBerlin(): void
    {
        $result = ['state' => 'state'];
        $this->repo->findAllActiveStatesWithBerlin()->willReturn($result);
        $runtime = new MenuRuntime(
            $this->repo->reveal(),
            $this->wishRepo->reveal(),
            $this->wahlkreisRepo->reveal(),
            new RequestStack()
        );
        $this->assertSame($result, $runtime->getPosterStatesWithBerlin());
    }

    public function testGetPosterStates(): void
    {
        $result = ['state' => 'state'];
        $this->repo->findAllActiveStates()->willReturn($result);
        $runtime = new MenuRuntime(
            $this->repo->reveal(),
            $this->wishRepo->reveal(),
            $this->wahlkreisRepo->reveal(),
            new RequestStack()
        );
        $this->assertSame($result, $runtime->getPosterStates());
    }

    public function testGetWishPosterStates(): void
    {
        $result = ['Berlin' => 'Berlin', 'Bayern' => 'Bayern'];
        $this->wishRepo->findAllActiveStates()->shouldBeCalledOnce()->willReturn($result);

        $runtime = new MenuRuntime(
            $this->repo->reveal(),
            $this->wishRepo->reveal(),
            $this->wahlkreisRepo->reveal(),
            new RequestStack()
        );
        $this->assertSame($result, $runtime->getWishPosterStates());
    }

    #[TestWith([false, []])]
    #[TestWith([false, ['isMobile' => MobileDetectListener::DESKTOP]])]
    #[TestWith([false, ['isMobile' => MobileDetectListener::TABLET]])]
    #[TestWith([true, ['isMobile' => MobileDetectListener::Mobile]])]
    public function testIsMobile(bool $expected, array $attributes): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(new Request(attributes: $attributes));

        $runtime = new MenuRuntime(
            $this->repo->reveal(),
            $this->wishRepo->reveal(),
            $this->wahlkreisRepo->reveal(),
            $requestStack
        );
        $this->assertSame($expected, $runtime->isMobile());
    }

    public function testGetLtwStates(): void
    {
        $result = ['Berlin' => 'Berlin', 'Bayern' => 'Bayern'];
        $this->wahlkreisRepo->findAllStatesByType('LTW')->shouldBeCalledOnce()->willReturn($result);

        $runtime = new MenuRuntime(
            $this->repo->reveal(),
            $this->wishRepo->reveal(),
            $this->wahlkreisRepo->reveal(),
            new RequestStack()
        );
        $this->assertSame($result, $runtime->getLtwStates());
    }
}
