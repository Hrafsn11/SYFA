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

    <ul class="menu-inner py-1">
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

        <li class="menu-item {{ request()->routeIs('report-pengembalian*') ? 'active' : '' }}">
            <a href="{{ route('report-pengembalian.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-wallet"></i>
                <div data-i18n="Report Pengembalian">Report Pengembalian</div>
            </a>
        </li>

        <!-- Debitur dan Investor Section -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Debitur dan Investor</span>
        </li>

        <li class="menu-item {{ request()->is('debitur-dan-investor*') ? 'open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-user"></i>
                <div data-i18n="Debitur dan Investor">Debitur dan Investor</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('debitur-dan-investor/form-kerja-investor*') ? 'active' : '' }}">
                    <a href="{{ route('form-kerja-investor.index') }}" class="menu-link">
                        <div data-i18n="Investor">Investor</div>
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
    </ul>
</aside>
