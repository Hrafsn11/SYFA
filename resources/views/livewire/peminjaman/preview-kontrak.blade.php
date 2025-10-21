@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Button Kembali -->
            <div class="mb-3">
                <a href="{{ route('peminjaman.detail', $kontrak['id_invoice_financing']) }}" class="btn btn-outline-primary">
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
                <div class="card-body p-4 p-md-5">
                    <!-- Header Kontrak -->
                    <div class="text-center mb-4 pb-3">
                        <h5 class="fw-bold mb-1">{{ $kontrak['nama_perusahaan'] }}</h5>
                    </div>

                    <hr class="my-4">

                    <!-- Judul Kontrak -->
                    <div class="text-center mb-5">
                        <h4 class="fw-bold mb-2">FINANCING CONTRACT</h4>
                        <h6 class="text-primary">No: SSP3026092025</h6>
                    </div>

                    <!-- Isi Kontrak -->
                    <div class="kontrak-content">
                        <p class="mb-4">
                            Yang bertandatangan dibawah ini:
                        </p>

                        <!-- Pihak Pertama -->
                        <div class="mb-4">
                            <p class="fw-bold mb-3">I. {{ $kontrak['nama_perusahaan'] }}</p>
                            <p class="text-justify lh-lg">
                                suatu perusahaan yang mengelola treasury serta memberikan pelayanan private equity, yang
                                berkedudukan di Bandung, beralamat di PermataKuningan Building 17th Floor, Kawasan
                                Epicentrum, HR Rasuna Said, Jl. Kuningan Mulia, RT.6/RW.1, Menteng Atas, Setiabudi, South
                                Jakarta City, Jakarta12920 (“Kreditur”) dalam hal ini diwakili oleh S-FINANCE berkedudukan
                                di Jakarta sebagai Pengelola Fasilitas yang menyalurkan dan mengelola transaksi-transaksi
                                terkait Fasilitas Pembiayaan yang bertindak sebagai kuasa (selanjutnya disebut “Perseroan”),
                                dan
                            </p>
                        </div>

                        <!-- Pihak Kedua -->
                        <div class="mb-5">
                            <p class="fw-bold mb-3">II. Debitur, sebagaimana dimaksud dalam Struktur dan Kontrak Pembiayaan
                                ini</p>
                            <p class="text-justify lh-lg">
                                Dengan ini sepakat untuk menetapkan hal-hal pokok, yang selanjutnya akan disebut sebagai
                                “Struktur dan Kontrak Pembiayaan” sehubungan dengan Perjanjian Pembiayaan Project Dengan
                                Cara Pencairan Dengan Pembayaran Secara Angsuran atau Kontan ini (selanjutnya disebut
                                sebagai “Perjanjian”), sebagai berikut:
                            </p>
                        </div>

                        <!-- Data Pembiayaan -->
                        <div class="mb-5">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td width="5%" class="py-2 align-top">1.</td>
                                        <td width="35%" class="py-2 align-top">Jenis Pembiayaan</td>
                                        <td width="60%" class="py-2 align-top">: {{ $kontrak['jenis_pembiayaan'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 align-top">2.</td>
                                        <td class="py-2 align-top fw-semibold">Debitur</td>
                                        <td class="py-2 align-top"></td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 align-top"></td>
                                        <td class="py-2 align-top ps-3">a. Nama Perusahaan</td>
                                        <td class="py-2 align-top">: {{ $kontrak['nama_debitur'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 align-top"></td>
                                        <td class="py-2 align-top ps-3">b. Nama Pimpinan</td>
                                        <td class="py-2 align-top">: {{ $kontrak['nama_pimpinan'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 align-top"></td>
                                        <td class="py-2 align-top ps-3">c. Alamat Perusahaan</td>
                                        <td class="py-2 align-top">: {{ $kontrak['alamat'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 align-top"></td>
                                        <td class="py-2 align-top ps-3">d. Tujuan Pembiayaan</td>
                                        <td class="py-2 align-top">: {{ $kontrak['tujuan_pembiayaan'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 align-top">3.</td>
                                        <td class="py-3 align-top fw-semibold">Detail Pembiayaan</td>
                                        <td class="py-3 align-top"></td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 align-top"></td>
                                        <td class="py-2 align-top ps-3">a. Nilai Pembiayaan</td>
                                        <td class="py-2 align-top fw-semibold">:
                                            {{ $kontrak['nilai_pembiayaan'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 align-top"></td>
                                        <td class="py-2 align-top ps-3">b. Hutang Pokok</td>
                                        <td class="py-2 align-top fw-semibold">: {{ $kontrak['hutang_pokok'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 align-top">4.</td>
                                        <td class="py-2 align-top">Tenor Pembiayaan</td>
                                        <td class="py-2 align-top">: {{ $kontrak['tenor'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 align-top">5.</td>
                                        <td class="py-2 align-top">Biaya Administrasi</td>
                                        <td class="py-2 align-top">: {{ $kontrak['biaya_admin'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 align-top">6.</td>
                                        <td class="py-2 align-top">Bagi Hasil (Nisbah)</td>
                                        <td class="py-2 align-top">: {{ $kontrak['nisbah'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 align-top">7.</td>
                                        <td class="py-2 align-top">Denda Keterlambatan</td>
                                        <td class="py-2 align-top">: {{ $kontrak['denda_keterlambatan'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 align-top">8.</td>
                                        <td class="py-2 align-top">Jaminan</td>
                                        <td class="py-2 align-top">: {{ $kontrak['jaminan'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 align-top">9.</td>
                                        <td class="py-2 align-top">Metode Pembiayaan</td>
                                        <td class="py-2 align-top">: Transfer</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Penutup Kontrak -->
                        <div class="mb-5">
                            <p class="fw-bold mb-3">Penutup</p>
                            <p class="text-justify lh-lg">
                                “Bahwa dengan menerima pembiayaan tersebut bersamaan dengan tanda tangan kami, maka segala
                                tanggung jawab pengembalian pembiayaan akan kami tepati sesuai dengan plan paid yang telah
                                kami buat sendiri yang tertera pada tabel diatas. Apabila terdapat keterlambatan pembayaran
                                kami bersedia untuk dikenakan denda penalti hingga sanksi tidak dapat mengakses pembiayaan
                                apapun yang terafiliasi denganS Finance sebelumtanggung jawab pelunasan hutang terlebih
                                dahulu kami selesaikan”
                            </p>
                        </div>

                        <!-- Tanggal Kontrak -->
                        <div class="text-start mb-5">
                            <p class="mb-0 text-muted">Jakarta, {{ $kontrak['tanggal_kontrak'] }}</p>
                        </div>

                        <!-- Area Tanda Tangan -->
                        <div class="row mt-5 pt-4">
                            <div class="col-md-6 text-center mb-4">
                                <p class="fw-bold mb-1">Kreditur</p>
                                <p class="mb-0 small">{{ $kontrak['nama_perusahaan'] }}</p>
                                
                                <!-- Placeholder untuk tanda tangan -->
                                <div class="my-5 py-4">
                                    <div class="border-bottom border-2 d-inline-block" style="width: 200px;"></div>
                                </div>
                                
                                <p class="text-muted small">Director</p>
                            </div>
                            <div class="col-md-6 text-center mb-4">
                                <p class="fw-bold mb-1">Debitur</p>
                                
                                <!-- Placeholder untuk tanda tangan -->
                                <div class="my-5 py-4">
                                    <div class="border-bottom border-2 d-inline-block" style="width: 200px;"></div>
                                </div>
                                
                                <p class="text-muted small">Pimpinan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Button Generate PDF -->
            <div class="mt-3 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-primary" id="btnGeneratePDF">
                    <i class="ti ti-download me-2"></i>
                    Generate PDF
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#btnGeneratePDF').on('click', function() {
                const $btn = $(this);
                const originalText = $btn.html();

                // Show loading state
                $btn.prop('disabled', true);
                $btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Generating PDF...');

                setTimeout(() => {
                    // Reset button
                    $btn.prop('disabled', false);
                    $btn.html(originalText);

                    // Success message
                    console.log('PDF Generated!');

                    // window.open('/peminjaman/{{ $kontrak['id_invoice_financing'] }}/download-kontrak', '_blank');
                }, 2000);
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .kontrak-content {
            font-size: 14px;
            line-height: 1.6;
        }

        .kontrak-content .lh-lg {
            line-height: 1.9 !important;
        }

        .table td {
            padding: 0.5rem 0.75rem;
            vertical-align: top;
            font-size: 14px;
        }

        .table .fw-semibold {
            font-weight: 600;
        }

        @media print {
            .btn {
                display: none;
            }

            .card {
                border: none;
                box-shadow: none;
            }

            .kontrak-content {
                page-break-inside: avoid;
            }

            .alert {
                display: none;
            }
        }
    </style>
@endpush
