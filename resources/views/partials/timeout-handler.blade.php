{{-- resources/views/partials/timeout-handler.blade.php --}}
<style>
    :root {
        --med-navy: #0f172a;
        --med-indigo: #6366f1;
        --med-rose: #f43f5e;
        --med-slate: #f8fafc;
    }

    /* Main Container */
    #timeout-warning {
        z-index: 9999;
        width: 380px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(0,0,0,0.08);
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        transform: translateY(150%); /* Start off-screen bottom */
    }

    /* Visibility States */
    #timeout-warning.show-toast { transform: translateY(0); }
    #timeout-warning.d-none { display: none !important; }

    /* Mobile Responsive Adaptation */
    @media (max-width: 480px) {
        #timeout-warning {
            width: 100% !important;
            margin: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            border-radius: 20px 20px 0 0; /* Modern Bottom Sheet Style */
        }
    }

    /* Minimized Circle State (Floating Action Button style) */
    #timeout-warning.minimized {
        width: 60px !important;
        height: 60px !important;
        border-radius: 30px !important;
        cursor: pointer;
        bottom: 20px !important;
        right: 20px !important;
        transform: translateY(0) scale(1);
    }
    #timeout-warning.minimized .med-content, 
    #timeout-warning.minimized .btn-close-custom { display: none !important; }
    #timeout-warning.minimized .med-header { 
        border-radius: 30px; 
        height: 100%; 
        justify-content: center !important; 
        background: var(--med-rose);
    }

    .med-header {
        background: var(--med-navy);
        color: white;
        padding: 14px 20px;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    .timer-ring {
        width: 45px; height: 45px;
        border: 3px solid #f1f5f9;
        border-top: 3px solid var(--med-rose);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 900; color: var(--med-rose);
    }

    .btn-med-restore {
        background: var(--med-indigo);
        color: white; font-weight: 700; border: none;
        padding: 10px 24px; border-radius: 10px; font-size: 0.8rem;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        transition: transform 0.2s;
    }
    .btn-med-restore:active { transform: scale(0.95); }

    .progress-container { height: 5px; background: #f1f5f9; width: 100%; position: absolute; bottom: 0; }
    #timeout-bar { height: 100%; background: var(--med-rose); width: 100%; }
</style>

<div id="timeout-warning" class="position-fixed bottom-0 end-0 m-4 d-none" onclick="expandToast()">
    <div class="med-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-user-shield text-indigo-400"></i>
            <span class="text-[10px] font-black uppercase tracking-widest">Security Guard</span>
        </div>
        <button class="btn-close-custom bg-transparent border-0 text-white opacity-50" onclick="minimizeToast(event)">
            <i class="fas fa-compress-alt small"></i>
        </button>
    </div>

    <div class="med-content p-4">
        <div class="d-flex align-items-center mb-4">
            <div class="timer-ring me-3" id="timer-display">120</div>
            <div>
                <div class="text-slate-900 font-black text-sm">Session Timeout</div>
                <div class="text-slate-500 text-[11px] leading-tight">Your session in the inventory system is about to expire.</div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between">
            <button id="restore-session-btn" onclick="executeRestore(event)" class="btn-med-restore">
                KEEP WORKING
            </button>
            <form id="timeout-logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-transparent border-0 text-rose-500 font-bold text-[11px] uppercase tracking-widest">
                    Logout
                </button>
            </form>
        </div>
    </div>
    <div class="progress-container"><div id="timeout-bar"></div></div>
</div>

<script>
    /**
     * CONFIGURATION
     * Lifetime from .env (20 mins)
     */
    const LIFETIME_MINS = {{ config('session.lifetime', 20) }}; 
    const BUFFER_SECONDS = 120; // 2 minute countdown
    
    let warningTimer, logoutTimer, countdownInterval;

    function initSecurityTimers() {
        stopAllLogic();
        
        const toast = document.getElementById('timeout-warning');
        const bar = document.getElementById('timeout-bar');
        const display = document.getElementById('timer-display');
        const btn = document.getElementById('restore-session-btn');

        // Reset UI
        toast.classList.add('d-none');
        toast.classList.remove('show-toast', 'minimized');
        bar.style.width = '100%';
        bar.style.transition = 'none'; // Prevent animation on reset
        display.innerText = BUFFER_SECONDS;
        btn.innerText = "KEEP WORKING";
        btn.disabled = false;

        // Calculate timing
        const msUntilWarning = (LIFETIME_MINS * 60 * 1000) - (BUFFER_SECONDS * 1000);
        
        // 1. Set Warning trigger
        warningTimer = setTimeout(triggerWarningUI, msUntilWarning);

        // 2. Set Absolute Logout
        logoutTimer = setTimeout(forceLogout, LIFETIME_MINS * 60 * 1000);
    }

    function stopAllLogic() {
        clearTimeout(warningTimer);
        clearTimeout(logoutTimer);
        clearInterval(countdownInterval);
    }

    function triggerWarningUI() {
        const toast = document.getElementById('timeout-warning');
        const bar = document.getElementById('timeout-bar');
        
        toast.classList.remove('d-none');
        // Small delay to trigger CSS transform animation
        setTimeout(() => toast.classList.add('show-toast'), 50);

        let secondsLeft = BUFFER_SECONDS;
        const display = document.getElementById('timer-display');
        
        bar.style.transition = 'width 1s linear';

        countdownInterval = setInterval(() => {
            secondsLeft--;
            if (display) display.innerText = secondsLeft;
            if (bar) bar.style.width = (secondsLeft / BUFFER_SECONDS) * 100 + '%';

            if (secondsLeft <= 0) {
                forceLogout();
            }
        }, 1000);
    }

    function executeRestore(e) {
        if(e) e.stopPropagation();
        const btn = document.getElementById('restore-session-btn');
        btn.disabled = true;
        btn.innerText = "REFRESHING...";

        // Ping the heartbeat route
        fetch("{{ route('session.heartbeat') }}")
            .then(res => {
                if(res.ok) {
                    initSecurityTimers(); // Reset 20-minute cycle
                } else {
                    forceLogout();
                }
            })
            .catch(() => forceLogout());
    }

    function forceLogout() {
        stopAllLogic();
        window.location.href = "{{ route('login') }}?reason=timeout";
    }

    function minimizeToast(e) {
        e.stopPropagation();
        document.getElementById('timeout-warning').classList.add('minimized');
    }

    function expandToast() {
        const toast = document.getElementById('timeout-warning');
        if (toast.classList.contains('minimized')) {
            toast.classList.remove('minimized');
        }
    }

    // Initialize logic
    window.onload = initSecurityTimers;

    /**
     * MULTI-DEVICE ACTIVITY TRACKING
     * Resets the 20-minute timer if the user is active.
     */
    const activityEvents = ['mousedown', 'touchstart', 'scroll', 'keypress'];
    let debounceTimer;

    activityEvents.forEach(eventName => {
        document.addEventListener(eventName, () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const toast = document.getElementById('timeout-warning');
                // Only reset the background timer if the user isn't currently 
                // looking at the "Warning" popup.
                if (toast && toast.classList.contains('d-none')) {
                    initSecurityTimers();
                }
            }, 5000); // Check activity every 5 seconds
        }, { passive: true });
    });

    // Detect when browser comes back from "Sleep" or "Background" (Mobile Fix)
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            // Ping server to see if we're still logged in
            fetch("{{ route('session.heartbeat') }}").catch(() => forceLogout());
        }
    });
</script>