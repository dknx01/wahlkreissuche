<?php

namespace App\Tests\Builder;

use Faker\Factory;
use Faker\Generator;

trait FakerTrait
{
    protected static ?Generator $faker = null;

    protected static function getFaker(): Generator
    {
        if (self::$faker === null) {
            self::$faker = Factory::create();
        }

        return self::$faker;
    }
}
