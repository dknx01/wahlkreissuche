<?php

namespace App\Service\Import\Config;

final readonly class Config
{
    public string $type;
    public string $state;
    public string $wkNr;
    public string $wkName;
    public ?string $wkLongDescription;
    public ?string $wkShortDescription;

    public function __construct(\stdClass $data)
    {
        $this->type = $data->kind;
        $this->state = $data->state;
        $this->wkNr = $data->fields->wk_nr;
        $this->wkLongDescription = property_exists($data->fields, 'wk_lon') ? $data->fields->wk_lon : null;
        $this->wkShortDescription = property_exists($data->fields, 'wk_short') ? $data->fields->wk_short : null;
        $this->wkName = $data->fields->wk_name;
    }
}
