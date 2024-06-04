<?php

declare(strict_types=1);

namespace App\Service\Domain\Wahlkreise\Transformer;

use App\Entity\Wahlkreis;
use App\Service\GeoJsonReader;
use Brick\Geo\IO\GeoJSON\Feature;

class GenericWahlkreis implements WahlkreisTypeTransformer
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
                'Name',
                $wahlkreis->getGenericWahlKreis()->getName()
            );
            $feature = $feature->withProperty(
                'Nr',
                $wahlkreis->getGenericWahlKreis()->getNr()
            );
            $feature = $feature->withProperty(
                'Short',
                $wahlkreis->getGenericWahlKreis()->getWahlkreisShort()
            );
            $feature = $feature->withProperty(
                'Long',
                $wahlkreis->getGenericWahlKreis()->getWahlkreisLong()
            );
            $description = sprintf(
                '<b>Wahlkreis: %s</b> (Wahlkreis %s)',
                $wahlkreis->getGenericWahlKreis()->getName(),
                $wahlkreis->getGenericWahlKreis()->getNr()
            );
            if ($wahlkreis->getGenericWahlKreis()->getWahlkreisLong()) {
                $description = + '<br> ' . $wahlkreis->getGenericWahlKreis()->getWahlkreisLong();
            }
            if ($wahlkreis->getGenericWahlKreis()->getWahlkreisShort()) {
                $description = + ' (' . $wahlkreis->getGenericWahlKreis()->getWahlkreisShort() . ')';
            }
            $feature = $feature->withProperty(
                'description',
                $description
            );
            $this->features[] = $feature;
        }
    }
}
