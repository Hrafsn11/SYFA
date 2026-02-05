<?php 

namespace App\Services;

class MappingDataPPS extends MappingRekursiData
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
                            [
                                'coordinate' => 'C34',
                                'component' => 'Gaji Pegawai Langsung',
                                'data' => ['E34', 'P34'],
                            ],
                            [
                                'coordinate' => 'C35',
                                'component' => 'Tunjangan Pegawai',
                                'data' => ['E35', 'P35'],
                            ],
                            [
                                'coordinate' => 'C36',
                                'component' => 'Project',
                                'data' => ['E36', 'P36'],
                            ],
                            [
                                'coordinate' => 'C37',
                                'component' => 'Gaji Karyawan Operasional',
                                'data' => ['E37', 'P37'],
                            ],
                            [
                                'coordinate' => 'C38',
                                'component' => 'Gaji Karyawan Sales & Marketing',
                                'data' => ['E38', 'P38'],
                            ],
                            [
                                'coordinate' => 'C39',
                                'component' => 'Lembur Karyawan Operasional',
                                'data' => ['E39', 'P39'],
                            ],
                            [
                                'coordinate' => 'C40',
                                'component' => 'Tunjangan Hari Raya Operasional',
                                'data' => ['E40', 'P40'],
                            ],
                            [
                                'coordinate' => 'C41',
                                'component' => 'Tunjangan Lainnya/Koperasi Operasional',
                                'data' => ['E41', 'P41'],
                            ],
                            [
                                'coordinate' => 'C42',
                                'component' => 'Tunjangan Lainnya/Koperasi Sales & Marketing',
                                'data' => ['E42', 'P42'],
                            ],
                        ],
                    ],
                    [
                        'coordinate' => 'C43',
                        'component' => 'Biaya Mitra',
                        'data' => ['E43', 'P43'],
                    ],
                    [
                        'coordinate' => 'C50',
                        'component' => 'Biaya Konsultasi',
                        'child' => [
                            [
                                'coordinate' => 'C51',
                                'component' => 'Kunjungan Konsultan',
                                'data' => ['E51', 'P51'],
                            ],
                            [
                                'coordinate' => 'C52',
                                'component' => 'Meals',
                                'data' => ['E52', 'P52'],
                            ],
                            [
                                'coordinate' => 'C53',
                                'component' => 'Biaya Konsultan Lainya',
                                'data' => ['E53', 'P53'],
                            ],
                            [
                                'coordinate' => 'C54',
                                'component' => 'Biaya Konsultasi ( Vendor )',
                                'data' => ['E54', 'P54'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C55',
                        'component' => 'Biaya Operasional Lain -lain',
                        'child' => [
                            [
                                'coordinate' => 'C56',
                                'component' => 'Biaya Transportasi Operasional',
                                'data' => ['E56', 'P56'],
                            ],
                            [
                                'coordinate' => 'C57',
                                'component' => 'Perawatan Kendaraan',
                                'data' => ['E57', 'P57'],
                            ],
                            [
                                'coordinate' => 'C58',
                                'component' => 'Biaya Administrasi Kendaraan',
                                'data' => ['E58', 'P58'],
                            ],
                            [
                                'coordinate' => 'C59',
                                'component' => 'Biaya Perjalanan Dinas',
                                'data' => ['E59', 'P59'],
                            ],
                            [
                                'coordinate' => 'C60',
                                'component' => 'Biaya Sertifikasi',
                                'data' => ['E60', 'P60'],
                            ],
                            [
                                'coordinate' => 'C61',
                                'component' => 'Perangkat atau Peralatan',
                                'data' => ['E61', 'P61'],
                            ],
                            [
                                'coordinate' => 'C62',
                                'component' => 'Penggantian Internet',
                                'data' => ['E62', 'P62'],
                            ],
                            [
                                'coordinate' => 'C63',
                                'component' => 'Biaya Operasional Lainnya',
                                'data' => ['E63', 'P63'],
                            ],
                            [
                                'coordinate' => 'C64',
                                'component' => 'Biaya Adm Bank Garansi',
                                'data' => ['E64', 'P64'],
                            ],
                            [
                                'coordinate' => 'C65',
                                'component' => 'Biaya Adm (Operasional)',
                                'data' => ['E65', 'P65'],
                            ],
                            [
                                'coordinate' => 'C66',
                                'component' => 'Biaya administrasi Legalitas',
                                'data' => ['E66', 'P66'],
                            ],
                        ]
                    ],
                ]
            ],
            [
                'coordinate' => 'C73',
                'component' => 'Biaya Umum & Administrasi',
                'child' => [
                    [
                        'coordinate' => 'C74',
                        'component' => 'Biaya Pegawai',
                        'child' => [
                            [
                                'coordinate' => 'C75',
                                'component' => 'Gaji Karyawan',
                                'data' => ['E75', 'P75'],
                            ],
                            [
                                'coordinate' => 'C76',
                                'component' => 'Lembur Karyawan',
                                'data' => ['E76', 'P76'],
                            ],
                            [
                                'coordinate' => 'C77',
                                'component' => 'Tunjangan Hari Raya',
                                'data' => ['E77', 'P77'],
                            ],
                            [
                                'coordinate' => 'C78',
                                'component' => 'Tunjangan/Komisi, Bonus atau Fee',
                                'data' => ['E78', 'P78'],
                            ],
                            [
                                'coordinate' => 'C79',
                                'component' => 'Beban Pegawai Magang',
                                'data' => ['E79', 'P79'],
                            ],
                            [
                                'coordinate' => 'C80',
                                'component' => 'Tunjangan Kehadiran',
                                'data' => ['E80', 'P80'],
                            ],
                            [
                                'coordinate' => 'C81',
                                'component' => 'Tunjangan Kesehatan (Medical)',
                                'data' => ['E81', 'P81'],
                            ],
                            [
                                'coordinate' => 'C82',
                                'component' => 'Tunjangan BPJS',
                                'data' => ['E82', 'P82'],
                            ],
                            [
                                'coordinate' => 'C83',
                                'component' => 'Tunjangan PPh Pasal 21',
                                'data' => ['E83', 'P83'],
                            ],
                            [
                                'coordinate' => 'C84',
                                'component' => 'Tunjangan Pensiun',
                                'data' => ['E84', 'P84'],
                            ],
                            [
                                'coordinate' => 'C85',
                                'component' => 'Pesangon Karyawan',
                                'data' => ['E85', 'P85'],
                            ],
                            [
                                'coordinate' => 'C86',
                                'component' => 'Tunjangan Telekomunikasi',
                                'data' => ['E86', 'P86'],
                            ],
                            [
                                'coordinate' => 'C87',
                                'component' => 'Tunjangan Lainnya/Koperasi ',
                                'data' => ['E87', 'P87'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C89',
                        'component' => 'Transportasi dan Perjalanan Dinas',
                        'child' => [
                            [
                                'coordinate' => 'C90',
                                'component' => 'Beban Transportasi Umum',
                                'data' => ['E90', 'P90'],
                            ],
                            [
                                'coordinate' => 'C91',
                                'component' => 'Beban Perjalanan Dinas Umum',
                                'data' => ['E91', 'P91'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C93',
                        'component' => 'Biaya Kantor',
                        'child' => [
                            [
                                'coordinate' => 'C94',
                                'component' => 'Beban Peralatan Kantor & Perangkat Kerja',
                                'data' => ['E94', 'P94'],
                            ],
                            [
                                'coordinate' => 'C95',
                                'component' => 'Beban Alat Tulis Kantor',
                                'data' => ['E95', 'P95'],
                            ],
                            [
                                'coordinate' => 'C96',
                                'component' => 'Beban Listrik & PAM',
                                'data' => ['E96', 'P96'],
                            ],
                            [
                                'coordinate' => 'C97',
                                'component' => 'Beban Telepon dan Internet Kantor',
                                'data' => ['E97', 'P97'],
                            ],
                            [
                                'coordinate' => 'C98',
                                'component' => 'Beban Ponsel',
                                'data' => ['E98', 'P98'],
                            ],
                            [
                                'coordinate' => 'C99',
                                'component' => 'Beban Fotocopy dan Cetak',
                                'data' => ['E99', 'P99'],
                            ],
                            [
                                'coordinate' => 'C100',
                                'component' => 'Beban Sewa Kantor',
                                'data' => ['E100', 'P100'],
                            ],
                            [
                                'coordinate' => 'C101',
                                'component' => 'Beban Pengiriman',
                                'data' => ['E101', 'P101'],
                            ],
                            [
                                'coordinate' => 'C102',
                                'component' => 'Beban Materai',
                                'data' => ['E102', 'P102'],
                            ],
                            [
                                'coordinate' => 'C103',
                                'component' => 'Beban Kantor Lainnya',
                                'data' => ['E103', 'P103'],
                            ],
                            [
                                'coordinate' => 'C104',
                                'component' => 'Beban Perlengkapan Kantor',
                                'data' => ['E104', 'P104'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C106',
                        'component' => 'Biaya Pemeliharaan',
                        'child' => [
                            [
                                'coordinate' => 'C107',
                                'component' => 'Beban Pemeliharaan Kendaraan',
                                'data' => ['E107', 'P107'],
                            ],
                            [
                                'coordinate' => 'C108',
                                'component' => 'Beban Adm Kendaraan',
                                'data' => ['E108', 'P108'],
                            ],
                            [
                                'coordinate' => 'C109',
                                'component' => 'Beban Asuransi Aset',
                                'data' => ['E109', 'P109'],
                            ],
                            [
                                'coordinate' => 'C110',
                                'component' => 'Beban Pemeliharaan Peralatan Kantor',
                                'data' => ['E110', 'P110'],
                            ],
                            [
                                'coordinate' => 'C111',
                                'component' => 'Beban Pemeliharaan Kantor',
                                'data' => ['E111', 'P111'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C112',
                        'component' => 'Biaya Rumah Tangga',
                        'child' => [
                            [
                                'coordinate' => 'C113',
                                'component' => 'Beban Rapat',
                                'data' => ['E113', 'P113'],
                            ],
                            [
                                'coordinate' => 'C114',
                                'component' => 'Beban Dekorasi',
                                'data' => ['E114', 'P114'],
                            ],
                            [
                                'coordinate' => 'C115',
                                'component' => 'Beban Pelatihan, Seminar, dan Pendidikan Internal',
                                'data' => ['E115', 'P115'],
                            ],
                            [
                                'coordinate' => 'C116',
                                'component' => 'Beban Pantry',
                                'data' => ['E116', 'P116'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C117',
                        'component' => 'Biaya Promosi',
                        'child' => [
                            [
                                'coordinate' => 'C118',
                                'component' => 'Beban Iklan',
                                'data' => ['E118', 'P118'],
                            ],
                            [
                                'coordinate' => 'C119',
                                'component' => 'Beban Cetak',
                                'data' => ['E119', 'P119'],
                            ],
                            [
                                'coordinate' => 'C120',
                                'component' => 'Beban Marketing dan Promosi',
                                'data' => ['E120', 'P120'],
                            ],
                            [
                                'coordinate' => 'C121',
                                'component' => 'Beban Entertainment',
                                'data' => ['E121', 'P121'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C122',
                        'component' => 'Beban Umum Lain-lain',
                        'child' => [
                            [
                                'coordinate' => 'C123',
                                'component' => 'Bingkisan atau Parcel',
                                'data' => ['E123', 'P123'],
                            ],
                            [
                                'coordinate' => 'C124',
                                'component' => 'Sumbangan',
                                'data' => ['E124', 'P124'],
                            ],
                            [
                                'coordinate' => 'C125',
                                'component' => 'Beban Perizinan',
                                'data' => ['E125', 'P125'],
                            ],
                            [
                                'coordinate' => 'C126',
                                'component' => 'Beban Umum Lainnya',
                                'data' => ['E126', 'P126'],
                            ],
                            [
                                'coordinate' => 'C128',
                                'component' => 'IT Charges',
                                'data' => ['E128', 'P128'],
                            ],
                            [
                                'coordinate' => 'C129',
                                'component' => 'Digital Charge',
                                'data' => ['E129', 'P129'],
                            ],
                            [
                                'coordinate' => 'C130',
                                'component' => 'HR Charges',
                                'data' => ['E130', 'P130'],
                            ],
                            [
                                'coordinate' => 'C131',
                                'component' => 'GA Charge',
                                'data' => ['E131', 'P131'],
                            ],
                            [
                                'coordinate' => 'C132',
                                'component' => 'Accounting Charge',
                                'data' => ['E132', 'P132'],
                            ],
                            [
                                'coordinate' => 'C133',
                                'component' => 'Coaching & Mentoring',
                                'data' => ['E133', 'P133'],
                            ],
                            [
                                'coordinate' => 'C134',
                                'component' => 'Malaka Charge',
                                'data' => ['E134', 'P134'],
                            ],
                            [
                                'coordinate' => 'C135',
                                'component' => 'Sapbi Charge',
                                'data' => ['E135', 'P135'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C136',
                        'component' => 'Penyusutan dan Amortisasi',
                        'child' => [
                            [
                                'coordinate' => 'C137',
                                'component' => 'Beban Penyusutan Bangunan',
                                'data' => ['E137', 'P137'],
                            ],
                            [
                                'coordinate' => 'C138',
                                'component' => 'Beban Penyusutan Kendaraan',
                                'data' => ['E138', 'P138'],
                            ],
                            [
                                'coordinate' => 'C139',
                                'component' => 'Beban Penyusutan Peralatan Kantor',
                                'data' => ['E139', 'P139'],
                            ],
                            [
                                'coordinate' => 'C140',
                                'component' => 'Beban Penyusutan Perangkat Lunak',
                                'data' => ['E140', 'P140'],
                            ],
                            [
                                'coordinate' => 'C141',
                                'component' => 'Beban Penyusutan Furniture Kantor',
                                'data' => ['E141', 'P141'],
                            ],
                            [
                                'coordinate' => 'C142',
                                'component' => 'Beban Penyusutan Interior dan Renovasi',
                                'data' => ['E142', 'P142'],
                            ],
                        ]
                    ],
                ]
            ],
            [
                'coordinate' => 'C148',
                'component' => 'Pendapatan & (Biaya) Non Operasional',
                'child' => [
                    [
                        'coordinate' => 'C149',
                        'component' => 'Pendapatan Non Operasional',
                        'child' => [
                            [
                                'coordinate' => 'C150',
                                'component' => 'Pendapatan Bunga',
                                'data' => ['E150', 'P150'],
                            ],
                            [
                                'coordinate' => 'C151',
                                'component' => 'Pendapatan Deposito',
                                'data' => ['E151', 'P151'],
                            ],
                            [
                                'coordinate' => 'C152',
                                'component' => 'Keuntungan Selisih Kurs',
                                'data' => ['E152', 'P152'],
                            ],
                            [
                                'coordinate' => 'C153',
                                'component' => 'Pendapatan Lain',
                                'data' => ['E153', 'P153'],
                            ],
                            [
                                'coordinate' => 'C154',
                                'component' => 'Keuntungan Atas Penjualan Aktiva Tetap',
                                'data' => ['E154', 'P154'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C157',
                        'component' => '(Biaya ) Non Operasional',
                        'child' => [
                            [
                                'coordinate' => 'C158',
                                'component' => 'Beban Bunga',
                                'data' => ['E158', 'P158'],
                            ],
                            [
                                'coordinate' => 'C159',
                                'component' => 'Beban Administrasi Bank',
                                'data' => ['E159', 'P159'],
                            ],
                            [
                                'coordinate' => 'C160',
                                'component' => 'Kerugian Selisih Kurs',
                                'data' => ['E160', 'P160'],
                            ],
                            [
                                'coordinate' => 'C161',
                                'component' => 'Kerugian Atas Penjualan Aktiva Tetap',
                                'data' => ['E161', 'P161'],
                            ],
                            [
                                'coordinate' => 'C162',
                                'component' => 'Beban Bunga Sewa Guna Usaha',
                                'data' => ['E162', 'P162'],
                            ],
                            [
                                'coordinate' => 'C163',
                                'component' => 'Beban Lain-lain',
                                'data' => ['E163', 'P163'],
                            ],
                        ]
                    ],
                ]
            ],
            [
                'coordinate' => 'C171',
                'component' => 'Laba/Rugi sebelum pajak',
                'data' => ['E171', 'P171']
            ],
            [
                'coordinate' => 'C173',
                'component' => 'Pajak',
                'child' => [
                    [
                        'coordinate' => 'C174',
                        'component' => 'Pajak Penghasilan Badan',
                        'data' => ['E174', 'P174']  
                    ],
                    [
                        'coordinate' => 'C175',
                        'component' => 'Pajak Lainnya',
                        'data' => ['E175', 'P175']  
                    ],
                ]
            ],
            [
                'coordinate' => 'C177',
                'component' => 'Laba/Rugi sesudah pajak  [ E - F ]',
                'data' => ['E177', 'P177']
            ],
        ];
    }
}