<div class="services-wrapper mx-auto pt-5">
    {{-- Header Section (Logo, Title, User Info) --}}
    <div class="row align-items-center mb-5 header-offset">
        <div class="col-md-6 d-flex align-items-center gap-3">
            <div class="app-brand-logo">
                <img src="{{ asset('assets/img/logo.png') }}" alt="SYFA Logo" width="42" height="42">
            </div>
            <div class="d-flex flex-column">
                <span class="fs-3 fw-bold text-heading">SYFA</span>
            </div>
        </div>

        {{-- USER INFO & DROPDOWN LOGOUT FEATURE --}}
        <div class="col-md-6 d-flex justify-content-end">
            <div class="dropdown">
                {{-- Dropdown Trigger (Clickable User Area) --}}
                <div class="d-flex align-items-center gap-3 cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                    <div class="text-end me-2">
                        <div class="fw-semibold">
                            {{ auth()->user()->name ?? 'Super Admin' }}
                        </div>
                        <small class="text-muted">
                            {{ auth()->user()->currentTeam->name ?? 'Super Admin' }}
                        </small>
                    </div>
                    <div class="position-relative">
                        <span class="avatar avatar-sm rounded-circle bg-label-warning d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-award fs-5 text-warning"></i>
                        </span>
                        <span class="position-absolute bottom-0 end-0 translate-middle p-1 bg-success border border-2 border-card rounded-circle">
                            <span class="visually-hidden">Online</span>
                        </span>
                    </div>
                </div>
                {{-- /Dropdown Trigger --}}

                {{-- Dropdown Menu --}}
                <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width: 14rem;">
                    {{-- User Profile Header --}}
                    <li>
                        <div class="dropdown-item p-3 border-bottom d-flex align-items-center">
                            <span class="avatar avatar-sm rounded-circle bg-label-warning me-3">
                                <i class="ti ti-user fs-5 text-warning"></i>
                            </span>
                            <div class="d-flex flex-column">
                                <p class="mb-0 fw-semibold">{{ auth()->user()->name ?? 'Super Admin' }}</p>
                                <small class="text-muted">{{ auth()->user()->email ?? 'superadmin@syfa.test' }}</small>
                            </div>
                        </div>
                    </li>
                    
                    <li><hr class="dropdown-divider my-1"></li>

                    {{-- Logout Form Action --}}
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="ti ti-logout me-2"></i>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
                {{-- /Dropdown Menu --}}
            </div>
        </div>
        {{-- /USER INFO & DROPDOWN LOGOUT FEATURE --}}
    </div>

    {{-- Main Card Container for Services --}}
    <div class="card border-0 shadow-sm main-card">
        <div class="card-body px-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Services</h4>
                </div>
            </div>

            <div class="row g-4">
                {{-- SFinance (CORE) --}}
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card h-100 border-0 service-card">
                        <div class="card-body d-flex flex-column align-items-center text-center position-relative">
                            <span class="badge bg-success text-uppercase fw-semibold small badge-top-right">
                                CORE
                            </span>
                            <div class="service-icon mb-3 mt-4">
                                <i class="ti ti-gauge text-primary icon-size-lg"></i>
                            </div>
                            <h5 class="mb-1 fw-semibold">SFinance</h5>
                            <p class="text-muted mb-4 small flex-grow-1">
                                Modul utama pengelolaan pembiayaan dan investasi SYFA.
                            </p>
                            <a href="#" class="btn btn-primary fw-semibold px-4 mt-auto">
                                Go to Application
                            </a>
                        </div>
                    </div>
                </div>

                {{-- SFinlog (SUPPORT) --}}
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card h-100 border-0 service-card">
                        <div class="card-body d-flex flex-column align-items-center text-center position-relative">
                            <span class="badge bg-info text-uppercase fw-semibold small badge-top-right">
                                SUPPORT
                            </span>
                            <div class="service-icon mb-3 mt-4">
                                <i class="ti ti-clipboard-text text-info icon-size-lg"></i>
                            </div>
                            <h5 class="mb-1 fw-semibold">SFinlog</h5>
                            <p class="text-muted mb-4 small flex-grow-1">
                                Monitoring dan pencatatan transaksi keuangan operasional.
                            </p>
                            <a href="#" class="btn btn-primary fw-semibold px-4 mt-auto">
                                Go to Application
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Master Data & Configuration (CONFIGURATION) --}}
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card h-100 border-0 service-card">
                        <div class="card-body d-flex flex-column align-items-center text-center position-relative">
                            <span class="badge bg-primary text-uppercase fw-semibold small badge-top-right">
                                CONFIGURATION
                            </span>
                            <div class="service-icon mb-3 mt-4">
                                <i class="ti ti-settings-cog text-warning icon-size-lg"></i>
                            </div>
                            <h5 class="mb-1 fw-semibold text-center">Master Data &amp; Configuration</h5>
                            <p class="text-muted mb-4 small flex-grow-1">
                                Pengaturan master data, parameter sistem, dan konfigurasi aplikasi.
                            </p>
                            <a href="#" class="btn btn-primary fw-semibold px-4 mt-auto">
                                Go to Application
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Portofolio (INSIGHT) --}}
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card h-100 border-0 service-card">
                        <div class="card-body d-flex flex-column align-items-center text-center position-relative">
                            <span class="badge bg-success text-uppercase fw-semibold small badge-top-right">
                                INSIGHT
                            </span>
                            <div class="service-icon mb-3 mt-4">
                                <i class="ti ti-chart-line text-success icon-size-lg"></i>
                            </div>
                            <h5 class="mb-1 fw-semibold">Portofolio</h5>
                            <p class="text-muted mb-4 small flex-grow-1">
                                Ringkasan kinerja portofolio pembiayaan dan investasi.
                            </p>
                            <a href="#" class="btn btn-primary fw-semibold px-4 mt-auto">
                                Go to Application
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .services-wrapper {
            max-width: 1400px;
        }

        /* OFFSET HEADER */
        .header-offset {
            padding-top: 30px;
        }

        /* === SERVICE CARDS === */
        .service-card {
            border-radius: 1rem;
            border: 1px solid #e2e6f0;
            box-shadow: 0 0.75rem 1.5rem rgba(15, 23, 42, 0.06);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
            
            /* Tambahan agar card modul seragam */
            min-height: 370px; /* ← SESUAIKAN: 300–360px */
            display: flex;
            flex-direction: column;
        }

        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 1.25rem 2rem rgba(15, 23, 42, 0.08);
        }

        .service-card .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Buat ikon, teks, dan tombol tersusun rapi */
        }

        /* ICON */
        .service-icon .icon-size-lg {
            padding: 1.5rem;
            border-radius: 50%;
            font-size: 3.2rem;
            display: inline-flex;
        }

        /* BADGE */
        .badge-top-right {
            position: absolute;
            top: 0;
            right: 0;
            margin-top: 1rem;
            margin-right: 1rem;
            padding: 0.3rem 0.75rem;
            border-radius: 0.5rem;
        }

        /* Main Card */
        .main-card {
            min-height: 480px; 
        }

        /* Custom style for clickable dropdown trigger */
        .cursor-pointer {
            cursor: pointer;
        }

    </style>
@endpush