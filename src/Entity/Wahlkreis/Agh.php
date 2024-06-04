<?php

namespace App\Entity\Wahlkreis;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Agh
{
    #[ORM\Column(nullable: true)]
    private ?string $wahlkreisLong;
    #[ORM\Column(nullable: true)]
    private ?string $wahlkreisShort;
    #[ORM\Column(nullable: true)]
    private ?string $bezirk;

    public function __construct(?string $wahlkreisLong = null, ?string $wahlkreisShort = null, ?string $bezirk = null)
    {
        $this->wahlkreisLong = $wahlkreisLong;
        $this->wahlkreisShort = $wahlkreisShort;
        $this->bezirk = $bezirk;
    }

    public function getWahlkreisLong(): ?string
    {
        return $this->wahlkreisLong;
    }

    public function getWahlkreisShort(): ?string
    {
        return $this->wahlkreisShort;
    }

    public function getBezirk(): ?string
    {
        return $this->bezirk;
    }

    public function isAghEntry(): bool
    {
        return $this->bezirk !== null
            || $this->wahlkreisLong !== null
            || $this->wahlkreisShort !== null;
    }
}
