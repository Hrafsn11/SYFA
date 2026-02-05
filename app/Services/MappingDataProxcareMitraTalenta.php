<?php 

namespace App\Services;

class MappingDataProxcareMitraTalenta extends MappingRekursiData
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
                        'component' => 'Pendapatan',
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
                'coordinate' => 'C24',
                'component' => 'HPP',
                'child' => [
                    [
                        'coordinate' => 'C25',
                        'component' => 'Mitra Trekking ID',
                        'data' => ['E25', 'P25'],
                    ],
                    [
                        'coordinate' => 'C26',
                        'component' => 'Mitra Proxcare',
                        'data' => ['E26', 'P26'],
                    ],
                    [
                        'coordinate' => 'C27',
                        'component' => 'Mitra Ubar',
                        'data' => ['E27', 'P27'],
                    ],
                    [
                        'coordinate' => 'C28',
                        'component' => 'Mitra Malaka',
                        'data' => ['E28', 'P28'],
                    ],
                    [
                        'coordinate' => 'C29',
                        'component' => 'Mitra Difin',
                        'data' => ['E29', 'P29'],
                    ],
                ]
            ],
            [
                'coordinate' => 'C31',
                'component' => 'Biaya Langsung',
                'child' => [
                    [
                        'coordinate' => 'C32',
                        'component' => 'Tenaga Kerja Langsung',
                        'child' => [
                            [
                                'coordinate' => 'C33',
                                'component' => 'Biaya Tenaga Ahli',
                                'data' => ['E33', 'P33'],
                            ],
                            [
                                'coordinate' => 'C34',
                                'component' => 'Komisi Penjualan',
                                'data' => ['E34', 'P34'],
                            ],
                        ],
                    ],
                    [
                        'coordinate' => 'C36',
                        'component' => 'Biaya Pegawai Operasional',
                        'child' => [
                            [
                                'coordinate' => 'C37',
                                'component' => 'Gaji Pegawai Langsung',
                                'data' => ['E37', 'P37'],
                            ],
                            [
                                'coordinate' => 'C38',
                                'component' => 'Tunjangan Hari Raya Opersional',
                                'data' => ['E38', 'P38'],
                            ],
                            [
                                'coordinate' => 'C39',
                                'component' => 'Project',
                                'data' => ['E39', 'P39'],
                            ],
                        ],
                    ],
                    [
                        'coordinate' => 'C40',
                        'component' => 'Biaya Mitra',
                        'data' => ['E40', 'P40'],
                    ],
                    [
                        'coordinate' => 'C47',
                        'component' => 'Biaya Konsultasi',
                        'child' => [
                            [
                                'coordinate' => 'C48',
                                'component' => 'Kunjungan Konsultan',
                                'data' => ['E48', 'P48'],
                            ],
                            [
                                'coordinate' => 'C49',
                                'component' => 'Meals',
                                'data' => ['E49', 'P49'],
                            ],
                            [
                                'coordinate' => 'C50',
                                'component' => 'Biaya Konsultan Lainya',
                                'data' => ['E50', 'P50'],
                            ],
                            [
                                'coordinate' => 'C51',
                                'component' => 'Biaya Konsultasi ( Vendor )',
                                'data' => ['E51', 'P51'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C52',
                        'component' => 'Biaya Operasional Lain-lain',
                        'child' => [
                            [
                                'coordinate' => 'C53',
                                'component' => 'Biaya Transportasi Operasional',
                                'data' => ['E53', 'P53'],
                            ],
                            [
                                'coordinate' => 'C54',
                                'component' => 'Perawatan Kendaraan',
                                'data' => ['E54', 'P54'],
                            ],
                            [
                                'coordinate' => 'C55',
                                'component' => 'Biaya Administrasi Kendaraan',
                                'data' => ['E55', 'P55'],
                            ],
                            [
                                'coordinate' => 'C56',
                                'component' => 'Biaya Perjalanan Dinas',
                                'data' => ['E56', 'P56'],
                            ],
                            [
                                'coordinate' => 'C57',
                                'component' => 'Biaya Sertifikasi',
                                'data' => ['E57', 'P57'],
                            ],
                            [
                                'coordinate' => 'C58',
                                'component' => 'Perangkat atau Peralatan',
                                'data' => ['E58', 'P58'],
                            ],
                            [
                                'coordinate' => 'C59',
                                'component' => 'Penggantian Internet',
                                'data' => ['E59', 'P59'],
                            ],
                            [
                                'coordinate' => 'C60',
                                'component' => 'Biaya Operasional Lainnya',
                                'data' => ['E60', 'P60'],
                            ],
                            [
                                'coordinate' => 'C61',
                                'component' => 'Biaya Adm Bank Garansi',
                                'data' => ['E61', 'P61'],
                            ],
                            [
                                'coordinate' => 'C62',
                                'component' => 'Biaya Adm (Operasional)',
                                'data' => ['E62', 'P62'],
                            ],
                            [
                                'coordinate' => 'C63',
                                'component' => 'Biaya administrasi Legalitas',
                                'data' => ['E63', 'P63'],
                            ],
                        ]
                    ],
                ]
            ],
            [
                'coordinate' => 'C83',
                'component' => 'Biaya Umum & Administrasi',
                'child' => [
                    [
                        'coordinate' => 'C84',
                        'component' => 'Biaya Pegawai',
                        'child' => [
                            [
                                'coordinate' => 'C85',
                                'component' => 'Gaji Karyawan',
                                'data' => ['E85', 'P85'],
                            ],
                            [
                                'coordinate' => 'C86',
                                'component' => 'Lembur Karyawan',
                                'data' => ['E86', 'P86'],
                            ],
                            [
                                'coordinate' => 'C87',
                                'component' => 'Tunjangan Hari Raya',
                                'data' => ['E87', 'P87'],
                            ],
                            [
                                'coordinate' => 'C88',
                                'component' => 'Tunjangan/Komisi, Bonus atau Fee',
                                'data' => ['E88', 'P88'],
                            ],
                            [
                                'coordinate' => 'C89',
                                'component' => 'Beban Pegawai Magang',
                                'data' => ['E89', 'P89'],
                            ],
                            [
                                'coordinate' => 'C90',
                                'component' => 'Tunjangan Kehadiran',
                                'data' => ['E90', 'P90'],
                            ],
                            [
                                'coordinate' => 'C91',
                                'component' => 'Tunjangan Kesehatan (Medical)',
                                'data' => ['E91', 'P91'],
                            ],
                            [
                                'coordinate' => 'C92',
                                'component' => 'Tunjangan BPJS',
                                'data' => ['E92', 'P92'],
                            ],
                            [
                                'coordinate' => 'C93',
                                'component' => 'Tunjangan PPh Pasal 21',
                                'data' => ['E93', 'P93'],
                            ],
                            [
                                'coordinate' => 'C94',
                                'component' => 'Tunjangan Telekomunikasi',
                                'data' => ['E94', 'P94'],
                            ],
                            [
                                'coordinate' => 'C95',
                                'component' => 'Pesangon Karyawan',
                                'data' => ['E95', 'P95'],
                            ],
                            [
                                'coordinate' => 'C96',
                                'component' => 'Tunjangan Lainnya/Koperasi',
                                'data' => ['E96', 'P96'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C97',
                        'component' => 'Transportasi dan Perjalanan Dinas',
                        'child' => [
                            [
                                'coordinate' => 'C98',
                                'component' => 'Beban Transportasi Umum',
                                'data' => ['E98', 'P98'],
                            ],
                            [
                                'coordinate' => 'C99',
                                'component' => 'Beban Perjalanan Dinas Umum',
                                'data' => ['E99', 'P99'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C101',
                        'component' => 'Biaya Kantor',
                        'child' => [
                            [
                                'coordinate' => 'C102',
                                'component' => 'Beban Peralatan Kantor & Perangkat Kerja',
                                'data' => ['E102', 'P102'],
                            ],
                            [
                                'coordinate' => 'C103',
                                'component' => 'Beban Alat Tulis Kantor',
                                'data' => ['E103', 'P103'],
                            ],
                            [
                                'coordinate' => 'C104',
                                'component' => 'Beban Listrik & PAM',
                                'data' => ['E104', 'P104'],
                            ],
                            [
                                'coordinate' => 'C105',
                                'component' => 'Beban Telepon dan Internet Kantor',
                                'data' => ['E105', 'P105'],
                            ],
                            [
                                'coordinate' => 'C106',
                                'component' => 'Beban Ponsel',
                                'data' => ['E106', 'P106'],
                            ],
                            [
                                'coordinate' => 'C107',
                                'component' => 'Beban Fotocopy dan Cetak',
                                'data' => ['E107', 'P107'],
                            ],
                            [
                                'coordinate' => 'C108',
                                'component' => 'Beban Sewa Kantor',
                                'data' => ['E108', 'P108'],
                            ],
                            [
                                'coordinate' => 'C109',
                                'component' => 'Beban Pengiriman',
                                'data' => ['E109', 'P109'],
                            ],
                            [
                                'coordinate' => 'C110',
                                'component' => 'Beban Materai',
                                'data' => ['E110', 'P110'],
                            ],
                            [
                                'coordinate' => 'C111',
                                'component' => 'Beban Kantor Lainnya',
                                'data' => ['E111', 'P111'],
                            ],
                            [
                                'coordinate' => 'C112',
                                'component' => 'Beban Perlengkapan Kantor',
                                'data' => ['E112', 'P112'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C114',
                        'component' => 'Biaya Pemeliharaan',
                        'child' => [
                            [
                                'coordinate' => 'C115',
                                'component' => 'Beban Pemeliharaan Kendaraan',
                                'data' => ['E115', 'P115'],
                            ],
                            [
                                'coordinate' => 'C116',
                                'component' => 'Beban Adm Kendaraan',
                                'data' => ['E116', 'P116'],
                            ],
                            [
                                'coordinate' => 'C117',
                                'component' => 'Beban Asuransi Aset',
                                'data' => ['E117', 'P117'],
                            ],
                            [
                                'coordinate' => 'C118',
                                'component' => 'Beban Pemeliharaan Peralatan Kantor',
                                'data' => ['E118', 'P118'],
                            ],
                            [
                                'coordinate' => 'C119',
                                'component' => 'Beban Pemeliharaan Kantor',
                                'data' => ['E119', 'P119'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C120',
                        'component' => 'Biaya Rumah Tangga',
                        'child' => [
                            [
                                'coordinate' => 'C121',
                                'component' => 'Beban Rapat',
                                'data' => ['E121', 'P121'],
                            ],
                            [
                                'coordinate' => 'C122',
                                'component' => 'Beban Dekorasi',
                                'data' => ['E122', 'P122'],
                            ],
                            [
                                'coordinate' => 'C123',
                                'component' => 'Beban Pelatihan, Seminar, dan Pendidikan Internal',
                                'data' => ['E123', 'P123'],
                            ],
                            [
                                'coordinate' => 'C124',
                                'component' => 'Beban Pantry',
                                'data' => ['E124', 'P124'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C125',
                        'component' => 'Biaya Promosi',
                        'child' => [
                            [
                                'coordinate' => 'C126',
                                'component' => 'Beban Iklan',
                                'data' => ['E126', 'P126'],
                            ],
                            [
                                'coordinate' => 'C127',
                                'component' => 'Beban Cetak',
                                'data' => ['E127', 'P127'],
                            ],
                            [
                                'coordinate' => 'C128',
                                'component' => 'Beban Marketing dan Promosi',
                                'data' => ['E128', 'P128'],
                            ],
                            [
                                'coordinate' => 'C129',
                                'component' => 'Beban Entertainment',
                                'data' => ['E129', 'P129'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C130',
                        'component' => 'Beban Umum Lain-lain',
                        'child' => [
                            [
                                'coordinate' => 'C131',
                                'component' => 'Bingkisan atau Parcel',
                                'data' => ['E131', 'P131'],
                            ],
                            [
                                'coordinate' => 'C132',
                                'component' => 'Sumbangan',
                                'data' => ['E132', 'P132'],
                            ],
                            [
                                'coordinate' => 'C133',
                                'component' => 'Beban Perizinan',
                                'data' => ['E133', 'P133'],
                            ],
                            [
                                'coordinate' => 'C134',
                                'component' => 'Beban Umum Lainnya',
                                'data' => ['E134', 'P134'],
                            ],
                            [
                                'coordinate' => 'C135',
                                'component' => 'Beban Lain',
                                'data' => ['E135', 'P135'],
                            ],
                            [
                                'coordinate' => 'C137',
                                'component' => 'IT Charges',
                                'data' => ['E137', 'P137'],
                            ],
                            [
                                'coordinate' => 'C138',
                                'component' => 'Digital Charge',
                                'data' => ['E138', 'P138'],
                            ],
                            [
                                'coordinate' => 'C139',
                                'component' => 'HR Charges',
                                'data' => ['E139', 'P139'],
                            ],
                            [
                                'coordinate' => 'C140',
                                'component' => 'Coaching and Mentoring',
                                'data' => ['E140', 'P140'],
                            ],
                            [
                                'coordinate' => 'C141',
                                'component' => 'GA Charge',
                                'data' => ['E141', 'P141'],
                            ],
                            [
                                'coordinate' => 'C142',
                                'component' => 'Accounting Charge',
                                'data' => ['E142', 'P142'],
                            ],
                            [
                                'coordinate' => 'C143',
                                'component' => 'Proxcare Charge',
                                'data' => ['E143', 'P143'],
                            ],
                            [
                                'coordinate' => 'C144',
                                'component' => 'Miminku Charge',
                                'data' => ['E144', 'P144'],
                            ],
                            [
                                'coordinate' => 'C145',
                                'component' => 'Sapbi Charge',
                                'data' => ['E145', 'P145'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C146',
                        'component' => 'Penyusutan dan Amortisasi',
                        'child' => [
                            [
                                'coordinate' => 'C147',
                                'component' => 'Beban Penyusutan Bangunan',
                                'data' => ['E147', 'P147'],
                            ],
                            [
                                'coordinate' => 'C148',
                                'component' => 'Beban Penyusutan Kendaraan',
                                'data' => ['E148', 'P148'],
                            ],
                            [
                                'coordinate' => 'C149',
                                'component' => 'Beban Penyusutan Peralatan Kantor',
                                'data' => ['E149', 'P149'],
                            ],
                            [
                                'coordinate' => 'C150',
                                'component' => 'Beban Penyusutan Perangkat Lunak',
                                'data' => ['E150', 'P150'],
                            ],
                            [
                                'coordinate' => 'C151',
                                'component' => 'Beban Penyusutan Furniture Kantor',
                                'data' => ['E151', 'P151'],
                            ],
                            [
                                'coordinate' => 'C152',
                                'component' => 'Amortisasi Aktiva Tak Berwujud',
                                'data' => ['E152', 'P152'],
                            ],
                        ]
                    ],
                ]
            ],
            [
                'coordinate' => 'C158',
                'component' => 'Pendapatan & (Biaya) Non Operasional',
                'child' => [
                    [
                        'coordinate' => 'C159',
                        'component' => 'Pendapatan Non Operasional',
                        'child' => [
                            [
                                'coordinate' => 'C160',
                                'component' => 'Pendapatan Bunga',
                                'data' => ['E160', 'P160'],
                            ],
                            [
                                'coordinate' => 'C161',
                                'component' => 'Pendapatan Deposit',
                                'data' => ['E161', 'P161'],
                            ],
                            [
                                'coordinate' => 'C162',
                                'component' => 'Keuntungan Selisih Kurs',
                                'data' => ['E162', 'P162'],
                            ],
                            [
                                'coordinate' => 'C163',
                                'component' => 'Pendapatan Lain',
                                'data' => ['E163', 'P163'],
                            ],
                            [
                                'coordinate' => 'C164',
                                'component' => 'Keuntungan Atas Penjualan Aktiva Tetap',
                                'data' => ['E164', 'P164'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C167',
                        'component' => '(Biaya ) Non Operasional',
                        'child' => [
                            [
                                'coordinate' => 'C168',
                                'component' => 'Beban Bunga',
                                'data' => ['E168', 'P168'],
                            ],
                            [
                                'coordinate' => 'C169',
                                'component' => 'Beban Administrasi Bank',
                                'data' => ['E169', 'P169'],
                            ],
                            [
                                'coordinate' => 'C170',
                                'component' => 'Kerugian Selisih Kurs',
                                'data' => ['E170', 'P170'],
                            ],
                            [
                                'coordinate' => 'C171',
                                'component' => 'Kerugian Atas Penjualan Aktiva Tetap',
                                'data' => ['E171', 'P171'],
                            ],
                            [
                                'coordinate' => 'C172',
                                'component' => 'Beban Bunga Sewa Guna Usaha',
                                'data' => ['E172', 'P172'],
                            ],
                            [
                                'coordinate' => 'C173',
                                'component' => 'Beban Lain-lain',
                                'data' => ['E173', 'P173'],
                            ],
                        ]
                    ],
                ]
            ],
            [
                'coordinate' => 'C181',
                'component' => 'Laba/Rugi sebelum pajak',
                'data' => ['E181', 'P181']
            ],
            [
                'coordinate' => 'C183',
                'component' => 'Pajak',
                'child' => [
                    [
                        'coordinate' => 'C184',
                        'component' => 'Beban Pajak PPh 29',
                        'data' => ['E184', 'P184']
                    ],
                    [
                        'coordinate' => 'C185',
                        'component' => 'Pajak Lainnya',
                        'data' => ['E185', 'P185']
                    ],
                ]
            ],
            [
                'coordinate' => 'C187',
                'component' => 'Laba/Rugi sesudah pajak',
                'data' => ['E187', 'P187']
            ],
        ];
    }
}