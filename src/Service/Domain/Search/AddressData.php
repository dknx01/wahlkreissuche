<?php

declare(strict_types=1);

namespace App\Service\Domain\Search;

use Brick\Geo\CoordinateSystem;
use Brick\Geo\Point;
use Location\Coordinate;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AddressData
{
    private UuidInterface $id;

    public function __construct(
        private string $houseNumber,
        private string $road,
        private string $suburb,
        private string $borough,
        private string $city,
        private string $state,
        private string $postCode,
        private string $country,
        private string $countryCode,
        private string $lat,
        private string $lon,
    ) {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    public function getRoad(): string
    {
        return $this->road;
    }

    public function getSuburb(): string
    {
        return $this->suburb;
    }

    public function getBorough(): string
    {
        return $this->borough;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getPostCode(): string
    {
        return $this->postCode;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getLat(): string
    {
        return $this->lat;
    }

    public function getLon(): string
    {
        return $this->lon;
    }

    public function getCoordinate(): Coordinate
    {
        return new Coordinate((float) $this->getLat(), (float) $this->getLon());
    }

    public function getPoint(): Point
    {
        return new Point(new CoordinateSystem(false, false), (float) $this->lon, (float) $this->lat);
    }

    public function equals(AddressData $addressData): bool
    {
        return $this->houseNumber === $addressData->getHouseNumber()
            && $this->road === $addressData->getRoad()
            && $this->city === $addressData->getCity()
            && $this->postCode === $addressData->getPostCode()
            && $this->state === $addressData->getState()
            && $this->countryCode === $addressData->getCountryCode();
    }
}
