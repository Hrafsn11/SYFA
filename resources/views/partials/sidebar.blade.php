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
                        class="menu-item {{ RouteHelper::routeIs('dashboard.*') || RouteHelper::routeIs('dashboard.pembiayaan') || RouteHelper::routeIs('dashboard.investasi-deposito') ? 'open' : '' }}">
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
                                    class="menu-item {{ RouteHelper::routeIs('dashboard.investasi-deposito') ? 'active' : '' }}">
                                    <a wire:navigate.hover href="{{ RouteHelper::route('dashboard.investasi-deposito') }}"
                                        class="menu-link">
                                        <div data-i18n="Pembiayaan Investasi Deposito SFinance">Pembiayaan Investasi Deposito
                                            SFinance</div>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
            @elseif ($isSFinlog)
                @if(auth()->user()->hasRole('super-admin') || auth()->user()->canany(['sfinlog.menu.dashboard_pembiayaan', 'sfinlog.menu.dashboard_investasi_deposito']))
                    <li
                        class="menu-item {{ RouteHelper::routeIs('dashboard.*') || RouteHelper::routeIs('dashboard.pembiayaan') || RouteHelper::routeIs('dashboard.investasi-deposito') ? 'open' : '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-smart-home"></i>
                            <div data-i18n="Dashboard">Dashboard</div>
                        </a>
                        <ul class="menu-sub">
                            @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('sfinlog.menu.dashboard_pembiayaan'))
                                <li class="menu-item {{ RouteHelper::routeIs('dashboard.pembiayaan') ? 'active' : '' }}">
                                    <a wire:navigate.hover href="{{ RouteHelper::route('dashboard.pembiayaan') }}"
                                        class="menu-link">
                                        <div data-i18n="Dashboard Pembiayaan SFinlog">Dashboard Pembiayaan SFinlog</div>
                                    </a>
                                </li>
                            @endif
                            @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('sfinlog.menu.dashboard_investasi_deposito'))
                                <li class="menu-item {{ RouteHelper::routeIs('dashboard.investasi-deposito') ? 'active' : '' }}">
                                    <a wire:navigate.hover href="{{ RouteHelper::route('dashboard.investasi-deposito') }}"
                                        class="menu-link">
                                        <div data-i18n="Dashboard Investasi Deposito SFinlog">Dashboard Investasi Deposito
                                            SFinlog</div>
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
                @canany(['sfinance.menu.pengajuan_peminjaman', 'sfinance.menu.ar_perbulan', 'sfinance.menu.ar_performance', 'sfinlog.menu.peminjaman_dana', 'sfinlog.menu.ar_perbulan', 'sfinlog.menu.ar_performance'])
                    <!-- Peminjaman Section -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Peminjaman</span>
                    </li>
                @endcanany

                @canany(['sfinance.menu.pengajuan_peminjaman', 'sfinlog.menu.peminjaman_dana'])
                    <li class="menu-item {{ RouteHelper::is('peminjaman') ? 'active' : '' }}">
                        @if ($isSFinlog)
                            <a href="{{ route('sfinlog.peminjaman.index') }}" class="menu-link">
                            @elseif($isSFinance)
                                <a href="{{ route('sfinance.peminjaman') }}" class="menu-link">
                                @else
                                    <a href="{{ route('peminjaman') }}" class="menu-link">
                        @endif
                        <i class="menu-icon tf-icons ti ti-briefcase"></i>
                        <div data-i18n="Peminjaman Dana">Peminjaman Dana</div>
                        </a>
                    </li>
                @endcanany

                @canany(['sfinance.menu.ar_perbulan', 'sfinlog.menu.ar_perbulan'])
                    <li class="menu-item {{ RouteHelper::routeIs('ar-perbulan*') ? 'active' : '' }}">
                        <a href="{{ RouteHelper::route('ar-perbulan.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-archive"></i>
                            <div data-i18n="AR Perbulan">AR Perbulan</div>
                        </a>
                    </li>
                @endcanany

                @canany(['sfinance.menu.ar_performance', 'sfinlog.menu.ar_performance'])
                    <li class="menu-item {{ RouteHelper::routeIs('ar-performance*') ? 'active' : '' }}">
                        <a href="{{ RouteHelper::route('ar-performance.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-chart-line"></i>
                            <div data-i18n="AR Performance">AR Performance</div>
                        </a>
                    </li>
                @endcanany


                @if (!$isSFinlog)
                    @canany(['sfinance.menu.pengajuan_restukturisasi', 'sfinance.menu.program_restukturisasi'])
                        <!-- Restrukturisasi Section -->
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Restrukturisasi</span>
                        </li>
                    @endcanany

                    @can('sfinance.menu.pengajuan_restukturisasi')
                        <li class="menu-item {{ RouteHelper::routeIs('pengajuan-restrukturisasi*') ? 'active' : '' }}">
                            <a href="{{ RouteHelper::route('pengajuan-restrukturisasi.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-file-text"></i>
                                <div data-i18n="Pengajuan Restrukturisasi">Pengajuan Restrukturisasi</div>
                            </a>
                        </li>
                    @endcan

                    @can('sfinance.menu.program_restukturisasi')
                        <li class="menu-item {{ RouteHelper::routeIs('program-restrukturisasi*') ? 'active' : '' }}">
                            <a href="{{ RouteHelper::route('program-restrukturisasi.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-calculator"></i>
                                <div data-i18n="Program Restrukturisasi">Program Restrukturisasi</div>
                            </a>
                        </li>
                    @endcan
                @endif


                @canany(['sfinance.menu.pengembalian_dana', 'sfinance.menu.debitur_piutang', 'sfinance.menu.report_pengembalian', 'sfinlog.menu.pengembalian_dana', 'sfinlog.menu.debitur_piutang', 'sfinlog.menu.report_pengembalian'])
                    <!-- Pengembalian Section -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Pengembalian</span>
                    </li>
                @endcanany

                @canany(['sfinance.menu.pengembalian_dana', 'sfinlog.menu.pengembalian_dana'])
                    <li
                        class="menu-item {{ RouteHelper::routeIs('pengembalian.index') || RouteHelper::routeIs('sfinlog.pengembalian-pinjaman.index') ? 'active' : '' }}">
                        @if ($isSFinlog)
                            <a wire:navigate.hover href="{{ route('sfinlog.pengembalian-pinjaman.index') }}" class="menu-link">
                            @else
                                <a wire:navigate.hover href="{{ RouteHelper::route('pengembalian.index') }}" class="menu-link">
                        @endif
                        <i class="menu-icon tf-icons ti ti-wallet"></i>
                        <div data-i18n="Pengembalian Dana">Pengembalian Dana</div>
                        </a>
                    </li>
                @endcanany

                @canany(['sfinance.menu.debitur_piutang', 'sfinlog.menu.debitur_piutang'])
                    <li class="menu-item {{ RouteHelper::routeIs('debitur-piutang*') ? 'active' : '' }}">
                        @if ($isSFinlog)
                            <a href="{{ route('sfinlog.debitur-piutang.index') }}" class="menu-link">
                            @else
                                <a href="{{ RouteHelper::route('debitur-piutang.index') }}" class="menu-link">
                        @endif
                        <i class="menu-icon tf-icons ti ti-receipt"></i>
                        <div data-i18n="Debitur Piutang">Debitur Piutang</div>
                        </a>
                    </li>
                @endcanany

                @canany(['sfinance.menu.report_pengembalian', 'sfinlog.menu.report_pengembalian'])
                    <li class="menu-item {{ RouteHelper::routeIs('report-pengembalian*') ? 'active' : '' }}">
                        @if ($isSFinlog)
                            <a wire:navigate.hover href="{{ route('sfinlog.report-pengembalian.index') }}" class="menu-link">
                            @else
                                <a wire:navigate.hover href="{{ RouteHelper::route('report-pengembalian.index') }}"
                                    class="menu-link">
                        @endif
                        <i class="menu-icon tf-icons ti ti-file-text"></i>
                        <div data-i18n="Report Pengembalian">Report Pengembalian</div>
                        </a>
                    </li>
                @endcanany

                @canany(['sfinance.menu.pengajuan_investasi', 'sfinance.menu.report_penyaluran_dana', 'sfinance.menu.penyaluran_deposito', 'sfinance.menu.kertas_kerja_sfinance', 'sfinance.menu.pengembalian_investasi', 'sfinlog.menu.pengajuan_investasi', 'sfinlog.menu.report_penyaluran_dana', 'sfinlog.menu.penyaluran_deposito', 'sfinlog.menu.kertas_kerja_investor', 'sfinlog.menu.pengembalian_investasi'])
                    <!-- Debitur dan Investor Section -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Investasi</span>
                    </li>
                @endcanany


                @canany(['sfinance.menu.pengajuan_investasi', 'sfinance.menu.report_penyaluran_dana', 'sfinance.menu.penyaluran_deposito', 'sfinance.menu.kertas_kerja_sfinance', 'sfinance.menu.pengembalian_investasi', 'sfinlog.menu.pengajuan_investasi', 'sfinlog.menu.report_penyaluran_dana', 'sfinlog.menu.penyaluran_deposito', 'sfinlog.menu.kertas_kerja_investor', 'sfinlog.menu.pengembalian_investasi'])
                    <li
                        class="menu-item {{ RouteHelper::is('*form-kerja-investor*') || RouteHelper::is('*penyaluran-deposito*') || RouteHelper::is('*pengembalian-investasi*') || RouteHelper::is('*kertas-kerja-investor*') ? 'open' : '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-id-badge"></i>
                            <div data-i18n="Pengajuan Investasi">Pengajuan Investasi</div>
                        </a>
                        <ul class="menu-sub">
                            @canany(['sfinance.menu.pengajuan_investasi', 'sfinlog.menu.pengajuan_investasi'])
                                <li class="menu-item {{ RouteHelper::is('*pengajuan-investasi*') ? 'active' : '' }}">
                                    <a href="{{ RouteHelper::route('pengajuan-investasi.index') }}" class="menu-link">
                                        <div data-i18n="Pengajuan Investasi">Pengajuan Investasi</div>
                                    </a>
                                </li>
                            @endcanany

                            @canany(['sfinance.menu.report_penyaluran_dana', 'sfinlog.menu.report_penyaluran_dana'])
                                <li class="menu-item {{ RouteHelper::is('*report-penyaluran-dana-investasi*') ? 'active' : '' }}">
                                    <a href="{{ RouteHelper::route('report-penyaluran-dana-investasi.index') }}"
                                        class="menu-link">
                                        <div data-i18n="Report Penyaluran Dana Investasi">Report Penyaluran Dana Investasi</div>
                                    </a>
                                </li>
                            @endcanany

                            @canany(['sfinance.menu.penyaluran_deposito', 'sfinlog.menu.penyaluran_deposito'])
                                <li
                                    class="menu-item {{ RouteHelper::is('*penyaluran-deposito*') || RouteHelper::is('*penyaluran-deposito-sfinlog*') ? 'active' : '' }}">
                                    @if ($isSFinance)
                                        <a href="{{ route('sfinance.penyaluran-deposito.index') }}" class="menu-link">
                                            <div data-i18n="Penyaluran Deposito">Penyaluran Deposito</div>
                                        </a>
                                    @elseif($isSFinlog)
                                        <a href="{{ route('sfinlog.penyaluran-deposito-sfinlog.index') }}" class="menu-link">
                                            <div data-i18n="Penyaluran Deposito">Penyaluran Deposito</div>
                                        </a>
                                    @else
                                        <a href="{{ RouteHelper::route('penyaluran-deposito.index') }}" class="menu-link">
                                            <div data-i18n="Penyaluran Deposito">Penyaluran Deposito</div>
                                        </a>
                                    @endif
                                </li>
                            @endcanany

                            @canany(['sfinance.menu.kertas_kerja_sfinance', 'sfinlog.menu.kertas_kerja_investor'])
                                <li class="menu-item {{ RouteHelper::is('*kertas-kerja-investor*') ? 'active' : '' }}">
                                    @if ($isSFinance)
                                        <a href="{{ route('sfinance.kertas-kerja-investor-sfinance.index') }}" class="menu-link">
                                            <div data-i18n="Kertas Kerja Investor SFinance">Kertas Kerja Investor SFinance</div>
                                        </a>
                                    @elseif($isSFinlog)
                                        <a href="{{ route('sfinlog.kertas-kerja-investor-sfinlog.index') }}" class="menu-link">
                                            <div data-i18n="Kertas Kerja Investor SFinlog">Kertas Kerja Investor SFinlog</div>
                                        </a>
                                    @endif
                                </li>
                            @endcanany

                            @canany(['sfinance.menu.pengembalian_investasi', 'sfinlog.menu.pengembalian_investasi'])
                                <li class="menu-item {{ RouteHelper::is('*pengembalian-investasi*') ? 'active' : '' }}">
                                    @if ($isSFinance)
                                        <a wire:navigate.hover href="{{ route('sfinance.pengembalian-investasi.index') }}"
                                            class="menu-link">
                                        @elseif($isSFinlog)
                                            <a wire:navigate.hover href="{{ route('sfinlog.pengembalian-investasi.index') }}"
                                                class="menu-link">
                                            @else
                                                <a wire:navigate.hover
                                                    href="{{ RouteHelper::route('pengembalian-investasi.index') }}"
                                                    class="menu-link">
                                    @endif
                                    <div data-i18n="Pengembalian Investasi">Pengembalian Investasi</div>
                                    </a>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany
            @endif

        @else
            {{-- Master Data Sidebar --}}
            <li class="menu-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                <a wire:navigate.hover href="{{ route('dashboard.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-smart-home"></i>
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>

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
                        <li class="menu-item {{ request()->routeIs('master-data.sumber-pendanaan-eksternal.*') ? 'active' : '' }}">
                            <a wire:navigate.hover href="{{ route('master-data.sumber-pendanaan-eksternal.index') }}"
                                class="menu-link">
                                <div data-i18n="Sumber Pendanaan Eksternal">Sumber Pendanaan Eksternal</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('master-data.debitur-investor.*') ? 'active' : '' }}">
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
                        <li class="menu-item {{ request()->routeIs('master-data.cells-project.*') ? 'active' : '' }}">
                            <a wire:navigate.hover href="{{ route('master-data.cells-project.index') }}"
                                class="menu-link">
                                <div data-i18n="Cells Project">Cells Project</div>
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
