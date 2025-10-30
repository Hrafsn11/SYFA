<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
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
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        <!-- Peminjaman Section -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Peminjaman</span>
        </li>

        <li class="menu-item {{ request()->routeIs('peminjaman*') ? 'active' : '' }}">
            <a href="{{ route('peminjaman') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-briefcase"></i>
                <div data-i18n="Peminjaman Dana">Peminjaman Dana</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('ar-perbulan*') ? 'active' : '' }}">
            <a href="{{ route('ar-perbulan.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-archive"></i>
                <div data-i18n="AR Perbulan">AR Perbulan</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('ar-performance*') ? 'active' : '' }}">
            <a href="{{ route('ar-performance.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-chart-line"></i>
                <div data-i18n="AR Performance">AR Performance</div>
            </a>
        </li>

        <!-- Pengembalian Section -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Pengembalian</span>
        </li>

        <li class="menu-item {{ request()->routeIs('pengembalian*') ? 'active' : '' }}">
            <a href="{{ route('pengembalian.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-wallet"></i>
                <div data-i18n="Pengembalian Dana">Pengembalian Dana</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('debitur-piutang*') ? 'active' : '' }}">
            <a href="{{ route('debitur-piutang.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-receipt"></i>
                <div data-i18n="Debitur Piutang">Debitur Piutang</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('report-pengembalian*') ? 'active' : '' }}">
            <a href="{{ route('report-pengembalian.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-file-text"></i>
                <div data-i18n="Report Pengembalian">Report Pengembalian</div>
            </a>
        </li>

        <!-- Debitur dan Investor Section -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Debitur dan Investor</span>
        </li>

        <li class="menu-item {{ request()->is('debitur-dan-investor*') ? 'open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-id-badge"></i>
                <div data-i18n="Debitur dan Investor">Debitur dan Investor</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('debitur-dan-investor/form-kerja-investor*') ? 'active' : '' }}">
                    <a href="{{ route('form-kerja-investor.index') }}" class="menu-link">
                        <div data-i18n="Investor">Investor</div>
                    </a>
                </li>

                <li
                    class="menu-item {{ request()->is('debitur-dan-investor/report-penyaluran-dana-investasi*') ? 'active' : '' }}">
                    <a href="{{ route('report-penyaluran-dana-investasi.index') }}" class="menu-link">
                        <div data-i18n="Report Penyaluran Dana Investasi">Report Penyaluran Dana Investasi</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('rencana-penagihan-deposito/ski*') ? 'active' : '' }}">
                    <a href="{{ route('rencana-penagihan-deposito.ski') }}" class="menu-link">
                        <div data-i18n="Penagihan Deposito SKI">Penagihan Deposito SKI</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('rencana-penagihan-deposito/penerima-dana*') ? 'active' : '' }}">
                    <a href="{{ route('rencana-penagihan-deposito.penerima-dana') }}" class="menu-link">
                        <div data-i18n="Penagihan Deposito Penerima Dana">Penagihan Deposito Penerima Dana</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('kertas-kerja-investor-sfinance*') ? 'active' : '' }}">
                    <a href="{{ route('kertas-kerja-investor-sfinance.index') }}" class="menu-link">
                        <div data-i18n="Kertas Kerja Investor SFinance">Kertas Kerja Investor SFinance</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Master Data Section -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Master Data</span>
        </li>

        <li class="menu-item {{ request()->is('master-data*') ? 'open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-database"></i>
                <div data-i18n="Master Data">Master Data</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('master-data/kol*') ? 'active' : '' }}">
                    <a href="{{ route('master-data.kol.index') }}" class="menu-link">
                        <div data-i18n="KOL">KOL</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('master-data/sumber-pendanaan-eksternal*') ? 'active' : '' }}">
                    <a href="{{ route('master-data.sumber-pendanaan-eksternal.index') }}" class="menu-link">
                        <div data-i18n="Sumber Pendanaan Eksternal">Sumber Pendanaan Eksternal</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('master-data/debitur-investor*') ? 'active' : '' }}">
                    <a href="{{ route('master-data.debitur-investor.index') }}" class="menu-link">
                        <div data-i18n="Debitur dan Investor">Debitur dan Investor</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('master-data/karyawan-ski*') ? 'active' : '' }}">
                    <a href="{{ route('master-data.karyawan-ski.index') }}" class="menu-link">
                        <div data-i18n="Master Karyawan SKI">Master Karyawan SKI</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('master-data/lainnya') ? 'active' : '' }}">
                    <a href="#" class="menu-link">
                        <div data-i18n="Menu Lainnya">Menu Lainnya</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Configuration Section -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Configuration</span>
        </li>

        <li class="menu-item {{ request()->routeIs('matrixpinjaman') ? 'active' : '' }}">
            <a href="{{ route('matrixpinjaman') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-building"></i>
                <div data-i18n="Config Matrix Pinjaman">Config Matrix Pinjaman</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('matrixscore') ? 'active' : '' }}">
            <a href="{{ route('matrixscore') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user"></i>
                <div data-i18n="Config Matrix Score">Config Matrix Score</div>
            </a>
        </li>

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
    </ul>
</aside>
