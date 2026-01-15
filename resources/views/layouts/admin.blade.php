<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Admin') - Inventory</title>
    <link rel="icon" type="image/png" href="{{ asset('build/assets/images/logo.png') }}">

    <link href="{{ asset('build/assets/css/styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('build/assets/css/dashboard_admin/dashboard_admin.css') }}" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />

    @stack('styles')
    
    <style>
        /* Keep helper classes */
        .status-active { background-color: #28a745; color: white; font-weight: bold; }
        .status-inactive { background-color: #dc3545; color: white; font-weight: bold; }
        .status-pending { background-color: #ffc107; color: #343a40; font-weight: bold; }
        .table th, .table td { vertical-align: middle; }
    </style>
</head>
<body class="sb-nav-fixed">
    
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="{{ route('admin.dashboard') }}">ADMIN PANEL</a>
        <button id="sidebarToggle" class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0"><i class="fas fa-bars"></i></button>
        
        <ul class="navbar-nav ms-auto me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i> 
                    {{ Auth::user()->username ?? 'Admin' }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li>
                        <form id="logout-form-dropdown" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item w-100 text-start">
                                <i class="fas fa-sign-out-alt fa-fw me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                            href="{{ route('admin.dashboard') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div> Dashboard
                        </a>

                        <div class="sb-sidenav-menu-heading">ADMINISTRATION</div>
                        
                        <a class="nav-link {{ request()->routeIs('admin.submissions.*') ? 'active' : '' }}"
                            href="{{ route('admin.submissions.pending') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-list-check"></i></div> Pending Reviews
                        </a>

                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div> Manage Users
                        </a>
                        
                        {{-- START OF CONSOLIDATED LINK --}}
                        <a class="nav-link {{ request()->routeIs('admin.units-management.*') || request()->routeIs('admin.departments.*') || request()->routeIs('admin.units.*') ? 'active' : '' }}" 
                            href="{{ route('admin.units-management.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-university"></i></div> Academic & Administrative Units Management
                        </a>
                        {{-- The old separate Department and Unit links are removed --}}
                        {{-- END OF CONSOLIDATED LINK --}}
                        
                        <div class="sb-sidenav-menu-heading">INVENTORY & REPORTS</div>
                        
                        <a class="nav-link {{ request()->routeIs('admin.assets.*') ? 'active' : '' }}" href="{{ route('admin.assets.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div> All Assets
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div> Comprehensive Reports
                        </a>
                        
                    </div>
                </div>

                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    {{ Auth::user()->username ?? Auth::user()->email }}
                </div>
            </nav>
        </div>

        <div id="layoutSidenav_content">
            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script> 
    
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('build/assets/js/scripts.js') }}"></script>
    <script src="{{ asset('build/assets/js/admin.js') }}"></script>

    @stack('scripts')
    @auth
        @include('partials.timeout-handler')
    @endauth
</body>
</html>