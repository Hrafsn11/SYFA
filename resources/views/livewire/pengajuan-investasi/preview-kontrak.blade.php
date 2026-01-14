@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">

            <!-- Button Kembali -->
            <div class="mb-3">
                <a href="{{ route('pengajuan-investasi.show', $kontrak['id_investasi']) }}" class="btn btn-outline-primary">
                    <i class="ti ti-arrow-left me-2"></i>
                    Kembali
                </a>
            </div>

            <!-- Judul Halaman -->
            <div class="mb-4">
                <h4 class="fw-bold mb-1">Contract Preview</h4>
            </div>

            <!-- Card Preview Kontrak -->
            <div class="card">
                <div class="card-body p-4 p-md-5" id="kontrak-content">
                    <!-- Header dengan Logo -->
                    <div class="text-end mb-4">
                        <img src="{{ asset('assets/img/branding/Logo.jpg') }}" alt="S-Capital Logo" style="height: 60px;">
                    </div>

                    <!-- Judul Kontrak -->
                    <div class="text-center mb-4">
                        <h5 class="fw-bold mb-2">SURAT PERJANJIAN KERJASAMA INVESTASI DEPOSITO
                            {{ $kontrak['jenis_deposito'] }}
                        </h5>
                        <p class="mb-0"><strong>No: {{ $kontrak['nomor_kontrak'] }}</strong></p>
                    </div>

                    <br><br>

                    <!-- Pembukaan -->
                    <div class="kontrak-content" style="text-align: justify; line-height: 1.8;">
                        <p class="mb-4">
                            Pada hari ini <strong>{{ $kontrak['hari'] }}</strong>, tanggal
                            <strong>{{ $kontrak['tanggal_kontrak'] }}</strong>, yang bertanda tangan di bawah ini:
                        </p>

                        <!-- Pihak Pertama (Investor) -->
                        <div class="mb-4">
                            <table style="width: 100%; border: none;">
                                <tbody>
                                    <tr>
                                        <td width="5%" style="vertical-align: top; padding: 2px 0;">1.</td>
                                        <td width="25%" style="vertical-align: top; padding: 2px 0;">Nama</td>
                                        <td width="70%" style="vertical-align: top; padding: 2px 0;">:
                                            {{ $kontrak['nama_investor'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 2px 0;"></td>
                                        <td style="vertical-align: top; padding: 2px 0;">Perusahaan</td>
                                        <td style="vertical-align: top; padding: 2px 0;">:
                                            {{ $kontrak['perusahaan_investor'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 2px 0;"></td>
                                        <td style="vertical-align: top; padding: 2px 0;">Alamat</td>
                                        <td style="vertical-align: top; padding: 2px 0;">: {{ $kontrak['alamat_investor'] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="mt-2">Untuk selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.</p>
                        </div>

                        <!-- Pihak Kedua (PT Synnovac) -->
                        <div class="mb-4">
                            <table style="width: 100%; border: none;">
                                <tbody>
                                    <tr>
                                        <td width="5%" style="vertical-align: top; padding: 2px 0;">2.</td>
                                        <td width="25%" style="vertical-align: top; padding: 2px 0;">Nama</td>
                                        <td width="70%" style="vertical-align: top; padding: 2px 0;">: Muhamad Kurniawan
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 2px 0;"></td>
                                        <td style="vertical-align: top; padding: 2px 0;">Perusahaan</td>
                                        <td style="vertical-align: top; padding: 2px 0;">: PT. Synnovac Kapital Indonesia
                                        </td>
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
                            Bahwa sebelum ditandatanganinya Surat Perjanjian Investasi ini berupa Penempatan Dana Deposito,
                            PARA PIHAK terlebih dahulu menerangkan hal–hal sebagai berikut:
                        </p>

                        <div class="mb-4" style="padding-left: 20px;">
                            <p class="mb-2">1. Bahwa PIHAK PERTAMA adalah selaku Investor yang memiliki dana sebesar
                                {{ $kontrak['jumlah_investasi_angka'] }} ({{ $kontrak['jumlah_investasi_text'] }}) untuk
                                selanjutnya disebut sebagai Dana Deposito kepada S-Finance untuk Pembiayaan usaha yang
                                dibawah naungan S-Finance.
                            </p>

                            <p class="mb-2">2. Bahwa PIHAK KEDUA adalah Penyalur, monitoring dan Penjamin Dana Deposito yang
                                menerima Dana Deposito dari PIHAK PERTAMA.</p>

                            <p class="mb-2">3. Bahwa PARA PIHAK setuju untuk saling mengikatkan diri dalam suatu perjanjian
                                kerjasama Deposito sesuai dengan ketentuan hukum yang berlaku.</p>

                            <p class="mb-2">4. PARA PIHAK menyatakan bahwa bertindak atas dasar sukarela dan tanpa paksaan
                                dari pihak manapun.</p>

                            <p class="mb-2">5. Bahwa berdasarkan hal-hal tersebut di atas, PARA PIHAK menyatakan sepakat dan
                                setuju untuk mengadakan Perjanjian Kerjasama Deposito ini yang dilaksanakan dengan ketentuan
                                dan syarat-syarat sebagai berikut.</p>
                        </div>

                        <br>

                        <!-- PASAL I -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL I<br>MAKSUD DAN TUJUAN</h6>
                            <p class="mt-3" style="padding-left: 20px;">
                                1. Membentuk kerjasama Deposito dari PARA PIHAK untuk pembiayaan S-Finance yang saling
                                menguntungkan dengan saling menjaga etika bisnis dari para pihak serta dilakukan secara
                                profesional dan amanah
                            </p>
                        </div>

                        <!-- PASAL II -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL II<br>RUANG LINGKUP</h6>
                            <div class="mt-3" style="padding-left: 20px;">
                                <p class="mb-2">1. Dalam pelaksanaan perjanjian ini, PIHAK PERTAMA memberi Dana Deposito
                                    kepada PIHAK KEDUA sebesar {{ $kontrak['jumlah_investasi_angka'] }}
                                    ({{ $kontrak['jumlah_investasi_text'] }}) dan PIHAK KEDUA dengan ini menerima penyerahan
                                    Dana Deposito tersebut dari PIHAK PERTAMA serta menyanggupi sebagai penyalur,
                                    monitoring, dan penjamin dana Deposito</p>

                                <p class="mb-2">2. PIHAK KEDUA dengan ini berjanji dan mengikatkan diri untuk mengelola
                                    perputaran Dana Deposito secara khusus pada Usaha Pembiayaan di dibawah naungan
                                    S-Finance.</p>
                            </div>
                        </div>

                        <!-- PASAL III -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL III<br>JANGKA WAKTU KERJASAMA</h6>
                            <div class="mt-3" style="padding-left: 20px;">
                                <p class="mb-2">1. Perjanjian kerjasama ini berlaku sampai tanggal
                                    {{ $kontrak['tanggal_jatuh_tempo'] }} dan dapat diperpanjang dengan persetujuan PARA
                                    PIHAK dengan konfirmasi 2 minggu sebelum berakhir kontrak.
                                </p>

                                <p class="mb-2">2. Jangka waktu penutupan deposito adalah sampai
                                    {{ $kontrak['tanggal_jatuh_tempo'] }}. Jika deposito diambil sebelum masa waktunya, maka
                                    akan dikenakan penalti sebesar 1% dari nilai nominal deposito
                                </p>

                                <p class="mb-2">3. Persetujuan perpanjangan Perjanjian kerjasama yang dimaksudkan dapat
                                    dilakukan secara otomatis berdasarkan konfirmasi awal dari PIHAK PERTAMA kepada PIHAK
                                    KEDUA, atau Non Otomatis jika diperlukan adanya Keputusan Deposito dari PIHAK PERTAMA
                                    jika terdapat perubahan objek atau skema Deposito didalam kelolaan usaha PIHAK KETIGA
                                </p>
                            </div>
                        </div>

                        <!-- PASAL IV -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL IV<br>HAK DAN KEWAJIBAN PIHAK PERTAMA</h6>
                            <p class="mt-3">Dalam Perjanjian Kerjasama ini, PIHAK PERTAMA memiliki Hak dan Kewajiban sebagai
                                berikut:</p>
                            <div style="padding-left: 20px;">
                                <p class="mb-2">1. Memberikan Dana Deposito kepada PIHAK KEDUA sebesar
                                    {{ $kontrak['jumlah_investasi_angka'] }} ({{ $kontrak['jumlah_investasi_text'] }}) yang
                                    di tempatkan/ditransfer ke rekening S-Finance, dengan data sebagai berikut :<br>
                                    Nama pemilik rekening : PT. Synnovac Kapital Indonesia<br>
                                    Nama Bank : Mandiri<br>
                                    Nomor rekening : 1240010052851
                                </p>

                                <p class="mb-2">2. Berhak meminta kembali Dana Deposito yang telah diserahkan kepada PIHAK
                                    KEDUA dengan ketentuan berdasarkan Pasal III Ayat 2.</p>

                                <p class="mb-2">3. Menerima hasil keuntungan atas pengelolaan Dana Deposito dari PIHAK
                                    KEDUA, sesuai dengan Pasal VI perjanjian ini</p>

                                <p class="mb-2">4. Menerima hasil laporan pengelolaan dana Deposito dari PIHAK KEDUA secara
                                    periodic</p>

                                <p class="mb-2">5. PIHAK PERTAMA akan menerima bukti penerbitan dana Deposito pembiayaan
                                    dari PIHAK KEDUA setelah dana diterima dan pembiayaan ditempatkan.</p>
                            </div>
                        </div>

                        <!-- PASAL V -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL V<br>HAK DAN KEWAJIBAN PIHAK KEDUA</h6>
                            <p class="mt-3">Dalam Perjanjian Kerjasama Deposito ini, PIHAK KEDUA memiliki Hak dan Kewajiban
                                sebagai berikut :</p>
                            <div style="padding-left: 20px;">
                                <p class="mb-2">1. Menerima Dana Deposito dari PIHAK PERTAMA sebesar
                                    {{ $kontrak['jumlah_investasi_angka'] }} ({{ $kontrak['jumlah_investasi_text'] }}) yang
                                    ditempatkan di rekening S-Finance
                                </p>

                                <p class="mb-2">2. PIHAK KEDUA akan memberikan bukti penerbitan dana Deposito pembiayaan
                                    kepada PIHAK PERTAMA setelah dana diterima.</p>

                                <p class="mb-2">3. Menyalurkan, monitoring Dana Deposito PIHAK PERTAMA</p>

                                <p class="mb-2">4. Memberikan bagian hasil keuntungan kepada PIHAK PERTAMA, sesuai dengan
                                    Pasal VI perjanjian ini.</p>

                                <p class="mb-2">5. Memberikan hasil laporan pengelolaan Dana Deposito kepada PIHAK PERTAMA
                                    secara periodic</p>
                            </div>
                        </div>

                        <!-- PASAL VI -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL VI<br>PEMBAGIAN HASIL</h6>
                            <p class="mt-3">Dalam Perjanjian Kerjasama Deposito ini, PARA PIHAK sepakat didalam hal
                                pembagian hasil Deposito sebagai berikut :</p>
                            <div style="padding-left: 20px;">
                                <p class="mb-2">1. Bagi Hasil kepada PIHAK PERTAMA sebesar {{ $kontrak['bagi_hasil'] }} %
                                    per Tahun terhitung dari tanggal diterimanya dana oleh PIHAK KEDUA dan nilai bagi hasil
                                    akan diberikan dari PIHAK KEDUA di akhir periode kerjasama.</p>

                                <p class="mb-2">2. Jika dana masuk di atas tanggal 20, maka bagi hasil akan di hitung di
                                    bulan berikutnya.</p>

                                <p class="mb-2">3. Bagi hasil yang dimaksud berlaku sampai dengan PIHAK PERTAMA menarik
                                    kembali Dana Deposito yang telah diserahkan kepada PIHAK KEDUA atau masa berlaku
                                    Deposito sudah berakhir.</p>
                            </div>
                        </div>

                        <!-- PASAL VII -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-center">PASAL VII<br>KEADAAN MEMAKSA (FORCE MAJEUR)</h6>
                            <div class="mt-3" style="padding-left: 20px;">
                                <p class="mb-2">1. Yang termasuk dalam Force Majeur adalah akibat dari kejadian-kejadian
                                    diluar kuasa dan kehendak dari kedua belah pihak diantaranya termasuk tidak terbatas
                                    bencana alam, banjir, badai, topan, gempa bumi, kebakaran, perang, huru-hara,
                                    pemberontakan, demonstrasi, pemogokan, kegagalan Deposito.</p>

                                <p class="mb-2">2. Jika dalam pelaksanaan perjanjian ini terhambat ataupun tertunda baik
                                    secara keseluruhan ataupun sebagian yang dikarenakan hal-hal tersebut dalam ayat 1
                                    diatas, maka PIHAK KEDUA bersedia mengganti sejumlah Dana Deposito dari PIHAK PERTAMA
                                    secara penuh apabila belum ada pembagian hasil keuntungan, atau pengembalian Dana
                                    Deposito dikurangi dengan pembagian hasil yang sudah terima oleh PIHAK PERTAMA.</p>

                                <p class="mb-2">3. Pengembalian Dana Deposito sebagaimana tersebut dalam ayat 2, mengenai
                                    tata cara pengembaliannya akan diadakan musyawarah terlebih dahulu antara PIHAK PERTAMA
                                    dan PIHAK KEDUA mengenai proses atau jangka waktu pengembaliannya.</p>
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

                                <p class="mb-2">2. Pihak yang merasa dirugikan atas tindakan Wanprestasi tersebut dalam ayat
                                    1 diatas, berhak meminta ganti kerugian dari pihak yang melakukan wanprestasi tersebut
                                    atas sejumlah kerugian yang dideritanya, kecuali dalam hal kerugian tersebut disebabkan
                                    karena adanya suatu keadaan memaksa, seperti tercantum dalam Pasal VII</p>
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

                        <p class="text-start mb-5">Jakarta, {{ $kontrak['tanggal_kontrak'] }}</p>

                        <!-- Tanda Tangan -->
                        <div class="row mt-5">
                            <div class="col-6 text-center">
                                <p class="mb-5"><strong>PIHAK PERTAMA</strong></p>
                                @if($kontrak['tanda_tangan_investor'])
                                    <img src="{{ asset('storage/' . $kontrak['tanda_tangan_investor']) }}" alt="TTD Investor"
                                        style="max-width: 150px; max-height: 80px;" class="mb-3">
                                @else
                                    <div style="height: 80px;" class="mb-3"></div>
                                @endif
                                <p class="mb-0"><strong>{{ $kontrak['nama_investor'] }}</strong></p>
                                <p class="mb-0">{{ $kontrak['perusahaan_investor'] }}</p>
                            </div>
                            <div class="col-6 text-center">
                                <p class="mb-5"><strong>PIHAK KEDUA</strong></p>
                                <div
                                    style="position: relative; display: inline-block; width: 150px; height: 80px; margin-bottom: 1rem; background-image: url('{{ asset('assets/img/image.png') }}'); background-size: contain; background-repeat: no-repeat; background-position: center;">
                                    <img src="{{ asset('assets/img/ttd.png') }}" alt="TTD CEO"
                                        style="position: absolute; top: 0; left: 0; width: 150px; height: 80px; object-fit: contain; z-index: 2; mix-blend-mode: multiply;"
                                        onload="this.style.opacity='1'" onerror="this.style.opacity='1'">
                                </div>
                                <p class="mb-0"><strong>Muhamad Kurniawan</strong></p>
                                <p class="mb-0">CEO PT. Synnovac Kapital Indonesia</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Button Generate PDF -->
            <div class="mt-3 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-danger" id="btnGeneratePDF">
                    <i class="ti ti-file-type-pdf me-2"></i>
                    Generate PDF
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#btnGeneratePDF').on('click', function () {
                const $btn = $(this);
                const originalText = $btn.html();
                const investasiId = '{{ $kontrak['id_investasi'] }}';

                // Show loading state
                $btn.prop('disabled', true);
                $btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Generating PDF...');

                // Direct download via GET request (simple redirect)
                const downloadUrl = `/pengajuan-investasi/${investasiId}/download-kontrak`;
                window.location.href = downloadUrl;

                // Reset button after delay
                setTimeout(function() {
                    $btn.prop('disabled', false);
                    $btn.html(originalText);
                    
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'PDF kontrak berhasil diunduh.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }, 2000);
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .kontrak-content p {
            margin-bottom: 0.5rem;
        }

        .kontrak-content table {
            margin-bottom: 1rem;
        }

        @media print {

            .btn,
            .mb-3:first-child {
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
    </style>
@endpush