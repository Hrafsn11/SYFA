<?php

namespace App\Enums;

class JenisPembiayaanEnum
{
    use EnumTrait;

    const INVOICE_FINANCING = 'Invoice Financing';
    const INSTALLMENT = 'Installment';

    public static function getPrefix(string $jenisPembiayaan): string
    {
        switch ($jenisPembiayaan) {
            case 'Invoice Financing':
                $prefix = 'INV';
                break;
            case 'Installment':
                $prefix = 'INS';
                break;
            default:
                $prefix = 'INV';
                break;
        }
        return $prefix;
    }
}