<?php

namespace App\UnitTests\Options;

use App\Options\BtwKreise;
use App\Options\States;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BtwKreiseTest extends TestCase
{
    /**
     * @param array<string, int|float> $expected
     */
    #[DataProvider('provideState')]
    public function testGetConfig(string $state, array $expected): void
    {
        $this->assertEquals(
            $expected,
            BtwKreise::getConfig($state)
        );
    }

    public function testGetStates(): void
    {
        $this->assertEquals(
            array_values(States::STATES),
            BtwKreise::getStates()
        );
    }

    public static function provideState(): \Generator
    {
        yield [
            'Berlin',
            [
                'zoom' => 11,
                'lat' => 52.516215,
                'lon' => 13.3922187,
            ],
        ];
        yield [
            'Foo',
            [
                'zoom' => 6.30,
                'lat' => 51.24600061390181,
                'lon' => 10.324952060844131,
            ],
        ];
    }
}
