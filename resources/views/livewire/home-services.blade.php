<div class="services-wrapper mx-auto pt-5">
    {{-- Header with User Profile --}}
    <div class="d-flex justify-content-between align-items-center mb-5 header-offset">
        <div class="col-md-6 d-flex align-items-center gap-3">
            <div class="app-brand-logo">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" width="40" />
            </div>
            <div class="d-flex flex-column">
                <span class="fs-3 fw-bold text-heading">SYFA</span>
            </div>
        </div>

        <div class="col-md-6 d-flex justify-content-end">
            <div class="dropdown">
                <div class="d-flex align-items-center gap-3 cursor-pointer" data-bs-toggle="dropdown"
                    aria-expanded="false" style="cursor: pointer;">
                    <div class="text-end me-2" style="max-width: 200px;">
                        <div class="fw-semibold text-truncate">
                            {{ auth()->user()->name ?? 'Super Admin' }}
                        </div>
                        <small class="text-muted d-block text-truncate">
                            {{ auth()->user()->currentTeam->name ?? "Super Admin's Team" }}
                        </small>
                    </div>
                    <div class="position-relative">
                        <span
                            class="avatar avatar-sm rounded-circle bg-label-warning d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-award fs-5 text-warning"></i>
                        </span>
                        <span
                            class="position-absolute bottom-0 end-0 translate-middle p-1 bg-success border border-2 border-card rounded-circle">
                            <span class="visually-hidden">Online</span>
                        </span>
                    </div>
                </div>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item mt-0" href="#">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <div class="avatar avatar-online">
                                        <span class="avatar-initial rounded-circle bg-label-warning">
                                            <i class="ti ti-user text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ auth()->user()->name ?? 'Super Admin' }}</h6>
                                    <small class="text-muted">{{ auth()->user()->email ?? 'admin@admin.com' }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1 mx-n2"></div>
                    </li>
                    <li>
                        <div class="d-grid px-2 pt-2 pb-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger d-flex w-100">
                                    <small class="align-middle">Logout</small>
                                    <i class="ti ti-logout ms-2 ti-14px"></i>
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    {{-- Services Grid --}}
    <div class="row g-4 justify-content-center mt-5">
        {{-- SFinance Card --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 border service-card shadow-none">
                <div class="card-body d-flex flex-column align-items-center text-center p-4">
                    <span class="badge bg-label-primary text-uppercase mb-3 px-3 py-2 fw-semibold">Core</span>
                    <div class="mb-3">
                        <div class="icon-wrapper bg-light-primary rounded-circle p-3 mb-2">
                            <i class="ti ti-building-bank text-primary" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h5 class="mb-2 fw-bold">SFinance</h5>
                    <p class="text-muted mb-4 small flex-grow-1">
                        Modul utama pengelolaan pembiayaan dan investasi SYFA
                    </p>
                    <a href="{{ route('sfinance.index') }}" class="btn btn-primary w-100 fw-semibold">
                        Go to Application
                    </a>
                </div>
            </div>
        </div>

        {{-- SFinlog Card --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 border service-card shadow-none">
                <div class="card-body d-flex flex-column align-items-center text-center p-4">
                    <span class="badge bg-label-info text-uppercase mb-3 px-3 py-2 fw-semibold">Support</span>
                    <div class="mb-3">
                        <div class="icon-wrapper bg-light-info rounded-circle p-3 mb-2">
                            <i class="ti ti-clipboard-data text-info" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h5 class="mb-2 fw-bold">SFinlog</h5>
                    <p class="text-muted mb-4 small flex-grow-1">
                        Monitoring dan pencatatan transaksi keuangan operasional
                    </p>
                    <a href="{{ route('sfinlog.index') }}" class="btn btn-info w-100 fw-semibold">
                        Go to Application
                    </a>
                </div>
            </div>
        </div>

        {{-- Master Data & Configuration Card --}}
        @if (!auth()->user()->hasAnyRole(['Debitur', 'Investor', 'IO (Investment Officer)', 'CEO S-Finlog', 'Direktur SKI', 'CEO SKI']))
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100 border service-card shadow-none">
                    <div class="card-body d-flex flex-column align-items-center text-center p-4">
                        <span
                            class="badge bg-label-warning text-uppercase mb-3 px-3 py-2 fw-semibold">Configuration</span>
                        <div class="mb-3">
                            <div class="icon-wrapper bg-light-warning rounded-circle p-3 mb-2">
                                <i class="ti ti-database-cog text-warning" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                        <h5 class="mb-2 fw-bold">Master Data</h5>
                        <p class="text-muted mb-4 small flex-grow-1">
                            Pengaturan master data, parameter sistem, dan konfigurasi aplikasi
                        </p>
                        <a href="{{ route('master-data.kol.index') }}" class="btn btn-warning w-100 fw-semibold">
                            Go to Application
                        </a>
                    </div>
                </div>
            </div>
        @endif


        {{-- Portofolio Card --}}
        @if (!auth()->user()->hasAnyRole(['Debitur', 'Investor', 'IO (Investment Officer)', 'CEO S-Finlog', 'Direktur SKI', 'CEO SKI']))
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100 border service-card shadow-none">
                    <div class="card-body d-flex flex-column align-items-center text-center p-4">
                        <span class="badge bg-label-success text-uppercase mb-3 px-3 py-2 fw-semibold">Insight</span>
                        <div class="mb-3">
                            <div class="icon-wrapper bg-light-success rounded-circle p-3 mb-2">
                                <i class="ti ti-chart-pie-2 text-success" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                        <h5 class="mb-2 fw-bold">Portofolio</h5>
                        <p class="text-muted mb-4 small flex-grow-1">
                            Ringkasan kinerja portofolio pembiayaan dan investasi
                        </p>
                        <a href="#" class="btn btn-success w-100 fw-semibold">
                            Go to Application
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('styles')
    <style>
        /* Layout */
        .services-wrapper {
            max-width: 1400px;
        }

        .header-offset {
            padding-top: 30px;
        }

        /* Main Card */
        .main-card {
            border-radius: 0.5rem;
            min-height: 480px;
        }

        /* Service Card - Clean & Formal */
        .service-card {
            border-radius: 0.5rem;
            border: 1px solid #e2e6f0;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            min-height: 400px;
        }

        /* Icon Wrapper - Simple Circle */
        .icon-wrapper {
            width: 100px;
            height: 100px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Light Background Colors */
        .bg-light-primary {
            background-color: rgba(105, 108, 255, 0.08);
        }

        .bg-light-info {
            background-color: rgba(3, 195, 236, 0.08);
        }

        .bg-light-warning {
            background-color: rgba(255, 171, 0, 0.08);
        }

        .bg-light-success {
            background-color: rgba(113, 221, 55, 0.08);
        }

        /* Badge */
        .service-card .badge {
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        /* Button */
        .service-card .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
        }

        /* Typography */
        .service-card h5 {
            font-size: 1.125rem;
            margin-bottom: 0.5rem;
        }

        .service-card p {
            font-size: 0.875rem;
            line-height: 1.5;
        }

        /* User Dropdown */
        .cursor-pointer {
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .icon-wrapper {
                width: 80px;
                height: 80px;
            }

            .icon-wrapper i {
                font-size: 2.5rem !important;
            }
        }
    </style>
@endpush
