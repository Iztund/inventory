<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>CoMUI Admin | College of Medicine, UI</title>

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
                        'admin-accent': '#f59e0b' 
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
</head>

<body class="sb-nav-fixed bg-slate-100 text-[13px] antialiased">

    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-admin-navy border-b border-slate-700 shadow-sm">
        <a class="navbar-brand ps-3 flex items-center gap-2 animate-shimmer-fast no-underline" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('build/assets/images/logo.png') }}" alt="COMIS Logo" class="w-7 h-7 object-contain brightness-110 contrast-125">
            <span class="font-black tracking-tighter text-white text-base">CoMUI</span>
        </a>
        
        <button id="sidebarToggle" class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0 text-slate-400">
            <i class="fas fa-bars"></i>
        </button>

        <span class="hidden md:inline-block font-black tracking-tighter text-white text-lg uppercase opacity-90">
        Admin Dash<span class="text-amber-400 ml-0.5">board</span>
        </span>

        <ul class="navbar-nav ms-auto me-3 me-lg-4 items-center">
            <li class="nav-item d-none d-md-block me-4 text-right">
                <div id="liveClockTime" class="text-amber-400 font-bold text-sm leading-none"></div>
                <div id="liveClockDate" class="text-slate-300 text-[10px] uppercase mt-1 tracking-widest"></div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle fa-lg text-slate-400"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end border border-white/20 shadow-2xl mt-3 p-2 backdrop-blur-xl bg-white/80 overflow-hidden" 
                    style="min-width: 220px; border-radius: 1.25rem;">
                    
                    <li class="px-3 py-3 mb-2 rounded-xl bg-gradient-to-br from-slate-50/50 to-slate-100/50 border border-white/40 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center text-white shadow-md shadow-amber-200">
                                <i class="fas fa-shield-halved text-xs"></i>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="block text-[9px] text-slate-400 font-black uppercase tracking-widest leading-none mb-1">
                                    Administrator
                                </span>
                                <span class="block font-black text-slate-800 text-[13px] truncate tracking-tight">
                                    {{ Auth::user()->full_name ?? Auth::user()->username }}
                                </span>
                                <span class="block text-[10px] text-slate-500 truncate italic">
                                    {{ Auth::user()->affiliation->primary ?? 'College of Medicine' }}
                                </span>
                            </div>
                        </div>
                    </li>

                    <div class="h-[1px] bg-slate-200/50 mx-2 mb-1"></div>

                    <li class="mt-1">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item group flex items-center px-3 py-2.5 rounded-lg text-rose-600 hover:bg-rose-500 hover:text-white transition-all duration-200 w-full">
                                <i class="fas fa-power-off me-3 text-[14px] text-rose-400 group-hover:text-white transition-colors"></i>
                                <span class="font-bold text-[12px]">Sign Out</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion bg-admin-navy border-r border-slate-800">
                <div class="sb-sidenav-menu">
                    <div class="nav pt-4 px-3">
                        
                        {{-- Section: Control Panel --}}
                        <div class="flex items-center gap-2 mb-3 px-2">
                            <span class="text-white/80 text-[11px] font-black uppercase tracking-[0.2em]">Control Panel</span>
                            <div class="h-[1px] flex-grow bg-slate-700/50"></div>
                        </div>

                        <a class="nav-link rounded px-3 py-2.5 mb-2 transition-all flex items-center group {{ request()->routeIs('admin.dashboard') ? 'bg-amber-600 text-white shadow-lg font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}" 
                        href="{{ route('admin.dashboard') }}">
                            <div class="sb-nav-link-icon text-white"><i class="fas fa-columns"></i></div> 
                            <span class="text-[13px]">Dashboard</span>
                        </a>

                        {{-- Section: Administration --}}
                        <div class="flex items-center gap-2 mb-3 mt-6 px-2">
                            <span class="text-white/80 text-[11px] font-black uppercase tracking-[0.2em]">Administration</span>
                            <div class="h-[1px] flex-grow bg-slate-700/50"></div>
                        </div>
                        
                        <a class="nav-link rounded-lg px-3 py-2.5 mb-2 transition-all flex items-center group {{ request()->routeIs('admin.submissions.*') ? 'bg-amber-600 text-white shadow-lg font-bold' : 'text-slate-300 hover:bg-white/10' }}" 
                            href="{{ route('admin.submissions.pending') }}">
                            <div class="sb-nav-link-icon">
                                <i class="fas fa-list-check {{ request()->routeIs('admin.submissions.*') ? 'text-white' : 'text-amber-400 group-hover:text-white' }}"></i>
                            </div> 
                            <span class="text-[13px]">Pending Reviews</span>
                        </a>

                        <a class="nav-link rounded-lg px-3 py-2.5 mb-2 transition-all flex items-center group {{ request()->routeIs('admin.approved_items.*') ? 'bg-amber-600 text-white shadow-lg font-bold' : 'text-slate-300 hover:bg-white/10' }}" 
                            href="{{ route('admin.approved_items.index') }}">
                            <div class="sb-nav-link-icon">
                                <i class="fas fa-check-double {{ request()->routeIs('admin.approved_items.*') ? 'text-white' : 'text-amber-400 group-hover:text-white' }}"></i>
                            </div> 
                            <span class="text-[13px]">Verified Registry</span>
                        </a>

                        <a class="nav-link rounded-lg px-3 py-2.5 mb-2 transition-all flex items-center group {{ request()->routeIs('admin.users.*') ? 'bg-amber-600 text-white shadow-lg font-bold' : 'text-slate-300 hover:bg-white/10' }}" 
                            href="{{ route('admin.users.index') }}">
                            <div class="sb-nav-link-icon">
                                <i class="fas fa-users {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-amber-400 group-hover:text-white' }}"></i>
                            </div> 
                            <span class="text-[13px]">Manage Users</span>
                        </a>

                        <a class="nav-link rounded-lg px-3 py-2.5 mb-1 transition-all flex items-center group {{ request()->routeIs('admin.units-management.*') ? 'bg-amber-600 text-white shadow-lg font-bold' : 'text-slate-300 hover:bg-white/10' }}" 
                            href="{{ route('admin.units-management.index') }}">
                            <div class="sb-nav-link-icon">
                                <i class="fas fa-university {{ request()->routeIs('admin.units-management.*') ? 'text-white' : 'text-amber-400 group-hover:text-white' }}"></i>
                            </div> 
                            <span class="text-[13px]">College Management</span>
                        </a>

                        {{-- Section: Data --}}
                        <div class="flex items-center gap-2 mb-3 mt-6 px-2">
                            <span class="text-white/80 text-[11px] font-black uppercase tracking-[0.2em]">Data & Reports</span>
                            <div class="h-[1px] flex-grow bg-slate-700/50"></div>
                        </div>

                        <a class="nav-link rounded-lg px-3 py-2.5 mb-2 transition-all flex items-center group {{ request()->routeIs('admin.bulk-assets.*') ? 'bg-amber-600 text-white shadow-lg font-bold' : 'text-slate-300 hover:bg-white/10' }}" 
                            href="{{ route('admin.bulk-assets.index') }}">
                            <div class="sb-nav-link-icon">
                                <i class="fas fa-file-import {{ request()->routeIs('admin.bulk-assets.*') ? 'text-white' : 'text-amber-400 group-hover:text-white' }}"></i>
                            </div> 
                            <span class="text-[13px]">Bulk Import</span>
                        </a>

                        <a class="nav-link rounded-lg px-3 py-2.5 mb-2 transition-all flex items-center group {{ request()->routeIs('admin.reports.*') ? 'bg-amber-600 text-white shadow-lg font-bold' : 'text-slate-300 hover:bg-white/10' }}" 
                            href="{{ route('admin.reports.index') }}">
                            <div class="sb-nav-link-icon">
                                <i class="fas fa-chart-pie {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-amber-400 group-hover:text-white' }}"></i>
                            </div> 
                            <span class="text-[13px]">Reports Index</span>
                        </a>
                        
                    </div>
                </div>

                {{-- Neat Admin Footer Card --}}
                <div class="sb-sidenav-footer bg-black/20 p-3 border-t border-slate-800 mt-auto">
                    <div class="bg-white/5 rounded-xl p-3 border border-white/10 shadow-sm hover:bg-white/10 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="relative flex-shrink-0">
                                <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center text-white font-black shadow-lg text-sm">
                                    {{ strtoupper(substr(Auth::user()->full_name ?? 'AD', 0, 2)) }}
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-amber-400 rounded-full border-2 border-admin-navy animate-pulse"></div>
                            </div>

                            <div class="flex flex-col min-w-0">
                                <span class="text-slate-200 text-[9px] uppercase font-black tracking-widest leading-none mb-1.5 opacity-80">Admin</span>
                                <span class="text-white text-[11px] font-bold truncate leading-tight">
                                    {{ Auth::user()->full_name ?? 'Administrator' }}
                                </span>
                                <span class="text-slate-200 text-[9px] truncate mt-0.5 font-medium">
                                    {{ Auth::user()->email }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <div id="layoutSidenav_content" class="flex flex-col min-h-screen">
            <main class="flex-grow p-4">
                @yield('content')
            </main>
            
            <footer class="py-3 bg-white mt-auto border-t border-slate-200">
                <div class="container-fluid px-4">
                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-600 tracking-[0.2em]">
                        <div>CoMUI © {{ date('Y') }}</div>
                        <div class="flex items-center space-x-2">
                            <div class="w-1 h-1 bg-slate-400 rounded-full"></div>
                            <img src="{{ asset('build/assets/images/logo.png') }}" alt="COMIS Logo" class="w-6 h-6 object-contain brightness-100">
                            <span class="text-slate-600">College of Medicine</span>
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
</body>
</html>