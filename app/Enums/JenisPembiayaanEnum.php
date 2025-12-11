<?php

namespace App\Enums;

class JenisPembiayaanEnum
{
    use EnumTrait;

    const INVOICE_FINANCING = 'Invoice Financing';
    const PO_FINANCING = 'PO Financing';
    const INSTALLMENT = 'Installment';
    const FACTORING = 'Factoring';

    public static function getPrefix(string $jenisPembiayaan): string
    {
        switch ($jenisPembiayaan) {
            case 'Invoice Financing':
                $prefix = 'INV';
                break;
            case 'PO Financing':
                $prefix = 'PO';
                break;
            case 'Installment':
                $prefix = 'INS';
                break;
            case 'Factoring':
                $prefix = 'FAC';
                break;
            default:
                $prefix = 'INV';
                break;
        }
        return $prefix;
    }
}