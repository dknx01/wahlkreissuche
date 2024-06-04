<?php

namespace App\GeoJson;

use Location\Formatter\Polygon\GeoJSON;
use Location\Polygon;

class Feature implements \JsonSerializable
{
    /**
     * @param array<array-key, mixed> $properties
     */
    public function __construct(private Polygon|MultiPolygon $polygon, private array $properties)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \JsonException
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => 'Feature',
            'geometry' => $this->getGeometry(),
            'properties' => $this->properties,
        ];
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \JsonException
     */
    private function getGeometry(): array
    {
        if ($this->polygon instanceof Polygon) {
            return json_decode((new GeoJSON())->format($this->polygon), true, 512, JSON_THROW_ON_ERROR);
        }
        $data = [
            'type' => 'MultiPolygon',
            'coordinates' => [],
        ];
        foreach ($this->polygon->getPolygons() as $polygon) {
            $geoJson = json_decode((new GeoJSON())->format($polygon), true, 512, JSON_THROW_ON_ERROR);
            $data['coordinates'][] = $geoJson['coordinates'];
        }

        return $data;
    }
}
