<?php

declare(strict_types=1);

namespace App\Service\Domain\Search;

use App\Entity\Wahlkreis;
use Brick\Geo\Geometry;

class Geofence
{
    private ?AddressData $address;

    public function __construct(private Wahlkreis $wahlkreis, private Geometry $polygon)
    {
    }

    public function getWahlkreis(): Wahlkreis
    {
        return $this->wahlkreis;
    }

    public function getPolygon(): Geometry
    {
        return $this->polygon;
    }

    public function getAddress(): ?AddressData
    {
        return $this->address;
    }

    public function setAddress(AddressData $address): void
    {
        $this->address = $address;
    }

    public function getAddressString(): string
    {
        return sprintf(
            '%s %s %s %s',
            $this->address->getRoad(),
            $this->address->getHouseNumber(),
            $this->address->getPostCode(),
            $this->address->getCity(),
        );
    }
}
