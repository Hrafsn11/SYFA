<?php

namespace App\Enums;

class JenisPembiayaanEnum
{
    use EnumTrait;

    const INVOICE_FINANCING = 'Invoice Financing';
    const PO_FINANCING = 'PO Financing';
    const INSTALLMENT = 'Installment';
    const FACTORING = 'Factoring';
}