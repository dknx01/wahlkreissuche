<?php

namespace App\UnitTests\Service\Domain\Wahlkreise\Transformer;

use App\Service\Domain\Wahlkreise\Transformer\Btw;
use App\Service\GeoJsonReader;
use App\Tests\Builder\WahlkreisEntityBuilder;
use Brick\Geo\CoordinateSystem;
use Brick\Geo\IO\GeoJSON\Feature;
use Brick\Geo\LineString;
use Brick\Geo\Point;
use Brick\Geo\Polygon;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class BtwTest extends TestCase
{
    use ProphecyTrait;

    public function testAghTransformer(): void
    {
        $wahlkreis = WahlkreisEntityBuilder::createBtwWahlkreisEntity();
        $wahlkreise = [$wahlkreis];
        $geoJsonReader = $this->prophesize(GeoJsonReader::class);
        $cs = new CoordinateSystem(false, false, 4326);
        $polygon = new Polygon(
            $cs,
            new LineString(
                $cs,
                new Point($cs, 13.331607982334, 52.614456190528),
                new Point($cs, 13.331607982334, 52.614456190528),
            )
        );
        $geoJsonReader->read(
            $wahlkreis->getGeometry()->getGeometry()->toJson()
        )->willReturn(
            $polygon
        );
        $agh = new Btw($geoJsonReader->reveal());
        $agh->processWahlkreise($wahlkreise);
        $properties = new \stdClass();
        $properties->description = '<b>Name:</b> New North<br><b>Btw-Wahlkreis:</b> 80';
        $properties->Nummer = 80;
        $properties->Bundesland = 'Berlin';

        $expected = [
            new Feature(
                $polygon,
                $properties
            ),
        ];
        $this->assertEquals($expected, $agh->getData());
    }
}
