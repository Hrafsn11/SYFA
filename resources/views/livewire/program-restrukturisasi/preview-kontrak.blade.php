@extends('layouts.app')

@section('content')
    @php
        use Carbon\Carbon;

        $pengajuan = $program->pengajuanRestrukturisasi;
        $debitur = $pengajuan->debitur;

        $dataKontrak = [
            'nomor_kontrak' => $program->nomor_kontrak_restrukturisasi ?? $previewNomor ?? 'XXX-00-00000000',
            'jenis_restrukturisasi' => $pengajuan->jenis_restrukturisasi ?? [],
            'nama_perusahaan' => $pengajuan->nama_perusahaan ?? '-',
            'nama_pimpinan' => $pengajuan->nama_pic ?? ($debitur->nama_ceo ?? '-'),
            'alamat_perusahaan' => $pengajuan->alamat_kantor ?? ($debitur->alamat ?? '-'),
            'tujuan_restrukturisasi' => $pengajuan->alasan_restrukturisasi ?? '-',
            'nilai_plafon_awal' => $pengajuan->jumlah_plafon_awal ?? 0,
            'nilai_plafon_pembiayaan' => $program->plafon_pembiayaan ?? 0,
            'metode_perhitungan' => $program->metode_perhitungan ?? '-',
            'jangka_waktu' => $program->jangka_waktu_total ?? 0,
            'tanggal_mulai_cicilan' => $program->tanggal_mulai_cicilan,
            'suku_bunga' => $program->suku_bunga_per_tahun ?? 0,
            'masa_tenggang' => $program->masa_tenggang ?? 0,
            'jaminan' => $program->jaminan ?? '-',
            'nama_ceo_kreditur' => 'Muhamad Kurniawan',
            'tanggal_kontrak' => $program->kontrak_generated_at ?? Carbon::now(),
            'hari' => Carbon::parse($program->kontrak_generated_at ?? Carbon::now())->locale('id')->translatedFormat('l'),
            'tanggal_kontrak_formatted' => Carbon::parse($program->kontrak_generated_at ?? Carbon::now())->locale('id')->translatedFormat('d F Y'),
            'tanda_tangan_debitur' => $debitur->tanda_tangan ?? null,
        ];
    @endphp

    <div class="row">
        <div class="col-12">

            <!-- Button Kembali -->
            <div class="mb-3">
                <a href="{{ route('program-restrukturisasi.show', $program->id_program_restrukturisasi) }}"
                    class="btn btn-outline-primary">
                    <i class="ti ti-arrow-left me-2"></i>
                    Kembali
                </a>
            </div>

            <!-- Judul Halaman -->
            <div class="mb-4">
                <h4 class="fw-bold mb-1">Preview Kontrak Restrukturisasi</h4>
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
                        <h5 class="fw-bold mb-2">SURAT PERJANJIAN RESTRUKTURISASI PEMBIAYAAN</h5>
                        <p class="mb-0"><strong>No: {{ $dataKontrak['nomor_kontrak'] }}</strong></p>
                    </div>

                    <br><br>

                    <!-- Pembukaan -->
                    <div class="kontrak-content" style="text-align: justify; line-height: 1.8;">
                        <p class="mb-4">
                            Pada hari ini <strong>{{ $dataKontrak['hari'] }}</strong>, tanggal
                            <strong>{{ $dataKontrak['tanggal_kontrak_formatted'] }}</strong>, yang bertanda tangan di bawah
                            ini:
                        </p>

                        <!-- Pihak Pertama (Kreditur) -->
                        <div class="mb-4">
                            <table style="width: 100%; border: none;">
                                <tbody>
                                    <tr>
                                        <td width="5%" style="vertical-align: top; padding: 2px 0;">I.</td>
                                        <td colspan="2" style="vertical-align: top; padding: 2px 0;">
                                            <strong>PT SYNNOVAC KAPITAL INDONESIA</strong> suatu perusahaan yang mengelola
                                            treasury serta
                                            memberikan pelayanan private equity, yang berkedudukan di Jakarta, beralamat di
                                            Permata Kuningan
                                            Building 17th Floor, Kawasan Epicentrum, HR Rasuna Said, Jl. Kuningan Mulia,
                                            RT.6/RW.1, Menteng
                                            Atas, Setiabudi, South Jakarta City, Jakarta 12920 ("<strong>Kreditur</strong>")
                                            dalam hal ini
                                            diwakili oleh S-FINANCE berkedudukan di Jakarta sebagai Pengelola Fasilitas yang
                                            menyalurkan dan
                                            mengelola transaksi-transaksi terkait Fasilitas Pembiayaan yang bertindak
                                            sebagai kuasa (selanjutnya
                                            disebut "<strong>Perseroan</strong>"), dan
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pihak Kedua (Debitur) -->
                        <div class="mb-4">
                            <table style="width: 100%; border: none;">
                                <tbody>
                                    <tr>
                                        <td width="5%" style="vertical-align: top; padding: 2px 0;">II.</td>
                                        <td width="25%" style="vertical-align: top; padding: 2px 0;">Nama Perusahaan</td>
                                        <td width="70%" style="vertical-align: top; padding: 2px 0;">:
                                            {{ $dataKontrak['nama_perusahaan'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 2px 0;"></td>
                                        <td style="vertical-align: top; padding: 2px 0;">Nama Pimpinan</td>
                                        <td style="vertical-align: top; padding: 2px 0;">:
                                            {{ $dataKontrak['nama_pimpinan'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 2px 0;"></td>
                                        <td style="vertical-align: top; padding: 2px 0;">Alamat</td>
                                        <td style="vertical-align: top; padding: 2px 0;">:
                                            {{ $dataKontrak['alamat_perusahaan'] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="mt-2">Untuk selanjutnya disebut sebagai <strong>DEBITUR</strong>.</p>
                        </div>

                        <br>

                        <!-- Kesepakatan -->
                        <p class="mb-3">
                            Dengan ini sepakat untuk menetapkan hal-hal pokok, yang selanjutnya akan disebut sebagai
                            "<strong>Struktur
                                dan Kontrak Restrukturisasi</strong>" sehubungan dengan Perjanjian Restrukturisasi
                            Pembiayaan Dengan
                            Pembayaran Secara Angsuran ini (selanjutnya disebut sebagai "<strong>Perjanjian</strong>"),
                            sebagai berikut:
                        </p>

                        <br>

                        <!-- Detail Kontrak -->
                        <div class="mb-4">
                            <table style="width: 100%; border: none;">
                                <tbody>
                                    <tr>
                                        <td width="5%" style="vertical-align: top; padding: 5px 0;">1.</td>
                                        <td width="35%" style="vertical-align: top; padding: 5px 0;">Jenis Restrukturisasi
                                        </td>
                                        <td width="5%" style="vertical-align: top; padding: 5px 0;">:</td>
                                        <td style="vertical-align: top; padding: 5px 0;">
                                            @if(is_array($dataKontrak['jenis_restrukturisasi']) && count($dataKontrak['jenis_restrukturisasi']) > 0)
                                                {{ implode(', ', $dataKontrak['jenis_restrukturisasi']) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 5px 0;">2.</td>
                                        <td style="vertical-align: top; padding: 5px 0;">Tujuan Restrukturisasi</td>
                                        <td style="vertical-align: top; padding: 5px 0;">:</td>
                                        <td style="vertical-align: top; padding: 5px 0;">
                                            {{ $dataKontrak['tujuan_restrukturisasi'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 5px 0;">3.</td>
                                        <td style="vertical-align: top; padding: 5px 0;">Nilai Plafon Awal</td>
                                        <td style="vertical-align: top; padding: 5px 0;">:</td>
                                        <td style="vertical-align: top; padding: 5px 0;">Rp.
                                            {{ number_format($dataKontrak['nilai_plafon_awal'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 5px 0;">4.</td>
                                        <td style="vertical-align: top; padding: 5px 0;">Nilai Plafon Pembiayaan</td>
                                        <td style="vertical-align: top; padding: 5px 0;">:</td>
                                        <td style="vertical-align: top; padding: 5px 0;">Rp.
                                            {{ number_format($dataKontrak['nilai_plafon_pembiayaan'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 5px 0;">5.</td>
                                        <td style="vertical-align: top; padding: 5px 0;">Metode Perhitungan</td>
                                        <td style="vertical-align: top; padding: 5px 0;">:</td>
                                        <td style="vertical-align: top; padding: 5px 0;">
                                            {{ $dataKontrak['metode_perhitungan'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 5px 0;">6.</td>
                                        <td style="vertical-align: top; padding: 5px 0;">Jangka Waktu</td>
                                        <td style="vertical-align: top; padding: 5px 0;">:</td>
                                        <td style="vertical-align: top; padding: 5px 0;">{{ $dataKontrak['jangka_waktu'] }}
                                            bulan</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 5px 0;">7.</td>
                                        <td style="vertical-align: top; padding: 5px 0;">Tanggal Mulai Cicilan</td>
                                        <td style="vertical-align: top; padding: 5px 0;">:</td>
                                        <td style="vertical-align: top; padding: 5px 0;">
                                            {{ $dataKontrak['tanggal_mulai_cicilan'] ? Carbon::parse($dataKontrak['tanggal_mulai_cicilan'])->format('d F Y') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 5px 0;">8.</td>
                                        <td style="vertical-align: top; padding: 5px 0;">Bagi Hasil / Suku Bunga</td>
                                        <td style="vertical-align: top; padding: 5px 0;">:</td>
                                        <td style="vertical-align: top; padding: 5px 0;">
                                            {{ number_format($dataKontrak['suku_bunga'], 2) }}% per tahun</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 5px 0;">9.</td>
                                        <td style="vertical-align: top; padding: 5px 0;">Masa Tenggang</td>
                                        <td style="vertical-align: top; padding: 5px 0;">:</td>
                                        <td style="vertical-align: top; padding: 5px 0;">{{ $dataKontrak['masa_tenggang'] }}
                                            bulan</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding: 5px 0;">10.</td>
                                        <td style="vertical-align: top; padding: 5px 0;">Jaminan</td>
                                        <td style="vertical-align: top; padding: 5px 0;">:</td>
                                        <td style="vertical-align: top; padding: 5px 0;">{{ $dataKontrak['jaminan'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <br>

                        <!-- Metode Pembayaran -->
                        <div class="mb-4">
                            <h6 class="fw-bold">Metode Pembayaran</h6>
                            <div style="padding-left: 20px;">
                                <p class="mb-2">a. Debitur wajib untuk membayar hutang Restrukturisasi sesuai dengan tanggal
                                    kesepakatan jatuh tempo
                                    yang terdiri dari pokok hutang ditambahkan dengan bagi hasil.</p>
                                <p class="mb-2">b. Pembayaran hutang oleh Debitur dilakukan dengan cara transfer ke rekening
                                    bank Mandiri
                                    <strong>1240010052851</strong> AN <strong>PT Synnovac Kapital Indonesia</strong> dengan
                                    memberikan
                                    remark nomor kontrak pembiayaan.
                                </p>
                            </div>
                        </div>

                        <!-- Pernyataan Debitur -->
                        <div class="mb-4">
                            <h6 class="fw-bold">11. Pernyataan Debitur</h6>
                            <div class="p-3" style="background: #f9f9f9; border: 1px solid #ddd; font-style: italic;">
                                "Bahwa dengan menerima Restrukturisasi tersebut bersamaan dengan tanda tangan kami, maka
                                segala tanggung
                                jawab pengembalian pembiayaan akan kami tepati sesuai dengan plan paid yang telah kami buat
                                sendiri yang
                                tertera pada tabel diatas. Apabila terdapat keterlambatan pembayaran kami bersedia untuk
                                dikenakan denda
                                penalti hingga sanksi tidak dapat mengakses pembiayaan apapun yang terafiliasi dengan S
                                Finance sebelum
                                tanggung jawab pelunasan hutang terlebih dahulu kami selesaikan"
                            </div>
                        </div>

                        <br><br>

                        <p class="text-start mb-5">Jakarta, {{ $dataKontrak['tanggal_kontrak_formatted'] }}</p>

                        <!-- Tanda Tangan -->
                        <div class="row mt-5">
                            <div class="col-6 text-center">
                                <p class="mb-5"><strong>KREDITUR</strong></p>
                                <p class="text-muted mb-2" style="font-size: 11px;">CEO PT. Synnovac Kapital Indonesia</p>
                                <div
                                    style="position: relative; display: inline-block; width: 150px; height: 80px; margin-bottom: 1rem; background-image: url('{{ asset('assets/img/image.png') }}'); background-size: contain; background-repeat: no-repeat; background-position: center;">
                                    <img src="{{ asset('assets/img/ttd.png') }}" alt="TTD CEO"
                                        style="position: absolute; top: 0; left: 0; width: 150px; height: 80px; object-fit: contain; z-index: 2; mix-blend-mode: multiply;"
                                        onload="this.style.opacity='1'" onerror="this.style.opacity='1'">
                                </div>
                                <p class="mb-0"><strong>{{ $dataKontrak['nama_ceo_kreditur'] }}</strong></p>
                            </div>
                            <div class="col-6 text-center">
                                <p class="mb-5"><strong>DEBITUR</strong></p>
                                <p class="text-muted mb-2" style="font-size: 11px;">CEO
                                    {{ $dataKontrak['nama_perusahaan'] }}</p>
                                @if($dataKontrak['tanda_tangan_debitur'])
                                    <div style="height: 80px; margin-bottom: 1rem;">
                                        <img src="{{ asset('storage/' . $dataKontrak['tanda_tangan_debitur']) }}"
                                            alt="TTD Debitur" style="max-width: 150px; max-height: 80px; object-fit: contain;">
                                    </div>
                                @else
                                    <div style="height: 80px;" class="mb-3"></div>
                                @endif
                                <p class="mb-0"><strong>{{ $dataKontrak['nama_pimpinan'] }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Button Generate PDF -->
            <div class="mt-3 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-danger" id="btnPrint">
                    <i class="ti ti-printer me-2"></i>
                    Cetak / Print
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#btnPrint').on('click', function () {
                window.print();
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
            .mb-3:first-child,
            nav,
            aside,
            .sidebar,
            .menu-vertical,
            .layout-menu {
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

            .layout-content-navbar .layout-page {
                padding-left: 0 !important;
            }

            @page {
                size: A4;
                margin: 2cm;
            }
        }
    </style>
@endpush