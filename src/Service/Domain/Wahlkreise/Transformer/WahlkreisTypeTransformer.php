<?php

declare(strict_types=1);

namespace App\Service\Domain\Wahlkreise\Transformer;

use Brick\Geo\IO\GeoJSON\Feature;

interface WahlkreisTypeTransformer
{
    /**
     * @return Feature[]
     */
    public function getData(): array;

    /**
     * @param GenericWahlkreis[] $wahlkreise
     */
    public function processWahlkreise(array $wahlkreise): void;
}
