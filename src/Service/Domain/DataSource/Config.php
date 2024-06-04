<?php

namespace App\Service\Domain\DataSource;

class Config
{
    public const AS_GEOJSON = 'geojson';
    public const AS_ARRAY = 'array';
    public const AS_WKT = 'wkt';

    public function __construct(public string $outputFormat, public bool $withDbfData = false)
    {
        $formats = [self::AS_ARRAY, self::AS_WKT, self::AS_GEOJSON];
        if (!in_array($this->outputFormat, $formats)) {
            throw new \RuntimeException(sprintf('Unsupported output format %s, only %s are supported', $this->outputFormat, implode(', ', $formats)));
        }
    }
}
