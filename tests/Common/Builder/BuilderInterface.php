<?php

namespace App\Tests\Builder;

/**
 * @template T
 */
interface BuilderInterface
{
    /**
     * @return T
     */
    public function get(): object;
}
