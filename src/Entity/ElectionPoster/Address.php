<?php

declare(strict_types=1);

namespace App\Entity\ElectionPoster;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Address
{
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;
    #[ORM\Column(type: 'string', nullable: true)]
    private string $address = '';
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $district = null;
    #[ORM\Column(type: 'string')]
    private string $city;
    #[ORM\Column(type: 'string')]
    private string $state;

    public function __construct(
        ?float $longitude,
        ?float $latitude,
        string $address,
        ?string $district,
        string $city,
        string $state
    ) {
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->address = $address;
        $this->district = $district;
        $this->city = $city;
        $this->state = $state;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(?string $district): void
    {
        $this->district = $district;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public static function createEmpty(): self
    {
        return new self(0.0, 0.0, '', null, '', '');
    }
}
