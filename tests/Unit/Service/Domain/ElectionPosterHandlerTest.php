<?php

namespace App\UnitTests\Service\Domain;

use App\Repository\ElectionPosterRepository;
use App\Service\Domain\ElectionPosterHandler;
use App\Tests\Builder\ElectionPosterBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ElectionPosterHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testFindAllPlakatOrte(): void
    {
        $repo = $this->prophesize(ElectionPosterRepository::class);
        $electionPoster = (new ElectionPosterBuilder())->get();
        $repo->findAll()->shouldBeCalledOnce()->willReturn([$electionPoster]);
        $handler = new ElectionPosterHandler($repo->reveal());
        $this->assertSame([$electionPoster], $handler->findAllPlakatOrte($electionPoster));
    }

    public function testSaveEntity(): void
    {
        $repo = $this->prophesize(ElectionPosterRepository::class);
        $electionPoster = (new ElectionPosterBuilder())->get();
        /* @noinspection PhpVoidFunctionResultUsedInspection */
        $repo->save($electionPoster)->shouldBeCalledOnce();
        $handler = new ElectionPosterHandler($repo->reveal());
        $handler->saveEntity($electionPoster);
    }
}
