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
                <li class="menu-item {{ request()->is('master-data/master-data-kol*') ? 'active' : '' }}">
                    <a href="{{ route('masterdatakol.index') }}" class="menu-link">
                        <div data-i18n="Master Data KOL">Master Data KOL</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('master-data/sumber-pendanaan-eksternal*') ? 'active' : '' }}">
                    <a href="{{ route('sumberpendanaaneksternal.index') }}" class="menu-link">
                        <div data-i18n="Sumber Pendanaan Eksternal">Sumber Pendanaan Eksternal</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('master-data/debitur-investor*') ? 'active' : '' }}">
                    <a href="{{ route('debiturinvestor.index') }}" class="menu-link">
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
