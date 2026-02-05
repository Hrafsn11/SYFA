<?php

namespace App\Enums;

class NamaSBUEnum
{
    use EnumTrait;

    const PAS = 'PAS';
    const PROXCARE_MITRA_TALENTA = 'Proxcare Mitra Talenta';
    const DIFIN_KREATIF_INC = 'DIFIN KREATIF INC';
    const LOGHOS = 'Loghos';
    const MALAKA = 'Malaka';
    const PPS = 'PPS';
    const PROX_EVENT = 'Prox-Event';
    const SKI = 'SKI';

    public static function getMapping($name)
    {
        if (!in_array($name, self::getConstants())) abort (403);

        $data = [
            self::PAS => 'MappingDataPAS',
            self::MALAKA => 'MappingDataMalaka',
            self::PPS => 'MappingDataPPS',
            self::SKI => 'MappingDataSKI',
            self::PROXCARE_MITRA_TALENTA => 'MappingDataProxcareMitraTalenta',
            self::PROX_EVENT => 'MappingDataProxEvent',
        ];

        return $data[$name];
    }
}