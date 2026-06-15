<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;

class PinjamanLteInvoiceRule implements ValidationRule, DataAwareRule
{
    protected $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $rawPinjaman = rupiahToRawValue($value);
        if ($rawPinjaman <= 0) {
            $fail('Nilai pinjaman harus lebih dari 0.');
            return;
        }

        $invoiceAttr = str_replace('nilai_pinjaman', 'nilai_invoice', $attribute);
        $nilaiInvoiceVal = data_get($this->data, $invoiceAttr);

        $rawInvoice = rupiahToRawValue($nilaiInvoiceVal ?? 0);

        if ($rawInvoice > 0 && $rawPinjaman > $rawInvoice) {
            $fail('Nilai pinjaman tidak boleh lebih besar dari nilai invoice.');
        }
    }
}
