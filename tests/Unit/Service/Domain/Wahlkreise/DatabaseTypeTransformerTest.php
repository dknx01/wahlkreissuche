<?php

namespace App\UnitTests\Service\Domain\Wahlkreise;

use App\Service\Domain\Wahlkreise\DatabaseTypeTransformer;
use App\Tests\Builder\LongitudeOne\GeometryPolygonBuilder;
use Brick\Geo\CoordinateSystem;
use Brick\Geo\LineString;
use Brick\Geo\Point;
use LongitudeOne\Spatial\PHP\Types\Geometry\MultiPolygon;
use PHPUnit\Framework\TestCase;

class DatabaseTypeTransformerTest extends TestCase
{
    public function testMultiPolygon(): void
    {
        $transformer = new DatabaseTypeTransformer();

        $polygon = (new GeometryPolygonBuilder())->build();
        $cs = new CoordinateSystem(false, false);
        $points = [];

        foreach ($polygon->toArray() as $ring) {
            foreach ($ring as $coordinates) {
                $points[] = new Point($cs, $coordinates[1], $coordinates[0]);
            }
        }
        $expected = new \Brick\Geo\MultiPolygon($cs, new \Brick\Geo\Polygon($cs, new LineString($cs, ...$points)));
        $multiPolygon = new MultiPolygon([$polygon], null);
        $this->assertEquals($expected, $transformer->multiPolygon($multiPolygon));
    }

    public function testPolygon(): void
    {
        $transformer = new DatabaseTypeTransformer();

        $polygon = (new GeometryPolygonBuilder())->build();
        $cs = new CoordinateSystem(false, false);
        $points = [];
        foreach ($polygon->toArray() as $ring) {
            foreach ($ring as $coordinates) {
                $points[] = new Point($cs, $coordinates[1], $coordinates[0]);
            }
        }
        $expected = new \Brick\Geo\Polygon($cs, new LineString($cs, ...$points));
        $this->assertEquals($expected, $transformer->polygon($polygon));
    }
}
