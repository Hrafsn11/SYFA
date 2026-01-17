<?php 

namespace App\Services;

class MappingDataMalaka
{
    protected $data;

    public function from($fromData = null)
    {
        $this->data = $fromData;
        return $this;
    }

    public function mapping()
    {
        return [
            [
                'coordinate' => 'C10',
                'component' => 'Pendapatan',
                'child' => [
                    
                ]
            ],
            [
                'component' => 'Biaya Langsung',
            ],
            [
                'component' => 'Biaya Umum & Administrasi',
            ],
            [
                'component' => 'Pendapatan & (Biaya) Non Operasional',
            ],
            [
                'component' => 'Laba/Rugi sebelum pajak [A-B-C-D]',
            ],
            [
                'component' => 'Pajak',
            ],
            [
                'component' => 'Laba/Rugi sesudah pajak [E-F]',
            ],
            [
                'component' => 'Pendapatan',
            ],
            [
                'component' => 'Biaya langsung gaji',
            ],
            [
                'component' => 'Biaya langsung non gaji',
            ],
            [
                'component' => 'laba Kotor',
            ],
            [
                'component' => 'Biaya Pegawai',
            ],
            [
                'component' => 'Biaya umum dan administrasi',
            ],
            [
                'component' => 'Laba sebelum pajak dan bunga',
            ],
            [
                'component' => 'Pendapatan (biaya) lainnya',
            ],
            [
                'component' => 'Operating profit',
            ],
            [
                'component' => 'Pajak',
            ],
            [
                'component' => 'Net Profit (Loss)',
            ],
            [
                'component' => 'Gross Margin',
            ],
            [
                'component' => 'Operating Margin',
            ],
            [
                'component' => 'Net Margin'
            ],
        ];
    }
}