{{-- resources/views/partials/timeout-handler.blade.php --}}
<style>
    :root {
        --med-navy: #0f172a;
        --med-blue: #3b82f6;
        --med-crimson: #ef4444;
        --med-slate: #f8fafc;
    }

    #timeout-warning {
        z-index: 10000;
        width: 380px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(15, 23, 42, 0.1);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        transform: translateX(120%);
    }

    #timeout-warning.show-toast { transform: translateX(0); }

    /* Minimized State */
    #timeout-warning.minimized {
        width: 60px;
        height: 60px;
        cursor: pointer;
        border-radius: 50%;
        margin-bottom: 20px;
    }
    #timeout-warning.minimized .med-toast-body, 
    #timeout-warning.minimized .med-toast-header span,
    #timeout-warning.minimized .med-toast-header .btn-close-custom {
        display: none !important;
    }
    #timeout-warning.minimized .med-toast-header {
        padding: 18px;
        justify-content: center;
        background: var(--med-crimson);
    }

    .med-toast-header {
        background-color: var(--med-navy);
        padding: 10px 15px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .med-toast-body { padding: 20px 15px; background: var(--med-slate); }

    .timer-circle {
        width: 45px; height: 45px;
        border: 3px solid #e2e8f0;
        border-top: 3px solid var(--med-crimson);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; color: var(--med-crimson);
    }

    .btn-restore {
        background: var(--med-blue); color: white; border: none;
        padding: 8px 18px; border-radius: 6px; font-weight: 600; font-size: 0.8rem;
    }

    .progress-track { height: 4px; background: #e2e8f0; width: 100%; position: absolute; bottom: 0; left: 0; }
    #timeout-bar { height: 100%; background: var(--med-crimson); width: 100%; transition: width 1s linear; }
    
    .btn-minimize { background: none; border: none; color: white; opacity: 0.6; cursor: pointer; }
    .btn-minimize:hover { opacity: 1; }
</style>

<div id="timeout-warning" class="position-fixed bottom-0 end-0 m-4 d-none" onclick="expandToast()">
    <div class="med-toast-header">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-shield-alt"></i>
            <span class="fw-bold small text-uppercase">Security Alert</span>
        </div>
        <button class="btn-minimize btn-close-custom" onclick="minimizeToast(event)">
            <i class="fas fa-compress-alt"></i>
        </button>
    </div>
    
    <div class="med-toast-body">
        <div class="d-flex align-items-center mb-3">
            <div class="timer-circle me-3" id="timer-seconds">120</div>
            <div>
                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Session Expiring</h6>
                <p class="text-muted small mb-0">Save your work or re-authenticate.</p>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between">
            <button onclick="resetTimer(event)" class="btn-restore">RESTORE</button>
            {{-- Fixed Logout Link --}}
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
               class="text-decoration-none text-danger fw-bold small">
                Log Out
            </a>
        </div>
    </div>
    <div class="progress-track"><div id="timeout-bar"></div></div>
</div>

{{-- Hidden Logout Form --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
    const sessionLifetime = {{ config('session.lifetime') }} * 60 * 1000;
    const warningBuffer = 120 * 1000; 
    let warningTimer, logoutTimer, countdownInterval;

    function startTimers() {
        const toast = document.getElementById('timeout-warning');
        toast.classList.add('d-none');
        toast.classList.remove('show-toast', 'minimized');
        clearInterval(countdownInterval);
        clearTimeout(warningTimer);
        clearTimeout(logoutTimer);

        warningTimer = setTimeout(showWarning, sessionLifetime - warningBuffer);
        logoutTimer = setTimeout(() => {
            window.location.href = "{{ route('login') }}?reason=timeout";
        }, sessionLifetime);
    }

    function showWarning() {
        const toast = document.getElementById('timeout-warning');
        toast.classList.remove('d-none');
        setTimeout(() => toast.classList.add('show-toast'), 50);

        let timeLeft = 120;
        const countdownEl = document.getElementById('timer-seconds');
        const barEl = document.getElementById('timeout-bar');
        
        countdownInterval = setInterval(() => {
            timeLeft--;
            if (countdownEl) countdownEl.innerText = timeLeft;
            if (barEl) barEl.style.width = (timeLeft / 120) * 100 + '%';
            if (timeLeft <= 0) clearInterval(countdownInterval);
        }, 1000);
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

    function resetTimer(e) {
        if(e) e.stopPropagation();
        fetch("{{ route('dashboard') }}")
            .then(() => startTimers())
            .catch(() => window.location.reload());
    }

    window.onload = startTimers;
</script>