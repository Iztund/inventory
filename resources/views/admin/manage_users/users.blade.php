@extends('layouts.admin')

@section('title', 'User Administration')

@section('content')

{{-- Toast notification (password copy confirmation) --}}
<div id="copyToast" 
     class="position-fixed top-0 end-0 mt-4 me-4 bg-slate-900 text-white px-4 py-3 rounded-3 shadow-lg d-flex align-items-center gap-3 opacity-0 translate-y-n2"
     style="z-index:9999; pointer-events:none; transition:opacity 0.3s, transform 0.3s;">
    <i class="fas fa-check-circle text-success"></i>
    <span class="fw-bold" style="font-size:0.82rem;">Password copied to clipboard!</span>
</div>

{{-- Main container --}}
<div class="min-vh-100 py-5 px-3 px-lg-5 bg-slate-50">
<div style="max-width:1280px;" class="mx-auto">

    {{-- Header Banner --}}
    <div class="position-relative overflow-hidden rounded-4 mb-5 p-5 bg-slate-900" style="min-height:148px; background:linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #334155 100%);">
        {{-- Decorative blobs --}}
        <div class="position-absolute rounded-circle" style="top:-70px; right:-80px; width:280px; height:280px; background:radial-gradient(circle, rgba(245,158,11,0.16) 0%, transparent 70%); pointer-events:none;"></div>
        <div class="position-absolute rounded-circle" style="bottom:-60px; left:8%; width:200px; height:200px; background:radial-gradient(circle, rgba(99,102,241,0.10) 0%, transparent 70%); pointer-events:none;"></div>
        
        <div class="position-relative d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4" style="z-index:1;">
            <div class="d-flex align-items-center gap-4">
                {{-- Back Button --}}
                <a href="{{ route('admin.dashboard') }}" 
                class="d-flex align-items-center justify-content-center rounded-3 border border-white border-opacity-10 text-white text-decoration-none shadow-sm"
                style="width:48px; height:48px; background:rgba(255,255,255,0.06); backdrop-filter:blur(10px); transition:all 0.3s ease;"
                onmouseenter="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateX(-5px)';"
                onmouseleave="this.style.background='rgba(255,255,255,0.06)'; this.style.transform='translateX(0)';"
                title="Back to Dashboard">
                    <i class="fas fa-chevron-left"></i>
                </a>

                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="d-inline-block rounded-circle bg-warning" style="width:8px; height:8px; box-shadow:0 0 8px rgba(251,191,36,0.5);"></span>
                        <span class="text-uppercase fw-bold text-slate-100" style="font-size:0.67rem; letter-spacing:0.22em;">College of Medicine</span>
                    </div>
                    <h1 class="text-white fw-black mb-0" style="font-size:1.85rem; letter-spacing:-0.03em;">User Administration</h1>
                    <p class="text-slate-100 mb-0 mt-1" style="font-size:0.82rem;">Manage access, roles &amp; credentials across the registry</p>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-3 align-items-center">  
                {{-- Stat chips (Rest of your code stays exactly the same) --}}
                <div class="d-flex gap-2">
                    <div class="rounded-3 px-3 py-2 d-flex align-items-center gap-2 border border-white border-opacity-10" style="background:rgba(255,255,255,0.06); backdrop-filter:blur(10px);">
                        <i class="fas fa-users text-info"></i>
                        <div>
                            <div class="text-slate-100 text-uppercase" style="font-size:0.57rem; letter-spacing:0.14em; line-height:1;">Total</div>
                            <div class="text-white fw-black" style="font-size:0.95rem; line-height:1.3;">{{ $users->total() }}</div>
                        </div>
                    </div>
                    
                    <div class="rounded-3 px-3 py-2 d-flex align-items-center gap-2 border border-white border-opacity-10" style="background:rgba(255,255,255,0.06); backdrop-filter:blur(10px);">
                        <i class="fas fa-check-circle text-success"></i>
                        <div>
                            <div class="text-slate-100 text-uppercase" style="font-size:0.57rem; letter-spacing:0.14em; line-height:1;">Active</div>
                            <div class="text-white fw-black" style="font-size:0.95rem; line-height:1.3;">{{ $activeCount }}</div>
                        </div>
                    </div>
                    
                    <div class="rounded-3 px-3 py-2 d-flex align-items-center gap-2 border border-white border-opacity-10" style="background:rgba(255,255,255,0.06); backdrop-filter:blur(10px);">
                        <i class="fas fa-ban text-danger"></i>
                        <div>
                            <div class="text-slate-100 text-uppercase" style="font-size:0.57rem; letter-spacing:0.14em; line-height:1;">Inactive</div>
                            <div class="text-white fw-black" style="font-size:0.95rem; line-height:1.3;">{{ $inactiveCount }}</div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.users.create') }}"
                class="btn text-white fw-black d-flex align-items-center gap-2 text-decoration-none rounded-3 shadow-lg"
                style="background:linear-gradient(135deg, #f59e0b, #d97706); font-size:0.77rem; letter-spacing:0.07em; text-transform:uppercase; padding:10px 22px; transition:transform 0.2s, box-shadow 0.2s;"
                onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 22px rgba(217,119,6,0.45)';"
                onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(217,119,6,0.35)';">
                    <i class="fas fa-user-plus"></i> Add User
                </a>
            </div>
        </div>
    </div>

    {{-- Temporary Password Alert --}}
    @if(session('default_password'))
    <div id="pwAlert" class="rounded-4 mb-4 overflow-hidden bg-warning bg-opacity-10 border border-warning border-opacity-25 shadow-sm">
        <div class="d-flex align-items-start p-4 gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 bg-warning bg-opacity-10" style="width:46px; height:46px;">
                <i class="fas fa-key text-warning"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                    <h6 class="fw-black mb-0 text-warning-emphasis" style="font-size:0.88rem;">Temporary Password Generated</h6>
                    <button type="button" class="btn-close btn-close-sm opacity-50" onclick="document.getElementById('pwAlert').remove();"></button>
                </div>
                <p class="mb-3 mt-1 text-warning-emphasis opacity-75" style="font-size:0.78rem;">Share this credential with the new user. They will be forced to change it on first login.</p>
                <div class="d-inline-flex align-items-center rounded-3 overflow-hidden bg-white border border-warning border-opacity-20">
                    <code id="defaultPasswordText" class="px-4 py-2 text-warning fw-black" style="font-size:1.05rem; letter-spacing:0.06em;">{{ session('default_password') }}</code>
                    <button type="button" class="btn btn-sm fw-bold border-0 bg-warning bg-opacity-10 text-warning-emphasis" style="font-size:0.74rem; padding:8px 14px; border-radius:0;" onclick="copyPassword(this)">
                        <i class="fas fa-copy me-1"></i>Copy
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Flash Messages --}}
    @if(session('success') || session('error'))
    @php $isSuccess = (bool)session('success'); @endphp
    <div id="flashMsg" class="rounded-4 mb-4 px-4 py-3 d-flex justify-content-between align-items-center shadow-sm {{ $isSuccess ? 'bg-success-subtle border border-success border-opacity-25' : 'bg-danger-subtle border border-danger border-opacity-25' }}">
        <div class="d-flex align-items-center gap-2">
            <i class="fas {{ $isSuccess ? 'fa-check-circle text-success' : 'fa-exclamation-triangle text-danger' }}"></i>
            <span class="fw-bold {{ $isSuccess ? 'text-success-emphasis' : 'text-danger-emphasis' }}" style="font-size:0.84rem;">{{ session('success') ?? session('error') }}</span>
        </div>
        <button type="button" class="btn-close btn-close-sm opacity-40" onclick="document.getElementById('flashMsg').remove();"></button>
    </div>
    @endif

    {{-- Search Bar --}}
    <div class="rounded-4 mb-4 p-3 bg-white border border-slate-200 shadow-sm">
        <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex gap-2 align-items-center">
            <div class="flex-grow-1 position-relative">
                <i class="fas fa-search position-absolute text-slate-400" style="left:15px; top:50%; transform:translateY(-50%); font-size:0.82rem;"></i>
                <input type="text" name="q" value="{{ request('q') }}"
                       class="form-control border-0 rounded-3 shadow-none bg-slate-50"
                       style="padding:10px 16px 10px 42px; font-size:0.88rem; color:#0f172a;"
                       placeholder="Search by name, username, or email…"
                       onfocus="this.style.background='#f1f5f9';" onblur="this.style.background='#f8fafc';">
            </div>
            @if(request('q'))
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm border-0 rounded-3 text-decoration-none text-danger bg-danger bg-opacity-10 fw-bold" style="font-size:0.82rem;">
                    <i class="fas fa-times me-1"></i>Clear
                </a>
            @endif
            <button type="submit" class="btn btn-dark border-0 rounded-3 fw-black text-white" style="font-size:0.78rem; letter-spacing:0.06em; text-transform:uppercase; padding:10px 24px;">
                Search
            </button>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="rounded-4 overflow-hidden bg-white border border-slate-200 shadow-sm">

        {{-- Desktop Table --}}
        <div class="d-none d-md-block table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-slate-50 border-bottom border-slate-200" style="border-bottom-width:2px !important;">
                    <tr>
                        <th class="ps-5 py-3 fw-black text-uppercase text-slate-600" style="font-size:0.60rem; letter-spacing:0.20em;">Identity</th>
                        <th class="py-3 fw-black text-uppercase text-slate-600" style="font-size:0.60rem; letter-spacing:0.20em;">Role</th>
                        <th class="py-3 fw-black text-uppercase text-slate-600" style="font-size:0.60rem; letter-spacing:0.20em;">Affiliation</th>
                        <th class="py-3 fw-black text-uppercase text-slate-600 text-center" style="font-size:0.60rem; letter-spacing:0.20em;">Status</th>
                        <th class="pe-5 py-3 fw-black text-uppercase text-slate-600 text-end" style="font-size:0.60rem; letter-spacing:0.20em;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $userItem)
                    @php
                        $fullName = $userItem->full_name ?: ($userItem->profile->full_name ?? 'No Name');
                        $isActive = $userItem->status == 'active';
                        // ... rest of your code
                        $roleName = $userItem->role->role_name ?? 'N/A';
                        
                        // Deterministic avatar color
                        $palette = ['#6366f1','#ec4899','#f59e0b','#10b981','#3b82f6','#8b5cf6','#ef4444','#06b6d4'];
                        $avatarBg = $palette[$userItem->user_id % count($palette)];
                        
                        // Role pill colors
                        $roleStyles = [
                            'Admin' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700'],
                            'Staff' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700'],
                            'Auditor' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700'],
                        ];
                        $rs = $roleStyles[$roleName] ?? ['bg' => 'bg-slate-200', 'text' => 'text-slate-900'];
                        
                        // Affiliation
                    @endphp

                    <tr class="border-bottom border-slate-100" 
                        style="animation:fadeInUp 0.3s ease-out both; animation-delay:{{ $index * 0.04 }}s;"
                        onmouseenter="this.style.background='#f8fafc';" 
                        onmouseleave="this.style.background='transparent';">

                        {{-- Identity --}}
                        <td class="ps-5 py-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="position-relative flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center text-white fw-black"
                                     style="width:42px; height:42px; background:{{ $avatarBg }}; box-shadow:0 3px 14px {{ $avatarBg }}44; font-size:0.87rem;">
                                    @if($isActive)
                                    <span class="position-absolute bg-success rounded-circle border border-white border-3" 
                                          style="bottom:1px; right:1px; width:13px; height:13px;"></span>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-black text-slate-900" style="font-size:0.93rem; line-height:1.3;">{{ $fullName }}</div>
                                    <div class="text-slate-600" style="font-size:0.76rem;">{{ $userItem->email ?? $userItem->username }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Role --}}
                        <td class="py-4">
                            <span class="d-inline-block rounded-2 px-3 py-1 fw-black {{ $rs['bg'] }} {{ $rs['text'] }}" 
                                  style="font-size:0.72rem; letter-spacing:0.05em; text-transform:uppercase;">
                                {{ $roleName }}
                            </span>
                        </td>

                        {{-- Affiliation --}}
                        <td class="py-4">
                            @if($userItem->affiliation->primary)
                            <div class="fw-bold text-slate-800" style="font-size:0.84rem;">
                                <i class="fas {{ $userItem->affiliation->icon }} me-1 text-slate-500" style="font-size:0.78rem;"></i>{{ $userItem->affiliation->primary }}
                            </div>
                            @endif
                            <div class="text-slate-700" style="font-size:0.75rem;">{{ $userItem->affiliation->sub }}</div>
                        </td>

                        {{-- Status --}}
                        <td class="py-4 text-center">
                            <span class="d-inline-flex align-items-center gap-2 rounded-pill px-3 py-1 fw-bold {{ $isActive ? 'bg-success-subtle text-success' : 'bg-slate-100 text-slate-600' }}" 
                                  style="font-size:0.73rem;">
                                <span class="d-inline-block rounded-circle" style="width:7px; height:7px; background:{{ $isActive ? '#22c55e' : '#cbd5e1' }};"></span>
                                {{ $isActive ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="pe-5 py-4 text-end">
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <a href="{{ route('admin.users.edit', $userItem->user_id) }}"
                                   class="btn btn-sm border-0 rounded-3 d-inline-flex align-items-center gap-1 text-decoration-none fw-bold bg-slate-100 text-slate-700"
                                   style="font-size:0.75rem; transition:all 0.18s;"
                                   onmouseenter="this.classList.remove('bg-slate-100','text-slate-700'); this.classList.add('bg-slate-900','text-white'); this.style.boxShadow='0 3px 10px rgba(15,23,42,0.25)';"
                                   onmouseleave="this.classList.remove('bg-slate-900','text-white'); this.classList.add('bg-slate-100','text-slate-700'); this.style.boxShadow='';">
                                    <i class="fas fa-pen" style="font-size:0.68rem;"></i> Edit
                                </a>
                                <form action="{{ route('admin.users.destroy', $userItem->user_id) }}" method="POST" class="m-0 d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm border-0 rounded-3 d-inline-flex align-items-center gap-1 fw-bold bg-danger bg-opacity-10 text-danger"
                                            style="font-size:0.75rem; transition:all 0.18s;"
                                            onmouseenter="this.classList.remove('bg-opacity-10'); this.classList.add('bg-opacity-100','text-white'); this.style.boxShadow='0 3px 10px rgba(220,38,38,0.25)';"
                                            onmouseleave="this.classList.add('bg-opacity-10'); this.classList.remove('bg-opacity-100','text-white'); this.style.boxShadow='';"
                                            onclick="return confirm('This action cannot be undone.\nAre you sure you want to delete this user?')">
                                        <i class="fas fa-trash-alt" style="font-size:0.68rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="5" class="py-5">
                            <div class="text-center py-4">
                                <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center bg-slate-100 mb-3" 
                                     style="width:72px; height:72px;">
                                    <i class="fas fa-users-slash text-slate-300" style="font-size:1.7rem;"></i>
                                </div>
                                <p class="fw-black text-slate-900 mb-0" style="font-size:0.93rem;">No users found</p>
                                <p class="text-slate-500 mb-0 mt-1" style="font-size:0.78rem;">Try clearing or adjusting your search query</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="d-md-none">
            @forelse($users as $userItem)
            @php
                $fullName = $userItem->full_name ?: ($userItem->profile->full_name ?? 'No Name');
                $isActive = $userItem->status == 'active';
                // ... rest of your code
                $roleName = $userItem->role->role_name ?? 'N/A';
                $palette = ['#6366f1','#ec4899','#f59e0b','#10b981','#3b82f6','#8b5cf6','#ef4444','#06b6d4'];
                $avatarBg = $palette[$userItem->user_id % count($palette)];
                $roleStyles = ['Admin'=>['bg'=>'bg-indigo-50','text'=>'text-indigo-700'],'Staff'=>['bg'=>'bg-emerald-50','text'=>'text-emerald-700'],'Auditor'=>['bg'=>'bg-amber-50','text'=>'text-amber-700']];
                $rs = $roleStyles[$roleName] ?? ['bg'=>'bg-slate-100','text'=>'text-slate-700'];
                $affSub = $userItem->department->dept_name ?? $userItem->unit->unit_name ?? 'General';
            @endphp

            <div class="p-4 border-bottom border-slate-100">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="position-relative flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center text-white fw-black"
                             style="width:46px; height:46px; background:{{ $avatarBg }}; box-shadow:0 3px 12px {{ $avatarBg }}44; font-size:0.92rem;">
                            @if($isActive)
                            <span class="position-absolute bg-success rounded-circle border border-white border-3" 
                                  style="bottom:1px; right:1px; width:13px; height:13px;"></span>
                            @endif
                        </div>
                        <div>
                            <div class="fw-black text-slate-900" style="font-size:0.93rem;">{{ $fullName }}</div>
                            <div class="text-slate-500" style="font-size:0.75rem;">{{ $userItem->email ?? $userItem->username }}</div>
                        </div>
                    </div>
                    <span class="d-inline-block rounded-2 px-2 py-1 fw-black {{ $rs['bg'] }} {{ $rs['text'] }}" 
                          style="font-size:0.68rem; letter-spacing:0.05em; text-transform:uppercase; white-space:nowrap;">
                        {{ $roleName }}
                    </span>
                </div>

                <div class="d-flex align-items-center gap-3 mt-3 ps-5 ms-2">
                    <span class="text-slate-500 fw-semibold" style="font-size:0.76rem;">
                        <i class="fas fa-building me-1"></i>{{ $affSub }}
                    </span>
                    <span class="d-inline-flex align-items-center gap-1 fw-bold {{ $isActive ? 'text-success' : 'text-slate-600' }}" 
                          style="font-size:0.74rem;">
                        <span class="d-inline-block rounded-circle" style="width:6px; height:6px; background:{{ $isActive ? '#22c55e' : '#cbd5e1' }};"></span>
                        {{ $isActive ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="d-flex gap-2 mt-3 ps-5 ms-2">
                    <a href="{{ route('admin.users.edit', $userItem->user_id) }}"
                       class="btn btn-sm border-0 rounded-3 fw-bold text-decoration-none flex-grow-1 text-center bg-slate-100 text-slate-700"
                       style="font-size:0.78rem;">
                        <i class="fas fa-pen me-1"></i> Edit
                    </a>
                    <form action="{{ route('admin.users.destroy', $userItem->user_id) }}" method="POST" class="m-0">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm border-0 rounded-3 fw-bold bg-danger bg-opacity-10 text-danger px-3"
                                style="font-size:0.78rem;"
                                onclick="return confirm('Delete this user?')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>

            @empty
            <div class="text-center py-5">
                <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center bg-slate-100 mb-3" 
                     style="width:72px; height:72px;">
                    <i class="fas fa-users-slash text-slate-300" style="font-size:1.7rem;"></i>
                </div>
                <p class="fw-bold text-slate-600 mb-0" style="font-size:0.88rem;">No users found</p>
            </div>
            @endforelse
        </div>

        {{-- Footer / Pagination --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 px-5 py-3 bg-slate-50 border-top border-slate-200">
            <span class="text-slate-500 fw-bold" style="font-size:0.76rem;">
                Showing <strong class="text-slate-700">{{ $users->firstItem() }}</strong> –
                <strong class="text-slate-700">{{ $users->lastItem() }}</strong> of
                <strong class="text-slate-900">{{ $users->total() }}</strong> users
            </span>
            {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>
</div>

{{-- Minimal CSS for animations and toast --}}
<style>
@keyframes fadeInUp {
    from { opacity:0; transform:translateY(10px); }
    to { opacity:1; transform:translateY(0); }
}

/* Pagination styling */
.pagination { margin-bottom:0; gap:4px; }
.page-link {
    border:none !important;
    border-radius:8px !important;
    color:#64748b;
    font-size:0.78rem;
    font-weight:700;
    padding:6px 13px;
    background:transparent;
    transition:all 0.15s;
}
.page-link:hover { background:#f1f5f9; color:#0f172a; }
.page-item.active .page-link {
    background:#0f172a !important;
    color:#fff !important;
    box-shadow:0 2px 8px rgba(15,23,42,0.30);
}
.page-item.disabled .page-link { opacity:0.35; }

/* Toast show state */
.toast-show {
    opacity:1 !important;
    transform:translateY(0) !important;
    pointer-events:auto !important;
}
</style>

{{-- Scripts --}}
<script>
// Auto-dismiss flash after 4s
document.addEventListener('DOMContentLoaded', function() {
    const flash = document.getElementById('flashMsg');
    if (flash) setTimeout(() => flash.remove(), 4000);
});

// Copy password to clipboard
function copyPassword(btn) {
    const text = document.getElementById('defaultPasswordText').innerText.trim();
    const orig = btn.innerHTML;
    
    navigator.clipboard.writeText(text).then(() => {
        // Button feedback
        btn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
        btn.classList.remove('bg-warning', 'bg-opacity-10');
        btn.classList.add('bg-success', 'text-white');
        
        setTimeout(() => {
            btn.innerHTML = orig;
            btn.classList.remove('bg-success', 'text-white');
            btn.classList.add('bg-warning', 'bg-opacity-10');
        }, 1900);
        
        // Toast notification
        const toast = document.getElementById('copyToast');
        toast.classList.add('toast-show');
        setTimeout(() => toast.classList.remove('toast-show'), 2300);
    });
}
</script>

@endsection