<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="index.html" class="app-brand-link">
            <img src="{{ asset('logo.png') }}" alt="" width="30">
            <span class="app-brand-text demo menu-text fw-bold">MAOS</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Page -->
        <li class="menu-item mb-2 {{ request()->is('/') ? 'active open' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div>Dashboard</div>
            </a>
        </li>

        @if (Auth::user()->role === 'Admin')
            <li class="menu-item mb-2 {{ request()->is('pengguna*') ? 'active open' : '' }}">
                <a href="{{ route('pengguna.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-user"></i>
                    <div>Pengguna</div>
                </a>
            </li>
        @endif

        <li class="menu-item mb-2 {{ request()->is('laporan*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-app-window"></i>
                <div>Laporan</div>
            </a>
            <ul class="menu-sub">

                @if (in_array(Auth::user()->role, ['Admin', 'Ketua', 'Gudep']))
                    <li class="menu-item {{ request()->is('laporan-gudep*') ? 'active' : '' }}">
                        <a href="{{ route('laporan-gudep.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-app-window"></i>
                            <div>Gugus Depan</div>
                        </a>
                    </li>
                @endif

                @if (in_array(Auth::user()->role, ['Admin', 'Ketua', 'Pengurus']))
                    <li class="menu-item {{ request()->is('laporan-pengurus*') ? 'active' : '' }}">
                        <a href="{{ route('laporan-pengurus.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-app-window"></i>
                            <div>Pengurus</div>
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        <li class="menu-item mb-2 {{ request()->is('proposal*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-app-window"></i>
                <div>Proposal</div>
            </a>
            <ul class="menu-sub">

                @if (in_array(Auth::user()->role, ['Admin', 'Ketua', 'Gudep']))
                    <li class="menu-item {{ request()->is('proposal-gudep*') ? 'active' : '' }}">
                        <a href="{{ route('proposal-gudep.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-app-window"></i>
                            <div>Gugus Depan</div>
                        </a>
                    </li>
                @endif

                @if (in_array(Auth::user()->role, ['Admin', 'Ketua', 'Pengurus']))
                    <li class="menu-item {{ request()->is('proposal-pengurus*') ? 'active' : '' }}">
                        <a href="{{ route('proposal-pengurus.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-app-window"></i>
                            <div>Pengurus</div>
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        <li class="menu-item mb-2 {{ request()->is('lpj*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-app-window"></i>
                <div>LPJ</div>
            </a>
            <ul class="menu-sub">

                @if (in_array(Auth::user()->role, ['Admin', 'Ketua', 'Gudep']))
                    <li class="menu-item {{ request()->is('lpj-gudep*') ? 'active' : '' }}">
                        <a href="{{ route('lpj-gudep.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-app-window"></i>
                            <div>Gugus Depan</div>
                        </a>
                    </li>
                @endif

                @if (in_array(Auth::user()->role, ['Admin', 'Ketua', 'Pengurus']))
                    <li class="menu-item {{ request()->is('lpj-pengurus*') ? 'active' : '' }}">
                        <a href="{{ route('lpj-pengurus.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-app-window"></i>
                            <div>Pengurus</div>
                        </a>
                    </li>
                @endif
            </ul>
        </li>

    </ul>
</aside>
