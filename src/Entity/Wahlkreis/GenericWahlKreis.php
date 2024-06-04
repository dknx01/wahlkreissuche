<?php

namespace App\Entity\Wahlkreis;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class GenericWahlKreis
{
    #[ORM\Column(nullable: true)]
    private ?string $wahlkreisLong;
    #[ORM\Column(nullable: true)]
    private ?string $wahlkreisShort;
    #[ORM\Column(nullable: true)]
    private ?string $name;
    #[ORM\Column(nullable: true)]
    private ?int $nr;

    public function __construct(
        ?string $wahlkreisLong = null,
        ?string $wahlkreisShort = null,
        ?string $name = null,
        ?int $nr = null
    ) {
        $this->wahlkreisLong = $wahlkreisLong;
        $this->wahlkreisShort = $wahlkreisShort;
        $this->name = $name;
        $this->nr = $nr;
    }

    public function getWahlkreisLong(): ?string
    {
        return $this->wahlkreisLong;
    }

    public function getWahlkreisShort(): ?string
    {
        return $this->wahlkreisShort;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function isEntry(): bool
    {
        return $this->name !== null
            || $this->wahlkreisLong !== null
            || $this->wahlkreisShort !== null;
    }

    public function getNr(): ?int
    {
        return $this->nr;
    }

    public function setNr(?int $nr): void
    {
        $this->nr = $nr;
    }
}
