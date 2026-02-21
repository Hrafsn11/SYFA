@php
    use App\Helpers\ModuleHelper;
    use App\Helpers\RouteHelper;

    $currentModule = ModuleHelper::getCurrentModule();
    $isSFinance = ModuleHelper::isSFinance();
    $isSFinlog = ModuleHelper::isSFinlog();
    $isMasterData = ModuleHelper::isMasterData();
    $isPortofolio = ModuleHelper::isPortofolio();

    // Determine which sidebar to show
    $showSFinanceSidebar = $isSFinance || $isSFinlog;
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">

        <a href="{{ route('dashboard.index') }}" class="app-brand-link">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" width="40" />
            <span class="app-brand-text demo menu-text fw-bold">SYFA</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
        </a>

    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1 mb-3">
        <!-- Dashboard -->
        @if (!$isMasterData)
            @if ($isSFinance)
                @canany(['sfinance.menu.dashboard_pembiayaan', 'sfinance.menu.dashboard_pembiayaan_investasi'])
                    <li
                        class="menu-item {{ RouteHelper::routeIs('dashboard.*') || RouteHelper::routeIs('dashboard.pembiayaan') || RouteHelper::routeIs('dashboard.investasi') ? 'open' : '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-smart-home"></i>
                            <div data-i18n="Dashboard">Dashboard</div>
                        </a>
                        <ul class="menu-sub">
                            @can('sfinance.menu.dashboard_pembiayaan')
                                <li class="menu-item {{ RouteHelper::routeIs('dashboard.pembiayaan') ? 'active' : '' }}">
                                    <a wire:navigate.hover href="{{ RouteHelper::route('dashboard.pembiayaan') }}"
                                        class="menu-link">
                                        <div data-i18n="Dashboard Pembiayaan SFinance">Dashboard Pembiayaan SFinance</div>
                                    </a>
                                </li>
                            @endcan
                            @can('sfinance.menu.dashboard_pembiayaan_investasi')
                                <li
                                    class="menu-item {{ RouteHelper::routeIs('dashboard.investasi') ? 'active' : '' }}">
                                    <a wire:navigate.hover href="{{ RouteHelper::route('dashboard.investasi') }}"
                                        class="menu-link">
                                        <div data-i18n="Pembiayaan Investasi Deposito SFinance">Pembiayaan Investasi Deposito
                                            SFinance
                                        </div>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
            @elseif ($isSFinlog)
                @if (auth()->user()->hasRole('super-admin') ||
                        auth()->user()->canany(['sfinlog.menu.dashboard_pembiayaan', 'sfinlog.menu.dashboard_investasi_deposito']))
                    <li
                        class="menu-item {{ RouteHelper::routeIs('dashboard.*') || RouteHelper::routeIs('dashboard.pembiayaan') || RouteHelper::routeIs('dashboard.investasi-deposito') ? 'open' : '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-smart-home"></i>
                            <div data-i18n="Dashboard">Dashboard</div>
                        </a>
                        <ul class="menu-sub">
                            @if (auth()->user()->hasRole('super-admin') || auth()->user()->can('sfinlog.menu.dashboard_pembiayaan'))
                                <li
                                    class="menu-item {{ RouteHelper::routeIs('dashboard.pembiayaan') ? 'active' : '' }}">
                                    <a wire:navigate.hover href="{{ RouteHelper::route('dashboard.pembiayaan') }}"
                                        class="menu-link">
                                        <div data-i18n="Dashboard Pembiayaan SFinlog">Dashboard Pembiayaan SFinlog</div>
                                    </a>
                                </li>
                            @endif
                            @if (auth()->user()->hasRole('super-admin') || auth()->user()->can('sfinlog.menu.dashboard_investasi_deposito'))
                                <li
                                    class="menu-item {{ RouteHelper::routeIs('dashboard.investasi-deposito') ? 'active' : '' }}">
                                    <a wire:navigate.hover
                                        href="{{ RouteHelper::route('dashboard.investasi-deposito') }}"
                                        class="menu-link">
                                        <div data-i18n="Dashboard Investasi Deposito SFinlog">Dashboard Investasi
                                            Deposito SFinlog
                                        </div>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
            @else
                <li class="menu-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                    <a wire:navigate.hover href="{{ route('dashboard.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-smart-home"></i>
                        <div data-i18n="Dashboard">Dashboard</div>
                    </a>
                </li>
            @endif

            @if ($showSFinanceSidebar)
                @canany(['sfinance.menu.pengajuan_peminjaman', 'sfinance.menu.laporan_tagihan_bulanan',
                    'sfinance.menu.monitoring_pembayaran', 'sfinlog.menu.peminjaman_dana', 'sfinlog.menu.laporan_tagihan_bulanan',
                    'sfinlog.menu.monitoring_pembayaran'])
                    <!-- Peminjaman Section -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Peminjaman</span>
                    </li>
                @endcanany

                @canany(['sfinance.menu.pengajuan_peminjaman', 'sfinlog.menu.peminjaman_dana'])
                    <li class="menu-item {{ RouteHelper::is('peminjaman') ? 'active' : '' }}">
                        @if ($isSFinlog)
                            <a href="{{ route('sfinlog.peminjaman.index') }}" class="menu-link" wire:navigate.hover>
                            @else
                                {{-- Untuk SFinance dan fallback, gunakan route Livewire --}}
                                <a href="{{ route('peminjaman.index') }}" class="menu-link" wire:navigate.hover>
                        @endif
                        <i class="menu-icon tf-icons ti ti-briefcase"></i>
                        <div data-i18n="Peminjaman Dana">Peminjaman Dana</div>
                        </a>
                    </li>
                @endcanany

                @canany(['sfinance.menu.laporan_tagihan_bulanan', 'sfinlog.menu.laporan_tagihan_bulanan'])
                    <li class="menu-item {{ RouteHelper::routeIs('laporan-tagihan-bulanan*') ? 'active' : '' }}">
                        <a href="{{ RouteHelper::route('laporan-tagihan-bulanan.index') }}" class="menu-link" wire:navigate.hover>
                            <i class="menu-icon tf-icons ti ti-archive"></i>
                            <div data-i18n="Laporan Tagihan Bulanan">Laporan Tagihan Bulanan</div>
                        </a>
                    </li>
                @endcanany

                @canany(['sfinance.menu.monitoring_pembayaran', 'sfinlog.menu.monitoring_pembayaran'])
                    <li class="menu-item {{ RouteHelper::routeIs('monitoring-pembayaran*') ? 'active' : '' }}">
                        <a href="{{ RouteHelper::route('monitoring-pembayaran.index') }}" class="menu-link" wire:navigate.hover>
                            <i class="menu-icon tf-icons ti ti-chart-line"></i>
                            <div data-i18n="Monitoring Pembayaran">Monitoring Pembayaran</div>
                        </a>
                    </li>
                @endcanany

                @if (!$isSFinlog)
                    @canany(['sfinance.menu.pengajuan_restukturisasi', 'sfinance.menu.penyesuaian_cicilan'])
                        <!-- Restrukturisasi Section -->
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Restrukturisasi</span>
                        </li>
                    @endcanany

                    @can('sfinance.menu.pengajuan_restukturisasi')
                        <li class="menu-item {{ RouteHelper::routeIs('pengajuan-cicilan*') ? 'active' : '' }}">
                            <a href="{{ RouteHelper::route('pengajuan-cicilan.index') }}" class="menu-link"
                                wire:navigate.hover>
                                <i class="menu-icon tf-icons ti ti-file-text"></i>
                                <div data-i18n="Pengajuan Cicilan">Pengajuan Cicilan</div>
                            </a>
                        </li>
                    @endcan

                    @can('sfinance.menu.penyesuaian_cicilan')
                        <li class="menu-item {{ RouteHelper::routeIs('penyesuaian-cicilan*') ? 'active' : '' }}">
                            <a href="{{ RouteHelper::route('penyesuaian-cicilan.index') }}" class="menu-link"
                                wire:navigate.hover>
                                <i class="menu-icon tf-icons ti ti-calculator"></i>
                                <div data-i18n="Penyesuaian Cicilan">Penyesuaian Cicilan</div>
                            </a>
                        </li>
                    @endcan
                @endif


                @canany(['sfinance.menu.pengembalian_dana', 'sfinance.menu.riwayat_tagihan',
                    'sfinance.menu.laporan_pengembalian', 'sfinlog.menu.pengembalian_dana', 'sfinlog.menu.riwayat_tagihan',
                    'sfinlog.menu.laporan_pengembalian'])
                    <!-- Pengembalian Section -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Pengembalian</span>
                    </li>
                @endcanany

                @canany(['sfinance.menu.pengembalian_dana', 'sfinlog.menu.pengembalian_dana'])
                    <li class="menu-item {{ RouteHelper::routeIs('pengembalian.index') || RouteHelper::routeIs('sfinlog.pengembalian-pinjaman.index') ? 'active' : '' }}"
                        wire:navigate.hover>
                        @if ($isSFinlog)
                            <a wire:navigate.hover href="{{ route('sfinlog.pengembalian-pinjaman.index') }}"
                                class="menu-link">
                            @else
                                <a wire:navigate.hover href="{{ RouteHelper::route('pengembalian.index') }}"
                                    class="menu-link">
                        @endif
                        <i class="menu-icon tf-icons ti ti-wallet"></i>
                        <div data-i18n="Pengembalian Dana">Pengembalian Dana</div>
                        </a>
                    </li>
                @endcanany

                @canany(['sfinance.menu.riwayat_tagihan', 'sfinlog.menu.riwayat_tagihan'])
                    <li class="menu-item {{ RouteHelper::routeIs('riwayat-tagihan*') ? 'active' : '' }}">
                        @if ($isSFinlog)
                            <a href="{{ route('sfinlog.riwayat-tagihan.index') }}" class="menu-link">
                            @else
                                <a href="{{ RouteHelper::route('riwayat-tagihan.index') }}" class="menu-link">
                        @endif
                        <i class="menu-icon tf-icons ti ti-receipt"></i>
                        <div data-i18n="Riwayat Tagihan">Riwayat Tagihan </div>
                        </a>
                    </li>
                @endcanany

                @canany(['sfinance.menu.laporan_pengembalian', 'sfinlog.menu.laporan_pengembalian'])
                    <li class="menu-item {{ RouteHelper::routeIs('laporan-pengembalian*') ? 'active' : '' }}">
                        @if ($isSFinlog)
                            <a wire:navigate.hover href="{{ route('sfinlog.laporan-pengembalian.index') }}"
                                class="menu-link">
                            @else
                                <a wire:navigate.hover href="{{ RouteHelper::route('laporan-pengembalian.index') }}"
                                    class="menu-link">
                        @endif
                        <i class="menu-icon tf-icons ti ti-file-text"></i>
                        <div data-i18n="Report Pengembalian">Report Pengembalian</div>
                        </a>
                    </li>
                @endcanany


                @canany(['sfinance.menu.pengajuan_investasi', 'sfinance.menu.report_penyaluran_dana',
                    'sfinance.menu.penyaluran_dana_investasi', 'sfinance.menu.laporan_investasi',
                    'sfinance.menu.pengembalian_investasi', 'sfinlog.menu.pengajuan_investasi',
                    'sfinlog.menu.report_penyaluran_dana', 'sfinlog.menu.penyaluran_deposito',
                    'sfinlog.menu.kertas_kerja_investor', 'sfinlog.menu.pengembalian_investasi'])
                    <!-- Investasi Section -->
                    @if(Auth::user()->role !== 'debitur')
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Investasi</span>
                        </li>
                    @endif
                @endcanany

                @if ($isSFinlog)
                    @can('sfinlog.menu.cell_project_profile')
                        <li
                            class="menu-item {{ RouteHelper::routeIs('sfinlog.cell-project-profile.index') ? 'active' : '' }}">
                            <a href="{{ route('sfinlog.cell-project-profile.index') }}" class="menu-link"
                                wire:navigate.hover>
                                <i class="menu-icon tf-icons ti ti-file-dollar"></i>
                                <div data-i18n="Cell Project Profile">Cell Project Profile</div>
                            </a>
                        </li>
                    @endcan
                @endif

                {{-- Pengajuan Investasi --}}
                @canany(['sfinance.menu.pengajuan_investasi', 'sfinlog.menu.pengajuan_investasi'])
                    <li class="menu-item {{ RouteHelper::is('*pengajuan-investasi*') ? 'active' : '' }}">
                        <a href="{{ RouteHelper::route('pengajuan-investasi.index') }}" class="menu-link"
                            wire:navigate.hover>
                            <i class="menu-icon tf-icons ti ti-file-dollar"></i>
                            <div data-i18n="Pengajuan Investasi">Pengajuan Investasi</div>
                        </a>
                    </li>
                @endcanany

                {{-- Aset Investasi (formerly Report Penyaluran Dana Investasi) --}}
                @canany(['sfinance.menu.report_penyaluran_dana', 'sfinlog.menu.report_penyaluran_dana'])
                    <li
                        class="menu-item {{ RouteHelper::is('*penyaluran-dana-investasi*') || RouteHelper::is('*penyaluran-deposito-sfinlog*') ? 'active' : '' }}">
                        @if ($isSFinance)
                            <a href="{{ route('sfinance.penyaluran-dana-investasi.index') }}" class="menu-link">
                            @elseif($isSFinlog)
                                <a href="{{ route('sfinlog.penyaluran-deposito-sfinlog.index') }}" class="menu-link">
                                @else
                                    <a href="{{ RouteHelper::route('penyaluran-dana-investasi.index') }}" class="menu-link">
                        @endif
                        <i class="menu-icon tf-icons ti ti-report-money"></i>
                        <div data-i18n="Penyaluran Dana Investasi">Penyaluran Dana Investasi</div>
                        </a>
                    </li>
                @endcanany

                {{-- Kertas Kerja Investor --}}
                @canany(['sfinance.menu.laporan_investasi', 'sfinlog.menu.kertas_kerja_investor'])
                    <li class="menu-item {{ RouteHelper::is('*laporan-investasi-sfinance*') || RouteHelper::is('*kertas-kerja-investor*') ? 'active' : '' }}">
                        @if ($isSFinance)
                            <a href="{{ route('sfinance.laporan-investasi-sfinance.index') }}" class="menu-link"
                                wire:navigate.hover>
                                <i class="menu-icon tf-icons ti ti-file-text"></i>
                                <div data-i18n="Laporan Investasi">Laporan Investasi</div>
                            </a>
                        @elseif($isSFinlog)
                            <a href="{{ route('sfinlog.kertas-kerja-investor-sfinlog.index') }}" class="menu-link"
                                wire:navigate.hover>
                                <i class="menu-icon tf-icons ti ti-file-text"></i>
                                <div data-i18n="Kertas Kerja Investor">Kertas Kerja Investor</div>
                            </a>
                        @endif
                    </li>
                @endcanany

                {{-- Pengembalian Investasi --}}
                @canany(['sfinance.menu.pengembalian_investasi', 'sfinlog.menu.pengembalian_investasi'])
                    <li class="menu-item {{ RouteHelper::is('*pengembalian-investasi*') ? 'active' : '' }}">
                        @if ($isSFinance)
                            <a href="{{ route('sfinance.pengembalian-investasi.index') }}" class="menu-link">
                            @elseif($isSFinlog)
                                <a href="{{ route('sfinlog.pengembalian-investasi.index') }}" class="menu-link">
                                @else
                                    <a href="{{ RouteHelper::route('pengembalian-investasi.index') }}" class="menu-link">
                        @endif
                        <i class="menu-icon tf-icons ti ti-cash-banknote"></i>
                        <div data-i18n="Pengembalian Investasi">Pengembalian Investasi</div>
                        </a>
                    </li>
                @endcanany

            @endif
        @else
            <!-- Master Data Section -->
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Master Data</span>
            </li>

            @can('master_data.view')
                <li class="menu-item {{ request()->routeIs('master-data.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons ti ti-database"></i>
                        <div data-i18n="Master Data">Master Data</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->routeIs('master-data.kol.*') ? 'active' : '' }}">
                            <a wire:navigate.hover href="{{ route('master-data.kol.index') }}" class="menu-link">
                                <div data-i18n="KOL">KOL</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a wire:navigate.hover href="{{ route('master-data.sumber-pendanaan-eksternal.index') }}"
                                class="menu-link">
                                <div data-i18n="Sumber Pendanaan Eksternal">Sumber Pendanaan Eksternal</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a wire:navigate.hover href="{{ route('master-data.debitur-investor.index') }}"
                                class="menu-link">
                                <div data-i18n="Debitur dan Investor">Debitur dan Investor</div>
                            </a>
                        </li>
                        {{-- <li class="menu-item {{ request()->is('master-data/karyawan-ski*') ? 'active' : '' }}">
                            <a href="{{ route('master-data.karyawan-ski.index') }}" class="menu-link">
                                <div data-i18n="Master Karyawan SKI">Master Karyawan SKI</div>
                            </a>
                        </li> --}}
                        <li class="menu-item {{ request()->is('master-data/cells-project') ? 'active' : '' }}">
                            <a wire:navigate.hover href="{{ route('master-data.cells-project.index') }}"
                                class="menu-link">
                                <div data-i18n="List Cells Project SFinlog">List Cells Project SFinlog</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            <!-- Configuration Section -->
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Configuration</span>
            </li>

            <li class="menu-item {{ request()->routeIs('config-matrix-pinjaman.index') ? 'active' : '' }}">
                <a wire:navigate.hover href="{{ route('config-matrix-pinjaman.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-building"></i>
                    <div data-i18n="Config Matrix Pinjaman">Config Matrix Pinjaman</div>
                </a>
            </li>

            {{-- <li class="menu-item {{ request()->routeIs('matrixscore') ? 'active' : '' }}">
                <a href="{{ route('matrixscore') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-user"></i>
                    <div data-i18n="Config Matrix Score">Config Matrix Score</div>
                </a>
            </li> --}}

            <!-- Access Control Section - Only in Master Data Module -->
            @role('super-admin')
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Access Control</span>
                </li>

                <li
                    class="menu-item {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons ti ti-settings"></i>
                        <div data-i18n="Roles & Permissions">Roles & Permissions</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <a href="{{ route('users.index') }}" class="menu-link">
                                <div data-i18n="Users">Users</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                            <a href="{{ route('roles.index') }}" class="menu-link">
                                <div data-i18n="Roles">Roles</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                            <a href="{{ route('permissions.index') }}" class="menu-link">
                                <div data-i18n="Permissions">Permissions</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endrole
        @endif
    </ul>
</aside>
