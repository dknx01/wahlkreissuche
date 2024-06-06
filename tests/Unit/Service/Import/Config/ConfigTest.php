<?php

namespace App\UnitTests\Service\Import\Config;

use App\Service\Import\Config\Config;
use Ergebnis\Json\Json;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    public function testProcessConfigFile(): void
    {
        $config = new Config(
            json_decode(
                <<<JSON
{
  "kind": "LTW",
  "state": "Berlin",
  "fields": {
    "wk_lon": "FooBarLONG",
    "wk_short": "FooBar",
    "wk_nr": "nr_wk",
    "wk_name": "name_wk"
  }
}
JSON,
                false,
                512,
                JSON_THROW_ON_ERROR
            )
        );

        $this->assertEquals('LTW', $config->type);
        $this->assertEquals('Berlin', $config->state);
        $this->assertEquals('name_wk', $config->wkName);
        $this->assertEquals('nr_wk', $config->wkNr);
        $this->assertEquals('FooBarLONG', $config->wkLongDescription);
        $this->assertEquals('FooBar', $config->wkShortDescription);
    }
    /**
     * @throws \JsonException
     */
    #[TestDox('No Description fields in config file')]
    public function testProcessConfigFileWithoutDescription(): void
    {
        $config = new Config(
            json_decode(
                <<<JSON
{
  "kind": "LTW",
  "state": "Berlin",
  "fields": {
    "wk_nr": "nr_wk",
    "wk_name": "name_wk"
  }
}
JSON,
                false,
                512,
                JSON_THROW_ON_ERROR
            )
        );

        $this->assertEquals('LTW', $config->type);
        $this->assertEquals('Berlin', $config->state);
        $this->assertEquals('name_wk', $config->wkName);
        $this->assertEquals('nr_wk', $config->wkNr);
        $this->assertNull($config->wkLongDescription);
        $this->assertNull($config->wkShortDescription);
    }
}
