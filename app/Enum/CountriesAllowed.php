<?php

namespace App\Enum;

class CountriesAllowed extends BasicEnum
{
    const VEN = 'Venezuela';
    const PER = 'Perú';
    const CHL = 'Chile';
    const HND = 'Honduras';
    const GTM = 'Guatemala';
    const USA = 'Estados Unidos';

    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public static function getCountries()
    {
        return [
            'VEN' => self::VEN,
            'PER' => self::PER,
            'CHL' => self::CHL,
            'HND' => self::HND,
            'GTM' => self::GTM,
            'USA' => self::USA,
        ];
    }
}