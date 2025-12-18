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
            $query->where('periode', 'like', $this->selectedMonth . '%');
        }

        return $query->orderBy('periode', 'desc')->orderBy('nama_debitur', 'asc');
    }

    public function headings(): array
    {
        return [
            'No.',
            'Periode',
            'Nama Debitur',
            'DEL 1-30',
            'DEL 31-60',
            'DEL 61-90',
            'NPL 91-179',
            'WRITE OFF >180',
            'Total AR',
        ];
    }

    public function map($row): array
    {
        static $index = 0;
        $index++;

        $totalAR = ($row->del_1_30 ?? 0) + ($row->del_31_60 ?? 0) + ($row->del_61_90 ?? 0) + ($row->npl_91_179 ?? 0) + ($row->write_off_180 ?? 0);

        return [
            $index,
            $row->periode ?? '-',
            $row->nama_debitur ?? '-',
            $row->del_1_30 ?? 0,
            $row->del_31_60 ?? 0,
            $row->del_61_90 ?? 0,
            $row->npl_91_179 ?? 0,
            $row->write_off_180 ?? 0,
            $totalAR,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
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
        $sheet->getStyle('D:I')->getNumberFormat()->setFormatCode('#,##0');

        // Center align No. column
        $sheet->getStyle('A')->getAlignment()->setHorizontal('center');

        return $sheet;
    }
}
