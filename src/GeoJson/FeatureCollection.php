<?php

namespace App\GeoJson;

class FeatureCollection implements \JsonSerializable
{
    /** @var array<string, mixed> */
    private array $features = [];

    /**
     * @param array<string, mixed> $features
     */
    public function __construct(array $features)
    {
        foreach ($features as $feature) {
            $this->features[] = json_encode($feature, JSON_THROW_ON_ERROR);
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \JsonException
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => 'FeatureCollection',
            'features' => array_map(
                static fn (string $data) => \json_decode($data, true, 512, JSON_THROW_ON_ERROR),
                $this->features
            ),
        ];
    }
}
