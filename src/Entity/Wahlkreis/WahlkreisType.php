<?php

namespace App\Entity\Wahlkreis;

abstract class WahlkreisType implements WahlkreisData
{
    public function isEmpty(): bool
    {
        foreach (get_object_vars($this) as $name => $value) {
            if (null !== $value) {
                return false;
            }
        }

        return true;
    }
}
