<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Expired</title>
    {{-- Add your CSS links here or use CDN for Tailwind/Bootstrap --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
        <div class="max-w-md w-full text-center">
            {{-- Icon --}}
            <div class="mb-8 relative inline-block">
                <div class="w-24 h-24 bg-white rounded-3xl border border-slate-200 shadow-sm flex items-center justify-center mx-auto">
                    <i class="fas fa-hourglass-end text-slate-300 text-4xl animate-pulse"></i>
                </div>
                <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center text-white shadow-lg border-4 border-slate-50">
                    <i class="fas fa-sync-alt text-xs"></i>
                </div>
            </div>

            {{-- Text --}}
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mb-3">Security Session Expired</h1>
            <p class="text-slate-500 text-sm leading-relaxed mb-8">
                For the protection of the College's inventory data, sessions are timed out after a period of inactivity. 
                <br><span class="font-bold text-slate-700">Please refresh to continue.</span>
            </p>

            {{-- Actions --}}
            <div class="space-y-3">
                <button onclick="window.location.reload()" 
                    class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 rounded-2xl transition-all flex items-center justify-center gap-3 shadow-xl shadow-slate-200">
                    <i class="fas fa-redo-alt text-xs"></i>
                    Refresh Page
                </button>
                
                <a href="{{ route('login') }}" 
                    class="block w-full text-slate-500 hover:text-slate-800 text-xs font-bold uppercase tracking-widest no-underline transition-colors py-2">
                    Return to Login Screen
                </a>
            </div>

            {{-- Footer --}}
            <div class="mt-12 pt-8 border-t border-slate-200">
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">
                    College of Medicine Inventory System
                </p>
            </div>
        </div>
    </div>
</body>
</html>