<?php

namespace App\Entity;

use App\Entity\Wahlkreis\Agh;
use App\Entity\Wahlkreis\Btw;
use App\Entity\Wahlkreis\GenericWahlKreis;
use App\Entity\Wahlkreis\Geometry;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'wahlkreis')]
class Wahlkreis
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;
    #[ORM\Column]
    private string $type;
    #[ORM\Column]
    private string $state;
    #[ORM\Embedded(class: Agh::class)]
    private Agh $agh;
    #[ORM\Embedded(class: Btw::class)]
    private Btw $btw;
    #[ORM\Embedded(class: Geometry::class)]
    private Geometry $geometry;
    #[ORM\Embedded(class: GenericWahlKreis::class)]
    private GenericWahlKreis $genericWahlKreis;

    public function __construct(
        Geometry $geometry,
        string $type,
        string $state,
        Agh $agh,
        Btw $btw,
        GenericWahlKreis $genericWahlKreis
    ) {
        $this->geometry = $geometry;
        $this->type = $type;
        $this->state = $state;
        $this->agh = $agh;
        $this->btw = $btw;
        $this->genericWahlKreis = $genericWahlKreis;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAgh(): Agh
    {
        return $this->agh;
    }

    public function getBtw(): Btw
    {
        return $this->btw;
    }

    public function getGeometry(): Geometry
    {
        return $this->geometry;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function isAgh(): bool
    {
        return $this->agh->isAghEntry();
    }

    public function isBtw(): bool
    {
        return $this->btw->isBtwEntry();
    }

    public function setAgh(Agh $agh): void
    {
        $this->agh = $agh;
    }

    public function setBtw(Btw $btw): void
    {
        $this->btw = $btw;
    }

    public function getGenericWahlKreis(): GenericWahlKreis
    {
        return $this->genericWahlKreis;
    }

    public function setGenericWahlKreis(GenericWahlKreis $genericWahlKreis): void
    {
        $this->genericWahlKreis = $genericWahlKreis;
    }
}
