@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <h4 class="fw-bold mb-1">Services</h4>
            <p class="text-muted mb-0">Silakan pilih aplikasi yang ingin Anda gunakan.</p>
        </div>
    </div>

    <div class="row g-4">
        {{-- SFinance --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column align-items-center text-center">
                    <span class="badge bg-label-success text-uppercase mb-3 px-3 py-1 fw-semibold small">Core</span>
                    <div class="mb-3">
                        <i class="ti ti-chart-pie-2 text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="mb-1 fw-semibold">SFinance</h5>
                    <p class="text-muted mb-4 small">
                        Modul utama pengelolaan pembiayaan dan investasi SYFA.
                    </p>
                    <a href="#"
                       class="btn btn-primary fw-semibold px-4 mt-auto">
                        Go to Application
                    </a>
                </div>
            </div>
        </div>

        {{-- SFinlog --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column align-items-center text-center">
                    <span class="badge bg-label-info text-uppercase mb-3 px-3 py-1 fw-semibold small">Support</span>
                    <div class="mb-3">
                        <i class="ti ti-clipboard-text text-info" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="mb-1 fw-semibold">SFinlog</h5>
                    <p class="text-muted mb-4 small">
                        Monitoring dan pencatatan transaksi keuangan operasional.
                    </p>
                    <a href="#"
                       class="btn btn-primary fw-semibold px-4 mt-auto">
                        Go to Application
                    </a>
                </div>
            </div>
        </div>

        {{-- Master Data & Configuration --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column align-items-center text-center">
                    <span class="badge bg-label-primary text-uppercase mb-3 px-3 py-1 fw-semibold small">Configuration</span>
                    <div class="mb-3">
                        <i class="ti ti-settings-cog text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="mb-1 fw-semibold text-center">Master Data &amp; Configuration</h5>
                    <p class="text-muted mb-4 small">
                        Pengaturan master data, parameter sistem, dan konfigurasi aplikasi.
                    </p>
                    <a href="#"
                       class="btn btn-primary fw-semibold px-4 mt-auto">
                        Go to Application
                    </a>
                </div>
            </div>
        </div>

        {{-- Portofolio --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column align-items-center text-center">
                    <span class="badge bg-label-success text-uppercase mb-3 px-3 py-1 fw-semibold small">Insight</span>
                    <div class="mb-3">
                        <i class="ti ti-chart-line text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="mb-1 fw-semibold">Portofolio</h5>
                    <p class="text-muted mb-4 small">
                        Ringkasan kinerja portofolio pembiayaan dan investasi.
                    </p>
                    <a href="#"
                       class="btn btn-primary fw-semibold px-4 mt-auto">
                        Go to Application
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection


