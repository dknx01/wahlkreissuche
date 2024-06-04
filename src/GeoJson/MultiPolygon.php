<?php

namespace App\GeoJson;

use Location\Polygon;

class MultiPolygon
{
    /**
     * @param Polygon[] $polygons
     */
    public function __construct(private array $polygons)
    {
    }

    public function getPolygons(): \Generator
    {
        foreach ($this->polygons as $polygone) {
            yield $polygone;
        }
    }
}
