<?php

namespace App\UnitTests\Service\Domain\Search;

use App\Service\Domain\Search\AddressData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class AddressDataTest extends TestCase
{
    public function testGetPoint(): void
    {
        $addressData = $this->getData();
        $this->assertEquals(51.987654323456, $addressData->getPoint()->x());
        $this->assertEquals(13.87654323456, $addressData->getPoint()->y());
        $this->assertNull($addressData->getPoint()->z());
        $this->assertNull($addressData->getPoint()->m());
    }

    public function testGetCoordinate(): void
    {
        $addressData = $this->getData();
        $this->assertEquals(13.87654323456, $addressData->getCoordinate()->getLat());
        $this->assertEquals(51.987654323456, $addressData->getCoordinate()->getLng());
        $this->assertEquals('World Geodetic System 1984', $addressData->getCoordinate()->getEllipsoid()->getName());
    }

    #[DataProvider('provideData')]
    public function testFunctions(mixed $data, string $property): void
    {
        $addressData = $this->getData();
        $reflObj = new \ReflectionObject($addressData);
        $reflProperty = $reflObj->getProperty($property);
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($addressData, $data);

        $this->assertSame($data, $addressData->{'get' . ucfirst($property)}());
    }

    public function testEquals(): void
    {
        $this->assertFalse($this->getData()->equals($this->getData(houseNumber: 999)));
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

    public static function provideData(): \Generator
    {
        yield ['DE', 'country'];
        yield [Uuid::uuid4(), 'id'];
        yield ['21', 'houseNumber'];
        yield ['Quigley Forks', 'road'];
        yield ['North', 'suburb'];
        yield ['New Kimbraland', 'borough'];
        yield ['South Louisshire', 'city'];
        yield ['New Hampshire', 'state'];
        yield ['72081', 'postCode'];
        yield ['Yemen', 'country'];
        yield ['ET', 'countryCode'];
        yield ['51.987654', 'lat'];
        yield ['13.87325765', 'lon'];
    }
}
