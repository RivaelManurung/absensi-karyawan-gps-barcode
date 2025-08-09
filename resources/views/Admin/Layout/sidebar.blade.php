<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        @php
        $homeRoute = 'login'; // Default
        if (Auth::check()) {
            $group = Auth::user()->group; // Menggunakan 'group' sesuai pembahasan sebelumnya
            if ($group === 'admin' || $group === 'superadmin') {
                $homeRoute = 'admin.dashboard'; // Asumsi route dashboard admin
            } elseif ($group === 'user') {
                $homeRoute = 'attendances.index'; // Halaman utama absensi karyawan
            }
        }
        @endphp
        <a href="{{ route($homeRoute) }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('assets/Logo.png') }}" alt="Logo" width="30" />
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">Absensi</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
        </a>
    </div>
    <div class="menu-divider mt-0"></div>
    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        {{-- =============================================== --}}
        {{-- | MENU UNTUK ADMIN / SUPERADMIN | --}}
        {{-- =============================================== --}}
        @if (in_array(Auth::user()->group, ['admin', 'superadmin']))
        <li class="menu-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate">Dashboard</div>
            </a>
        </li>

        {{-- Manajemen Utama --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Manajemen Utama</span>
        </li>
        <li class="menu-item {{ Request::routeIs('attendances.report*') ? 'active' : '' }}">
            <a href="#" class="menu-link"> {{-- Arahkan ke route laporan --}}
                <i class="menu-icon tf-icons bx bx-check-square"></i>
                <div class="text-truncate">Laporan Absensi</div>
            </a>
        </li>
        <li class="menu-item {{ Request::routeIs('leave-requests*') ? 'active' : '' }}">
            <a href="#" class="menu-link"> {{-- Arahkan ke route pengajuan izin --}}
                <i class="menu-icon tf-icons bx bx-envelope"></i>
                <div class="text-truncate">Pengajuan Izin</div>
            </a>
        </li>

        {{-- Data Master --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Data Master</span>
        </li>
        {{-- <li class="menu-item {{ Request::routeIs('users*') ? 'active' : '' }}">
            <a href="{{ route('users.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div class="text-truncate">Data Karyawan</div>
            </a>
        </li> --}}
        <li class="menu-item {{ Request::routeIs('divisions*') ? 'active' : '' }}">
            <a href="{{ route('admin.divisions.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-building"></i>
                <div class="text-truncate">Data Divisi</div>
            </a>
        </li>
        <li class="menu-item {{ Request::routeIs('job-titles*') ? 'active' : '' }}">
            <a href="{{ route('admin.job-titles.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-id-card"></i>
                <div class="text-truncate">Data Jabatan</div>
            </a>
        </li>
         <li class="menu-item {{ Request::routeIs('shifts*') ? 'active' : '' }}">
            <a href="{{ route('admin.shifts.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-time"></i>
                <div class="text-truncate">Data Shift</div>
            </a>
        </li>
        <li class="menu-item {{ Request::routeIs('barcodes*') ? 'active' : '' }}">
            <a href="{{ route('admin.barcodes.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-qr-scan"></i>
                <div class="text-truncate">Data Lokasi (Barcode)</div>
            </a>
        </li>

        {{-- =============================================== --}}
        {{-- | MENU UNTUK KARYAWAN (USER) | --}}
        {{-- =============================================== --}}
        @elseif (Auth::user()->group === 'user')
        <li class="menu-item {{ Request::routeIs('attendances.index') ? 'active' : '' }}">
            <a href="{{ route('admin.attendances.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-fingerprint"></i>
                <div class="text-truncate">Absensi Saya</div>
            </a>
        </li>
        <li class="menu-item {{ Request::routeIs('attendances.history') ? 'active' : '' }}">
            <a href="{{ route('admin.attendances.history') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-history"></i>
                <div class="text-truncate">Riwayat Absensi</div>
            </a>
        </li>
        <li class="menu-item {{ Request::routeIs('attendances.request.create') ? 'active' : '' }}">
            <a href="{{ route('admin.attendances.request.create') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-envelope"></i>
                <div class="text-truncate">Ajukan Izin</div>
            </a>
        </li>
        @endif
    </ul>
</aside>