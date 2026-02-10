<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Staff Portal') - COMIS</title>
    
    <!-- Tailwind and Bootstrap -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="{{ asset('build/assets/images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('build/assets/css/styles.css') }}" rel="stylesheet" />
    
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    
    @stack('styles')
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 
                        'staff-navy': '#0f172a',
                        'staff-primary': '#059669',      // Emerald-600
                        'staff-secondary': '#0d9488',    // Teal-600
                        'staff-light': '#d1fae5',        // Emerald-100
                    },
                    animation: { 
                        'shimmer-fast': 'shimmer 2s infinite',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        shimmer: {
                            '0%': { opacity: '0.7' },
                            '50%': { opacity: '1', transform: 'scale(1.02)' },
                            '100%': { opacity: '0.7' },
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Shared Helper Classes */
        .status-active { background-color: #059669; color: white; font-weight: bold; }
        .status-inactive { background-color: #dc3545; color: white; font-weight: bold; }
        .status-pending { background-color: #0d9488; color: white; font-weight: bold; }
        
        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            height: 38px;
            padding: 0.375rem 0.75rem;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 22px;
            color: #334155;
        }
        
        /* DataTables Custom Styling */
        .datatable-table th {
            background-color: #f8fafc !important;
            color: #64748b !important;
            font-weight: 700 !important;
            font-size: 0.7rem !important;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.75rem 1rem !important;
        }
    </style>
</head>

<body class="sb-nav-fixed bg-slate-100">

<nav class="sb-topnav navbar navbar-expand navbar-dark bg-staff-navy border-b border-slate-700 shadow-sm">
    <a class="navbar-brand ps-3 flex items-center space-x-2 animate-shimmer-fast" href="{{ route('staff.dashboard') }}">
        <div class="p-1 bg-emerald-600 rounded shadow-lg shadow-emerald-500/20">
            <i class="fas fa-user-tie text-white text-xs"></i>
        </div>
        <span class="font-black tracking-tighter text-white">COMIS<span class="text-emerald-400 font-light">STAFF</span></span>
    </a>
    
    <button id="sidebarToggle" class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0 text-slate-400">
        <i class="fas fa-bars"></i>
    </button>
    
    <ul class="navbar-nav ms-auto me-3 me-lg-4 items-center">
        <li class="nav-item d-none d-md-block me-4 text-right">
            <div id="liveClockTime" class="text-emerald-400 font-bold text-sm leading-none"></div>
            <div id="liveClockDate" class="text-slate-400 text-[10px] uppercase mt-1 tracking-widest"></div>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fas fa-user-circle fa-lg text-slate-400"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                <li class="px-3 py-2 bg-slate-50 border-b">
                    <span class="block text-[10px] text-slate-400 font-bold uppercase">Staff Member</span>
                    <span class="block text-sm font-bold text-slate-800">{{ Auth::user()->profile->full_name ?? Auth::user()->username }}</span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-sm py-2" href="#">
                        <i class="fas fa-user-edit me-2 text-slate-500"></i>My Profile
                    </a>
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger font-bold py-2 text-sm">
                            <i class="fas fa-power-off me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</nav>

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark bg-staff-navy">
            <div class="sb-sidenav-menu">
                <div class="nav pt-3 px-2">
                    
                    <div class="sb-sidenav-menu-heading text-slate-500 text-[10px] font-bold uppercase tracking-[0.2em] mb-2 ps-3">Overview</div>
                    <a class="nav-link rounded py-2.5 mb-1 {{ request()->routeIs('staff.dashboard') ? 'active bg-emerald-600 text-white shadow-md' : '' }}" 
                       href="{{ route('staff.dashboard') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div> 
                        <span class="text-sm">Dashboard</span>
                    </a>

                    <div class="sb-sidenav-menu-heading text-slate-500 text-[10px] font-bold uppercase tracking-[0.2em] mb-2 mt-4 ps-3">Inventory Management</div>
                    
                    <a class="nav-link rounded py-2.5 mb-1 {{ request()->routeIs('staff.submissions.create') ? 'active bg-emerald-600 text-white shadow-md' : '' }}" 
                       href="{{ route('staff.submissions.create') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-plus-square"></i></div> 
                        <span class="text-sm">New Submission</span>
                    </a>

                    <a class="nav-link rounded py-2.5 mb-1 {{ request()->routeIs('staff.submissions.index') ? 'active bg-emerald-600 text-white shadow-md' : '' }}" 
                       href="{{ route('staff.submissions.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-history"></i></div> 
                        <span class="text-sm">My Submissions</span>
                    </a>

                    <div class="sb-sidenav-menu-heading text-slate-500 text-[10px] font-bold uppercase tracking-[0.2em] mb-2 mt-4 ps-3">Resources</div>

                    <a class="nav-link rounded py-2.5 mb-1 {{ request()->routeIs('staff.assets.index') ? 'active bg-emerald-600 text-white shadow-md' : '' }}" 
                       href="{{ route('staff.assets.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-boxes-stacked"></i></div> 
                        <span class="text-sm">
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
                                My
                            @endif
                            Assets
                        </span>
                    </a>

                    <a class="nav-link rounded py-2.5 mb-1 {{ request()->routeIs('staff.guidelines.index') ? 'active bg-emerald-600 text-white shadow-md' : '' }}" 
                       href="{{ route('staff.guidelines.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div> 
                        <span class="text-sm">Guidelines/Manual</span>
                    </a>
                    
                </div>
            </div>

            <div class="sb-sidenav-footer bg-gradient-to-t from-black/50 to-transparent p-3 border-t border-slate-800 mt-auto">
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 border border-white/10 shadow-lg hover:shadow-xl transition-all">
                    <div class="flex items-start gap-3">
                        <div class="relative flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center text-white font-bold shadow-md text-sm">
                                {{ strtoupper(substr(Auth::user()->full_name ?? Auth::user()->username, 0, 2)) }}
                            </div>
                            <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 rounded-full border-2 border-staff-navy animate-pulse"></div>
                        </div>

                        <div class="flex flex-col min-w-0 flex-1">
                            <span class="text-slate-400 text-[8px] uppercase font-black leading-none mb-1.5 tracking-wider">
                                Assigned To
                            </span>

                            @if(Auth::user()->unit_id)
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <i class="fas fa-microscope text-emerald-400" style="font-size:0.7rem;"></i>
                                    <span class="text-emerald-400 text-[11px] font-bold truncate leading-tight">
                                        {{ Auth::user()->unit->unit_name }}
                                    </span>
                                </div>
                                @if(Auth::user()->office)
                                <span class="text-slate-400 text-[9px] truncate leading-none">
                                    {{ Auth::user()->office->office_name }}
                                </span>
                                @endif
                            @elseif(Auth::user()->department_id)
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <i class="fas fa-building-columns text-emerald-400" style="font-size:0.7rem;"></i>
                                    <span class="text-emerald-400 text-[11px] font-bold truncate leading-tight">
                                        {{ Auth::user()->department->dept_name }}
                                    </span>
                                </div>
                                @if(Auth::user()->faculty)
                                <span class="text-slate-400 text-[9px] truncate leading-none">
                                    {{ Auth::user()->faculty->faculty_name }}
                                </span>
                                @endif
                            @elseif(Auth::user()->institute_id)
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <i class="fas fa-university text-emerald-400" style="font-size:0.7rem;"></i>
                                    <span class="text-emerald-400 text-[11px] font-bold truncate leading-tight">
                                        {{ Auth::user()->institute->institute_name }}
                                    </span>
                                </div>
                            @elseif(Auth::user()->office_id)
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <i class="fas fa-briefcase text-emerald-400" style="font-size:0.7rem;"></i>
                                    <span class="text-emerald-400 text-[11px] font-bold truncate leading-tight">
                                        {{ Auth::user()->office->office_name }}
                                    </span>
                                </div>
                            @elseif(Auth::user()->faculty_id)
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <i class="fas fa-graduation-cap text-emerald-400" style="font-size:0.7rem;"></i>
                                    <span class="text-emerald-400 text-[11px] font-bold truncate leading-tight">
                                        {{ Auth::user()->faculty->faculty_name }}
                                    </span>
                                </div>
                            @else
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <i class="fas fa-user-tag text-emerald-400" style="font-size:0.7rem;"></i>
                                    <span class="text-emerald-400 text-[11px] font-bold truncate leading-tight">
                                        General Staff
                                    </span>
                                </div>
                            @endif

                            <div class="flex items-center gap-1.5 mt-1.5">
                                <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse-slow"></div>
                                <span class="text-emerald-400 text-[8px] font-black uppercase tracking-widest">
                                    Active
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div id="layoutSidenav_content">
        <main class="container-fluid p-4">
            {{-- Dynamic Page Header --}}
            <div class="mb-4">
                @yield('header')
            </div>

            @yield('content')
        </main>
        
        <footer class="py-3 bg-white mt-auto border-t border-slate-200">
            <div class="container-fluid px-4">
                <div class="flex items-center justify-between text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                    <div>COMIS &copy; {{ date('Y') }}</div>
                    <div class="flex items-center space-x-2">
                        <div class="w-1 h-1 bg-slate-300 rounded-full"></div>
                        <span>College of Medicine</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('build/assets/js/scripts.js') }}"></script>
<script src="{{ asset('build/assets/js/admin.js') }}"></script>

<script>
    function updateClock() {
        const now = new Date();
        const timeEl = document.getElementById('liveClockTime');
        const dateEl = document.getElementById('liveClockDate');
        if(timeEl) timeEl.innerText = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        if(dateEl) dateEl.innerText = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

@stack('scripts')

@auth
    @include('partials.timeout-handler')
@endauth

</body>
</html>