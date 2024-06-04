<?php

namespace App\Tests\Builder;

use App\Entity\Wahlkreis;
use App\Options\States;

class WahlkreisBuilder
{
    private Wahlkreis\Agh $agh;
    private Wahlkreis\Geometry $geometry;
    private string $type;
    private string $state;
    private Wahlkreis\Btw $btw;

    public function __construct()
    {
        $this->state = array_rand(States::STATES);
        $this->type = 'polygone';
        $this->geometry = new Wahlkreis\Geometry('polygone', [51.1234, 13.98765]);
        $this->agh = new Wahlkreis\Agh('Köpenick', 'TK', 'Treptow-Köpenick');
        $this->btw = new Wahlkreis\Btw(999, 'Caramel', 'Einstein', 47);
    }

    public function __call(string $name, mixed $argument)
    {
        $this->$name = $argument;
    }

    public function build(): Wahlkreis
    {
        return new Wahlkreis($this->geometry, $this->type, $this->state, $this->agh, $this->btw, new Wahlkreis\GenericWahlKreis());
    }
}
