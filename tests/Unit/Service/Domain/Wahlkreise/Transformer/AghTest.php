<?php

namespace App\UnitTests\Service\Domain\Wahlkreise\Transformer;

use App\Service\Domain\Wahlkreise\Transformer\Agh;
use App\Service\GeoJsonReader;
use App\Tests\Builder\WahlkreisEntityBuilder;
use Brick\Geo\CoordinateSystem;
use Brick\Geo\IO\GeoJSON\Feature;
use Brick\Geo\LineString;
use Brick\Geo\Point;
use Brick\Geo\Polygon;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class AghTest extends TestCase
{
    use ProphecyTrait;

    public function testAghTransformer(): void
    {
        $wahlkreis = WahlkreisEntityBuilder::createAghWahlkreisEntity();
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
        $agh = new Agh($geoJsonReader->reveal());
        $agh->processWahlkreise($wahlkreise);
        $properties = new \stdClass();
        $properties->AWK = '1204';
        $properties->BEZ = 'Reinickendorf';
        $properties->description = '<b>Bezirk:</b> Reinickendorf<br><b>AGH-Wahlkreis:</b> 04';
        $expected = [
            new Feature(
                $polygon,
                $properties
            ),
        ];
        $this->assertEquals($expected, $agh->getData());
    }
}
