<?php

namespace App\UnitTests\Service\Domain\Wahlkreise;

use App\Service\Domain\Wahlkreise\Transformer;
use App\Tests\Builder\FeatureBuilder;
use App\Tests\Builder\WahlkreisBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\DependencyInjection\ServiceLocator;

class TransformerTest extends TestCase
{
    use ProphecyTrait;

    public function testToStringWithEmptyList(): void
    {
        $transformer = new Transformer($this->prophesize(ServiceLocator::class)->reveal());

        $this->assertEquals('{"type":"FeatureCollection","features":[]}', $transformer->__toString());
    }

    public function testTransformAgh(): void
    {
        $data = [
            (new WahlkreisBuilder())->build(),
        ];
        $serviceLocator = $this->prophesize(ServiceLocator::class);
        $agh = $this->prophesize(Transformer\Agh::class);
        $agh->processWahlkreise($data)->shouldBeCalledOnce();
        $agh->getData()->willReturn(
            [
                FeatureBuilder::getBrickFeatureAgh(),
            ]
        );

        $serviceLocator->get(Transformer\Agh::class)->shouldBeCalledTimes(2)->willReturn($agh->reveal());

        $transformer = new Transformer($serviceLocator->reveal());
        $transformer->transformAgh($data);
        $this->assertEquals(<<<OUT
{"type":"FeatureCollection","features":[{"type":"Feature","properties":{"AWK":"1204","BEZ":"Reinickendorf","description":"<b>Bezirk:<\/b> Reinickendorf<br><b>AGH-Wahlkreis:<\/b> 04"},"geometry":{"type":"Polygon","coordinates":[[[13.331607982334,52.614456190528],[13.331607982334,52.614456190528]]]}}]}
OUT
            , (string) $transformer);
    }

    public function testTransformBtw(): void
    {
        $data = [
            (new WahlkreisBuilder())->build(),
        ];
        $serviceLocator = $this->prophesize(ServiceLocator::class);
        $btw = $this->prophesize(Transformer\Btw::class);
        $btw->processWahlkreise($data)->shouldBeCalledOnce();
        $btw->getData()->willReturn(
            [
                FeatureBuilder::getBrickFeatureBtw(),
            ]
        );

        $serviceLocator->get(Transformer\Btw::class)->shouldBeCalledTimes(2)->willReturn($btw->reveal());

        $transformer = new Transformer($serviceLocator->reveal());
        $transformer->transformBtw($data);
        $this->assertEquals(<<<OUT
{"type":"FeatureCollection","features":[{"type":"Feature","properties":{"description":"<b>Name:<\/b> New North<br><b>Btw-Wahlkreis:<\/b> 80","Nummer":80,"Bundesland":"Berlin"},"geometry":{"type":"Polygon","coordinates":[[[13.331607982334,52.614456190528],[13.331607982334,52.614456190528]]]}}]}
OUT
            , (string) $transformer);
    }
}
