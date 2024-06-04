<?php

namespace App\UnitTests\Service\Domain\Search;

use App\Service\Domain\Search\AddressData;
use App\Service\Domain\Search\Geofence;
use App\Tests\Builder\WahlkreisBuilder;
use Brick\Geo\CoordinateSystem;
use Brick\Geo\LineString;
use Brick\Geo\Point;
use Brick\Geo\Polygon;
use PHPUnit\Framework\TestCase;

class GeofenceTest extends TestCase
{
    public function testGeofence(): void
    {
        $cs = new CoordinateSystem(false, false);
        $points = [
            new Point($cs, 51.123, 13.9876),
            new Point($cs, 51.123, 13.9876),
        ];
        $wahlkreis = (new WahlkreisBuilder())->build();
        $polygon = new Polygon($cs, new LineString($cs, ...$points));
        $address = $this->getData();
        $geofence = new Geofence($wahlkreis, $polygon);
        $geofence->setAddress($address);

        $this->assertSame($wahlkreis, $geofence->getWahlkreis());
        $this->assertSame($polygon, $geofence->getPolygon());
        $this->assertSame($address, $geofence->getAddress());
        $this->assertEquals(
            sprintf(
                '%s %s %s %s',
                $address->getRoad(),
                $address->getHouseNumber(),
                $address->getPostCode(),
                $address->getCity(),
            ),
            $geofence->getAddressString()
        );
    }

    private function getData(
        string $houseNumber = '651',
        string $road = 'Eura Forge',
        string $suburb = 'Brakusstad',
        string $borough = 'Port',
        string $city = 'New Sam',
        string $state = 'Tennessee',
        string $postCode = '51478',
        string $country = 'Germany',
        string $countryCode = 'DE',
        string $lat = '13.87654323456',
        string $lon = '51.987654323456',
    ): AddressData {
        return new AddressData(
            $houseNumber,
            $road,
            $suburb,
            $borough,
            $city,
            $state,
            $postCode,
            $country,
            $countryCode,
            $lat,
            $lon
        );
    }
}
