<?php

namespace App\Tests\Builder\LongitudeOne;

use LongitudeOne\Spatial\PHP\Types\AbstractLineString;
use LongitudeOne\Spatial\PHP\Types\Geometry\Polygon;

class GeometryPolygonBuilder
{
    private ?int $srid;
    /** @var AbstractLineString[]|array[] */
    private array $rings;

    public function __construct()
    {
        $this->srid = $srid = null;
        $this->rings = \App\Tests\Builder\Data\Polygon::getRings();
    }

    public function __call(string $name, mixed $argument)
    {
        $this->$name = $argument;
    }

    public function build(): Polygon
    {
        return new Polygon(
            rings: $this->rings,
            srid: $this->srid
        );
    }
}
