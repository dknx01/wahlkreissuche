<?php

namespace App\Tests\Builder;

use App\Tests\Builder\LongitudeOne\GeometryPolygonBuilder;
use App\Entity\Wahlkreis\Geometry;

class GeometryBuilder
{
    public static function getGeometry(): Geometry
    {
        $geometry = new Geometry(
            type: 'Polygon',
            coordinates: Data\Polygon::getCoordinates(),
        );
        $geometry->setGeometry(
            (new GeometryPolygonBuilder())->build()
        );

        return $geometry;
    }
}
