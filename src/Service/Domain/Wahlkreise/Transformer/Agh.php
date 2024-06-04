<?php

declare(strict_types=1);

namespace App\Service\Domain\Wahlkreise\Transformer;

use App\Entity\Wahlkreis;
use App\Service\GeoJsonReader;
use Brick\Geo\IO\GeoJSON\Feature;

class Agh implements WahlkreisTypeTransformer
{
    /**
     * @var Feature[]
     */
    private array $features = [];

    public function __construct(private GeoJsonReader $reader)
    {
    }

    /**
     * @return Feature[]
     */
    public function getData(): array
    {
        return $this->features;
    }

    /**
     * @param Wahlkreis[] $wahlkreise
     */
    public function processWahlkreise(array $wahlkreise): void
    {
        foreach ($wahlkreise as $wahlkreis) {
            $geometry = $this->reader->read($wahlkreis->getGeometry()->getGeometry()->toJson());
            $feature = new Feature($geometry);
            $feature = $feature->withProperty(
                'AWK',
                $wahlkreis->getAgh()->getWahlkreisLong()
            );
            $feature = $feature->withProperty(
                'BEZ',
                $wahlkreis->getAgh()->getBezirk()
            );
            $feature = $feature->withProperty(
                'description',
                sprintf(
                    '<b>Bezirk:</b> %s<br><b>AGH-Wahlkreis:</b> %s',
                    $wahlkreis->getAgh()->getBezirk(),
                    $wahlkreis->getAgh()->getWahlkreisShort()
                )
            );
            $this->features[] = $feature;
        }
    }
}
