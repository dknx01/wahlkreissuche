<?php

declare(strict_types=1);

namespace App\UnitTests\Service\Domain;

use App\Repository\WishElectionPosterRepository;
use App\Service\Domain\WishElectionPosterLocationHandler;
use App\Tests\Builder\WishElectionPosterBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class WishElectionPosterLocationHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testFindAllWishPlacesByUser(): void
    {
        $user = 'foo';
        $poster = (new WishElectionPosterBuilder(createdBy: $user))->get();
        $repo = $this->prophesize(WishElectionPosterRepository::class);
        $repo->findAllByUser($user, 'active')->shouldBeCalledOnce()->willReturn([$poster]);
        $handler = new WishElectionPosterLocationHandler($repo->reveal());
        $this->assertSame([$poster], $handler->findAllWishPlacesByUser($user, 'active'));
    }

    public function testFindAllPlakatOrte(): void
    {
        $poster = (new WishElectionPosterBuilder())->get();
        $repo = $this->prophesize(WishElectionPosterRepository::class);
        $repo->findAll()->shouldBeCalledOnce()->willReturn([$poster]);
        $handler = new WishElectionPosterLocationHandler($repo->reveal());
        $this->assertSame([$poster], $handler->findAllPlakatOrte());
    }

    public function testSaveEntity(): void
    {
        $poster = (new WishElectionPosterBuilder())->get();
        $repo = $this->prophesize(WishElectionPosterRepository::class);
        /* @noinspection PhpVoidFunctionResultUsedInspection */
        $repo->save($poster)->shouldBeCalledOnce();
        $handler = new WishElectionPosterLocationHandler($repo->reveal());
        $handler->saveEntity($poster);
    }
}
