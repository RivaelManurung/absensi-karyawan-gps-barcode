<!doctype html>
<html lang="id" class="layout-menu-fixed layout-compact" data-assets-path="{{ asset('assets/') }}"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>@yield('title') | Absensi</title>

    <meta name="description" content="Aplikasi manajemen keuangan pribadi" />

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/Logo.png') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />


    @stack('styles')

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

        /* App Brand Link Styles for Admin */
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
    </style>

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('admin.layout.sidebar')
            <div class="layout-page">
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <h5 class="mb-0">@yield('title', 'Dashboard')</h5>
                            </div>
                        </div>

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    {{-- PERBAIKAN: Avatar gambar diganti dengan ikon --}}
                                    <div class="avatar avatar-online">
                                        <span class="avatar-initial rounded-circle bg-primary"><i
                                                class="bx bx-user"></i></span>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        {{-- PERBAIKAN: Avatar gambar diganti dengan ikon --}}
                                                        <span class="avatar-initial rounded-circle bg-primary"><i
                                                                class="bx bx-user"></i></span>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    {{-- Menampilkan nama lengkap user yang login --}}
                                                    <span class="fw-medium d-block">{{ Auth::user()->fullname }}</span>
                                                    <small class="text-muted">{{ ucfirst(Auth::user()->peran) }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    {{-- PERBAIKAN: Menu "My Profile" dihapus --}}
                                    <li>
                                        {{-- Form Logout yang aman --}}
                                        <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                            @csrf
                                            <a class="dropdown-item" href="#"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                <i class="bx bx-power-off me-2"></i>
                                                <span class="align-middle">Log Out</span>
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="content-wrapper">
                    @yield('content')

                    @include('admin.layout.footer')
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    @stack('scripts')

</body>

</html>