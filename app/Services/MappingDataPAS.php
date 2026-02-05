<?php 

namespace App\Services;

use Illuminate\Support\Str;

class MappingDataPAS extends MappingRekursiData
{

    protected $data;
    protected $tahun;
    protected $id_laporan_investasi;

    public static function from($fromData = null, $tahun = null, $id_laporan_investasi = null)
    {
        $instance = new static();
        $instance->data = $fromData;
        $instance->tahun = $tahun;
        $instance->id_laporan_investasi = $id_laporan_investasi;
        return $instance;
    }

    public function mapping()
    {
        $data = $this->rekursiData($this->mappingAddressCell(), null, $this->id_laporan_investasi);
        return $data;
    }

    public function mappingAddressCell(): array
    {
        return [
            [
                'coordinate' => 'C10',
                'component' => 'Pendapatan',
                'child' => [
                    [
                        'coordinate' => 'C11',
                        'component' => 'Pendapatan Operasional ',
                        'data' => ['E11', 'P11']
                    ],
                    [
                        'coordinate' => 'C12',
                        'component' => 'Pendapatan Jasa Digital',
                        'data' => ['E12', 'P12']
                    ],
                    [
                        'coordinate' => 'C13',
                        'component' => 'Pendapatan Operasional Lain',
                        'data' => ['E13', 'P13']
                    ],
                    [
                        'coordinate' => 'C14',
                        'component' => 'Pendapatan Jasa Koperasi',
                        'data' => ['E14', 'P14']
                    ],
                    [
                        'coordinate' => 'C15',
                        'component' => 'Penjualan Panen Segar',
                        'data' => ['E15', 'P15']
                    ],
                    [
                        'coordinate' => 'C16',
                        'component' => 'Pendapatan Trekking.id',
                        'data' => ['E16', 'P16']
                    ],
                    [
                        'coordinate' => 'C17',
                        'component' => 'Potongan Penjualan',
                        'data' => ['E17', 'P17']
                    ],
                    [
                        'coordinate' => 'C18',
                        'component' => 'Return Penjualan',
                        'data' => ['E18', 'P18']
                    ],
                    [
                        'coordinate' => 'C19',
                        'component' => 'Discount Penjualan',
                        'data' => ['E19', 'P19']
                    ],
                ]
            ],
            [
                'coordinate' => 'C23',
                'component' => 'HPP',
                'child' => [
                    [
                        'coordinate' => 'C24',
                        'component' => 'Mitra Trekking ID',
                        'data' => ['E24', 'P24'],
                    ],
                    [
                        'coordinate' => 'C25',
                        'component' => 'Mitra Proxcare',
                        'data' => ['E25', 'P25'],
                    ],
                    [
                        'coordinate' => 'C26',
                        'component' => 'Mitra Ubar',
                        'data' => ['E26', 'P26'],
                    ],
                    [
                        'coordinate' => 'C27',
                        'component' => 'Mitra Malaka',
                        'data' => ['E27', 'P27'],
                    ],
                    [
                        'coordinate' => 'C28',
                        'component' => 'Mitra Difin',
                        'data' => ['E28', 'P28'],
                    ],
                ]
            ],
            [
                'coordinate' => 'C30',
                'component' => 'Biaya Langsung',
                'child' => [
                    [
                        'coordinate' => 'C31',
                        'component' => 'Tenaga Kerja Langsung',
                        'child' => [
                            [
                                'coordinate' => 'C32',
                                'component' => 'Biaya Tenaga Ahli',
                                'data' => ['E32', 'P32'],
                            ],
                            [
                                'coordinate' => 'C33',
                                'component' => 'Komisi Penjualan',
                                'data' => ['E33', 'P33'],
                            ],
                        ],
                    ],
                    [
                        'coordinate' => 'C35',
                        'component' => 'Gaji Pegawai Opersional',
                        'child' => [
                            [
                                'coordinate' => 'C36',
                                'component' => 'Gaji Pegawai Langsung',
                                'data' => ['E36', 'P36'],
                            ],
                            [
                                'coordinate' => 'C37',
                                'component' => 'Tunjangan Hari Raya Operasional',
                                'data' => ['E37', 'P37'],
                            ],
                            [
                                'coordinate' => 'C38',
                                'component' => 'Project',
                                'data' => ['E38', 'P38'],
                            ],
                            [
                                'coordinate' => 'C39',
                                'component' => 'Tunjangan Lainnya/Koperasi Operasional',
                                'data' => ['E39', 'P39'],
                            ],
                            [
                                'coordinate' => 'C40',
                                'component' => 'Tunjangan BPJS Operasional',
                                'data' => ['E40', 'P40'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C41',
                        'component' => 'Biaya Mitra',
                        'data' => ['E41', 'P41'],
                    ],
                    [
                        'coordinate' => 'C48',
                        'component' => 'Biaya Konsultasi',
                        'child' => [
                            [
                                'coordinate' => 'C49',
                                'component' => 'Kunjungan Konsultan',
                                'data' => ['E49', 'P49'],
                            ],
                            [
                                'coordinate' => 'C50',
                                'component' => 'Meals',
                                'data' => ['E50', 'P50'],
                            ],
                            [
                                'coordinate' => 'C51',
                                'component' => 'Biaya Konsultan Lainya',
                                'data' => ['E51', 'P51'],
                            ],
                            [
                                'coordinate' => 'C52',
                                'component' => 'Biaya Konsultasi ( Vendor )',
                                'data' => ['E52', 'P52'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C53',
                        'component' => 'Biaya Operasional Lain -lain',
                        'child' => [
                            [
                                'coordinate' => 'C54',
                                'component' => 'Biaya Transportasi Operasional',
                                'data' => ['E54', 'P54'],
                            ],
                            [
                                'coordinate' => 'C55',
                                'component' => 'Perawatan Kendaraan',
                                'data' => ['E55', 'P55'],
                            ],
                            [
                                'coordinate' => 'C56',
                                'component' => 'Biaya Administrasi Kendaraan',
                                'data' => ['E56', 'P56'],
                            ],
                            [
                                'coordinate' => 'C57',
                                'component' => 'Biaya Perjalanan Dinas',
                                'data' => ['E57', 'P57'],
                            ],
                            [
                                'coordinate' => 'C58',
                                'component' => 'Biaya Sertifikasi',
                                'data' => ['E58', 'P58'],
                            ],
                            [
                                'coordinate' => 'C59',
                                'component' => 'Perangkat atau Peralatan',
                                'data' => ['E59', 'P59'],
                            ],
                            [
                                'coordinate' => 'C60',
                                'component' => 'Penggantian Internet',
                                'data' => ['E60', 'P60'],
                            ],
                            [
                                'coordinate' => 'C61',
                                'component' => 'Biaya Operasional Lainnya',
                                'data' => ['E61', 'P61'],
                            ],
                            [
                                'coordinate' => 'C62',
                                'component' => 'Biaya Adm Bank Garansi',
                                'data' => ['E62', 'P62'],
                            ],
                            [
                                'coordinate' => 'C63',
                                'component' => 'Biaya Adm (Operasional)',
                                'data' => ['E63', 'P63'],
                            ],
                            [
                                'coordinate' => 'C64',
                                'component' => 'Biaya administrasi Legalitas',
                                'data' => ['E64', 'P64'],
                            ],
                        ]
                    ],
                ]
            ],
            [
                'coordinate' => 'C71',
                'component' => 'Biaya Umum & Administrasi',
                'child' => [
                    [
                        'coordinate' => 'C72',
                        'component' => 'Biaya Pegawai',
                        'child' => [
                            [
                                'coordinate' => 'C73',
                                'component' => 'Gaji Karyawan',
                                'data' => ['E73', 'P73'],
                            ],
                            [
                                'coordinate' => 'C74',
                                'component' => 'Lembur Karyawan',
                                'data' => ['E74', 'P74'],
                            ],
                            [
                                'coordinate' => 'C75',
                                'component' => 'Tunjangan Hari Raya',
                                'data' => ['E75', 'P75'],
                            ],
                            [
                                'coordinate' => 'C76',
                                'component' => 'Tunjangan/Komisi, Bonus atau Fee',
                                'data' => ['E76', 'P76'],
                            ],
                            [
                                'coordinate' => 'C77',
                                'component' => 'Beban Pegawai Magang',
                                'data' => ['E77', 'P77'],
                            ],
                            [
                                'coordinate' => 'C78',
                                'component' => 'Tunjangan Kehadiran',
                                'data' => ['E78', 'P78'],
                            ],
                            [
                                'coordinate' => 'C79',
                                'component' => 'Tunjangan Kesehatan (Medical)',
                                'data' => ['E79', 'P79'],
                            ],
                            [
                                'coordinate' => 'C80',
                                'component' => 'Tunjangan BPJS',
                                'data' => ['E80', 'P80'],
                            ],
                            [
                                'coordinate' => 'C81',
                                'component' => 'Tunjangan PPh Pasal 21',
                                'data' => ['E81', 'P81'],
                            ],
                            [
                                'coordinate' => 'C82',
                                'component' => 'Tunjangan Telekomunikasi',
                                'data' => ['E82', 'P82'],
                            ],
                            [
                                'coordinate' => 'C83',
                                'component' => 'Pesangon Karyawan',
                                'data' => ['E83', 'P83'],
                            ],
                            [
                                'coordinate' => 'C84',
                                'component' => 'Tunjangan Lainnya/Koperasi ',
                                'data' => ['E84', 'P84'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C85',
                        'component' => 'Transportasi dan Perjalanan Dinas',
                        'child' => [
                            [
                                'coordinate' => 'C86',
                                'component' => 'Beban Transportasi Umum',
                                'data' => ['E86', 'P86'],
                            ],
                            [
                                'coordinate' => 'C87',
                                'component' => 'Beban Perjalanan Dinas Umum',
                                'data' => ['E87', 'P87'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C89',
                        'component' => 'Biaya Kantor',
                        'child' => [
                            [
                                'coordinate' => 'C90',
                                'component' => 'Beban Peralatan Kantor & Perangkat Kerja',
                                'data' => ['E90', 'P90'],
                            ],
                            [
                                'coordinate' => 'C91',
                                'component' => 'Beban Alat Tulis Kantor',
                                'data' => ['E91', 'P91'],
                            ],
                            [
                                'coordinate' => 'C92',
                                'component' => 'Beban Listrik & PAM',
                                'data' => ['E92', 'P92'],
                            ],
                            [
                                'coordinate' => 'C93',
                                'component' => 'Beban Telepon dan Internet Kantor',
                                'data' => ['E93', 'P93'],
                            ],
                            [
                                'coordinate' => 'C94',
                                'component' => 'Beban Ponsel',
                                'data' => ['E94', 'P94'],
                            ],
                            [
                                'coordinate' => 'C95',
                                'component' => 'Beban Fotocopy dan Cetak',
                                'data' => ['E95', 'P95'],
                            ],
                            [
                                'coordinate' => 'C96',
                                'component' => 'Beban Sewa Kantor',
                                'data' => ['E96', 'P96'],
                            ],
                            [
                                'coordinate' => 'C97',
                                'component' => 'Beban Pengiriman',
                                'data' => ['E97', 'P97'],
                            ],
                            [
                                'coordinate' => 'C98',
                                'component' => 'Beban Materai',
                                'data' => ['E98', 'P98'],
                            ],
                            [
                                'coordinate' => 'C99',
                                'component' => 'Beban Kantor Lainnya',
                                'data' => ['E99', 'P99'],
                            ],
                            [
                                'coordinate' => 'C100',
                                'component' => 'Beban Perlengkapan Kantor',
                                'data' => ['E100', 'P100'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C102',
                        'component' => 'Biaya Pemeliharaan',
                        'child' => [
                            [
                                'coordinate' => 'C103',
                                'component' => 'Beban Pemeliharaan Kendaraan',
                                'data' => ['E103', 'P103'],
                            ],
                            [
                                'coordinate' => 'C104',
                                'component' => 'Beban Adm Kendaraan',
                                'data' => ['E104', 'P104'],
                            ],
                            [
                                'coordinate' => 'C105',
                                'component' => 'Beban Asuransi Aset',
                                'data' => ['E105', 'P105'],
                            ],
                            [
                                'coordinate' => 'C106',
                                'component' => 'Beban Pemeliharaan Peralatan Kantor',
                                'data' => ['E106', 'P106'],
                            ],
                            [
                                'coordinate' => 'C107',
                                'component' => 'Beban Pemeliharaan Kantor',
                                'data' => ['E107', 'P107'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C108',
                        'component' => 'Biaya Rumah Tangga',
                        'child' => [
                            [
                                'coordinate' => 'C109',
                                'component' => 'Beban Rapat',
                                'data' => ['E109', 'P109'],
                            ],
                            [
                                'coordinate' => 'C110',
                                'component' => 'Beban Dekorasi',
                                'data' => ['E110', 'P110'],
                            ],
                            [
                                'coordinate' => 'C111',
                                'component' => 'Beban Pelatihan, Seminar, dan Pendidikan Internal',
                                'data' => ['E111', 'P111'],
                            ],
                            [
                                'coordinate' => 'C112',
                                'component' => 'Beban Pantry',
                                'data' => ['E112', 'P112'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C113',
                        'component' => 'Biaya Promosi',
                        'child' => [
                            [
                                'coordinate' => 'C114',
                                'component' => 'Beban Iklan',
                                'data' => ['E114', 'P114'],
                            ],
                            [
                                'coordinate' => 'C115',
                                'component' => 'Beban Cetak',
                                'data' => ['E115', 'P115'],
                            ],
                            [
                                'coordinate' => 'C116',
                                'component' => 'Beban Marketing dan Promosi',
                                'data' => ['E116', 'P116'],
                            ],
                            [
                                'coordinate' => 'C117',
                                'component' => 'Beban Entertainment',
                                'data' => ['E117', 'P117'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C118',
                        'component' => 'Beban Umum Lain-lain',
                        'child' => [
                            [
                                'coordinate' => 'C119',
                                'component' => 'Bingkisan atau Parcel',
                                'data' => ['E119', 'P119'],
                            ],
                            [
                                'coordinate' => 'C120',
                                'component' => 'Sumbangan',
                                'data' => ['E120', 'P120'],
                            ],
                            [
                                'coordinate' => 'C121',
                                'component' => 'Beban Perizinan',
                                'data' => ['E121', 'P121'],
                            ],
                            [
                                'coordinate' => 'C122',
                                'component' => 'Beban Umum Lainnya',
                                'data' => ['E122', 'P122'],
                            ],
                            [
                                'coordinate' => 'C123',
                                'component' => 'IT Charges',
                                'data' => ['E123', 'P123'],
                            ],
                            [
                                'coordinate' => 'C124',
                                'component' => 'Digital Charge',
                                'data' => ['E124', 'P124'],
                            ],
                            [
                                'coordinate' => 'C125',
                                'component' => 'HR Charges',
                                'data' => ['E125', 'P149'],
                            ],
                            [
                                'coordinate' => 'C126',
                                'component' => 'Coaching & Mentoring',
                                'data' => ['E126', 'P126'],
                            ],
                            [
                                'coordinate' => 'C127',
                                'component' => 'GA Charge',
                                'data' => ['E127', 'P127'],
                            ],
                            [
                                'coordinate' => 'C128',
                                'component' => 'Accounting Charge',
                                'data' => ['E128', 'P128'],
                            ],
                            [
                                'coordinate' => 'C129',
                                'component' => 'Sapbi Charge',
                                'data' => ['E129', 'P129'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C130',
                        'component' => 'Penyusutan dan Amortisasi',
                        'child' => [
                            [
                                'coordinate' => 'C131',
                                'component' => 'Beban Penyusutan Bangunan',
                                'data' => ['E131', 'P131'],
                            ],
                            [
                                'coordinate' => 'C132',
                                'component' => 'Beban Penyusutan Kendaraan',
                                'data' => ['E132', 'P132'],
                            ],
                            [
                                'coordinate' => 'C133',
                                'component' => 'Beban Penyusutan Peralatan Kantor',
                                'data' => ['E133', 'P133'],
                            ],
                            [
                                'coordinate' => 'C134',
                                'component' => 'Beban Penyusutan Perangkat Lunak',
                                'data' => ['E134', 'P134'],
                            ],
                            [
                                'coordinate' => 'C135',
                                'component' => 'Beban Penyusutan Furniture Kantor',
                                'data' => ['E135', 'P135'],
                            ],
                            [
                                'coordinate' => 'C136',
                                'component' => 'Amortisasi Aktiva Tak Berwujud',
                                'data' => ['E136', 'P136'],
                            ],
                        ]
                    ],
                ]
            ],
            [
                'coordinate' => 'C142',
                'component' => 'Pendapatan & (Biaya) Non Operasional',
                'child' => [
                    [
                        'coordinate' => 'C143',
                        'component' => 'Pendapatan Non Operasional',
                        'child' => [
                            [
                                'coordinate' => 'C144',
                                'component' => 'Pendapatan Bunga',
                                'data' => ['E144', 'P144'],
                            ],
                            [
                                'coordinate' => 'C145',
                                'component' => 'Pendapatan Deposit',
                                'data' => ['E145', 'P145'],
                            ],
                            [
                                'coordinate' => 'C146',
                                'component' => 'Keuntungan Selisih Kurs',
                                'data' => ['E146', 'P146'],
                            ],
                            [
                                'coordinate' => 'C147',
                                'component' => 'Pendapatan Lain',
                                'data' => ['E147', 'P147'],
                            ],
                            [
                                'coordinate' => 'C148',
                                'component' => 'Keuntungan Atas Penjualan Aktiva Tetap',
                                'data' => ['E148', 'P148'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C151',
                        'component' => '(Biaya ) Non Operasional ',
                        'child' => [
                            [
                                'coordinate' => 'C152',
                                'component' => 'Beban Bunga',
                                'data' => ['E152', 'P152'],
                            ],
                            [
                                'coordinate' => 'C153',
                                'component' => 'Beban Administrasi Bank',
                                'data' => ['E153', 'P153'],
                            ],
                            [
                                'coordinate' => 'C154',
                                'component' => 'Kerugian Selisih Kurs',
                                'data' => ['E154', 'P154'],
                            ],
                            [
                                'coordinate' => 'C155',
                                'component' => 'Kerugian Atas Penjualan Aktiva Tetap',
                                'data' => ['E155', 'P155'],
                            ],
                            [
                                'coordinate' => 'C156',
                                'component' => 'Beban Bunga Sewa Guna Usaha',
                                'data' => ['E156', 'P156'],
                            ],
                            [
                                'coordinate' => 'C157',
                                'component' => 'Beban Lain-lain',
                                'data' => ['E157', 'P157'],
                            ],
                        ]
                    ],
                ]
            ],
            [
                'coordinate' => 'C165',
                'component' => 'Laba/Rugi sebelum pajak',
                'data' => ['E165', 'P165']
            ],
            [
                'coordinate' => 'C167',
                'component' => 'Pajak',
                'child' => [
                    [
                        'coordinate' => 'C168',
                        'component' => 'Beban Pajak PPh 29',
                        'data' => ['E168', 'P168']
                    ],
                    [
                        'coordinate' => 'C169',
                        'component' => 'Pajak Lainnya',
                        'data' => ['E169', 'P169']
                    ],
                ]
            ],
            [
                'coordinate' => 'C171',
                'component' => 'Laba/Rugi sesudah pajak',
                'data' => ['E171', 'P171']
            ],
        ];
    }
}