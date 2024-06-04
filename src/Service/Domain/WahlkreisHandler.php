<?php

declare(strict_types=1);

namespace App\Service\Domain;

use App\Entity\Wahlkreis;
use App\Repository\WahlkreisRepository;
use App\Service\Domain\Wahlkreise\Transformer;

class WahlkreisHandler
{
    public function __construct(
        private Transformer $transformer,
        private WahlkreisRepository $wahlkreisRepository,
        private string $germanyGeoJsonFile
    ) {
    }

    /**
     * @return Wahlkreis[]
     */
    public function findAllAghWahlkreise(): array
    {
        return $this->wahlkreisRepository->findBy(['type' => 'AGH']);
    }

    public function getAghWahlkreiseAsString(): string
    {
        $this->transformer->transformAgh($this->findAllAghWahlkreise());

        return $this->transformer->__toString();
    }

    public function getBtwWahlkreiseAsString(): string
    {
        $this->transformer->transformBtw($this->wahlkreisRepository->findBy(['type' => 'BTW', 'btw.stateName' => 'Berlin']));

        return $this->transformer->__toString();
    }

    public function getBtwWahlkreiseAsString_BB(): string
    {
        $this->transformer->transformBtw($this->wahlkreisRepository->findBy(['btw.stateName' => 'Brandenburg', 'type' => 'BTW']));

        return $this->transformer->__toString();
    }

    public function getBtwWahlkreiseDeutschlandAsString(): string
    {
        return file_get_contents($this->germanyGeoJsonFile);
    }

    public function getBtwWahlkreiseAsStringByState(string $state): string
    {
        $this->transformer->transformBtw($this->wahlkreisRepository->findBy(['btw.stateName' => $state, 'type' => 'BTW']));

        return $this->transformer->__toString();
    }

    public function getLtwWahlkreiseAsStringByState(string $state): string
    {
        $this->transformer->transformWahlkreis($this->wahlkreisRepository->findBy(['state' => $state, 'type' => 'LTW']));

        return $this->transformer->__toString();
    }
}
