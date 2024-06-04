<?php

declare(strict_types=1);

namespace App\UnitTests\Misc;

use App\Entity\Wahlkreis;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use LongitudeOne\Spatial\DBAL\Types\GeometryType;
use Ramsey\Collection\Collection;
use Ramsey\Uuid\Uuid;

class WahlkreisHelper
{
    public static function getWahlkreise(): Collection
    {
        $converter = new GeometryType();
        $result = new Collection(Wahlkreis::class);
        $platform = new MariaDBPlatform();

        $jsonData = json_decode(file_get_contents(__DIR__ . '/wahlkreis_Berlin.json'), true);

        foreach ($jsonData['wahlkreis'] as $wahlkreisData) {
            $wahlkreis = new Wahlkreis(
                geometry: self::getGeometry($wahlkreisData, $converter, $platform),
                type: $wahlkreisData['type'],
                state: $wahlkreisData['state'],
                agh: new Wahlkreis\Agh(
                    wahlkreisLong: $wahlkreisData['agh_wahlkreis_long'],
                    wahlkreisShort: $wahlkreisData['agh_wahlkreis_short'],
                    bezirk: $wahlkreisData['agh_bezirk']
                ),
                btw: new Wahlkreis\Btw(
                    number: $wahlkreisData['btw_number'],
                    name: $wahlkreisData['btw_name'],
                    stateName: $wahlkreisData['btw_state_name'],
                    stateNumber: $wahlkreisData['btw_state_number']
                ),
                genericWahlKreis: new Wahlkreis\GenericWahlKreis()
            );
            $reflObject = new \ReflectionObject(
                $wahlkreis
            );
            $reflProperty = $reflObject->getProperty('id');
            $reflProperty->setAccessible(true);
            $reflProperty->setValue($wahlkreis, Uuid::uuid4());
            $result->add($wahlkreis);
        }

        return $result;
    }

    private static function getGeometry(array $wahlkreisData, GeometryType $converter, AbstractPlatform $platform): Wahlkreis\Geometry
    {
        $geometry = new Wahlkreis\Geometry(type: $wahlkreisData['geometry_type'], coordinates: json_decode($wahlkreisData['geometry_coordinates']));
        $reflObject = new \ReflectionObject(
            $geometry
        );
        $reflProperty = $reflObject->getProperty('geometry');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue(
            $geometry,
            $converter->convertToPHPValue(
                $wahlkreisData['geometry_geometry'],
                $platform
            )
        );

        return $geometry;
    }
}
