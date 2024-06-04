<?php

declare(strict_types=1);

namespace App\Options;

interface States
{
    public const BADEN_WURTTEMBERG = 'Baden-Württemberg';
    public const BAYERN = 'Bayern';
    public const BERLIN = 'Berlin';
    public const BRANDENBURG = 'Brandenburg';
    public const BREMEN = 'Bremen';
    public const HAMBURG = 'Hamburg';
    public const HESSEN = 'Hessen';
    public const MECKLENBURG_VORPOMMERN = 'Mecklenburg-Vorpommern';
    public const NIEDERSACHSEN = 'Niedersachsen';
    public const NORDRHEIN_WESTFALEN = 'Nordrhein-Westfalen';
    public const RHEINLAND_PFALZ = 'Rheinland-Pfalz';
    public const SAARLAND = 'Saarland';
    public const SACHSEN = 'Sachsen';
    public const SACHSEN_ANHALT = 'Sachsen-Anhalt';
    public const SCHLESWIG_HOLSTEIN = 'Schleswig-Holstein';
    public const THURINGEN = 'Thüringen';
    public const STATES = [
        self::BADEN_WURTTEMBERG => self::BADEN_WURTTEMBERG,
        self::BAYERN => self::BAYERN,
        self::BERLIN => self::BERLIN,
        self::BRANDENBURG => self::BRANDENBURG,
        self::BREMEN => self::BREMEN,
        self::HAMBURG => self::HAMBURG,
        self::HESSEN => self::HESSEN,
        self::MECKLENBURG_VORPOMMERN => self::MECKLENBURG_VORPOMMERN,
        self::NIEDERSACHSEN => self::NIEDERSACHSEN,
        self::NORDRHEIN_WESTFALEN => self::NORDRHEIN_WESTFALEN,
        self::RHEINLAND_PFALZ => self::RHEINLAND_PFALZ,
        self::SAARLAND => self::SAARLAND,
        self::SACHSEN => self::SACHSEN,
        self::SACHSEN_ANHALT => self::SACHSEN_ANHALT,
        self::SCHLESWIG_HOLSTEIN => self::SCHLESWIG_HOLSTEIN,
        self::THURINGEN => self::THURINGEN,
    ];
}
