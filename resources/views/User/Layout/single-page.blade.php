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
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    
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
                        <a href="#" class="app-brand-link">
                            <span class="app-brand-logo demo">
                                <img src="{{ asset('assets/Logo.png') }}" alt="Logo" width="30" />
                            </span>
                            <span class="app-brand-text demo menu-text fw-bold ms-2">Absensi Karyawan</span>
                        </a>
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <li class="nav-item">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bx bx-power-off me-1"></i> Log Out
                                    </button>
                                </form>
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