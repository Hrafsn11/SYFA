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
                <a href="{{ route('sfinlog.pengajuan-investasi.download-kontrak', $pengajuan->id_pengajuan_investasi_finlog) }}"
                    target="_blank" class="btn btn-primary">
                    <i class="ti ti-file-download me-2"></i>
                    Download PDF
                </a>
            </div>

            <!-- Card Preview Kontrak -->
            <div class="card shadow-sm border-0 document-card">
                <div class="card-body p-4 p-md-5" id="kontrak-content">
                    <div class="px-md-5 mx-xl-5">

                        <!-- Header dengan Logo -->
                        <div class="text-center mb-5">
                            <img src="{{ asset('assets/img/branding/Logo.jpg') }}" alt="S-Finlog Logo" style="height: 70px;"
                                class="mb-3">
                            <h4 class="fw-bold text-uppercase text-dark mb-1" style="letter-spacing: 1px;">SURAT PERJANJIAN
                                KERJASAMA INVESTASI PEMBIAYAAN USAHA</h4>
                            <p class="text-muted fw-bold mb-0">No: {{ $data['nomor_kontrak'] }}</p>
                        </div>

                        @include('livewire.sfinlog.pengajuan-investasi.partials.preview-kontrak-content')

                        <!-- Tanda Tangan -->
                        <div class="row mt-5 pt-4 page-break-inside-avoid">
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
                                <div
                                    style="position: relative; display: inline-block; width: 150px; height: 80px; margin-bottom: 1rem;">
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
        /* CSS Umum */
        .text-justify {
            text-align: justify;
            text-justify: inter-word;
        }

        .legal-table tr td {
            padding-bottom: 0.8rem;
            color: #333;
        }

        .kontrak-content p {
            margin-bottom: 0.8rem;
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
