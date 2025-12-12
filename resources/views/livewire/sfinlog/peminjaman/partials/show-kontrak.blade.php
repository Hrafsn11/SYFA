@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="d-flex justify-content-end mb-3 no-print">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="ti ti-printer me-2"></i> Cetak Dokumen
                </button>
            </div>

            <div class="card shadow-sm border-0 document-card">
                <div class="card-body p-4 p-md-5">

                    <div class="px-md-5 mx-xl-5">

                        <div class="text-center mb-5">
                            <img src="{{ asset('assets/img/branding/Logo.jpg') }}" alt="Logo" style="height: 70px;"
                                class="mb-3">
                            <h4 class="fw-bold text-uppercase text-dark mb-1" style="letter-spacing: 1px;">Financing
                                Contract</h4>
                            <p class="text-muted fw-bold mb-0">No: {{ $data['nomor_kontrak'] }}</p>
                        </div>

                        <div class="legal-text mb-4">
                            <p class="mb-3">Yang bertanda tangan dibawah ini:</p>

                            <div class="d-flex mb-3">
                                <div style="min-width: 30px;">I.</div>
                                <div class="text-justify">
                                    <strong>PT SYNNOVAC KAPITAL INDONESIA</strong> suatu perusahaan yang mengelola
                                    <em>treasury</em> serta memberikan pelayanan <em>private equity</em>, yang beralamat di
                                    Permata Kuningan Building 17th Floor, Kawasan Epicentrum, HR Rasuna Said, Jl. Kuningan
                                    Mulia, RT.6/RW.1, Menteng Atas, Setiabudi, South Jakarta City, Jakarta 12920
                                    (“<strong>Kreditur</strong>”) dalam hal ini diwakili oleh <strong>S-FINLOG</strong>
                                    berkedudukan di Jakarta sebagai Pengelola Fasilitas yang menyalurkan dan mengelola
                                    transaksi-transaksi terkait Fasilitas Pembiayaan yang bertindak sebagai kuasa
                                    (selanjutnya disebut “<strong>Perseroan</strong>”), dan
                                </div>
                            </div>

                            <div class="d-flex mb-3">
                                <div style="min-width: 30px;">II.</div>
                                <div class="text-justify">
                                    <strong>{{ strtoupper($data['nama_principal']) }}</strong> selaku Principal, suatu
                                    perusahaan yang berkedudukan di <strong>{{ $data['alamat_principal'] }}</strong> dan
                                    bergerak di bidang <strong>{{ $data['deskripsi_bidang'] }}</strong> dan bertindak sebagai pihak yang
                                    bertanggung jawab atas pengelolaan Debitur-Debitur (atau Client) yang secara tidak
                                    langsung dibiayai oleh S-FINLOG.
                                </div>
                            </div>

                            <p class="text-justify mt-4">
                                Dengan ini sepakat untuk menetapkan hal-hal pokok, yang selanjutnya akan disebut sebagai
                                “<strong>Struktur dan Kontrak Pembiayaan</strong>” sehubungan dengan Perjanjian Pembiayaan
                                Project Dengan Cara Pencairan Dengan Pembayaran Secara Angsuran atau Kontan ini (selanjutnya
                                disebut sebagai “<strong>Perjanjian</strong>”), sebagai berikut:
                            </p>
                        </div>

                        <div class="table-responsive mb-5">
                            <table class="table table-borderless align-top legal-table">
                                <tbody>
                                    <tr>
                                        <td width="5%">1.</td>
                                        <td width="30%">Jenis Pembiayaan</td>
                                        <td width="2%">:</td>
                                        <td class="fw-bold">Pembiayaan Project</td>
                                    </tr>

                                    <tr>
                                        <td>2.</td>
                                        <td colspan="3" class="fw-bold text-decoration-underline">Principal</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td class="ps-3">a. Nama Principal</td>
                                        <td>:</td>
                                        <td>{{ $data['nama_principal'] }}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td class="ps-3">b. Nama PIC</td>
                                        <td>:</td>
                                        <td>{{ $data['nama_pic'] }}</td>
                                    </tr>

                                    <tr>
                                        <td>3.</td>
                                        <td colspan="3" class="fw-bold text-decoration-underline">Debitur / Client</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td class="ps-3">a. Nama Perusahaan</td>
                                        <td>:</td>
                                        <td>{{ $data['nama_perusahaan'] }}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td class="ps-3">b. Nama CEO</td>
                                        <td>:</td>
                                        <td>{{ $data['nama_ceo'] }}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td class="ps-3">c. Alamat Perusahaan</td>
                                        <td>:</td>
                                        <td class="text-justify">{{ $data['alamat_perusahaan'] }}</td>
                                    </tr>

                                    <tr>
                                        <td>4.</td>
                                        <td>Tujuan Pembiayaan</td>
                                        <td>:</td>
                                        <td class="text-justify">Pembiayaan Project - {{ $data['nama_perusahaan'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>5.</td>
                                        <td>Tenor Pembiayaan</td>
                                        <td>:</td>
                                        <td><strong>{{ $data['tenor_pembiayaan'] }} Hari</strong></td>
                                    </tr>
                                    <tr>
                                        <td>6.</td>
                                        <td>Biaya Administrasi</td>
                                        <td>:</td>
                                        <td><strong>Rp {{ number_format($data['biaya_administrasi'], 0, ',', '.') }}
                                            ({{ ucwords(\App\Helpers\Terbilang::convert($data['biaya_administrasi'])) }} Rupiah)</strong></td>
                                    </tr>
                                    <tr>
                                        <td>7.</td>
                                        <td>Bagi Hasil (Nisbah)</td>
                                        <td>:</td>
                                        <td>{{ $data['persentase_bagi_hasil'] }}% flat setiap pencairan</td>
                                    </tr>
                                    <tr>
                                        <td>8.</td>
                                        <td>Denda Keterlambatan</td>
                                        <td>:</td>
                                        <td class="text-justify">¼ (seperempat) dari nilai bagi hasil yang seharusnya
                                            diterima oleh S-Finlog untuk setiap minggu keterlambatan.</td>
                                    </tr>
                                    <tr>
                                        <td>9.</td>
                                        <td>Jaminan</td>
                                        <td>:</td>
                                        <td>{{ $data['jaminan'] }}</td>
                                    </tr>

                                    <tr>
                                        <td>10.</td>
                                        <td>Metode Pembayaran</td>
                                        <td>:</td>
                                        <td class="text-justify">
                                            <div class="d-flex mb-2">
                                                <span class="me-2">a.</span>
                                                <span>Principal wajib menagih ke Debitur menggunakan invoice yang tercantum
                                                    nomor rekening Principal untuk tujuan pembayarannya maksimal
                                                    <strong>{{ $data['tenor_pembiayaan'] }} hari kalender</strong> sejak
                                                    pencairan di lakukan oleh S-FINLOG.</span>
                                            </div>
                                            <div class="d-flex">
                                                <span class="me-2">b.</span>
                                                <span>Atas pembayaran yang dilakukan oleh Debitur, maka Principal akan
                                                    membayarkan pokok Hutang dan nilai Bagi Hasil kepada S-FINLOG,
                                                    selebihnya untuk Principal.</span>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>11.</td>
                                        <td>Pernyataan Principal</td>
                                        <td>:</td>
                                        <td>
                                            <div class="p-3">
                                                <em class="text-justify d-block" style="line-height: 1.6;">
                                                    “Bahwa dengan menerima limit pembiayaan bagi Debitur tersebut bersamaan
                                                    dengan tanda tangan kami selaku Principal, maka segala tanggung jawab
                                                    pengembalian pembiayaan akan kami tepati sesuai dengan paid plan yang
                                                    telah kami buat sendiri yang tertera pada data di atas. Apabila terdapat
                                                    keterlambatan pembayaran kami bersedia untuk dikenakan denda penalti
                                                    hingga sanksi atas Debitur tersebut tidak dapat mengakses pembiayaan
                                                    apapun yang terafiliasi dengan S-Finlog sebelum tanggung jawab pelunasan
                                                    hutang terlebih dahulu kami selesaikan”
                                                </em>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-4 mt-5">
                            <p class="mb-0">Jakarta,
                                {{ \Carbon\Carbon::parse($data['tanggal_kontrak'])->locale('id')->isoFormat('D MMMM YYYY') }}
                            </p>
                        </div>

                        <div class="row pt-2 page-break-inside-avoid">
                            <div class="col-6 text-center">
                                <p class="mb-0"><strong>Kreditur</strong></p>
                                <p class="mb-0">CEO S-Finlog</p>

                                <div style="position: relative; display: inline-block; width: 150px; height: 80px; margin-bottom: 1rem;">
                                    <img src="{{ asset('assets/img/image.png') }}" alt="Logo"
                                        style="position: absolute; top: -25px; left: -10px; width: 150px; height: 80px; object-fit: contain; z-index: 1; opacity: 1;">
                                    <img src="{{ asset('assets/img/TTD-CEO-FINLOG.png') }}" alt="TTD CEO S-Finlog"
                                        style="position: absolute; top: 0; left: 0; width: 150px; height: 80px; object-fit: contain; z-index: 2; mix-blend-mode: multiply;"
                                        onload="this.style.opacity='1'" onerror="this.style.opacity='1'">
                                </div>
                                <p class="mb-0"><strong>Aditia Pratama</strong></p>
                            </div>

                            <div class="col-6 text-center">
                                <p class="mb-0"><strong>Principal</strong></p>
                                <p class="mb-5">{{ $data['nama_principal'] }}</p>
                                @if ($peminjaman->debitur && $peminjaman->debitur->tanda_tangan)
                                    <img src="{{ asset('storage/' . $peminjaman->debitur->tanda_tangan) }}"
                                        alt="TTD Principal" style="max-width: 150px; max-height: 80px;" class="mb-3">
                                @else
                                    <div style="height: 80px;" class="mb-3"></div>
                                @endif
                                <p class="mb-0"><strong>{{ $data['nama_pic'] }}</strong></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* CSS Umum */
        .text-justify {
            text-align: justify;
            text-justify: inter-word;
        }

        .legal-table tr td {
            padding-bottom: 0.8rem;
            color: #333;
        }

        /* CSS Khusus Print */
        @media print {

            /* Sembunyikan elemen non-cetak */
            .no-print {
                display: none !important;
            }

            /* Reset Background & Warna */
            body {
                background-color: #fff !important;
                color: #000 !important;
            }

            /* Reset Card Styles */
            .document-card {
                box-shadow: none !important;
                border: none !important;
            }

            .card-body {
                padding: 0 !important;
            }

            /* Reset Margin/Padding Wrapper agar pas di kertas */
            .px-md-5,
            .mx-xl-5 {
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Paksa background color (misal untuk kolom pernyataan) tercetak */
            .bg-light {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
            }

            /* Mencegah Tanda Tangan terpotong halaman */
            .page-break-inside-avoid {
                page-break-inside: avoid;
            }

            /* Set ukuran kertas */
            @page {
                size: A4;
                margin: 2.5cm 2cm;
            }
        }
    </style>
@endsection
