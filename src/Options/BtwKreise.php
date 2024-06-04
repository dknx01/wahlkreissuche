<?php

namespace App\Options;

class BtwKreise
{
    private const CONFIG = [
        States::BADEN_WURTTEMBERG => [
            'zoom' => 7.45,
            'lat' => 48.64915047373977,
            'lon' => 9.118539258329466,
        ],
        States::BAYERN => [
            'zoom' => 7.45,
            'lat' => 48.975051003288215,
            'lon' => 11.735867909478404,
        ],
        States::BERLIN => [
            'zoom' => 11,
            'lat' => 52.516215,
            'lon' => 13.3922187,
        ],
        States::BRANDENBURG => [
            'zoom' => 7.5,
            'lat' => 52.52396321760753,
            'lon' => 13.4163451533685,
        ],
        States::BREMEN => [
            'zoom' => 9.4,
            'lat' => 53.311830030464655,
            'lon' => 8.523626969611781,
        ],
        States::HAMBURG => [
            'zoom' => 9.4,
            'lat' => 53.75166461854616,
            'lon' => 9.40804887739166,
        ],
        States::HESSEN => [
            'zoom' => 8,
            'lat' => 50.60489549430837,
            'lon' => 8.981963558053717,
        ],
        States::MECKLENBURG_VORPOMMERN => [
            'zoom' => 8,
            'lat' => 53.71386659230693,
            'lon' => 12.584919799882242,
        ],
        States::NIEDERSACHSEN => [
            'zoom' => 7.4,
            'lat' => 52.89824538031976,
            'lon' => 9.2244495753585,
        ],
        States::NORDRHEIN_WESTFALEN => [
            'zoom' => 7.75,
            'lat' => 51.462826216595815,
            'lon' => 7.399870792466985,
        ],
        States::RHEINLAND_PFALZ => [
            'zoom' => 7.75,
            'lat' => 49.90669874219103,
            'lon' => 7.441697324043028,
        ],
        States::SAARLAND => [
            'zoom' => 9.5,
            'lat' => 49.37265597730092,
            'lon' => 6.99916715100015,
        ],
        States::SACHSEN => [
            'zoom' => 8.45,
            'lat' => 51.04033405908293,
            'lon' => 13.615515142759035,
        ],
        States::SACHSEN_ANHALT => [
            'zoom' => 7.9,
            'lat' => 51.91679860755061,
            'lon' => 11.692200262691866,
        ],
        States::SCHLESWIG_HOLSTEIN => [
            'zoom' => 8,
            'lat' => 54.16208483190983,
            'lon' => 9.91812204500672,
        ],
        States::THURINGEN => [
            'zoom' => 8.48,
            'lat' => 50.92819996893771,
            'lon' => 10.979361135671615,
        ],
        'default' => [
            'zoom' => 6.30,
            'lat' => 51.24600061390181,
            'lon' => 10.324952060844131,
        ],
    ];

    /**
     * @return array<string, float>
     */
    public static function getConfig(string $state): array
    {
        return array_key_exists($state, self::CONFIG) ? self::CONFIG[$state] : self::CONFIG['default'];
    }

    /**
     * @return string[]
     */
    public static function getStates(): array
    {
        return array_values(States::STATES);
    }
}
