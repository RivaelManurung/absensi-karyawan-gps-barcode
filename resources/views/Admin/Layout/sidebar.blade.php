<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        @php
            // Logika ini menentukan link utama berdasarkan peran user
            $homeRoute = Auth::check() && Auth::user()->isAdmin ? 'admin.dashboard' : 'attendances.index';
        @endphp
        <a href="{{ route($homeRoute) }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('assets/Logo.png') }}" alt="Logo" width="30" />
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">Absensi Karyawan</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>
    <div class="menu-divider mt-0"></div>
    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

    {{-- =============================================== --}}
    {{-- | MENU UNTUK ADMIN / SUPERADMIN | --}}
    {{-- =============================================== --}}
    @if (Auth::user() && Auth::user()->isAdmin)
        <li class="menu-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate">Dashboard</div>
            </a>
        </li>

        {{-- Manajemen Utama --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Manajemen Utama</span>
        </li>
        <li class="menu-item {{ Request::routeIs('admin.reports*') ? 'active' : '' }}">
            <a href="{{ route('admin.reports.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-chart"></i>
                <div class="text-truncate">Laporan Absensi</div>
            </a>
        </li>
        <li class="menu-item {{ Request::routeIs('admin.leave-requests*') ? 'active' : '' }}">
            <a href="{{ route('admin.leave-requests.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-envelope"></i>
                <div class="text-truncate">Kelola Pengajuan Izin</div>
            </a>
        </li>

        {{-- Data Master --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Data Master</span>
        </li>
        {{-- ✅ PERBAIKAN 1: Menu untuk Manajemen Karyawan (CRUD) --}}
        <li class="menu-item {{ Request::routeIs('admin.users.index') ? 'active' : '' }}">
            <a href="{{ route('admin.users.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div class="text-truncate">Manajemen Karyawan</div>
            </a>
        </li>
        {{-- ✅ PERBAIKAN 2: Menu terpisah untuk Karyawan per Divisi --}}
        <li class="menu-item {{ Request::routeIs('admin.users.per-division') ? 'active' : '' }}">
            <a href="{{ route('admin.users.per-division') }}" class="menu-link">
                <i class="menu-icon tf-icons bxs-user-detail"></i>
                <div class="text-truncate">Karyawan per Divisi</div>
            </a>
        </li>
        <li class="menu-item {{ Request::routeIs('admin.divisions.index') ? 'active' : '' }}">
            <a href="{{ route('admin.divisions.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-building"></i>
                <div class="text-truncate">Data Divisi</div>
            </a>
        </li>
        <li class="menu-item {{ Request::routeIs('admin.job-titles.index') ? 'active' : '' }}">
            <a href="{{ route('admin.job-titles.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-id-card"></i>
                <div class="text-truncate">Data Jabatan</div>
            </a>
        </li>
        <li class="menu-item {{ Request::routeIs('admin.shifts.index') ? 'active' : '' }}">
            <a href="{{ route('admin.shifts.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-time"></i>
                <div class="text-truncate">Data Shift</div>
            </a>
        </li>
        <li class="menu-item {{ Request::routeIs('admin.statuses.index') ? 'active' : '' }}">
            <a href="{{ route('admin.statuses.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-category"></i>
                <div class="text-truncate">Data Status</div>
            </a>
        </li>
        <li class="menu-item {{ Request::routeIs('admin.barcodes.index') ? 'active' : '' }}">
            <a href="{{ route('admin.barcodes.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-qr-scan"></i>
                <div class="text-truncate">Data Lokasi</div>
            </a>
        </li>

    {{-- =============================================== --}}
    {{-- | MENU UNTUK KARYAWAN (USER) | --}}
    {{-- =============================================== --}}
    @elseif(Auth::user() && Auth::user()->isUser)
        <li class="menu-item {{ Request::routeIs('attendances.index') ? 'active' : '' }}">
            <a href="{{ route('attendances.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-fingerprint"></i>
                <div class="text-truncate">Absensi Saya</div>
            </a>
        </li>
        <li class="menu-item {{ Request::routeIs('attendances.history') ? 'active' : '' }}">
            <a href="{{ route('attendances.history') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-history"></i>
                <div class="text-truncate">Riwayat Absensi</div>
            </a>
        </li>
        <li class="menu-item {{ Request::routeIs('attendances.request.create') ? 'active' : '' }}">
            <a href="{{ route('attendances.request.create') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-envelope"></i>
                <div class="text-truncate">Ajukan Izin</div>
            </a>
        </li>
    @endif
    </ul>
</aside>