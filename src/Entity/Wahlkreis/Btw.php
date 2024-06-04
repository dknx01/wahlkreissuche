<?php

namespace App\Entity\Wahlkreis;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Btw
{
    #[ORM\Column(nullable: true)]
    private ?int $number;
    #[ORM\Column(nullable: true)]
    private ?string $name;
    #[ORM\Column(nullable: true)]
    private ?string $stateName;
    #[ORM\Column(nullable: true)]
    private ?string $stateNumber;

    public function __construct(
        ?int $number = null,
        ?string $name = null,
        ?string $stateName = null,
        ?string $stateNumber = null
    ) {
        $this->number = $number;
        $this->name = $name;
        $this->stateName = $stateName;
        $this->stateNumber = $stateNumber;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getStateName(): ?string
    {
        return $this->stateName;
    }

    public function getStateNumber(): ?string
    {
        return $this->stateNumber;
    }

    public function isBtwEntry(): bool
    {
        return $this->name !== null
            || $this->number !== null
            || $this->stateName !== null
            || $this->stateNumber !== null;
    }
}
