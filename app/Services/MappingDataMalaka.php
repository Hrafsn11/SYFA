<?php 

namespace App\Services;

use Illuminate\Support\Str;

class MappingDataMalaka extends MappingRekursiData
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
                    [
                        'coordinate' => 'C51',
                        'component' => 'Biaya Pegawai Operasional',
                        'child' => [
                            [
                                'coordinate' => 'C52',
                                'component' => 'Gaji Karyawan Operasional',
                                'data' => ['E52', 'P52']
                            ],
                            [
                                'coordinate' => 'C53',
                                'component' => 'Lembur Karyawan Operasional',
                                'data' => ['E53', 'P53']
                            ],
                            [
                                'coordinate' => 'C54',
                                'component' => 'Tunjangan Hari Raya Operasional',
                                'data' => ['E54', 'P54']
                            ],
                            [
                                'coordinate' => 'C55',
                                'component' => 'Tunjangan/ Komisi, Bonus atau Fee Operasional',
                                'data' => ['E55', 'P55']
                            ],
                            [
                                'coordinate' => 'C56',
                                'component' => 'Beban Pegawai Magang Operasional',
                                'data' => ['E56', 'P56']
                            ],
                            [
                                'coordinate' => 'C57',
                                'component' => 'Tunjangan Kehadiran Operasional',
                                'data' => ['E57', 'P57']
                            ],
                            [
                                'coordinate' => 'C58',
                                'component' => 'Tunjangan Kesehatan (Medical) Operasional',
                                'data' => ['E58', 'P58']
                            ],
                            [
                                'coordinate' => 'C59',
                                'component' => 'Tunjangan BPJS Operasional',
                                'data' => ['E59', 'P59']
                            ],
                            [
                                'coordinate' => 'C60',
                                'component' => 'Tunjangan PPH Pasal 21 Operasional',
                                'data' => ['E60', 'P60']
                            ],
                            [
                                'coordinate' => 'C61',
                                'component' => 'Tunjangan Pensiun Operasional',
                                'data' => ['E61', 'P61']
                            ],
                            [
                                'coordinate' => 'C62',
                                'component' => 'Tunjangan Telekomunikasi Operasional',
                                'data' => ['E62', 'P62']
                            ],
                            [
                                'coordinate' => 'C63',
                                'component' => 'Tunjangan Lainnya/Tunjangan Koperasi Operasional',
                                'data' => ['E63', 'P63']
                            ],
                        ],
                    ],
                    [
                        'coordinate' => 'C64',
                        'component' => 'Biaya Pegawai Sales & Marketing',
                        'child' => [
                            [
                                'coordinate' => 'C65',
                                'component' => 'Gaji Karyawan Sales & Marketing',
                                'data' => ['E65', 'P65']
                            ],
                            [
                                'coordinate' => 'C66',
                                'component' => 'Lembur Karyawan Sales & Marketing',
                                'data' => ['E66', 'P66']
                            ],
                            [
                                'coordinate' => 'C67',
                                'component' => 'Tunjangan Hari Raya Sales & Marketing',
                                'data' => ['E67', 'P67']
                            ],
                            [
                                'coordinate' => 'C68',
                                'component' => 'Tunjangan/ Komisi, Bonus atau Fee Sales & Marketing',
                                'data' => ['E68', 'P68']
                            ],
                            [
                                'coordinate' => 'C69',
                                'component' => 'Beban Pegawai Magang Sales & Marketing',
                                'data' => ['E69', 'P69']
                            ],
                            [
                                'coordinate' => 'C70',
                                'component' => 'Tunjangan Kehadiran Sales & Marketing',
                                'data' => ['E70', 'P70']
                            ],
                            [
                                'coordinate' => 'C71',
                                'component' => 'Tunjangan Kesehatan (Medical) Sales & Marketing',
                                'data' => ['E71', 'P71']
                            ],
                            [
                                'coordinate' => 'C72',
                                'component' => 'Tunjangan BPJS Sales & Marketing',
                                'data' => ['E72', 'P72']
                            ],
                            [
                                'coordinate' => 'C73',
                                'component' => 'Tunjangan PPH Pasal 21 Sales & Marketing',
                                'data' => ['E73', 'P73']
                            ],
                            [
                                'coordinate' => 'C74',
                                'component' => 'Tunjangan Pensiun Sales & Marketing',
                                'data' => ['E74', 'P74']
                            ],
                            [
                                'coordinate' => 'C75',
                                'component' => 'Tunjangan Telekomunikasi Sales & Marketing',
                                'data' => ['E75', 'P75']
                            ],
                            [
                                'coordinate' => 'C76',
                                'component' => 'Tunjangan Lainnya/Tunjangan Koperasi Sales & Marketing',
                                'data' => ['E76', 'P76']
                            ],
                        ],
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
                                'data' => ['E85', 'P85']
                            ],
                            [
                                'coordinate' => 'C86',
                                'component' => 'Lembur Karyawan',
                                'data' => ['E86', 'P86']
                            ],
                            [
                                'coordinate' => 'C87',
                                'component' => 'Tunjangan Hari Raya',
                                'data' => ['E87', 'P87']
                            ],
                            [
                                'coordinate' => 'C88',
                                'component' => 'Tunjangan/Komisi, Bonus atau Fee',
                                'data' => ['E88', 'P88']
                            ],
                            [
                                'coordinate' => 'C89',
                                'component' => 'Beban Pegawai Magang',
                                'data' => ['E89', 'P89']
                            ],
                            [
                                'coordinate' => 'C90',
                                'component' => 'Tunjangan Kehadiran',
                                'data' => ['E90', 'P90']
                            ],
                            [
                                'coordinate' => 'C91',
                                'component' => 'Tunjangan Kesehatan (Medical)',
                                'data' => ['E91', 'P91']
                            ],
                            [
                                'coordinate' => 'C92',
                                'component' => 'Tunjangan BPJS',
                                'data' => ['E92', 'P92']
                            ],
                            [
                                'coordinate' => 'C93',
                                'component' => 'Tunjangan PPh Pasal 21',
                                'data' => ['E93', 'P93']
                            ],
                            [
                                'coordinate' => 'C94',
                                'component' => 'Tunjangan Pensiun',
                                'data' => ['E94', 'P94']
                            ],
                            [
                                'coordinate' => 'C95',
                                'component' => 'Pesangon Karyawan',
                                'data' => ['E95', 'P95']
                            ],
                            [
                                'coordinate' => 'C96',
                                'component' => 'Tunjangan Telekomunikasi',
                                'data' => ['E96', 'P96']
                            ],
                            [
                                'coordinate' => 'C97',
                                'component' => 'Tunjangan Lainnya/Tunjangan Koperasi Manajemen',
                                'data' => ['E97', 'P97']
                            ],
                            [
                                'coordinate' => 'C98',
                                'component' => 'Tunjangan Lainnya/Tunjangan Koperasi Supporting',
                                'data' => ['E98', 'P98']
                            ]
                        ]
                    ],
                    [
                        'coordinate' => 'C99',
                        'component' => 'Transportasi dan Perjalanan Dinas',
                        'child' => [
                            [
                                'coordinate' => 'C100',
                                'component' => 'Beban Transportasi Umum',
                                'data' => ['E100', 'P100']
                            ],
                            [
                                'coordinate' => 'C101',
                                'component' => 'Beban Perjalanan Dinas Umum',
                                'data' => ['E101', 'P101']
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C103',
                        'component' => 'Biaya Kantor',
                        'child' => [
                            [
                                'coordinate' => 'C104',
                                'component' => 'Beban Peralatan Kantor & Perangkat Kerja',
                                'data' => ['E104', 'P104']
                            ],
                            [
                                'coordinate' => 'C105',
                                'component' => 'Beban Alat Tulis Kantor',
                                'data' => ['E105', 'P105']
                            ],
                            [
                                'coordinate' => 'C106',
                                'component' => 'Beban Listrik & PAM',
                                'data' => ['E106', 'P106']
                            ],
                            [
                                'coordinate' => 'C107',
                                'component' => 'Beban Telepon dan Internet Kantor',
                                'data' => ['E107', 'P107']
                            ],
                            [
                                'coordinate' => 'C108',
                                'component' => 'Beban Ponsel',
                                'data' => ['E108', 'P108']
                            ],
                            [
                                'coordinate' => 'C109',
                                'component' => 'Beban Fotocopy dan Cetak',
                                'data' => ['E109', 'P109']
                            ],
                            [
                                'coordinate' => 'C110',
                                'component' => 'Beban Sewa Kantor',
                                'data' => ['E110', 'P110']
                            ],
                            [
                                'coordinate' => 'C111',
                                'component' => 'Beban Pengiriman',
                                'data' => ['E111', 'P111']
                            ],
                            [
                                'coordinate' => 'C112',
                                'component' => 'Beban Materai',
                                'data' => ['E112', 'P112']
                            ],
                            [
                                'coordinate' => 'C113',
                                'component' => 'Beban Kantor Lainnya',
                                'data' => ['E113', 'P113']
                            ],
                            [
                                'coordinate' => 'C114',
                                'component' => 'Beban Perlengkapan Kantor',
                                'data' => ['E114', 'P114']
                            ],
                        ],
                    ],
                    [
                        'coordinate' => 'C116',
                        'component' => 'Biaya Pemeliharaan',
                        'child' => [
                            [
                                'coordinate' => 'C117',
                                'component' => 'Beban Pemeliharaan Kendaraan',
                                'data' => ['E117', 'P117']
                            ],
                            [
                                'coordinate' => 'C118',
                                'component' => 'Beban Adm Kendaraan',
                                'data' => ['E118', 'P118']
                            ],
                            [
                                'coordinate' => 'C119',
                                'component' => 'Beban Asuransi Aset',
                                'data' => ['E119', 'P119']
                            ],
                            [
                                'coordinate' => 'C120',
                                'component' => 'Beban Pemeliharaan Peralatan Kantor',
                                'data' => ['E120', 'P120']
                            ],
                            [
                                'coordinate' => 'C121',
                                'component' => 'Beban Pemeliharaan Kantor',
                                'data' => ['E121', 'P121']
                            ]
                        ],
                    ],
                    [
                        'coordinate' => 'C122',
                        'component' => 'Biaya Rumah Tangga',
                        'child' => [
                            [
                                'coordinate' => 'C123',
                                'component' => 'Beban Rapat',
                                'data' => ['E123', 'P123']
                            ],
                            [
                                'coordinate' => 'C124',
                                'component' => 'Beban Dekorasi',
                                'data' => ['E124', 'P124']
                            ],
                            [
                                'coordinate' => 'C125',
                                'component' => 'Beban Pelatihan, Seminar, dan Pendidikan Internal',
                                'data' => ['E125', 'P125']
                            ],
                            [
                                'coordinate' => 'C126',
                                'component' => 'Beban Pantry',
                                'data' => ['E126', 'P126']
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C127',
                        'component' => 'Biaya Promosi',
                        'child' => [
                            [
                                'coordinate' => 'C128',
                                'component' => 'Beban Iklan',
                                'data' => ['E128', 'P128']
                            ],
                            [
                                'coordinate' => 'C129',
                                'component' => 'Beban Cetak',
                                'data' => ['E129', 'P129']
                            ],
                            [
                                'coordinate' => 'C130',
                                'component' => 'Beban Marketing dan Promosi',
                                'data' => ['E130', 'P130']
                            ],
                            [
                                'coordinate' => 'C131',
                                'component' => 'Beban Entertainment',
                                'data' => ['E131', 'P131']
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C132',
                        'component' => 'Beban Umum Lain-lain',
                        'child' => [
                            [
                                'coordinate' => 'C133',
                                'component' => 'Bingkisan atau Parcel',
                                'data' => ['E133', 'P133']
                            ],
                            [
                                'coordinate' => 'C134',
                                'component' => 'Sumbangan',
                                'data' => ['E134', 'P134']
                            ],
                            [
                                'coordinate' => 'C135',
                                'component' => 'Beban Perizinan',
                                'data' => ['E135', 'P135']
                            ],
                            [
                                'coordinate' => 'C136',
                                'component' => 'Beban Umum Lainnya',
                                'data' => ['E136', 'P136']
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C137',
                        'component' => 'Internal Charges',
                        'child' => [
                            [
                                'coordinate' => 'C138',
                                'component' => 'IT Charges',
                                'data' => ['E138', 'P138']
                            ],
                            [
                                'coordinate' => 'C139',
                                'component' => 'Digital Charge',
                                'data' => ['E139', 'P139']
                            ],
                            [
                                'coordinate' => 'C140',
                                'component' => 'HR Charges',
                                'data' => ['E140', 'P140']
                            ],
                            [
                                'coordinate' => 'C141',
                                'component' => 'Coaching & Mentoring',
                                'data' => ['E141', 'P141']
                            ],
                            [
                                'coordinate' => 'C142',
                                'component' => 'GA Charge',
                                'data' => ['E142', 'P142']
                            ],
                            [
                                'coordinate' => 'C143',
                                'component' => 'Accounting Charge',
                                'data' => ['E143', 'P143']
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C145',
                        'component' => 'Penyusutan dan Amortisasi',
                        'child' => [
                            [
                                'coordinate' => 'C146',
                                'component' => 'Penyusutan Bangunan',
                                'data' => ['E146', 'P146']
                            ],
                            [
                                'coordinate' => 'C147',
                                'component' => 'Penyusutan Interior dan Renovasi',
                                'data' => ['E147', 'P147']
                            ],
                            [
                                'coordinate' => 'C148',
                                'component' => 'Penyusutan Furniture',
                                'data' => ['E148', 'P148']
                            ],
                            [
                                'coordinate' => 'C149',
                                'component' => 'Penyusutan Peralatan Kantor',
                                'data' => ['E149', 'P149']
                            ],
                            [
                                'coordinate' => 'C150',
                                'component' => 'Penyusutan Kendaraan',
                                'data' => ['E150', 'P150']
                            ],
                            [
                                'coordinate' => 'C151',
                                'component' => 'Amortisasi Aktiva Tak Berwujud',
                                'data' => ['E151', 'P151']
                            ],
                        ]
                    ],
                ]
            ],
            [
                'coordinate' => 'C157',
                'component' => 'Pendapatan & (Biaya) Non Operasional',
                'child' => [
                    [
                        'coordinate' => 'C158',
                        'component' => 'Pendapatan Non Operasional',
                        'child' => [
                            [
                                'coordinate' => 'C159',
                                'component' => 'Pendapatan Bunga',
                                'data' => ['E159', 'P159']
                            ],
                            [
                                'coordinate' => 'C160',
                                'component' => 'Pendapatan Deposit',
                                'data' => ['E160', 'P160']
                            ],
                            [
                                'coordinate' => 'C161',
                                'component' => 'Keuntungan Selisih Kurs',
                                'data' => ['E161', 'P161']
                            ],
                            [
                                'coordinate' => 'C162',
                                'component' => 'Pendapatan Lain',
                                'data' => ['E162', 'P162']
                            ],
                            [
                                'coordinate' => 'C163',
                                'component' => 'Keuntungan Atas Penjualan Aktiva Tetap',
                                'data' => ['E163', 'P163']
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C166',
                        'component' => '(Biaya ) Non Operasional',
                        'child' => [
                            [
                                'coordinate' => 'C167',
                                'component' => 'Beban Bunga',
                                'data' => ['E167', 'P167']
                            ],
                            [
                                'coordinate' => 'C168',
                                'component' => 'Beban Administrasi Bank ',
                                'data' => ['E168', 'P168']
                            ],
                            [
                                'coordinate' => 'C169',
                                'component' => 'Kerugian Selisih Kurs ',
                                'data' => ['E169', 'P169']
                            ],
                            [
                                'coordinate' => 'C170',
                                'component' => 'Kerugian Atas Penjualan Aktivas Tetap',
                                'data' => ['E170', 'P170']
                            ],
                            [
                                'coordinate' => 'C171',
                                'component' => 'Beban Bunga Sewa Guna Usaha',
                                'data' => ['E171', 'P171']
                            ],
                            [
                                'coordinate' => 'C172',
                                'component' => 'Beban Lain-lain',
                                'data' => ['E172', 'P172']
                            ],
                        ]
                    ]
                ]
            ],
            [
                'coordinate' => 'C180',
                'component' => 'Laba/Rugi sebelum pajak [A-B-C-D]',
                'data' => ['E180', 'P180']
            ],
            [
                'coordinate' => 'C182',
                'component' => 'Pajak',
                'child' => [
                    [
                        'coordinate' => 'C183',
                        'component' => 'Pajak Penghasilan Badan',
                        'data' => ['E183', 'P183']
                    ],
                    [
                        'coordinate' => 'C184',
                        'component' => 'Pajak Lainnya',
                        'data' => ['E184', 'P184']
                    ],
                ]
            ],
            [
                'coordinate' => 'C186',
                'component' => 'Laba/Rugi sesudah pajak [E-F]',
                'data' => ['E186', 'P186']
            ],
            // [ // check
            //     'coordinate' => 'C188',
            //     'component' => 'Pendapatan',
            //     'data' => ['E188', 'P188']
            // ],
            // [
            //     'coordinate' => 'C189',
            //     'component' => 'Biaya langsung gaji',
            //     'data' => ['E189', 'P189']
            // ],
            // [
            //     'coordinate' => 'C190',
            //     'component' => 'Biaya langsung non gaji',
            //     'data' => ['E190', 'P190']
            // ],
            // [
            //     'coordinate' => 'C191',
            //     'component' => 'laba Kotor',
            //     'data' => ['E191', 'P191']
            // ],
            // [
            //     'coordinate' => 'C192',
            //     'component' => 'Biaya Pegawai',
            //     'data' => ['E192', 'P192']
            // ],
            // [
            //     'coordinate' => 'C193',
            //     'component' => 'Biaya umum dan administrasi',
            //     'data' => ['E193', 'P193']
            // ],
            // [
            //     'coordinate' => 'C194',
            //     'component' => 'Laba sebelum pajak dan bunga',
            //     'data' => ['E194', 'P194']
            // ],
            // [
            //     'coordinate' => 'C195',
            //     'component' => 'Pendapatan (biaya) lainnya',
            //     'data' => ['E195', 'P195']
            // ],
            // [
            //     'coordinate' => 'C196',
            //     'component' => 'Operating profit',
            //     'data' => ['E196', 'P196']
            // ],
            // [
            //     'coordinate' => 'C197',
            //     'component' => 'Pajak',
            //     'data' => ['E197', 'P197']
            // ],
            // [
            //     'coordinate' => 'C198',
            //     'component' => 'Net Profit (Loss)',
            //     'data' => ['E198', 'P198']
            // ],
        ];
    }
}