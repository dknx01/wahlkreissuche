<?php

declare(strict_types=1);

namespace App\Entity\Wahlkreis;

use Doctrine\ORM\Mapping as ORM;
use LongitudeOne\Spatial\PHP\Types\AbstractGeometry;

#[ORM\Embeddable]
class Geometry
{
    #[ORM\Column(type: 'geometry', nullable: true)]
    protected AbstractGeometry $geometry;
    #[ORM\Column(type: 'string', nullable: true)]
    protected string $type;
    /**
     * @var array<array-key, array<array-key, float>>
     */
    #[ORM\Column(type: 'json')]
    private array $coordinates;

    /**
     * @param array<array-key, array<array-key, float>> $coordinates
     */
    public function __construct(string $type, array $coordinates)
    {
        $this->type = $type;
        $this->coordinates = $coordinates;
    }

    /**
     * @return array<array-key, array<array-key, float>>
     */
    public function getCoordinates(): array
    {
        return $this->coordinates;
    }

    public function getGeometry(): AbstractGeometry
    {
        return $this->geometry;
    }

    public function setGeometry(AbstractGeometry $geometry): void
    {
        $this->geometry = $geometry;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
