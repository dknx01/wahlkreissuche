<?php

declare(strict_types=1);

namespace App\Service;

use Brick\Geo\Engine\GeometryEngineRegistry;
use Brick\Geo\Engine\SQLite3Engine;

class GeoJsonReader extends \Brick\Geo\IO\GeoJSONReader
{
    public function __construct(bool $lenient = false)
    {
        parent::__construct($lenient);
        if (!$this->geometryEngineSet()) {
            $this->loadEngine();
        }
    }

    private function geometryEngineSet(): bool
    {
        return GeometryEngineRegistry::has();
    }

    private function loadEngine(): void
    {
        $sqlite3 = new \SQLite3(':memory:');
        $sqlite3->loadExtension('mod_spatialite.so');
        GeometryEngineRegistry::set(new SQLite3Engine($sqlite3));
    }
}
