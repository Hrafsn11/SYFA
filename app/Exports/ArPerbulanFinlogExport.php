<?php

namespace App\Exports;

use App\Models\ArPerbulanFinlog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArPerbulanFinlogExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $selectedMonth = '';

    public function __construct($selectedMonth = '')
    {
        $this->selectedMonth = $selectedMonth;
    }

    public function query()
    {
        $query = ArPerbulanFinlog::query();

        if ($this->selectedMonth) {
            $query->where('bulan', $this->selectedMonth);
        }

        return $query->orderBy('bulan', 'desc')->orderBy('nama_perusahaan', 'asc');
    }

    public function headings(): array
    {
        return [
            'No.',
            'Bulan',
            'Nama Perusahaan',
            'Sisa AR Pokok',
            'Sisa Bagi Hasil',
            'Sisa AR Total',
        ];
    }

    public function map($row): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $row->bulan ?? '-',
            $row->nama_perusahaan ?? '-',
            $row->sisa_ar_pokok ?? 0,
            $row->sisa_bagi_hasil ?? 0,
            $row->sisa_ar_total ?? 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '0070C0'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        // Format currency columns
        $sheet->getStyle('D:F')->getNumberFormat()->setFormatCode('#,##0');

        // Center align No. column
        $sheet->getStyle('A')->getAlignment()->setHorizontal('center');

        return $sheet;
    }
}
