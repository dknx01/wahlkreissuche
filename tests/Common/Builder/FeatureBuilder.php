<?php

namespace App\Tests\Builder;

use Brick\Geo\CoordinateSystem;
use Brick\Geo\IO\GeoJSON\Feature;
use Brick\Geo\LineString;
use Brick\Geo\Point;
use Brick\Geo\Polygon;

class FeatureBuilder
{
    public static function getBrickFeatureAgh(): Feature
    {
        $cs = new CoordinateSystem(false, false, 4326);

        $polygon = new Polygon(
            $cs,
            new LineString(
                $cs,
                new Point($cs, 13.331607982334, 52.614456190528),
                new Point($cs, 13.331607982334, 52.614456190528),
            )
        );
        $properties = new \stdClass();
        $properties->AWK = '1204';
        $properties->BEZ = 'Reinickendorf';
        $properties->description = '<b>Bezirk:</b> Reinickendorf<br><b>AGH-Wahlkreis:</b> 04';

        return new Feature(
            $polygon,
            $properties
        );
    }

    public static function getBrickFeatureBtw(): Feature
    {
        $cs = new CoordinateSystem(false, false, 4326);

        $polygon = new Polygon(
            $cs,
            new LineString(
                $cs,
                new Point($cs, 13.331607982334, 52.614456190528),
                new Point($cs, 13.331607982334, 52.614456190528),
            )
        );
        $properties = new \stdClass();
        $properties->description = '<b>Name:</b> New North<br><b>Btw-Wahlkreis:</b> 80';
        $properties->Nummer = 80;
        $properties->Bundesland = 'Berlin';

        return new Feature(
            $polygon,
            $properties
        );
    }
}
