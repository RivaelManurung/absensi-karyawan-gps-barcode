<!doctype html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="{{ asset('assets/') }}"
    data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>@yield('title') | Absensi Karyawan</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/Logo.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome as primary icon font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Boxicons - Multiple fallbacks -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    
    <!-- Enhanced icon styles -->
    <style>
        @import url('https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css');
        
        /* Force icon display */
        .icon-circle {
            width: 80px !important;
            height: 80px !important;
            border-radius: 50% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-bottom: 1rem !important;
            position: relative;
        }
        
        .icon-circle-primary {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(13, 110, 253, 0.2)) !important;
            border: 3px solid rgba(13, 110, 253, 0.3) !important;
        }
        
        .icon-circle-success {
            background: linear-gradient(135deg, rgba(25, 135, 84, 0.1), rgba(25, 135, 84, 0.2)) !important;
            border: 3px solid rgba(25, 135, 84, 0.3) !important;
        }
        
        .icon-circle-warning {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.2)) !important;
            border: 3px solid rgba(255, 193, 7, 0.3) !important;
        }
        
        /* Ensure icons are always visible with fallbacks */
        .attendance-icon {
            font-size: 2.5rem !important;
            display: block !important;
        }
        
        .attendance-icon.primary { color: #0d6efd !important; }
        .attendance-icon.success { color: #198754 !important; }
        .attendance-icon.warning { color: #ffc107 !important; }
        
        /* Fallback for missing Boxicons */
        .bx-fingerprint:before,
        .fa-fingerprint:before {
            content: "�" !important;
            font-family: "Apple Color Emoji", "Segoe UI Emoji", "Noto Color Emoji", sans-serif !important;
        }
        
        .bx-check-double:before,
        .fa-check-double:before {
            content: "✅" !important;
            font-family: "Apple Color Emoji", "Segoe UI Emoji", "Noto Color Emoji", sans-serif !important;
        }
        
        .bx-hourglass:before,
        .fa-hourglass:before {
            content: "⏳" !important;
            font-family: "Apple Color Emoji", "Segoe UI Emoji", "Noto Color Emoji", sans-serif !important;
        }
        
        .bx-envelope:before,
        .fa-envelope:before {
            content: "📧" !important;
            font-family: "Apple Color Emoji", "Segoe UI Emoji", "Noto Color Emoji", sans-serif !important;
        }
    </style>
    
    <style>
        /* Custom Pagination Styles */
        .pagination {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .page-link {
            color: #6c757d;
            background-color: #fff;
            border: 1px solid #dee2e6;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s ease-in-out;
        }
        
        .page-link:hover {
            color: #0d6efd;
            background-color: #e9ecef;
            border-color: #dee2e6;
            transform: translateY(-1px);
        }
        
        .page-item.active .page-link {
            color: #fff;
            background-color: #0d6efd;
            border-color: #0d6efd;
            box-shadow: 0 0.125rem 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #fff;
            border-color: #dee2e6;
            opacity: 0.5;
        }
        
        .pagination-sm .page-link {
            padding: 0.375rem 0.5rem;
            font-size: 0.875rem;
        }
        
        /* Responsive pagination */
        @media (max-width: 576px) {
            .pagination {
                font-size: 0.8rem;
            }
            .pagination-sm .page-link {
                padding: 0.25rem 0.375rem;
            }
        }

        /* App Brand Link Styles */
        .app-brand-link {
            text-decoration: none !important;
            transition: all 0.3s ease;
        }
        
        .app-brand-link:hover {
            transform: translateY(-1px);
            text-decoration: none !important;
        }
        
        .app-brand-link:hover .app-brand-text {
            color: #696cff !important;
        }
        
        .app-brand-link:hover .app-brand-logo img {
            transform: scale(1.1);
            transition: transform 0.3s ease;
        }

        /* Profile Avatar Styles - Fix Header Alignment */
        .layout-navbar {
            min-height: 60px;
            max-height: 60px;
            overflow: visible;
        }
        
        .navbar-nav .dropdown-user .avatar {
            width: 32px;
            height: 32px;
        }
        
        .navbar-nav .dropdown-user .avatar img {
            width: 32px;
            height: 32px;
            object-fit: cover;
        }
        
        .dropdown-menu .avatar {
            width: 40px;
            height: 40px;
        }
        
        .dropdown-menu .avatar img {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }
        
        /* Ensure navbar items are properly aligned */
        .layout-navbar .navbar-nav {
            height: 100%;
            align-items: center;
        }
        
        .layout-navbar .nav-item {
            display: flex;
            align-items: center;
            height: 100%;
        }
        
        .layout-navbar .nav-link {
            display: flex;
            align-items: center;
            height: 100%;
            padding: 0.5rem 0.75rem;
        }
        
        /* Dropdown positioning */
        .navbar-dropdown .dropdown-menu {
            margin-top: 0.25rem;
        }

        /* Custom Background Subtle Classes */
        .bg-primary-subtle {
            background-color: rgba(13, 110, 253, 0.1) !important;
        }
        
        .bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.1) !important;
        }
        
        .bg-warning-subtle {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }
        
        .bg-danger-subtle {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }
        
        .bg-info-subtle {
            background-color: rgba(13, 202, 240, 0.1) !important;
        }

        /* Icon Fallback - in case Boxicons don't load */
        i[class*="bx-"]:before {
            content: attr(data-fallback) !important;
        }
        
        /* Ensure icons have proper sizing */
        .bx {
            font-family: boxicons !important;
            font-weight: normal;
            font-style: normal;
            line-height: 1;
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        {{-- ✅ Perhatikan: class layout-without-menu ditambahkan untuk layout full-width --}}
        <div class="layout-container layout-without-menu"> 
            <div class="layout-page">
                {{-- Navbar Sederhana --}}
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <a href="{{ route('attendances.index') }}" class="app-brand-link">
                            <span class="app-brand-logo demo">
                                <img src="{{ asset('assets/Logo.png') }}" alt="Logo" width="30" />
                            </span>
                            <span class="app-brand-text demo menu-text fw-bold ms-2">Absensi Karyawan</span>
                        </a>
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            {{-- Profile Dropdown --}}
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        @if(auth()->user()->profile_photo_path)
                                            <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" 
                                                 alt="Profile Photo" class="rounded-circle" />
                                        @else
                                            <img src="{{ asset('assets/img/avatars/1.png') }}" 
                                                 alt="Default Avatar" class="rounded-circle" />
                                        @endif
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        @if(auth()->user()->profile_photo_path)
                                                            <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" 
                                                                 alt="Profile Photo" class="rounded-circle" />
                                                        @else
                                                            <img src="{{ asset('assets/img/avatars/1.png') }}" 
                                                                 alt="Default Avatar" class="rounded-circle" />
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block">{{ auth()->user()->name }}</span>
                                                    <small class="text-muted">{{ auth()->user()->employee_id }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('user.profile.index') }}">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">Profil Saya</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('user.profile.change-password') }}">
                                            <i class="bx bx-lock-alt me-2"></i>
                                            <span class="align-middle">Ubah Password</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('attendances.index') }}">
                                            <i class="bx bx-time me-2"></i>
                                            <span class="align-middle">Absensi</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bx bx-power-off me-2"></i>
                                                <span class="align-middle">Log Out</span>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    @stack('scripts')
</body>
</html>