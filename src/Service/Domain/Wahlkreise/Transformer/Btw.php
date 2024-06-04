<?php

declare(strict_types=1);

namespace App\Service\Domain\Wahlkreise\Transformer;

use App\Entity\Wahlkreis;
use App\Entity\Wahlkreis\Geometry;
use App\Service\GeoJsonReader;
use Brick\Geo\IO\GeoJSON\Feature;

class Btw implements WahlkreisTypeTransformer
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
            if (!$wahlkreis->getGeometry() instanceof Geometry) {
                continue;
            }
            $geometry = $this->reader->read($wahlkreis->getGeometry()->getGeometry()->toJson());
            $feature = new Feature($geometry);
            $feature = $feature->withProperty(
                'Nummer',
                $wahlkreis->getBtw()->getNumber()
            );
            $feature = $feature->withProperty(
                'Bundesland',
                $wahlkreis->getBtw()->getStateName()
            );
            $feature = $feature->withProperty(
                'description',
                sprintf(
                    '<b>Name:</b> %s<br><b>Btw-Wahlkreis:</b> %s',
                    $wahlkreis->getBtw()->getName(),
                    $wahlkreis->getBtw()->getNumber()
                )
            );
            $this->features[] = $feature;
        }
    }
}
