<?php

namespace App\Tests\Builder;

use App\Entity\Wahlkreis;

class WahlkreisEntityBuilder
{
    public static function createAghWahlkreisEntity(): Wahlkreis
    {
        return new Wahlkreis(
            geometry: GeometryBuilder::getGeometry(),
            type: 'AGH',
            state: 'Berlin',
            agh: new Wahlkreis\Agh(
                wahlkreisLong: '1204',
                wahlkreisShort: '04',
                bezirk: 'Reinickendorf',
            ),
            btw: new Wahlkreis\Btw(
            ),
            genericWahlKreis: new Wahlkreis\GenericWahlKreis()
        );
    }

    public static function createBtwWahlkreisEntity(): Wahlkreis
    {
        return new Wahlkreis(
            geometry: GeometryBuilder::getGeometry(),
            type: 'Btw',
            state: 'Berlin',
            agh: new Wahlkreis\Agh(),
            btw: new Wahlkreis\Btw(
                number: 80,
                name: 'New North',
                stateName: 'Berlin',
                stateNumber: '0123',
            ),
            genericWahlKreis: new Wahlkreis\GenericWahlKreis()
        );
    }
}
