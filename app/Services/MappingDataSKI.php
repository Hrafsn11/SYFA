<?php 

namespace App\Services;

use Illuminate\Support\Str;

class MappingDataSKI extends MappingRekursiData
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
                        'component' => 'Pendapatan Incubation',
                        'data' => ['E11', 'P11']
                    ],
                    [
                        'coordinate' => 'C12',
                        'component' => 'Pendapatan Event',
                        'data' => ['E12', 'P12']
                    ],
                    [
                        'coordinate' => 'C13',
                        'component' => 'Pendapatan Penalty',
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
                        'component' => 'Penjualan Barang Dagang',
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
                        'component' => 'Capital Gain',
                        'data' => ['E19', 'P19']
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
                                'component' => 'Biaya Akomodasi Tenaga Ahli',
                                'data' => ['E34', 'P34'],
                            ],
                            [
                                'coordinate' => 'C35',
                                'component' => 'Biaya Fee Marketing',
                                'data' => ['E35', 'P35'],
                            ],
                        ],
                    ],
                    [
                        'coordinate' => 'C36',
                        'component' => 'Biaya Operasional Training',
                        'child' => [
                            [
                                'coordinate' => 'C37',
                                'component' => 'Sewa Tempat',
                                'data' => ['E37', 'P37'],
                            ],
                            [
                                'coordinate' => 'C38',
                                'component' => 'Training Kit',
                                'data' => ['E38', 'P38'],
                            ],
                            [
                                'coordinate' => 'C39',
                                'component' => 'Biaya Training ( Vendor )',
                                'data' => ['E39', 'P39'],
                            ],
                            [
                                'coordinate' => 'C40',
                                'component' => 'Biaya Operasional Training Lainya',
                                'data' => ['E40', 'P40'],
                            ],
                            [
                                'coordinate' => 'C41',
                                'component' => 'Biaya Training Advance',
                                'data' => ['E41', 'P41'],
                            ],
                            [
                                'coordinate' => 'C42',
                                'component' => 'Sertifikasi',
                                'data' => ['E42', 'P42'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C43',
                        'component' => 'Biaya Konsultasi',
                        'child' => [
                            [
                                'coordinate' => 'C44',
                                'component' => 'Kunjungan Konsultan',
                                'data' => ['E44', 'P44'],
                            ],
                            [
                                'coordinate' => 'C45',
                                'component' => 'Meals',
                                'data' => ['E45', 'P45'],
                            ],
                            [
                                'coordinate' => 'C46',
                                'component' => 'Biaya Konsultan Lainya',
                                'data' => ['E46', 'P46'],
                            ],
                            [
                                'coordinate' => 'C47',
                                'component' => 'Biaya Konsultan ( Vendor )',
                                'data' => ['E47', 'P47'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C48',
                        'component' => 'Biaya Operasional Lain -lain',
                        'child' => [
                            [
                                'coordinate' => 'C49',
                                'component' => 'Transportasi',
                                'data' => ['E49', 'P49'],
                            ],
                            [
                                'coordinate' => 'C50',
                                'component' => 'Cetakan',
                                'data' => ['E50', 'P50'],
                            ],
                            [
                                'coordinate' => 'C51',
                                'component' => 'ATK',
                                'data' => ['E51', 'P51'],
                            ],
                            [
                                'coordinate' => 'C52',
                                'component' => 'Photocopy',
                                'data' => ['E52', 'P52'],
                            ],
                            [
                                'coordinate' => 'C53',
                                'component' => 'Perjalanan Dinas',
                                'data' => ['E53', 'P53'],
                            ],
                            [
                                'coordinate' => 'C54',
                                'component' => 'Biaya Koperasi',
                                'data' => ['E54', 'P54'],
                            ],
                            [
                                'coordinate' => 'C55',
                                'component' => 'Perangkat Atau Peralatan',
                                'data' => ['E55', 'P55'],
                            ],
                            [
                                'coordinate' => 'C56',
                                'component' => 'Perlengkapan',
                                'data' => ['E56', 'P56'],
                            ],
                            [
                                'coordinate' => 'C57',
                                'component' => 'Biaya Operasional Lainya',
                                'data' => ['E57', 'P57'],
                            ],
                            [
                                'coordinate' => 'C58',
                                'component' => 'Biaya adm bank garansi ',
                                'data' => ['E58', 'P58'],
                            ],
                            [
                                'coordinate' => 'C59',
                                'component' => 'Materi',
                                'data' => ['E59', 'P59'],
                            ],
                            [
                                'coordinate' => 'C60',
                                'component' => 'Biaya dukungan pengembangan Start Up',
                                'data' => ['E60', 'P60'],
                            ],
                            [
                                'coordinate' => 'C61',
                                'component' => 'Biaya Tender',
                                'data' => ['E61', 'P61'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C63',
                        'component' => 'Biaya Pegawai Operasional',
                        'child' => [
                            [
                                'coordinate' => 'C64',
                                'component' => 'Gaji Karyawan Operasional',
                                'data' => ['E64', 'P64'],
                            ],
                            [
                                'coordinate' => 'C65',
                                'component' => 'Lembur Karyawan Operasional',
                                'data' => ['E65', 'P65'],
                            ],
                            [
                                'coordinate' => 'C66',
                                'component' => 'Tunjangan Hari Raya Operasional',
                                'data' => ['E66', 'P66'],
                            ],
                            [
                                'coordinate' => 'C67',
                                'component' => 'Tunjangan/ Komisi, Bonus atau Fee Operasional',
                                'data' => ['E67', 'P67'],
                            ],
                            [
                                'coordinate' => 'C68',
                                'component' => 'Beban Pegawai Magang Operasional',
                                'data' => ['E68', 'P68'],
                            ],
                            [
                                'coordinate' => 'C69',
                                'component' => 'Tunjangan Kehadiran Operasional',
                                'data' => ['E69', 'P69'],
                            ],
                            [
                                'coordinate' => 'C70',
                                'component' => 'Tunjangan Kesehatan (Medical) Operasional',
                                'data' => ['E70', 'P70'],
                            ],
                            [
                                'coordinate' => 'C71',
                                'component' => 'Tunjangan BPJS Operasional',
                                'data' => ['E71', 'P71'],
                            ],
                            [
                                'coordinate' => 'C72',
                                'component' => 'Tunjangan PPH Pasal 21 Operasional',
                                'data' => ['E72', 'P72'],
                            ],
                            [
                                'coordinate' => 'C73',
                                'component' => 'Tunjangan Pensiun Operasional',
                                'data' => ['E73', 'P73'],
                            ],
                            [
                                'coordinate' => 'C74',
                                'component' => 'Tunjangan Telekomunikasi Operasional',
                                'data' => ['E74', 'P74'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C75',
                        'component' => 'Biaya Pegawai Sales & Marketing',
                        'child' => [
                            [
                                'coordinate' => 'C76',
                                'component' => 'Gaji Karyawan Sales & Marketing',
                                'data' => ['E76', 'P76'],
                            ],
                            [
                                'coordinate' => 'C77',
                                'component' => 'Lembur Karyawan Sales & Marketing',
                                'data' => ['E77', 'P77'],
                            ],
                            [
                                'coordinate' => 'C78',
                                'component' => 'InterTunjangan Hari Raya Sales & Marketingnet',
                                'data' => ['E78', 'P78'],
                            ],
                            [
                                'coordinate' => 'C79',
                                'component' => 'Tunjangan/ Komisi, Bonus atau Fee Sales & Marketing',
                                'data' => ['E79', 'P79'],
                            ],
                            [
                                'coordinate' => 'C80',
                                'component' => 'Beban Pegawai Magang Sales & Marketing',
                                'data' => ['E80', 'P80'],
                            ],
                            [
                                'coordinate' => 'C81',
                                'component' => 'Tunjangan Kehadiran Sales & Marketing',
                                'data' => ['E81', 'P81'],
                            ],
                            [
                                'coordinate' => 'C82',
                                'component' => 'Tunjangan Kesehatan (Medical) Sales & Marketing',
                                'data' => ['E82', 'P82'],
                            ],
                            [
                                'coordinate' => 'C83',
                                'component' => 'Tunjangan BPJS Sales & Marketing',
                                'data' => ['E83', 'P83'],
                            ],
                            [
                                'coordinate' => 'C84',
                                'component' => 'Tunjangan PPH Pasal 21 Sales & Marketing',
                                'data' => ['E84', 'P84'],
                            ],
                            [
                                'coordinate' => 'C85',
                                'component' => 'Tunjangan Pensiun Sales & Marketing',
                                'data' => ['E85', 'P85'],
                            ],
                            [
                                'coordinate' => 'C86',
                                'component' => 'Tunjangan Telekomunikasi Sales & Marketing',
                                'data' => ['E86', 'P86'],
                            ],
                        ]
                    ],
                ]
            ],
            [
                'coordinate' => 'C93',
                'component' => 'Biaya Umum & Administrasi',
                'child' => [
                    [
                        'coordinate' => 'C94',
                        'component' => 'Biaya Pegawai',
                        'child' => [
                            [
                                'coordinate' => 'C95',
                                'component' => 'Gaji Karyawan',
                                'data' => ['E95', 'P95'],
                            ],
                            [
                                'coordinate' => 'C96',
                                'component' => 'Biaya Lembur Karyawan',
                                'data' => ['E96', 'P96'],
                            ],
                            [
                                'coordinate' => 'C97',
                                'component' => 'Tunj. Hari Raya ( THR )',
                                'data' => ['E97', 'P97'],
                            ],
                            [
                                'coordinate' => 'C98',
                                'component' => 'Komisi / Bonus / Fee',
                                'data' => ['E98', 'P98'],
                            ],
                            [
                                'coordinate' => 'C99',
                                'component' => 'Allowance karyawan',
                                'data' => ['E99', 'P99'],
                            ],
                            [
                                'coordinate' => 'C100',
                                'component' => 'Honor Karyawan / Non Karyawan',
                                'data' => ['E100', 'P100'],
                            ],
                            [
                                'coordinate' => 'C101',
                                'component' => 'Biaya Kehadiran Karyawan',
                                'data' => ['E101', 'P101'],
                            ],
                            [
                                'coordinate' => 'C102',
                                'component' => 'Medical Karyawan',
                                'data' => ['E102', 'P102'],
                            ],
                            [
                                'coordinate' => 'C103',
                                'component' => 'Asuransi Karyawan',
                                'data' => ['E103', 'P103'],
                            ],
                            [
                                'coordinate' => 'C104',
                                'component' => 'Pph Pasal 21',
                                'data' => ['E104', 'P104'],
                            ],
                            [
                                'coordinate' => 'C105',
                                'component' => 'Pesangon Karyawan',
                                'data' => ['E105', 'P105'],
                            ],
                            [
                                'coordinate' => 'C106',
                                'component' => 'Tunjangan Telekomunikasi',
                                'data' => ['E106', 'P106'],
                            ],
                            [
                                'coordinate' => 'C107',
                                'component' => 'Tunjangan lainnya/Koperasi',
                                'data' => ['E107', 'P107'],
                            ],
                            [
                                'coordinate' => 'C108',
                                'component' => 'Beban Pegawai Magang',
                                'data' => ['E108', 'P108'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C110',
                        'component' => 'Transportasi dan Perjalanan Dinas',
                        'child' => [
                            [
                                'coordinate' => 'C111',
                                'component' => 'Biaya BBM, Taxi, Tol & Parkir Office',
                                'data' => ['E111', 'P111'],
                            ],
                            [
                                'coordinate' => 'C112',
                                'component' => 'Biaya Transportasi Direksi',
                                'data' => ['E112', 'P112'],
                            ],
                            [
                                'coordinate' => 'C113',
                                'component' => 'Biaya Perjalanan Dinas Luar Kota Umum ',
                                'data' => ['E113', 'P113'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C114',
                        'component' => 'Biaya Kantor',
                        'child' => [
                            [
                                'coordinate' => 'C115',
                                'component' => 'Biaya peralatan kantor',
                                'data' => ['E115', 'P115'],
                            ],
                            [
                                'coordinate' => 'C116',
                                'component' => 'Biaya ATK',
                                'data' => ['E116', 'P116'],
                            ],
                            [
                                'coordinate' => 'C117',
                                'component' => 'Biaya Listrik, PAM',
                                'data' => ['E117', 'P117'],
                            ],
                            [
                                'coordinate' => 'C118',
                                'component' => 'Biaya Telpon & internet kantor',
                                'data' => ['E118', 'P118'],
                            ],
                            [
                                'coordinate' => 'C119',
                                'component' => 'Biaya Ponsel',
                                'data' => ['E119', 'P119'],
                            ],
                            [
                                'coordinate' => 'C120',
                                'component' => 'Biaya Fotocopy & cetak',
                                'data' => ['E120', 'P120'],
                            ],
                            [
                                'coordinate' => 'C121',
                                'component' => 'Biaya Sewa kantor',
                                'data' => ['E121', 'P121'],
                            ],
                            [
                                'coordinate' => 'C122',
                                'component' => 'Biaya Sewa kendaraan',
                                'data' => ['E122', 'P122'],
                            ],
                            [
                                'coordinate' => 'C123',
                                'component' => 'Biaya Pos & Pengiriman',
                                'data' => ['E123', 'P123'],
                            ],
                            [
                                'coordinate' => 'C124',
                                'component' => 'Biaya Materai',
                                'data' => ['E124', 'P124'],
                            ],
                            [
                                'coordinate' => 'C125',
                                'component' => 'Biaya Kantor Lainnya',
                                'data' => ['E125', 'P125'],
                            ],
                            [
                                'coordinate' => 'C126',
                                'component' => 'Biaya Perlengkapan Kantor',
                                'data' => ['E126', 'P126'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C127',
                        'component' => 'Biaya Pemeliharaan',
                        'child' => [
                            [
                                'coordinate' => 'C128',
                                'component' => 'Biaya Pemeliharaan Kendaraan',
                                'data' => ['E128', 'P128'],
                            ],
                            [
                                'coordinate' => 'C129',
                                'component' => 'Biaya Adm Kendaraan',
                                'data' => ['E129', 'P129'],
                            ],
                            [
                                'coordinate' => 'C130',
                                'component' => 'Biaya Asuransi Asset',
                                'data' => ['E130', 'P130'],
                            ],
                            [
                                'coordinate' => 'C131',
                                'component' => 'Biaya Pemeliharaan Peralatan Kantor',
                                'data' => ['E131', 'P131'],
                            ],
                            [
                                'coordinate' => 'C132',
                                'component' => 'Biaya Pemeliharaan Kantor',
                                'data' => ['E132', 'P132'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C133',
                        'component' => 'Biaya Rumah Tangga',
                        'child' => [
                            [
                                'coordinate' => 'C134',
                                'component' => 'Biaya Pantry',
                                'data' => ['E134', 'P134'],
                            ],
                            [
                                'coordinate' => 'C135',
                                'component' => 'Biaya Majalah / Koran',
                                'data' => ['E135', 'P135'],
                            ],
                            [
                                'coordinate' => 'C136',
                                'component' => 'Biaya Rapat',
                                'data' => ['E136', 'P136'],
                            ],
                            [
                                'coordinate' => 'C137',
                                'component' => 'Biaya Dekorasi',
                                'data' => ['E137', 'P137'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C138',
                        'component' => 'Biaya Promosi',
                        'child' => [
                            [
                                'coordinate' => 'C139',
                                'component' => 'Biaya Iklan',
                                'data' => ['E139', 'P139'],
                            ],
                            [
                                'coordinate' => 'C140',
                                'component' => 'Biaya Marketing dan Promosi ( Brosur dll )',
                                'data' => ['E140', 'P140'],
                            ],
                            [
                                'coordinate' => 'C141',
                                'component' => 'Biaya Entertaintment',
                                'data' => ['E141', 'P141'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C142',
                        'component' => 'Biaya Umum Lainnya',
                        'child' => [
                            [
                                'coordinate' => 'C143',
                                'component' => 'Biaya Training, Seminar & Pendidikan',
                                'data' => ['E143', 'P143'],
                            ],
                            [
                                'coordinate' => 'C144',
                                'component' => 'Bingkisan, Parcel',
                                'data' => ['E144', 'P144'],
                            ],
                            [
                                'coordinate' => 'C145',
                                'component' => 'Sumbangan',
                                'data' => ['E145', 'P145'],
                            ],
                            [
                                'coordinate' => 'C146',
                                'component' => 'Biaya Notaris , Perijinan dan konsultan',
                                'data' => ['E146', 'P146'],
                            ],
                            [
                                'coordinate' => 'C147',
                                'component' => 'Penyisihan Penghapusan Piutang',
                                'data' => ['E147', 'P147'],
                            ],
                            [
                                'coordinate' => 'C148',
                                'component' => 'Service Charge Groups',
                                'data' => ['E148', 'P148'],
                            ],
                            [
                                'coordinate' => 'C149',
                                'component' => 'Sapbi Charge',
                                'data' => ['E149', 'P149'],
                            ],
                            [
                                'coordinate' => 'C150',
                                'component' => 'Biaya Lain  ',
                                'data' => ['E150', 'P150'],
                            ],
                            [
                                'coordinate' => 'C151',
                                'component' => 'Biaya Finance & IT',
                                'data' => ['E151', 'P151'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C152',
                        'component' => 'Penyusutan dan Amortisasi',
                        'child' => [
                            [
                                'coordinate' => 'C153',
                                'component' => 'Penyusutan Bangunan',
                                'data' => ['E153', 'P153'],
                            ],
                            [
                                'coordinate' => 'C154',
                                'component' => 'Penyusutan Interior dan Renovasi',
                                'data' => ['E154', 'P154'],
                            ],
                            [
                                'coordinate' => 'C155',
                                'component' => 'Penyusutan Furniture',
                                'data' => ['E155', 'P155'],
                            ],
                            [
                                'coordinate' => 'C156',
                                'component' => 'Penyusutan Peralatan Kantor',
                                'data' => ['E156', 'P156'],
                            ],
                            [
                                'coordinate' => 'C157',
                                'component' => 'Penyusutan Kendaraan',
                                'data' => ['E157', 'P157'],
                            ],
                            [
                                'coordinate' => 'C158',
                                'component' => 'Amortisasi Aktiva Tak Berwujud',
                                'data' => ['E158', 'P158'],
                            ],
                        ]
                    ],
                ]
            ],
            [
                'coordinate' => 'C169',
                'component' => 'Pendapatan & (Biaya) Non Operasional',
                'child' => [
                    [
                        'coordinate' => 'C170',
                        'component' => 'Pendapatan Non Operasional',
                        'child' => [
                            [
                                'coordinate' => 'C171',
                                'component' => 'Pendapatan Bunga (jasa giro, bunga tabungan, deposito)',
                                'data' => ['E171', 'P171'],
                            ],
                            [
                                'coordinate' => 'C172',
                                'component' => 'Pendapatan Investasi Lainnya (Reksadana, dll)',
                                'data' => ['E172', 'P172'],
                            ],
                            [
                                'coordinate' => 'C173',
                                'component' => 'Keuntungan Selisih Kurs',
                                'data' => ['E173', 'P173'],
                            ],
                            [
                                'coordinate' => 'C174',
                                'component' => 'Keuntungan Penjualan Aktiva Tetap',
                                'data' => ['E174', 'P174'],
                            ],
                            [
                                'coordinate' => 'C175',
                                'component' => 'Pendapatan Lain-lain',
                                'data' => ['E175', 'P175'],
                            ],
                        ]
                    ],
                    [
                        'coordinate' => 'C178',
                        'component' => '(Biaya ) Non Operasional',
                        'child' => [
                            [
                                'coordinate' => 'C179',
                                'component' => 'Biaya Bunga Bank',
                                'data' => ['E179', 'P179'],
                            ],
                            [
                                'coordinate' => 'C180',
                                'component' => 'Biaya Bunga Sewa Guna Usaha',
                                'data' => ['E180', 'P180'],
                            ],
                            [
                                'coordinate' => 'C181',
                                'component' => 'Biaya Bunga Lainnya',
                                'data' => ['E181', 'P181'],
                            ],
                            [
                                'coordinate' => 'C182',
                                'component' => 'Biaya Propisi & adm kredit',
                                'data' => ['E182', 'P182'],
                            ],
                            [
                                'coordinate' => 'C183',
                                'component' => 'Biaya adm bank',
                                'data' => ['E183', 'P183'],
                            ],
                            [
                                'coordinate' => 'C184',
                                'component' => 'Kerugian Selisih Kurs',
                                'data' => ['E184', 'P184'],
                            ],
                            [
                                'coordinate' => 'C185',
                                'component' => 'Kerugian Penjualan Aktiva',
                                'data' => ['E185', 'P185'],
                            ],
                            [
                                'coordinate' => 'C186',
                                'component' => 'Kerugian Lain-lain',
                                'data' => ['E186', 'P186'],
                            ],
                            [
                                'coordinate' => 'C187',
                                'component' => 'Biaya Lain-lain',
                                'data' => ['E187', 'P187'],
                            ],
                        ]
                    ],
                ]
            ],
            [
                'coordinate' => 'C192',
                'component' => 'Laba/Rugi sebelum pajak',
                'data' => ['E192', 'P192']
            ],
            [
                'coordinate' => 'C194',
                'component' => 'Pajak',
                // 'data' => ['E194', 'P194']
                'child' => [
                    [
                        'coordinate' => 'C195',
                        'component' => 'Pajak Penghasilan Badan',
                        'data' => ['E195', 'P195']
                    ],
                    [
                        'coordinate' => 'C196',
                        'component' => 'Pajak Lainnya',
                        'data' => ['E196', 'P196']
                    ],
                ]
            ],
            [
                'coordinate' => 'C198',
                'component' => 'Laba/Rugi sesudah pajak',
                'data' => ['E198', 'P198']
            ],
        ];
    }
}