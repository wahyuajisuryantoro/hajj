
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ url('/dashboard') }}" class="app-brand-link">
            <img src="{{ asset('images/logo-hajj.png') }}" alt="Hajj Logo" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
            <span class="app-brand-text demo menu-text fw-bold ms-2">Mitra Panel</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ri-menu-fold-line"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-header mt-2">
            <span class="menu-header-text">Dashboard & Analitik</span>
        </li>
        <!-- Dashboard -->
        <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <a href="{{ route('member.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ri-dashboard-line"></i>
                <div>Dashboard</div>
            </a>
        </li>
        <li class="menu-header mt-5">
            <span class="menu-header-text">Peluang & Program</span>
        </li>
        <li class="menu-item {{ request()->is('program') ? 'active' : '' }}">
            <a href="{{ route('member.program') }}" class="menu-link">
                <i class="menu-icon tf-icons ri-booklet-line"></i>
                <div>Program</div>
            </a>
        </li>
        <li class="menu-header mt-5">
            <span class="menu-header-text">Jaringan Mitra</span>
        </li>
        <!-- Mitra -->
        <li class="menu-item {{ request()->is('mitra*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ri-team-line"></i>
                <div>Mitra</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('mitra/pendaftaran') ? 'active' : '' }}">
                    <a href="{{ route('mitra.registration') }}" class="menu-link">
                        <div>Pendaftaran</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('mitra/list') ? 'active' : '' }}">
                    <a href="{{ route('mitra.list') }}" class="menu-link">
                        <div>List Mitra</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('mitra/genealogy') ? 'active' : '' }}">
                    <a href="{{ route('mitra.genealogy') }}" class="menu-link">
                        <div>Level Mitra/Genealogi</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-header mt-5">
            <span class="menu-header-text">Keanggotaan & Komunitas</span>
        </li>
        <!-- Customer -->
        <li class="menu-item {{ request()->is('customers*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ri-user-follow-line"></i>
                <div>Customer</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('customers/pendaftaran') ? 'active' : '' }}">
                    <a href="{{ route('customer.create') }}" class="menu-link">
                        <div>Pendaftaran</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('customers/list') ? 'active' : '' }}">
                    <a href="{{ route('customer.list') }}" class="menu-link">
                        <div>List Customer</div>
                    </a>
                </li>
                {{-- <li class="menu-item {{ request()->is('customer/history') ? 'active' : '' }}">
                    <a href="#" class="menu-link">
                        <div>Riwayat Transaksi</div>
                    </a>
                </li> --}}
            </ul>
        </li>

        <!-- Jamaah -->
        <li class="menu-item {{ request()->is('jamaah*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ri-user-line"></i>
                <div>Jamaah</div>
            </a>
            <ul class="menu-sub">
                {{-- <li class="menu-item {{ request()->is('jamaah/pendaftaran') ? 'active' : '' }}">
                    <a href="{{ route('jamaah.registration') }}" class="menu-link">
                        <div>Pendaftaran</div>
                    </a>
                </li> --}}
                <li class="menu-item {{ request()->is('jamaah/list') ? 'active' : '' }}">
                    <a href="{{ route('jamaah.list') }}" class="menu-link">
                        <div>List Jamaah</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-header mt-5">
            <span class="menu-header-text">Penghasilan & Insentif</span>
        </li>
        <!-- Bonus -->
        <li class="menu-item {{ request()->is('bonus*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ri-money-dollar-circle-line"></i>
                <div>Bonus</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('bonus') ? 'active' : '' }}">
                    <a href="{{ route('bonus.index') }}" class="menu-link">
                        <div>Rekap Bonus</div>
                    </a>
                </li>
                {{-- <li class="menu-item {{ request()->is('bonus/history') ? 'active' : '' }}">
                    <a href="{{ route('bonus.history') }}" class="menu-link">
                        <div>Riwayat Bonus</div>
                    </a>
                </li> --}}
            </ul>
        </li>
        <li class="menu-header mt-5">
            <span class="menu-header-text">Alat Pemasaran</span>
        </li>
        <!-- Tools Marketing -->
        <li class="menu-item {{ request()->is('tools-marketing*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ri-tools-line"></i>
                <div>Tools Marketing</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('tools-marketing/e-flayer') ? 'active' : '' }}">
                    <a href="{{ route('tools-marketing.e-flayer') }}" class="menu-link">
                        <div>E-Flayer</div>
                    </a>
                </li>
                <!--<li class="menu-item {{ request()->is('tools-marketing/video-promosi') ? 'active' : '' }}">-->
                <!--    <a href="{{ route('tools-marketing.video-promosi') }}" class="menu-link">-->
                <!--        <div>Video Promosi</div>-->
                <!--    </a>-->
                <!--</li>-->
            </ul>
        </li>
        <li class="menu-header mt-5">
            <span class="menu-header-text">Pengelolaan Akun</span>
        </li>
        <!-- Account -->
        <li class="menu-item {{ request()->is('account*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ri-user-settings-line"></i>
                <div>Akun</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('account/settings') ? 'active' : '' }}">
                    <a href="{{ route('account.settings') }}" class="menu-link">
                        <div>Konfigurasi</div>
                    </a>
                </li>
                <!--<li class="menu-item {{ request()->is('account/info') ? 'active' : '' }}">-->
                <!--    <a href="{{ route('account.info') }}" class="menu-link">-->
                <!--        <div>Informasi</div>-->
                <!--    </a>-->
                <!--</li>-->
            </ul>
        </li>
    </ul>
</aside>
