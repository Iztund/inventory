<!-- Tailwind and Bootstrap -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="icon" type="image/png" href="{{ asset('build/assets/images/logo.png') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="{{ asset('build/assets/css/styles.css') }}" rel="stylesheet" />

<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

@stack('styles')

<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: { 
                    'admin-navy': '#0f172a', 
                    'admin-accent': '#f59e0b' // Amber accent for Admin distinction
                },
                animation: { 'shimmer-fast': 'shimmer 2s infinite' },
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
    .status-active { background-color: #28a745; color: white; font-weight: bold; }
    .status-inactive { background-color: #dc3545; color: white; font-weight: bold; }
    .status-pending { background-color: #ffc107; color: #343a40; font-weight: bold; }
</style>


</head>
<body class="sb-nav-fixed bg-slate-100">

<nav class="sb-topnav navbar navbar-expand navbar-dark bg-admin-navy border-b border-slate-700 shadow-sm">
    <a class="navbar-brand ps-3 flex items-center space-x-2 animate-shimmer-fast" href="{{ route('admin.dashboard') }}">
        <div class="p-1 bg-amber-500 rounded shadow-lg shadow-amber-500/20">
            <i class="fas fa-user-shield text-white text-xs"></i>
        </div>
        <span class="font-black tracking-tighter text-white">COMIS<span class="text-amber-400 font-light">ADMIN</span></span>
    </a>
    
    <button id="sidebarToggle" class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0 text-slate-400"><i class="fas fa-bars"></i></button>
    
    <ul class="navbar-nav ms-auto me-3 me-lg-4 items-center">
        <li class="nav-item d-none d-md-block me-4 text-right">
            <div id="liveClockTime" class="text-amber-400 font-bold text-sm leading-none"></div>
            <div id="liveClockDate" class="text-slate-400 text-[10px] uppercase mt-1 tracking-widest"></div>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fas fa-user-circle fa-lg text-slate-400"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                <li class="px-3 py-2 bg-slate-50 border-b">
                    <span class="block text-[10px] text-slate-400 font-bold uppercase">Administrator</span>
                    <span class="block text-sm font-bold text-slate-800">{{ Auth::user()->username }}</span>
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
        <nav class="sb-sidenav accordion sb-sidenav-dark bg-admin-navy">
            <div class="sb-sidenav-menu">
                <div class="nav pt-3 px-2">
                    
                    <div class="sb-sidenav-menu-heading text-slate-500 text-[10px] font-bold uppercase tracking-[0.2em] mb-2 ps-3">Control Panel</div>
                    <a class="nav-link rounded py-2.5 mb-1 {{ request()->routeIs('admin.dashboard') ? 'active bg-amber-600 text-white shadow-md' : '' }}" href="{{ route('admin.dashboard') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div> 
                        <span class="text-sm">Dashboard</span>
                    </a>

                    <div class="sb-sidenav-menu-heading text-slate-500 text-[10px] font-bold uppercase tracking-[0.2em] mb-2 mt-4 ps-3">Administration</div>
                    
                    <a class="nav-link rounded py-2.5 mb-1 {{ request()->routeIs('admin.submissions.pending') ? 'active bg-amber-600 text-white shadow-md' : '' }}" href="{{ route('admin.submissions.pending') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-list-check"></i></div> 
                        <span class="text-sm">Pending Reviews</span>
                    </a>

                    <a class="nav-link rounded py-2.5 mb-1 {{ request()->routeIs('admin.approved_items.*') ? 'active bg-amber-600 text-white shadow-md' : '' }}" href="{{ route('admin.approved_items.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-check-double"></i></div> 
                        <span class="text-sm">Verified Registry</span>
                    </a>

                    <a class="nav-link rounded py-2.5 mb-1 {{ request()->routeIs('admin.users.*') ? 'active bg-amber-600 text-white shadow-md' : '' }}" href="{{ route('admin.users.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div> 
                        <span class="text-sm">Manage Users</span>
                    </a>

                    <a class="nav-link rounded py-2.5 mb-1 {{ request()->routeIs('admin.units-management.*') || request()->routeIs('admin.departments.*') ? 'active bg-amber-600 text-white shadow-md' : '' }}" href="{{ route('admin.units-management.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-university"></i></div> 
                        <span class="text-sm">College Management</span>
                    </a>

                    <div class="sb-sidenav-menu-heading text-slate-500 text-[10px] font-bold uppercase tracking-[0.2em] mb-2 mt-4 ps-3">Inventory & Reports</div>

                    <a class="nav-link rounded py-2.5 mb-1 {{ request()->routeIs('admin.reports.*') ? 'active bg-amber-600 text-white shadow-md' : '' }}" href="{{ route('admin.reports.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div> 
                        <span class="text-sm">Comprehensive Reports</span>
                    </a>
                    
                </div>
            </div>

            <div class="sb-sidenav-footer bg-black/30 p-3 border-t border-slate-800 mt-auto">
                <div class="flex items-center space-x-2">
                    <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-slate-500 text-[8px] uppercase font-black leading-none mb-1 tracking-wider">
                            System Active
                        </span>

                        <span class="text-amber-400 text-[11px] font-bold truncate leading-tight">
                            {{ Auth::user()->full_name ?? Auth::user()->username }}
                        </span>

                        <span class="text-slate-400 text-[9px] truncate leading-none mt-0.5">
                            {{ Auth::user()->email }}
                        </span>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div id="layoutSidenav_content">
        <main class="container-fluid p-4">
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
<script src="{{ asset('build/assets/js/scripts.js') }}"></script>

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
