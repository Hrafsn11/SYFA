<?php

namespace App\Exports;

use App\Models\ArPerbulan;
use Illuminate\Support\Collection;

class ArPerbulanPdfExport
{
    protected $selectedMonth = '';

    public function __construct($selectedMonth = '')
    {
        $this->selectedMonth = $selectedMonth;
    }

    public function getData()
    {
        $query = ArPerbulan::query();

        if ($this->selectedMonth) {
            $query->where('bulan', $this->selectedMonth);
        }

        return $query->orderBy('bulan', 'desc')
                    ->orderBy('nama_perusahaan', 'asc')
                    ->get()
                    ->map(function ($row, $index) {
                        return [
                            'no' => $index + 1,
                            'bulan' => $this->sanitizeUtf8($row->bulan),
                            'nama_perusahaan' => $this->sanitizeUtf8($row->nama_perusahaan ?? '-'),
                            'sisa_ar_pokok' => $row->sisa_ar_pokok,
                            'sisa_bunga' => $row->sisa_bunga,
                            'sisa_ar_total' => $row->sisa_ar_total,
                        ];
                    });
    }

    private function sanitizeUtf8($string)
    {
        if (is_null($string)) {
            return '';
        }

        // Check and fix UTF-8 encoding
        if (!mb_check_encoding($string, 'UTF-8')) {
            // If not valid UTF-8, try to detect and convert
            $encoding = mb_detect_encoding($string, 'UTF-8, ISO-8859-1, WINDOWS-1252', true);
            if ($encoding && $encoding !== 'UTF-8') {
                $string = mb_convert_encoding($string, 'UTF-8', $encoding);
            }
        }

        // Remove any invalid UTF-8 sequences
        return mb_convert_encoding($string, 'UTF-8', 'UTF-8');
    }

    public function getFileName()
    {
        return 'AR_Perbulan_' . ($this->selectedMonth ?: 'All') . '_' . now()->format('Y-m-d_His');
    }
}
