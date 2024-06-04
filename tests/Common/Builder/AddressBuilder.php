<?php

namespace App\Tests\Builder;

use App\Entity\ElectionPoster\Address;

/**
 * @implements Address
 */
class AddressBuilder implements BuilderInterface
{
    use FakerTrait;

    public function __construct(
        private ?float $lon = null,
        private ?float $lat = null,
        private ?string $address = null,
        private ?string $city = null,
        private ?string $district = null,
        private ?string $state = null,
    ) {
    }

    public function get(): Address
    {
        return new Address(
            longitude: $this->lon ?? self::getFaker()->longitude(),
            latitude: $this->lat ?? self::getFaker()->latitude(),
            address: $this->address ?? self::getFaker()->address(),
            district: $this->district ?? self::getFaker()->randomElement(['Haven', 'Mouth']),
            city: $this->city ?? self::getFaker()->city(),
            state: $this->state ?? self::getFaker()->randomElement(['Rhode Island', 'Tennessee'])
        );
    }
}
