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
                    [
                        'coordinate' => 'C11',
                        'component' => 'Pendapatan Operasional',
                        'data' => ['E11', 'P11'] // from E11 -> P11 (Januari - Desember)
                    ],
                    [
                        'coordinate' => 'C12',
                        'component' => 'Pendapatan Operasional Lain',
                        'data' => ['E12', 'P12']
                    ],
                    [
                        'coordinate' => 'C13',
                        'component' => 'Pendapatan Jasa Digital',
                        'data' => ['E13', 'P13']
                    ],
                    [
                        'coordinate' => 'C14',
                        'component' => 'Pendapatan Operasional Lain',
                        'data' => ['E14', 'P14']
                    ],
                    [
                        'coordinate' => 'C15',
                        'component' => 'Pendapatan Jasa Koperasi',
                        'data' => ['E15', 'P15']
                    ],
                    [
                        'coordinate' => 'C16',
                        'component' => 'Penjualan Panen Segar',
                        'data' => ['E16', 'P16']
                    ],
                    [
                        'coordinate' => 'C17',
                        'component' => 'Pendapatan Trekking.id',
                        'data' => ['E17', 'P17']
                    ],
                    [
                        'coordinate' => 'C18',
                        'component' => 'Potongan Penjualan',
                        'data' => ['E18', 'P18']
                    ],
                    [
                        'coordinate' => 'C19',
                        'component' => 'Return Penjualan',
                        'data' => ['E19', 'P19']
                    ],
                    [
                        'coordinate' => 'C20',
                        'component' => 'Discount Penjualan',
                        'data' => ['E20', 'P20']
                    ],
                ]
            ],
            [
                'coordinate' => 'C25',
                'component' => 'Biaya Langsung',
                'child' => [
                    [
                        'coordinate' => 'C26',
                        'component' => 'Tenaga Kerja Langsung',
                        'child' => [
                            [
                                'coordinate' => 'C27',
                                'component' => 'Biaya Tenaga Ahli',
                                'data' => ['E27', 'P27']
                            ],
                            [
                                'coordinate' => 'C28',
                                'component' => 'Komisi Penjualan',
                                'data' => ['E28', 'P28']
                            ],
                            [
                                'coordinate' => 'C29',
                                'component' => 'Gaji Pegawai Langsung',
                                'data' => ['E29', 'P29']
                            ],
                            [
                                'coordinate' => 'C30',
                                'component' => 'Tunjangan Pegawai',
                                'data' => ['E30', 'P30']
                            ],
                            [
                                'coordinate' => 'C31',
                                'component' => 'Project',
                                'data' => ['E31', 'P31']
                            ],
                        ],
                    ],
                    [
                        'coordinate' => 'C33',
                        'component' => 'HPP',
                        'child' => [
                            [
                                'coordinate' => 'C34',
                                'component' => 'Mitra Malaka',
                                'data' => ['E34', 'P34']
                            ]
                        ],
                    ],
                    [
                        'coordinate' => 'C37',
                        'component' => 'Biaya Operasional Lain-lain',
                        'child' => [
                            [
                                'coordinate' => 'C38',
                                'component' => 'Biaya Transportasi Operasional',
                                'data' => ['E38', 'P38']
                            ],
                            [
                                'coordinate' => 'C39',
                                'component' => 'Perawatan kendaraan',
                                'data' => ['E39', 'P39']
                            ],
                            [
                                'coordinate' => 'C40',
                                'component' => 'Biaya Administrasi Kendaraan',
                                'data' => ['E40', 'P40']
                            ],
                            [
                                'coordinate' => 'C41',
                                'component' => 'Biaya Perjalan Dinas',
                                'data' => ['E41', 'P41']
                            ],
                            [
                                'coordinate' => 'C42',
                                'component' => 'Biaya Sertifikasi',
                                'data' => ['E42', 'P42']
                            ],
                            [
                                'coordinate' => 'C43',
                                'component' => 'Perangkat atau Peralatan',
                                'data' => ['E43', 'P43']
                            ],
                            [
                                'coordinate' => 'C44',
                                'component' => 'Penggantian Internet',
                                'data' => ['E44', 'P44']
                            ],
                            [
                                'coordinate' => 'C45',
                                'component' => 'Biaya Operasional Lainnya',
                                'data' => ['E45', 'P45']
                            ],
                            [
                                'coordinate' => 'C46',
                                'component' => 'Biaya Adm Bank Garansi',
                                'data' => ['E46', 'P46']
                            ],
                            [
                                'coordinate' => 'C47',
                                'component' => 'Biaya Adm (Operasional)',
                                'data' => ['E47', 'P47']
                            ],
                            [
                                'coordinate' => 'C48',
                                'component' => 'Biaya administrasi Legalitas',
                                'data' => ['E48', 'P48']
                            ],
                            [
                                'coordinate' => 'C49',
                                'component' => 'Biaya Manage Service',
                                'data' => ['E49', 'P49']
                            ]
                        ],
                    ],
                ]
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