<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    {{-- Updated title to reflect Staff context --}}
    <title>@yield('title', 'Staff Portal') - Inventory</title>
    <link rel="icon" type="image/png" href="{{ asset('build/assets/images/logo.png') }}">

    <link href="{{ asset('build/assets/css/styles.css') }}" rel="stylesheet" />
    {{-- Reusing the dashboard CSS for consistency --}}
    <link href="{{ asset('build/assets/css/dashboard_admin/dashboard_admin.css') }}" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />

    @stack('styles')
    
    <style>
        /* Shared helper classes for asset status */
        .status-active { background-color: #28a745; color: white; font-weight: bold; padding: 2px 8px; border-radius: 4px; }
        .status-inactive { background-color: #dc3545; color: white; font-weight: bold; padding: 2px 8px; border-radius: 4px; }
        .status-pending { background-color: #ffc107; color: #343a40; font-weight: bold; padding: 2px 8px; border-radius: 4px; }
        .table th, .table td { vertical-align: middle; }
        /* Custom Staff sidebar color to distinguish from Admin */
        .sb-sidenav-dark { background-color: #1a1d20; } 
    </style>
</head>
<body class="sb-nav-fixed">
    
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        {{-- Updated Brand --}}
        <a class="navbar-brand ps-3" href="{{ route('staff.dashboard') }}">INVENTORY PORTAL</a>
        <button id="sidebarToggle" class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0"><i class="fas fa-bars"></i></button>
        
        <ul class="navbar-nav ms-auto me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle fa-fw"></i> 
                    {{ Auth::user()->profile->full_name ?? Auth::user()->username }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-id-card me-2"></i>My Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form id="logout-form-dropdown" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item w-100 text-start text-danger">
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
                        
                        <a class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}"
                            href="{{ route('staff.dashboard') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div> Dashboard
                        </a>

                        <div class="sb-sidenav-menu-heading">INVENTORY SUBMISSIONS</div>
                        
                        <a class="nav-link {{ request()->routeIs('staff.submissions.create') ? 'active' : '' }}"
                            href="{{ route('staff.submissions.create') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-plus-square"></i></div> New Submission
                        </a>

                        <a class="nav-link {{ request()->routeIs('staff.submissions.index') ? 'active' : '' }}" 
                           href="{{ route('staff.submissions.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-history"></i></div> My Submissions
                        </a>
                        
                        <div class="sb-sidenav-menu-heading">RESOURCES</div>
                        
                        <a class="nav-link {{ request()->routeIs('staff.assets.index') ? 'active' : '' }}" href="{{ route('staff.assets.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-boxes-stacked"></i></div>
                            <span>
                                @if(Auth::user()->unit_id)
                                    {{ Auth::user()->unit->unit_name }}
                                @elseif(Auth::user()->department_id)
                                    {{ Auth::user()->department->dept_name }}
                                @elseif(Auth::user()->institute_id)
                                    {{ Auth::user()->institute->institute_name }}
                                @elseif(Auth::user()->office_id)
                                    {{ Auth::user()->office->office_name }}
                                @elseif(Auth::user()->faculty_id)
                                    {{ Auth::user()->faculty->faculty_name }}
                                @else
                                    My Assets
                                @endif
                                Assets
                            </span>
                        </a>

                        <a class="nav-link {{ request()->routeIs('staff.guidelines.index') ? 'active' : '' }}" 
                        href="{{ route('staff.guidelines.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div> Guidelines/Manual
                        </a>
                        
                    </div>
                </div>

                <div class="sb-sidenav-footer">
                    <div class="small">Assigned to:</div>
                    <span class="text-info small">
                        @if(Auth::user()->unit_id)
                            {{-- Offices and Units are related --}}
                            <i class="fas fa-microscope me-1"></i> {{ Auth::user()->unit->unit_name }}
                            <div class="text-dim" style="font-size: 0.75rem;">
                                ({{ Auth::user()->office->office_name ?? 'Unit' }})
                            </div>
                        @elseif(Auth::user()->department_id)
                            {{-- Faculty and Departments are related --}}
                            <i class="fas fa-building-columns me-1"></i> {{ Auth::user()->department->dept_name }}
                            <div class="text-muted" style="font-size: 0.75rem;">
                                ({{ Auth::user()->faculty->faculty_name ?? 'Department' }})
                            </div>
                        @elseif(Auth::user()->institute_id)
                            {{-- Institutes stand alone or relate to Dept --}}
                            <i class="fas fa-university me-1"></i> {{ Auth::user()->institute->institute_name }}
                        @elseif(Auth::user()->office_id)
                            <i class="fas fa-briefcase me-1"></i> {{ Auth::user()->office->office_name }}
                        @else
                            <i class="fas fa-user-tag me-1"></i> General Staff
                        @endif
                    </span>
                </div>
            </nav>
        </div>

        <div id="layoutSidenav_content">
            <main class="p-4">
                {{-- Dynamic Page Header for Staff Pages --}}
                <div class="mb-4">
                    @yield('header')
                </div>

                @yield('content')
            </main>
            
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">College of Medicine &copy; 2025</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script> 
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('build/assets/js/scripts.js') }}"></script>
    {{-- You can create a staff.js or keep using admin.js if functions are shared --}}
    <script src="{{ asset('build/assets/js/admin.js') }}"></script>

    @stack('scripts')
    @auth
        @include('partials.timeout-handler')
    @endauth
</body>
</html>