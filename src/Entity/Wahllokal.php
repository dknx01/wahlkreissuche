<?php

namespace App\Entity;

use App\Repository\WahllokalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WahllokalRepository::class)]
class Wahllokal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;
    #[ORM\Column(type: 'string', length: 255)]
    private string $adress;
    #[ORM\Column(type: 'string', length: 255)]
    private string $longitude;
    #[ORM\Column(type: 'string', length: 255)]
    private string $latitude;
    #[ORM\Column(type: 'string', length: 255)]
    private string $district;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $description;
    #[ORM\Column(type: 'string', length: 255)]
    private string $city;
    #[ORM\Column(type: 'integer')]
    private int $radius = 30;

    public function getRadius(): int
    {
        return $this->radius;
    }

    public function setRadius(int $radius): void
    {
        $this->radius = $radius;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(string $district): self
    {
        $this->district = $district;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }
}
