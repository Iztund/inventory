<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Staff Portal') - CoMUI</title>
    
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
        body { font-size: 0.85rem; line-height: 1.5; }
        
        /* Status Badges */
        .status-active { background-color: #059669; color: white; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 4px; }
        .status-inactive { background-color: #dc3545; color: white; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 4px; }
        .status-pending { background-color: #0d9488; color: white; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 4px; }
        
        /* Sidebar Structural Overrides */
        #layoutSidenav #layoutSidenav_nav { width: 240px; }
        #layoutSidenav #layoutSidenav_content { padding-left: 240px; }

        /* Custom Scrollbar for Sidebar */
        .sb-sidenav-menu::-webkit-scrollbar { width: 4px; }
        .sb-sidenav-menu::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    </style>
</head>

<body class="sb-nav-fixed bg-slate-100">

<nav class="sb-topnav navbar navbar-expand navbar-dark bg-staff-navy border-b border-slate-700 shadow-sm">
    <a class="navbar-brand ps-3 flex items-center space-x-2 animate-shimmer-fast" href="{{ route('staff.dashboard') }}">
        <img src="{{ asset('build/assets/images/logo.png') }}" alt="Logo" class="w-7 h-7 brightness-125">
        <span class="font-black tracking-tighter text-white">CoMUI</span>
    </a>
    
    <button id="sidebarToggle" class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0 text-slate-300 hover:text-white transition-colors">
        <i class="fas fa-bars fa-lg"></i>
    </button>
    
    <span class="hidden md:inline-block font-black tracking-tighter text-white text-lg uppercase opacity-90">
        Staff Dash<span class="text-emerald-400 ml-0.5">board</span>
    </span>
    
    <ul class="navbar-nav ms-auto me-3 me-lg-4 items-center">
        <li class="nav-item d-none d-md-block me-4 text-right">
            <div id="liveClockTime" class="text-emerald-400 font-black text-sm leading-none"></div>
            <div id="liveClockDate" class="text-slate-300 font-bold text-[10px] uppercase mt-1 tracking-widest"></div>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fas fa-user-circle fa-lg text-slate-300"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-xl border-0 mt-2 p-2">
                <li class="px-3 py-2 bg-slate-50 rounded-t border-b mb-1">
                    <span class="block text-[10px] text-slate-500 font-black uppercase tracking-wider">Staff Account</span>
                    <span class="block text-sm font-bold text-slate-900">{{ Auth::user()->full_name ?? Auth::user()->username }}</span>
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger font-bold py-2 rounded text-sm hover:bg-red-50">
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
        <nav class="sb-sidenav accordion sb-sidenav-dark bg-staff-navy border-r border-slate-800">
            <div class="sb-sidenav-menu">
                <div class="nav pt-4 px-3">
                    
                    {{-- Section: Overview --}}
                    <div class="flex items-center gap-2 mb-3 px-2">
                        <span class="text-slate-100 text-[11px] font-black uppercase tracking-[0.2em]">Overview</span>
                        <div class="h-[1px] flex-grow bg-slate-700/50"></div>
                    </div>

                    <a class="nav-link rounded px-3 py-2.5 mb-2 transition-all flex items-center {{ request()->routeIs('staff.dashboard') ? 'bg-emerald-600 text-white shadow-lg font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}" 
                       href="{{ route('staff.dashboard') }}">
                        <div class="sb-nav-link-icon text-white"><i class="fas fa-home"></i></div> 
                        <span class="text-[13px]">Dashboard</span>
                    </a>

                    {{-- Section: Inventory --}}
                    <div class="flex items-center gap-2 mb-3 mt-6 px-2">
                        <span class="text-slate-100 text-[11px] font-black uppercase tracking-[0.2em]">Inventory</span>
                        <div class="h-[1px] flex-grow bg-slate-700/50"></div>
                    </div>
                    
                    <a class="nav-link rounded-lg px-3 py-2.5 mb-2 transition-all flex items-center group {{ request()->routeIs('staff.submissions.create') ? 'bg-emerald-500 text-white shadow-[0_0_15px_rgba(16,185,129,0.4)] font-bold' : 'text-slate-100 hover:bg-white/10' }}" 
                         href="{{ route('staff.submissions.create') }}">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-plus-square {{ request()->routeIs('staff.submissions.create') ? 'text-white' : 'text-emerald-400 group-hover:text-white' }}"></i>
                        </div> 
                        <span class="text-[13px] {{ request()->routeIs('staff.submissions.create') ? 'text-white' : 'text-slate-100' }}">
                            New Submission
                        </span>
                    </a>

                    <a class="nav-link rounded-lg px-3 py-2.5 mb-1 transition-all flex items-center group {{ request()->routeIs('staff.submissions.index') ? 'bg-emerald-500 text-white shadow-[0_0_15px_rgba(16,185,129,0.4)] font-bold' : 'text-slate-100 hover:bg-white/10' }}" 
                    href="{{ route('staff.submissions.index') }}">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-history {{ request()->routeIs('staff.submissions.index') ? 'text-white' : 'text-emerald-400 group-hover:text-white' }}"></i>
                        </div> 
                        <span class="text-[13px] {{ request()->routeIs('staff.submissions.index') ? 'text-white' : 'text-slate-100' }}">
                            My Submissions
                        </span>
                    </a>

                    {{-- Section: Resources --}}
                    <div class="flex items-center gap-2 mb-3 mt-6 px-2">
                        <span class="text-slate-100 text-[11px] font-black uppercase tracking-[0.2em]">Resources</span>
                        <div class="h-[1px] flex-grow bg-slate-700/50"></div>
                    </div>

                    <a class="nav-link rounded-lg px-3 py-2.5 mb-2 transition-all flex items-center group {{ request()->routeIs('staff.assets.index') ? 'bg-emerald-600 text-white shadow-[0_0_15px_rgba(16,185,129,0.4)] font-bold' : 'text-slate-100 hover:bg-white/10' }}" 
                    href="{{ route('staff.assets.index') }}">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-boxes-stacked {{ request()->routeIs('staff.assets.index') ? 'text-white' : 'text-emerald-400 group-hover:text-white' }}"></i>
                        </div> 
                        <span class="text-[13px] truncate {{ request()->routeIs('staff.assets.index') ? 'text-white' : 'text-slate-100' }}">
                            {{ Auth::user()->organization_name }} Assets
                        </span>
                    </a>

                    <a class="nav-link rounded-lg px-3 py-2.5 mb-1 transition-all flex items-center group {{ request()->routeIs('staff.guidelines.index') ? 'bg-emerald-600 text-white shadow-[0_0_15px_rgba(16,185,129,0.4)] font-bold' : 'text-slate-100 hover:bg-white/10' }}" 
                    href="{{ route('staff.guidelines.index') }}">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-file-alt {{ request()->routeIs('staff.guidelines.index') ? 'text-white' : 'text-emerald-400 group-hover:text-white' }}"></i>
                        </div> 
                        <span class="text-[13px] {{ request()->routeIs('staff.guidelines.index') ? 'text-white' : 'text-slate-100' }}">
                            Guidelines / Manual
                        </span>
                    </a>
                    
                </div>
            </div>

            {{-- Sidebar Footer --}}
            <div class="sb-sidenav-footer bg-black/40 p-3 border-t border-slate-700 mt-auto">
                <div class="bg-white/5 rounded-xl p-3 border border-white/10 shadow-sm hover:bg-white/10 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="relative flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center text-white font-black shadow-lg text-sm">
                                {{ strtoupper(substr(Auth::user()->full_name ?? Auth::user()->username, 0, 2)) }}
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-400 rounded-full border-2 border-staff-navy animate-pulse"></div>
                        </div>

                        <div class="flex flex-col min-w-0">
                            <span class="text-slate-100 text-[9px] uppercase font-black tracking-widest leading-none mb-1.5 opacity-80">Affiliation</span>
                            <span class="text-emerald-400 text-[11px] font-bold truncate leading-tight">
                                {{ Auth::user()->affiliation->primary ?? 'College Staff' }}
                            </span>
                            <span class="text-slate-300 text-[9px] truncate mt-0.5 font-medium">
                                {{ Auth::user()->email }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div id="layoutSidenav_content">
        <main class="container-fluid p-4">
            <div class="mb-4">
                @yield('header')
            </div>
            @yield('content')
        </main>
        
        <footer class="py-4 bg-white mt-auto border-t border-slate-200">
            <div class="container-fluid px-4">
                <div class="flex items-center justify-between text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">
                    <div>COMIS &copy; {{ date('Y') }}</div>
                    <div class="flex items-center space-x-2">
                        <div class="w-1.5 h-1.5 bg-slate-300 rounded-full"></div>
                        <span>College of Medicine, UI</span>
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

<script>
    function updateClock() {
        const now = new Date();
        const timeEl = document.getElementById('liveClockTime');
        const dateEl = document.getElementById('liveClockDate');
        if(timeEl) timeEl.innerText = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
        if(dateEl) dateEl.innerText = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

@stack('scripts')

</body>
</html>