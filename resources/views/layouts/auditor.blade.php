<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Auditor Portal') - COMIS</title>
    <link rel="icon" type="image/png" href="{{ asset('build/assets/images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('build/assets/css/styles.css') }}" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    @stack('styles')

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 'audit-navy': '#0f172a', 'audit-indigo': '#6366f1' },
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
<body class="sb-nav-fixed bg-slate-200">
    
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-audit-navy border-b border-slate-700 shadow-sm">
        <a class="navbar-brand ps-3 flex items-center space-x-2 animate-shimmer-fast" href="{{ route('auditor.dashboard') }}">
            <div class="p-1 bg-indigo-600 rounded shadow-lg shadow-indigo-500/20">
                <i class="fas fa-shield-check text-white text-xs"></i>
            </div>
            <span class="font-black tracking-tighter text-white">COM<span class="text-indigo-400 font-light">AUDIT</span></span>
        </a>
        
        <button id="sidebarToggle" class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0 text-slate-400"><i class="fas fa-bars"></i></button>
        
        <ul class="navbar-nav ms-auto me-3 me-lg-4 items-center">
            <li class="nav-item d-none d-md-block me-4 text-right">
                <div id="liveClockTime" class="text-indigo-400 font-bold text-sm leading-none"></div>
                <div id="liveClockDate" class="text-slate-400 text-[10px] uppercase mt-1 tracking-widest"></div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle fa-lg text-slate-400"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                    <li class="px-3 py-2 bg-slate-50 border-b">
                        <span class="block text-[10px] text-slate-400 font-bold uppercase">Auditor ID</span>
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
            <!-- SLIMMER, NON-SCROLLABLE SIDEBAR -->
            <nav class="sb-sidenav accordion sb-sidenav-dark bg-audit-navy w-48 h-screen overflow-hidden flex flex-col">
                <div class="sb-sidenav-menu flex-grow overflow-hidden">
                    <div class="nav pt-1 px-2 space-y-2">
                        
                        <div class="sb-sidenav-menu-heading text-slate-200 text-[9px] font-bold uppercase tracking-[0.15em] mb-0.5 ps-2">Control Panel</div>
                        <a class="nav-link rounded py-1 mb-0.5 text-xs {{ request()->routeIs('auditor.dashboard') ? 'active bg-indigo-600 text-white shadow-sm' : 'text-slate-100 hover:bg-slate-800/80' }}" href="{{ route('auditor.dashboard') }}">
                            <div class="sb-nav-link-icon text-sm"><i class="fas fa-chart-line"></i></div> 
                            <span>Main Dashboard</span>
                        </a>

                        <div class="sb-sidenav-menu-heading text-slate-300 text-[9px] font-bold uppercase tracking-[0.15em] mb-0.5 mt-2 ps-2">Verification</div>
                        
                        <a class="nav-link rounded py-1 mb-0.5 text-xs {{ request()->routeIs('auditor.submissions.index') ? 'active bg-indigo-600 text-white shadow-sm' : 'text-slate-100 hover:bg-slate-800/80' }}" href="{{ route('auditor.submissions.index') }}">
                            <div class="sb-nav-link-icon text-sm"><i class="fas fa-list-check"></i></div> 
                            <span>Pending Reviews</span>
                            @if(($stats['pending'] ?? 0) > 0)
                                <span class="badge bg-danger ms-auto rounded-pill px-1.5 py-0.5 text-[8px] shadow-sm animate-pulse">
                                    {{ $stats['pending'] }}
                                </span>
                            @endif
                        </a>

                        <a class="nav-link rounded py-1 mb-0.5 text-xs {{ request()->routeIs('auditor.approved_items.index') ? 'active bg-indigo-600 text-white shadow-sm' : 'text-slate-100 hover:bg-slate-800/80' }}" href="{{ route('auditor.approved_items.index') }}">
                            <div class="sb-nav-link-icon text-sm"><i class="fas fa-clock-rotate-left"></i></div> 
                            <span>Approved Items</span>
                            @if(isset($stats['approved']) && $stats['approved'] > 0)
                                <span class="badge bg-slate-700 ms-auto rounded-pill px-1.5 py-0.5 text-[8px]">
                                    {{ $stats['approved'] }}
                                </span>
                            @endif
                        </a>
                        
                        <div class="sb-sidenav-menu-heading text-slate-500 text-[9px] font-bold uppercase tracking-[0.15em] mb-0.5 mt-1 ps-2">Master Registry</div>
                        
                        <a class="nav-link rounded py-1 mb-0.5 text-xs {{ request()->routeIs('auditor.registry.index') ? 'active bg-indigo-600 text-white shadow-sm' : 'text-slate-100 hover:bg-slate-800/80' }}" href="{{ route('auditor.registry.index') }}">
                            <div class="sb-nav-link-icon text-sm"><i class="fas fa-database"></i></div> 
                            <span>Central Registry</span>
                        </a>
                    </div>
                </div>

                <!-- Modern Logged As Section -->
                <div class="sb-sidenav-footer bg-gradient-to-t from-black/50 to-transparent p-2.5 border-t border-slate-700/40 mt-auto">
                    <div class="bg-white/10 backdrop-blur-md rounded-xl p-2 border border-white/10 shadow-lg hover:shadow-xl transition-all">
                        <div class="flex items-center gap-3">
                            <div class="relative flex-shrink-0">
                                <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold shadow-md text-sm">
                                    {{ strtoupper(substr(Auth::user()->profile->first_name ?? Auth::user()->username, 0, 1)) }}
                                </div>
                                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 rounded-full border-2 border-audit-navy animate-pulse"></div>
                            </div>

                            <div class="min-w-0">
                                <div class="text-white text-xs font-bold truncate leading-tight">
                                    {{ Auth::user()->full_name ?? Auth::user()->username }}
                                </div>
                                <div class="text-indigo-300 text-[9px] font-medium truncate">
                                    {{ Auth::user()->username }}
                                </div>
                                <div class="text-[8px] text-emerald-400 font-black uppercase mt-0.5 tracking-widest">
                                    Active
                                </div>
                            </div>
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
                        <div>COMIS © {{ date('Y') }}</div>
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
            document.getElementById('liveClockTime').innerText = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('liveClockDate').innerText = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
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