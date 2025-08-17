<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title', 'Inventory Management System')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Application Styles -->
    <link rel="stylesheet" href="{{ asset('dist/css/app.css') }}">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* Mobile Sidebar Styles */
        .mobile-menu {
            position: fixed;
            top: 0;
            left: -100%;
            width: 280px;
            height: 100vh;
            background: #1e293b;
            z-index: 9999;
            transition: left 0.3s ease-in-out;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
        }
        
        .mobile-menu.active {
            left: 0;
        }
        
        .mobile-menu-bar {
            background: #0f172a;
            padding: 1rem;
            border-bottom: 1px solid #334155;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .scrollable {
            height: calc(100vh - 80px);
            overflow-y: auto;
        }
        
        .scrollable__content {
            padding: 1rem;
        }
        
        .menu {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #e2e8f0;
            text-decoration: none;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .menu:hover {
            background: #334155;
            color: #ffffff;
        }
        
        .menu__icon {
            margin-right: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 1.5rem;
            height: 1.5rem;
        }
        
        .menu__title {
            font-weight: 500;
        }
        
        /* Mobile overlay */
        .mobile-menu::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        .mobile-menu.active::before {
            opacity: 1;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .side-nav {
                display: none;
            }
            
            .content {
                margin-left: 0 !important;
                padding-top: 70px;
            }
        }
        
        @media (min-width: 769px) {
            .mobile-menu {
                display: none;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="antialiased">
    <!-- Mobile Menu -->
    <div class="mobile-menu md:hidden">
        <div class="mobile-menu-bar">
            <a href="{{ route('dashboard') }}" class="flex mr-auto">
                <span class="text-white text-lg ml-3">Inventory</span>
            </a>
            <a href="javascript:;" class="mobile-menu-toggler">
                <i data-lucide="bar-chart-2" class="w-8 h-8 text-white transform -rotate-90"></i>
            </a>
        </div>
        <div class="scrollable" data-simplebar="init">
            <div class="simplebar-wrapper">
                <div class="simplebar-height-auto-observer-wrapper">
                    <div class="simplebar-height-auto-observer"></div>
                </div>
                <div class="simplebar-mask">
                    <div class="simplebar-offset">
                        <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content">
                            <div class="simplebar-content">
                                <a href="javascript:;" class="mobile-menu-toggler">
                                    <i data-lucide="x-circle" class="w-8 h-8 text-white transform -rotate-90"></i>
                                </a>
                                <ul class="scrollable__content py-2">
                                    <li>
                                        <a href="{{ route('dashboard') }}" class="menu">
                                            <div class="menu__icon">
                                                <i data-lucide="home" class="w-5 h-5"></i>
                                            </div>
                                            <div class="menu__title">Dashboard</div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('departments.index') }}" class="menu">
                                            <div class="menu__icon">
                                                <i data-lucide="building" class="w-5 h-5"></i>
                                            </div>
                                            <div class="menu__title">Departments</div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('supplies.index') }}" class="menu">
                                            <div class="menu__icon">
                                                <i data-lucide="package" class="w-5 h-5"></i>
                                            </div>
                                            <div class="menu__title">Supplies</div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('deployed-items.index') }}" class="menu">
                                            <div class="menu__icon">
                                                <i data-lucide="box" class="w-5 h-5"></i>
                                            </div>
                                            <div class="menu__title">Deployed Items</div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('categories.index') }}" class="menu">
                                            <div class="menu__icon">
                                                <i data-lucide="tag" class="w-5 h-5"></i>
                                            </div>
                                            <div class="menu__title">Categories</div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('users.index') }}" class="menu">
                                            <div class="menu__icon">
                                                <i data-lucide="users" class="w-5 h-5"></i>
                                            </div>
                                            <div class="menu__title">Users</div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('reports.index') }}" class="menu">
                                            <div class="menu__icon">
                                                <i data-lucide="file-text" class="w-5 h-5"></i>
                                            </div>
                                            <div class="menu__title">Reports</div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Navigation -->
    @includeWhen(View::exists('layouts.top-bar'), 'layouts.top-bar')

    <!-- Main Layout -->
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @includeWhen(View::exists('layouts.sidebar'), 'layouts.sidebar')

        <!-- Page Content -->
        <main class="content flex-1 overflow-x-hidden overflow-y-auto">
            <!-- Session Messages -->
            @foreach (['success', 'error', 'warning', 'info'] as $msg)
                @if (session()->has($msg))
                    <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible show flex items-center mb-4" role="alert">
                        @php
                            $icons = [
                                'success' => 'check-circle',
                                'error' => 'alert-octagon',
                                'warning' => 'alert-triangle',
                                'info' => 'info'
                            ];
                        @endphp
                        <i data-lucide="{{ $icons[$msg] ?? 'info' }}" class="w-6 h-6 mr-2"></i>
                        {{ session($msg) }}
                        <button type="button" class="btn-close" data-tw-dismiss="alert" aria-label="Close">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                @endif
            @endforeach

            @hasSection('content')
                @yield('content')
            @else
                <div class="p-4">
                    @yield('body')
                </div>
            @endif
        </main>
    </div>

    <!-- Application JavaScript -->
    <script src="{{ asset('dist/js/app.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Initialize modals
            document.querySelectorAll('.modal').forEach(modal => {
                if (typeof tailwind !== 'undefined') {
                    tailwind.Modal.getOrCreateInstance(modal);
                }
            });

            // Mobile menu toggle
            const mobileMenuToggler = document.querySelector('.mobile-menu-toggler');
            if (mobileMenuToggler) {
                mobileMenuToggler.addEventListener('click', () => {
                    const mobileMenu = document.querySelector('.mobile-menu');
                    if (mobileMenu) {
                        mobileMenu.classList.toggle('active');
                    }
                });
            }

            // Auto-dismiss alerts after 5 seconds
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    const closeBtn = alert.querySelector('[data-tw-dismiss="alert"]');
                    if (closeBtn) closeBtn.click();
                });
            }, 5000);
        });
    </script>

    @stack('scripts')
</body>
</html>
