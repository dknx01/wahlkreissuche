<?php

namespace App\Entity\Wahlkreis;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class GeoJsonGeometry
{
    #[ORM\Column]
    private string $type;
    /**
     * @var array<array-key, array<string, float>>
     */
    #[ORM\Column(type: 'json')]
    private array $coordinates;

    /**
     * @param array<array-key, array<string, float>> $coordinates
     */
    public function __construct(string $type, array $coordinates)
    {
        $this->type = $type;
        $this->coordinates = $coordinates;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array<array-key, array<string, float>>
     */
    public function getCoordinates(): array
    {
        return $this->coordinates;
    }
}
