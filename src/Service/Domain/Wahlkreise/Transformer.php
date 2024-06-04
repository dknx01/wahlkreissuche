<?php

declare(strict_types=1);

namespace App\Service\Domain\Wahlkreise;

use App\Entity\Wahlkreis;
use App\Service\Domain\Wahlkreise\Transformer\Agh;
use App\Service\Domain\Wahlkreise\Transformer\Btw;
use Brick\Geo\IO\GeoJSON\Feature;
use Brick\Geo\IO\GeoJSON\FeatureCollection;
use Brick\Geo\IO\GeoJSONWriter;
use Symfony\Component\DependencyInjection\ServiceLocator;

class Transformer implements \Stringable
{
    private FeatureCollection $featureCollection;
    /**
     * @var Feature[]
     */
    private array $features = [];
    private GeoJSONWriter $writer;

    public function __construct(private readonly ServiceLocator $typeTransformer)
    {
        $this->writer = new GeoJSONWriter();
        $this->featureCollection = new FeatureCollection();
    }

    /**
     * @param Wahlkreis[] $wahlkreise
     */
    public function transformAgh(array $wahlkreise): void
    {
        $this->typeTransformer->get(Agh::class)->processWahlkreise($wahlkreise);
        $this->features = $this->typeTransformer->get(Agh::class)->getData();
        $this->createFeatureCollection();
    }

    /**
     * @param Wahlkreis[] $wahlkreise
     */
    public function transformBtw(array $wahlkreise): void
    {
        $this->typeTransformer->get(Btw::class)->processWahlkreise($wahlkreise);
        $this->features = $this->typeTransformer->get(Btw::class)->getData();
        $this->createFeatureCollection();
    }

    /**
     * @param Wahlkreis[] $wahlkreise
     */
    public function transformWahlkreis(array $wahlkreise): void
    {
        $this->typeTransformer->get(Transformer\GenericWahlkreis::class)->processWahlkreise($wahlkreise);
        $this->features = $this->typeTransformer->get(Transformer\GenericWahlkreis::class)->getData();
        $this->createFeatureCollection();
    }

    public function __toString(): string
    {
        return $this->writer->write($this->featureCollection);
    }

    private function createFeatureCollection(): void
    {
        $this->featureCollection = new FeatureCollection(...$this->features);
    }
}
