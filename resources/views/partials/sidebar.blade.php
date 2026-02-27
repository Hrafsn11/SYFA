@php
    use App\Helpers\ModuleHelper;
    use App\Helpers\RouteHelper;

    $currentModule = ModuleHelper::getCurrentModule();
    $isSFinance = ModuleHelper::isSFinance();
    $isMasterData = ModuleHelper::isMasterData();
    $isPortofolio = ModuleHelper::isPortofolio();

    // Determine which sidebar to show
    $showSFinanceSidebar = $isSFinance;
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
                        class="menu-item {{ RouteHelper::routeIs('dashboard.*') || RouteHelper::routeIs('dashboard.pembiayaan') || RouteHelper::routeIs('dashboard.investasi') || RouteHelper::routeIs('dashboard.cicilan') ? 'open' : '' }}">
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
                            <li class="menu-item {{ RouteHelper::routeIs('dashboard.cicilan') ? 'active' : '' }}">
                                <a wire:navigate.hover href="{{ RouteHelper::route('dashboard.cicilan') }}"
                                    class="menu-link">
                                    <div data-i18n="Dashboard Cicilan Restrukturisasi">Dashboard Cicilan Restrukturisasi</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcanany
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
                    'sfinance.menu.monitoring_pembayaran'])
                    <!-- Peminjaman Section -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Peminjaman</span>
                    </li>
                @endcanany

                @can('sfinance.menu.pengajuan_peminjaman')
                    <li class="menu-item {{ RouteHelper::is('peminjaman') ? 'active' : '' }}">
                        <a href="{{ route('peminjaman.index') }}" class="menu-link" wire:navigate.hover>
                            <i class="menu-icon tf-icons ti ti-briefcase"></i>
                            <div data-i18n="Peminjaman Dana">Peminjaman Dana</div>
                        </a>
                    </li>
                @endcan

                @can('sfinance.menu.laporan_tagihan_bulanan')
                    <li class="menu-item {{ RouteHelper::routeIs('laporan-tagihan-bulanan*') ? 'active' : '' }}">
                        <a href="{{ RouteHelper::route('laporan-tagihan-bulanan.index') }}" class="menu-link" wire:navigate.hover>
                            <i class="menu-icon tf-icons ti ti-archive"></i>
                            <div data-i18n="Laporan Tagihan Bulanan">Laporan Tagihan Bulanan</div>
                        </a>
                    </li>
                @endcan

                @can('sfinance.menu.monitoring_pembayaran')
                    <li class="menu-item {{ RouteHelper::routeIs('monitoring-pembayaran*') ? 'active' : '' }}">
                        <a href="{{ RouteHelper::route('monitoring-pembayaran.index') }}" class="menu-link" wire:navigate.hover>
                            <i class="menu-icon tf-icons ti ti-chart-line"></i>
                            <div data-i18n="Monitoring Pembayaran">Monitoring Pembayaran</div>
                        </a>
                    </li>
                @endcan

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


                @canany(['sfinance.menu.pengembalian_dana', 'sfinance.menu.riwayat_tagihan',
                    'sfinance.menu.laporan_pengembalian'])
                    <!-- Pengembalian Section -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Pengembalian</span>
                    </li>
                @endcanany

                @can('sfinance.menu.pengembalian_dana')
                    <li class="menu-item {{ RouteHelper::routeIs('pengembalian.index') ? 'active' : '' }}">
                        <a wire:navigate.hover href="{{ RouteHelper::route('pengembalian.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-wallet"></i>
                            <div data-i18n="Pengembalian Dana">Pengembalian Dana</div>
                        </a>
                    </li>
                @endcan

                @can('sfinance.menu.riwayat_tagihan')
                    <li class="menu-item {{ RouteHelper::routeIs('riwayat-tagihan*') ? 'active' : '' }}">
                        <a href="{{ RouteHelper::route('riwayat-tagihan.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-receipt"></i>
                            <div data-i18n="Riwayat Tagihan">Riwayat Tagihan</div>
                        </a>
                    </li>
                @endcan

                @can('sfinance.menu.laporan_pengembalian')
                    <li class="menu-item {{ RouteHelper::routeIs('laporan-pengembalian*') ? 'active' : '' }}">
                        <a wire:navigate.hover href="{{ RouteHelper::route('laporan-pengembalian.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-file-text"></i>
                            <div data-i18n="Report Pengembalian">Report Pengembalian</div>
                        </a>
                    </li>
                @endcan


                @canany(['sfinance.menu.pengajuan_investasi', 'sfinance.menu.report_penyaluran_dana',
                    'sfinance.menu.penyaluran_dana_investasi', 'sfinance.menu.laporan_investasi',
                    'sfinance.menu.pengembalian_investasi'])
                    <!-- Investasi Section -->
                    @if(Auth::user()->role !== 'debitur')
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Investasi</span>
                        </li>
                    @endif
                @endcanany

                {{-- Pengajuan Investasi --}}
                @can('sfinance.menu.pengajuan_investasi')
                    <li class="menu-item {{ RouteHelper::is('*pengajuan-investasi*') ? 'active' : '' }}">
                        <a href="{{ RouteHelper::route('pengajuan-investasi.index') }}" class="menu-link"
                            wire:navigate.hover>
                            <i class="menu-icon tf-icons ti ti-file-dollar"></i>
                            <div data-i18n="Pengajuan Investasi">Pengajuan Investasi</div>
                        </a>
                    </li>
                @endcan

                {{-- Aset Investasi (formerly Report Penyaluran Dana Investasi) --}}
                @can('sfinance.menu.report_penyaluran_dana')
                    <li class="menu-item {{ RouteHelper::is('*penyaluran-dana-investasi*') ? 'active' : '' }}">
                        <a href="{{ route('sfinance.penyaluran-dana-investasi.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-report-money"></i>
                            <div data-i18n="Penyaluran Dana Investasi">Penyaluran Dana Investasi</div>
                        </a>
                    </li>
                @endcan

                {{-- Kertas Kerja Investor --}}
                @can('sfinance.menu.laporan_investasi')
                    <li class="menu-item {{ RouteHelper::is('*laporan-investasi-sfinance*') ? 'active' : '' }}">
                        <a href="{{ route('sfinance.laporan-investasi-sfinance.index') }}" class="menu-link"
                            wire:navigate.hover>
                            <i class="menu-icon tf-icons ti ti-file-text"></i>
                            <div data-i18n="Laporan Investasi">Laporan Investasi</div>
                        </a>
                    </li>
                @endcan

                {{-- Pengembalian Investasi --}}
                @can('sfinance.menu.pengembalian_investasi')
                    <li class="menu-item {{ RouteHelper::is('*pengembalian-investasi*') ? 'active' : '' }}">
                        <a href="{{ route('sfinance.pengembalian-investasi.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-cash-banknote"></i>
                            <div data-i18n="Pengembalian Investasi">Pengembalian Investasi</div>
                        </a>
                    </li>
                @endcan

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
