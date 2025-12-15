@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="mb-3 d-flex justify-content-between no-print">
                <a href="{{ route('sfinlog.pengajuan-investasi.detail', $pengajuan->id_pengajuan_investasi_finlog) }}"
                    class="btn btn-outline-primary">
                    <i class="ti ti-arrow-left me-2"></i>
                    Kembali
                </a>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="ti ti-printer me-2"></i>
                    Print
                </button>
            </div>

            <!-- Card Preview Kontrak -->
            <div class="card">
                <div class="card-body p-4 p-md-5" id="kontrak-content">
                    <!-- Header dengan Logo -->
                    <div class="text-end mb-4">
                        <img src="{{ asset('assets/img/branding/Logo.jpg') }}" alt="S-Finlog Logo" style="height: 60px;">
                    </div>

                    <!-- Judul Kontrak -->
                    <div class="text-center mb-4">
                        <h5 class="fw-bold mb-2">SURAT PERJANJIAN KERJASAMA INVESTASI PEMBIAYAAN USAHA</h5>
                        <p class="mb-0"><strong>No: {{ $data['nomor_kontrak'] }}</strong></p>
                    </div>

                    <br><br>

                    <!-- Pembukaan -->
                    <div class="kontrak-content" style="text-align: justify; line-height: 1.8;">
                        <p class="mb-4">
                            Pada hari ini
                            <strong>{{ \Carbon\Carbon::parse($data['tanggal_kontrak'])->locale('id')->isoFormat('dddd') }}</strong>,
                            tanggal
                            <strong>{{ \Carbon\Carbon::parse($data['tanggal_kontrak'])->locale('id')->isoFormat('D MMMM YYYY') }}</strong>,
                            yang bertanda tangan di bawah ini:
                        </p>

                        <!-- Pihak Pertama (Investor) -->
                        <div class="mb-4">
                            <table style="width: 100%; border: none;">
                                <tbody>
                                    <tr>
                                        <td width="5%" style="vertical-align: top; padding: 2px 0;">1.</td>
                                        <td width="25%" style="vertical-align: top; padding: 2px 0;">Nama</td>
                                        <td width="70%" style="vertical-align: top; padding: 2px 0;">:
                                            {{ $data['nama_investor'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 2px 0;"></td>
                                        <td style="vertical-align: top; padding: 2px 0;">Perusahaan</td>
                                        <td style="vertical-align: top; padding: 2px 0;">: {{ $data['nama_perusahaan'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 2px 0;"></td>
                                        <td style="vertical-align: top; padding: 2px 0;">Alamat</td>
                                        <td style="vertical-align: top; padding: 2px 0;">: {{ $data['alamat'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="mt-2">Untuk selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.</p>
                        </div>

                        <!-- Pihak Kedua (S-Finlog) -->
                        <div class="mb-4">
                            <table style="width: 100%; border: none;">
                                <tbody>
                                    <tr>
                                        <td width="5%" style="vertical-align: top; padding: 2px 0;">2.</td>
                                        <td width="25%" style="vertical-align: top; padding: 2px 0;">Nama</td>
                                        <td width="70%" style="vertical-align: top; padding: 2px 0;">: Aditia Pratama
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 2px 0;"></td>
                                        <td style="vertical-align: top; padding: 2px 0;">Perusahaan</td>
                                        <td style="vertical-align: top; padding: 2px 0;">: S-Finlog</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 2px 0;"></td>
                                        <td style="vertical-align: top; padding: 2px 0;">Alamat</td>
                                        <td style="vertical-align: top; padding: 2px 0;">: Permata Kuningan Building 17th
                                            Floor, Kawasan Epicentrum, HR Rasuna Said, Jl. Kuningan Mulia, RT.6/RW.1,
                                            Menteng Atas, Setiabudi, South Jakarta City, Jakarta 12920</td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="mt-2">Untuk selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.</p>
                        </div>

                        <br>

                        <!-- Pendahuluan -->
                        <p class="mb-3">
                            Bahwa sebelum ditandatanganinya Surat Perjanjian Kerjasama Investasi ini berupa penempatan Dana
                            Investasi untuk Pembiayaan Usaha, PARA PIHAK terlebih dahulu menerangkan hal–hal sebagai
                            berikut:
                        </p>

                        <div class="mb-4" style="padding-left: 20px;">
                            <p class="mb-2">1. Bahwa PIHAK PERTAMA adalah selaku Investor yang memiliki dan menyerahkan
                                dana sebesar <strong>Rp {{ number_format($data['nominal_investasi'], 0, ',', '.') }}
                                    ({{ ucwords(\App\Helpers\Terbilang::convert($data['nominal_investasi'])) }}
                                    Rupiah)</strong> yang selanjutnya disebut sebagai Dana Investasi Pembiayaan Usaha untuk
                                dikelola dalam bisnis S-Finlog.</p>

                            <p class="mb-2">2. Bahwa PIHAK KEDUA adalah Penerima, Penyalur Dana Investasi, monitoring dan
                                Penjamin Dana Investasi Pembiayaan Usaha yang dikelola bersama dengan mitra usaha bisnis
                                S-Finlog.</p>

                            <p class="mb-2">3. Bahwa PARA PIHAK setuju untuk saling mengikatkan diri dalam suatu
                                perjanjian kerjasama Investasi Pembiayaan Usaha sesuai dengan ketentuan hukum yang berlaku.
                            </p>

                            <p class="mb-2">4. PARA PIHAK menyatakan bahwa bertindak atas dasar sukarela dan tanpa paksaan
                                dari pihak manapun.</p>

                            <p class="mb-2">5. Bahwa berdasarkan hal-hal tersebut di atas, PARA PIHAK menyatakan sepakat
                                dan setuju untuk mengadakan Perjanjian Kerjasama Investasi Pembiayaan Usaha ini yang
                                dilaksanakan dengan ketentuan dan syarat-syarat sebagai berikut.</p>
                        </div>

                        <br>

                        <!-- PASAL I -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL I<br>MAKSUD DAN TUJUAN</h6>
                            <p class="mt-3" style="padding-left: 20px;">
                                1. Membentuk kerjasama Investasi Pembiayaan Usaha dari PARA PIHAK untuk mendukung pembiayaan
                                S-Finlog yang saling menguntungkan dengan saling menjaga etika bisnis dari para pihak serta
                                dilakukan secara profesional dan amanah.
                            </p>
                        </div>

                        <!-- PASAL II -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL II<br>RUANG LINGKUP</h6>
                            <div class="mt-3" style="padding-left: 20px;">
                                <p class="mb-2">1. Dalam pelaksanaan perjanjian ini, PIHAK PERTAMA memberi Dana Investasi
                                    kepada PIHAK KEDUA sebesar <strong>Rp
                                        {{ number_format($data['nominal_investasi'], 0, ',', '.') }}
                                        ({{ ucwords(\App\Helpers\Terbilang::convert($data['nominal_investasi'])) }}
                                        Rupiah)</strong> dan PIHAK KEDUA dengan ini menerima penyerahan Dana Investasi
                                    tersebut dari PIHAK PERTAMA serta menyanggupi sebagai penyalur, monitoring, dan penjamin
                                    dana Investasi.</p>

                                <p class="mb-2">2. PIHAK KEDUA dengan ini berjanji dan mengikatkan diri untuk mengelola
                                    perputaran Dana Investasi secara khusus pada Usaha Pembiayaan S-Finlog.</p>
                            </div>
                        </div>

                        <!-- PASAL III -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL III<br>JANGKA WAKTU KERJASAMA</h6>
                            <div class="mt-3" style="padding-left: 20px;">
                                <p class="mb-2">1. Perjanjian kerjasama ini berlaku sampai tanggal
                                    <strong>{{ \Carbon\Carbon::parse($data['tanggal_berakhir'])->locale('id')->isoFormat('D MMMM YYYY') }}</strong>
                                    ({{ $data['lama_investasi'] }} bulan) dan dapat diperpanjang dengan persetujuan PARA
                                    PIHAK dengan konfirmasi 2 minggu sebelum berakhir kontrak.</p>

                                <p class="mb-2">2. Jangka waktu penutupan investasi adalah sampai
                                    {{ \Carbon\Carbon::parse($data['tanggal_berakhir'])->locale('id')->isoFormat('D MMMM YYYY') }}.
                                    Jika investasi diambil sebelum masa waktunya, maka akan dikenakan penalti sebesar 1%
                                    dari nilai nominal investasi.</p>

                                <p class="mb-2">3. Persetujuan perpanjangan Perjanjian kerjasama yang dimaksudkan dapat
                                    dilakukan secara otomatis berdasarkan konfirmasi awal dari PIHAK PERTAMA kepada PIHAK
                                    KEDUA, atau Non Otomatis jika diperlukan adanya Keputusan Investasi dari PIHAK PERTAMA
                                    jika terdapat perubahan objek atau skema Investasi didalam kelolaan project S-Finlog.
                                </p>
                            </div>
                        </div>

                        <!-- PASAL IV -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL IV<br>HAK DAN KEWAJIBAN PIHAK PERTAMA</h6>
                            <p class="mt-3">Dalam Perjanjian Kerjasama ini, PIHAK PERTAMA memiliki Hak dan Kewajiban
                                sebagai berikut:</p>
                            <div style="padding-left: 20px;">
                                <p class="mb-2">1. Memberikan Dana Investasi kepada PIHAK KEDUA sebesar <strong>Rp
                                        {{ number_format($data['nominal_investasi'], 0, ',', '.') }}
                                        ({{ ucwords(\App\Helpers\Terbilang::convert($data['nominal_investasi'])) }}
                                        Rupiah)</strong> yang di tempatkan/ditransfer ke rekening S-Finlog, dengan data
                                    sebagai berikut:</p>
                                <div style="padding-left: 20px; margin-top: 10px; margin-bottom: 10px;">
                                    <table style="width: 100%; border: none;">
                                        <tbody>
                                            <tr>
                                                <td width="35%" style="vertical-align: top; padding: 2px 0;">Nama pemilik
                                                    rekening</td>
                                                <td width="5%" style="vertical-align: top; padding: 2px 0;">:</td>
                                                <td width="60%" style="vertical-align: top; padding: 2px 0;">PT. Synnovac
                                                    Kapital Indonesia</td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align: top; padding: 2px 0;">Nama Bank</td>
                                                <td style="vertical-align: top; padding: 2px 0;">:</td>
                                                <td style="vertical-align: top; padding: 2px 0;">Mandiri</td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align: top; padding: 2px 0;">Nomor rekening</td>
                                                <td style="vertical-align: top; padding: 2px 0;">:</td>
                                                <td style="vertical-align: top; padding: 2px 0;">1240010052851</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <p class="mb-2">2. Berhak meminta kembali Dana Investasi yang telah diserahkan kepada
                                    PIHAK KEDUA dengan ketentuan berdasarkan Pasal III Ayat 2.</p>

                                <p class="mb-2">3. Menerima hasil keuntungan atas pengelolaan Dana Investasi dari PIHAK
                                    KEDUA, sesuai dengan Pasal VI perjanjian ini.</p>

                                <p class="mb-2">4. Menerima hasil laporan pengelolaan dana Investasi dari PIHAK KEDUA
                                    secara periodic.</p>

                                <p class="mb-2">5. PIHAK PERTAMA akan menerima bukti penerbitan dana Investasi pembiayaan
                                    dari PIHAK KEDUA setelah dana diterima dan pembiayaan ditempatkan.</p>
                            </div>
                        </div>

                        <!-- PASAL V -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL V<br>HAK DAN KEWAJIBAN PIHAK KEDUA</h6>
                            <p class="mt-3">Dalam Perjanjian Kerjasama Investasi ini, PIHAK KEDUA memiliki Hak dan
                                Kewajiban sebagai berikut:</p>
                            <div style="padding-left: 20px;">
                                <p class="mb-2">1. Menerima Dana Investasi dari PIHAK PERTAMA sebesar <strong>Rp
                                        {{ number_format($data['nominal_investasi'], 0, ',', '.') }}
                                        ({{ ucwords(\App\Helpers\Terbilang::convert($data['nominal_investasi'])) }}
                                        Rupiah)</strong> yang ditempatkan di rekening S-Finlog.</p>

                                <p class="mb-2">2. PIHAK KEDUA akan memberikan bukti penerbitan dana Investasi pembiayaan
                                    kepada PIHAK PERTAMA setelah dana diterima.</p>

                                <p class="mb-2">3. Menyalurkan dan monitoring Dana Investasi Pembiayaan Usaha PIHAK
                                    PERTAMA yang dikelola
                                    bersama dengan mitra usaha bisnis S-Finlog .</p>

                                <p class="mb-2">4. Memberikan bagian hasil keuntungan kepada PIHAK PERTAMA, sesuai dengan
                                    Pasal VI perjanjian ini.</p>

                                <p class="mb-2">5. Memberikan hasil laporan pengelolaan Dana Investasi kepada PIHAK
                                    PERTAMA secara periodic.</p>
                            </div>
                        </div>

                        <!-- PASAL VI -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL VI<br>PEMBAGIAN HASIL</h6>
                            <p class="mt-3">Dalam Perjanjian Kerjasama Investasi ini, PARA PIHAK sepakat didalam hal
                                pembagian hasil Investasi sebagai berikut:</p>
                            <div style="padding-left: 20px;">
                                <p class="mb-2">1. Bagi Hasil kepada PIHAK PERTAMA sebesar
                                    <strong>{{ $data['persentase_bagi_hasil'] }}%</strong> per Tahun terhitung dari tanggal
                                    diterimanya dana oleh PIHAK KEDUA dan nilai bagi hasil akan diberikan dari PIHAK KEDUA
                                    di akhir periode kerjasama.</p>

                                <p class="mb-2">2. Jika dana masuk di atas tanggal 20, maka bagi hasil akan di hitung di
                                    bulan berikutnya.</p>

                                <p class="mb-2">3. Bagi hasil yang dimaksud berlaku sampai dengan PIHAK PERTAMA menarik
                                    kembali Dana Investasi yang telah diserahkan kepada PIHAK KEDUA atau masa berlaku
                                    Investasi sudah berakhir.</p>
                            </div>
                        </div>

                        <!-- PASAL VII -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL VII<br>KEADAAN MEMAKSA (FORCE MAJEUR)</h6>
                            <div class="mt-3" style="padding-left: 20px;">
                                <p class="mb-2">1. Yang termasuk dalam Force Majeur adalah akibat dari kejadian-kejadian
                                    diluar kuasa dan kehendak dari kedua belah pihak diantaranya termasuk tidak terbatas
                                    bencana alam, banjir, badai, topan, gempa bumi, kebakaran, perang, huru-hara,
                                    pemberontakan, demonstrasi, pemogokan, kegagalan Investasi.</p>

                                <p class="mb-2">2. Jika dalam pelaksanaan perjanjian ini terhambat ataupun tertunda baik
                                    secara keseluruhan ataupun sebagian yang dikarenakan hal-hal tersebut dalam ayat 1
                                    diatas, maka PIHAK KEDUA bersedia mengganti sejumlah Dana Investasi dari PIHAK PERTAMA
                                    secara penuh apabila belum ada pembagian hasil keuntungan, atau pengembalian Dana
                                    Investasi dikurangi dengan pembagian hasil yang sudah terima oleh PIHAK PERTAMA.</p>

                                <p class="mb-2">3. Pengembalian Dana Investasi sebagaimana tersebut dalam ayat 2,
                                    mengenai tata cara pengembaliannya akan diadakan musyawarah terlebih dahulu antara PIHAK
                                    PERTAMA dan PIHAK KEDUA mengenai proses atau jangka waktu pengembaliannya.</p>
                            </div>
                        </div>

                        <!-- PASAL VIII -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL VIII<br>WANPRESTASI</h6>
                            <div class="mt-3" style="padding-left: 20px;">
                                <p class="mb-2">1. Dalam hal salah satu pihak telah melanggar kewajibannya yang tercantum
                                    dalam salah satu Pasal perjanjian ini, telah cukup bukti dan tanpa perlu dibuktikan
                                    lebih lanjut, bahwa pihak yang melanggar tersebut telah melakukan tindakan Wanprestasi.
                                </p>

                                <p class="mb-2">2. Pihak yang merasa dirugikan atas tindakan Wanprestasi tersebut dalam
                                    ayat 1 diatas, berhak meminta ganti kerugian dari pihak yang melakukan wanprestasi
                                    tersebut atas sejumlah kerugian yang dideritanya, kecuali dalam hal kerugian tersebut
                                    disebabkan karena adanya suatu keadaan memaksa, seperti tercantum dalam Pasal VII.</p>
                            </div>
                        </div>

                        <!-- PASAL IX -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL IX<br>PERSELISIHAN</h6>
                            <p class="mt-3" style="padding-left: 20px;">
                                Bilamana dalam pelaksanaan perjanjian Kerjasama ini terdapat perselisihan antara PARA PIHAK
                                baik dalam pelaksanaannya ataupun dalam penafsiran salah satu Pasal dalam perjanjian ini,
                                maka PARA PIHAK sepakat untuk sedapat mungkin menyelesaikan perselisihan tersebut dengan
                                cara musyawarah. Apabila musyawarah telah dilakukan oleh PARA PIHAK, namun ternyata tidak
                                berhasil mencapai suatu kemufakatan maka Para Pihak sepakat bahwa semua sengketa yang timbul
                                dari perjanjian ini akan diselesaikan pada Kantor Kepaniteraan Pengadilan Negeri Jakarta
                                Pusat.
                            </p>
                        </div>

                        <!-- PASAL X -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL X<br>ATURAN PENUTUP</h6>
                            <p class="mt-3" style="padding-left: 20px;">
                                Hal-hal yang belum diatur atau belum cukup diatur dalam perjanjian ini apabila dikemudian
                                hari dibutuhkan dan dipandang perlu akan ditetapkan tersendiri secara musyawarah dan
                                selanjutnya akan ditetapkan dalam suatu ADDENDUM yang berlaku mengikat bagi PARA PIHAK, yang
                                akan direkatkan dan merupakan bagian yang tidak terpisahkan dari Perjanjian ini.
                            </p>
                        </div>

                        <br>

                        <!-- Penutup -->
                        <p class="mb-4">
                            Demikianlah surat perjanjian kerjasama ini dibuat dalam rangkap 2 (dua), untuk masing-masing
                            pihak, yang ditandatangani di atas kertas bermaterai cukup, yang masing-masing mempunyai
                            kekuatan hukum yang sama dan berlaku sejak ditandatangani.
                        </p>

                        <br><br>

                        <p class="text-start mb-5">Jakarta,
                            {{ \Carbon\Carbon::parse($data['tanggal_kontrak'])->locale('id')->isoFormat('D MMMM YYYY') }}
                        </p>

                        <!-- Tanda Tangan -->
                        <div class="row mt-5">
                            <div class="col-6 text-center">
                                <p class="mb-5"><strong>PIHAK PERTAMA</strong></p>
                                @if ($pengajuan->investor && $pengajuan->investor->tanda_tangan)
                                    <img src="{{ asset('storage/' . $pengajuan->investor->tanda_tangan) }}"
                                        alt="TTD Investor" style="max-width: 150px; max-height: 80px;" class="mb-3">
                                @else
                                    <div style="height: 80px;" class="mb-3"></div>
                                @endif
                                <p class="mb-0"><strong>{{ $data['nama_investor'] }}</strong></p>
                                <p class="mb-0">{{ $data['nama_perusahaan'] }}</p>
                            </div>
                            <div class="col-6 text-center">
                                <p class="mb-5"><strong>PIHAK KEDUA</strong></p>
                                <div style="position: relative; display: inline-block; width: 150px; height: 80px; margin-bottom: 1rem;">
                                    <img src="{{ asset('assets/img/image.png') }}" alt="Logo"
                                        style="position: absolute; top: -25px; left: -10px; width: 150px; height: 80px; object-fit: contain; z-index: 1; opacity: 1;">
                                    <img src="{{ asset('assets/img/TTD-CEO-FINLOG.png') }}" alt="TTD CEO S-Finlog"
                                        style="position: absolute; top: 0; left: 0; width: 150px; height: 80px; object-fit: contain; z-index: 2; mix-blend-mode: multiply;"
                                        onload="this.style.opacity='1'" onerror="this.style.opacity='1'">
                                </div>
                                <p class="mb-0"><strong>Aditia Pratama</strong></p>
                                <p class="mb-0">CEO S-Finlog</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            #kontrak-content {
                padding: 20px !important;
            }

            @page {
                size: A4;
                margin: 2cm;
            }
        }

        .kontrak-content p {
            margin-bottom: 0.5rem;
        }

        .kontrak-content table {
            margin-bottom: 1rem;
        }
    </style>
@endsection
