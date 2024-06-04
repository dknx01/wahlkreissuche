<?php

namespace App\UnitTests\Service\Domain;

use App\Repository\WahlkreisRepository;
use App\Service\Domain\Wahlkreise\Transformer;
use App\Service\Domain\WahlkreisHandler;
use App\Tests\Builder\WahlkreisBuilder;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class WahlkreisHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testFindAllAghWahlkreise(): void
    {
        $root = vfsStream::setup('test');
        $file = vfsStream::newFile('foo.json')
            ->at($root);
        $root->addChild($file);

        $entries = [
            (new WahlkreisBuilder())->build(),
        ];
        $repo = $this->prophesize(WahlkreisRepository::class);
        $repo->findBy(['type' => 'AGH'])->willReturn($entries);

        $transformer = $this->prophesize(Transformer::class);

        $handler = new WahlkreisHandler($transformer->reveal(), $repo->reveal(), $file->path());
        $this->assertSame($entries, $handler->findAllAghWahlkreise());
    }

    public function testGetBtwWahlkreiseAsString(): void
    {
        $root = vfsStream::setup('test');
        $file = vfsStream::newFile('foo.json')
            ->at($root);
        $root->addChild($file);

        $entries = [
            (new WahlkreisBuilder())->build(),
        ];
        $repo = $this->prophesize(WahlkreisRepository::class);
        $repo->findBy(['type' => 'BTW', 'btw.stateName' => 'Berlin'])->willReturn($entries);

        $transformer = $this->prophesize(Transformer::class);
        $transformer->transformBtw($entries)->shouldBeCalledOnce();
        $transformer->__toString()->willReturn('{"foo": 12345}');

        $handler = new WahlkreisHandler($transformer->reveal(), $repo->reveal(), $file->path());
        $this->assertEquals('{"foo": 12345}', $handler->getBtwWahlkreiseAsString());
    }

    public function testGetAghWahlkreiseAsString(): void
    {
        $root = vfsStream::setup('test');
        $file = vfsStream::newFile('foo.json')
            ->at($root);
        $root->addChild($file);

        $entries = [
            (new WahlkreisBuilder())->build(),
        ];
        $repo = $this->prophesize(WahlkreisRepository::class);
        $repo->findBy(['type' => 'AGH'])->willReturn($entries);

        $transformer = $this->prophesize(Transformer::class);
        $transformer->transformAgh($entries)->shouldBeCalledOnce();
        $transformer->__toString()->willReturn('{"foo": 12345}');

        $handler = new WahlkreisHandler($transformer->reveal(), $repo->reveal(), $file->path());
        $this->assertEquals('{"foo": 12345}', $handler->getAghWahlkreiseAsString());
    }

    public function testGetBtwWahlkreiseAsStringBB(): void
    {
        $root = vfsStream::setup('test');
        $file = vfsStream::newFile('foo.json')
            ->at($root);
        $root->addChild($file);

        $entries = [
            (new WahlkreisBuilder())->build(),
        ];
        $repo = $this->prophesize(WahlkreisRepository::class);
        $repo->findBy(['btw.stateName' => 'Brandenburg', 'type' => 'BTW'])->willReturn($entries);

        $transformer = $this->prophesize(Transformer::class);
        $transformer->transformBtw($entries)->shouldBeCalledOnce();
        $transformer->__toString()->willReturn('{"foo": 12345}');

        $handler = new WahlkreisHandler($transformer->reveal(), $repo->reveal(), $file->path());
        $this->assertEquals('{"foo": 12345}', $handler->getBtwWahlkreiseAsString_BB());
    }

    public function testGetBtwWahlkreiseAsStringByState(): void
    {
        $root = vfsStream::setup('test');
        $file = vfsStream::newFile('foo.json')
            ->at($root);
        $root->addChild($file);

        $entries = [
            (new WahlkreisBuilder())->build(),
        ];
        $state = 'Foo';
        $repo = $this->prophesize(WahlkreisRepository::class);
        $repo->findBy(['btw.stateName' => $state, 'type' => 'BTW'])->willReturn($entries);

        $transformer = $this->prophesize(Transformer::class);
        $transformer->transformBtw($entries)->shouldBeCalledOnce();
        $transformer->__toString()->willReturn('{"foo": 12345}');

        $handler = new WahlkreisHandler($transformer->reveal(), $repo->reveal(), $file->path());
        $this->assertEquals('{"foo": 12345}', $handler->getBtwWahlkreiseAsStringByState($state));
    }

    public function testGetBtwWahlkreiseDeutschlandAsString(): void
    {
        file_put_contents(__DIR__ . '/foo.json', '{"foo": 12345}');

        $repo = $this->prophesize(WahlkreisRepository::class);

        $transformer = $this->prophesize(Transformer::class);

        $handler = new WahlkreisHandler($transformer->reveal(), $repo->reveal(), __DIR__ . '/foo.json');
        $this->assertEquals('{"foo": 12345}', $handler->getBtwWahlkreiseDeutschlandAsString());

        @unlink(__DIR__ . '/foo.json');
    }
}
