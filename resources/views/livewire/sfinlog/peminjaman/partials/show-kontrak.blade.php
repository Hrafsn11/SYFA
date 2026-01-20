@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="d-flex justify-content-end mb-3 no-print">
                <a href="{{ url('sfinlog/peminjaman/download-kontrak/' . $peminjaman->getKey()) }}" target="_blank"
                    class="btn btn-primary">
                    <i class="ti ti-file-download me-2"></i> Download PDF
                </a>
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

                        @include('livewire.sfinlog.peminjaman.partials.show-kontrak-content')

                        <div class="row pt-2 page-break-inside-avoid">
                            <div class="col-6 text-center">
                                <p class="mb-0"><strong>Kreditur</strong></p>
                                <p class="mb-0">CEO S-Finlog</p>

                                <div
                                    style="position: relative; display: inline-block; width: 150px; height: 80px; margin-bottom: 1rem;">
                                    <img src="{{ asset('assets/img/image.png') }}" alt="Logo"
                                        style="position: absolute; top: -25px; left: -10px; width: 150px; height: 80px; object-fit: contain; z-index: 1; opacity: 1;">
                                    <img src="{{ asset('assets/img/TTD-CEO-FINLOG.png') }}" alt="TTD CEO S-Finlog"
                                        style="position: absolute; top: 0; left: 0; width: 150px; height: 80px; object-fit: contain; z-index: 2; mix-blend-mode: multiply;"
                                        onload="this.style.opacity='1'" onerror="this.style.opacity='1'">
                                </div>
                                <p class="mb-0"><strong>Rafi Ghani Razak</strong></p>
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
