<?php

declare(strict_types=1);

namespace App\Service\Domain\Wahlkreise;

use Brick\Geo\CoordinateSystem;
use Brick\Geo\LineString;
use Brick\Geo\Point;
use LongitudeOne\Spatial\PHP\Types\Geometry\MultiPolygon;
use LongitudeOne\Spatial\PHP\Types\Geometry\Polygon;

class DatabaseTypeTransformer
{
    private CoordinateSystem $coordinateSystem;

    public function __construct()
    {
        $this->coordinateSystem = new CoordinateSystem(false, false);
    }

    public function polygon(Polygon $polygon): \Brick\Geo\Polygon
    {
        $points = [];
        foreach ($polygon->toArray() as $ring) {
            foreach ($ring as $coordinates) {
                $points[] = new Point($this->coordinateSystem, $coordinates[1], $coordinates[0]);
            }
        }
        $ls = new LineString($this->coordinateSystem, ...$points);

        return new \Brick\Geo\Polygon($this->coordinateSystem, $ls);
    }

    public function multiPolygon(MultiPolygon $multiPolygon): \Brick\Geo\MultiPolygon
    {
        $polygons = [];
        $points = [];
        foreach ($multiPolygon->toArray() as $rings) {
            foreach ($rings as $ring) {
                foreach ($ring as $coordinates) {
                    $points[] = new Point($this->coordinateSystem, $coordinates[1], $coordinates[0]);
                }
                $ls = new LineString($this->coordinateSystem, ...$points);
                $polygons[] = new \Brick\Geo\Polygon($this->coordinateSystem, $ls);
            }
        }

        return new \Brick\Geo\MultiPolygon($this->coordinateSystem, ...$polygons);
    }
}
