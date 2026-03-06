@extends('layouts.admin')

@section('title', 'User Administration')

@section('content')

{{-- Toast Notification --}}
<div id="copyToast" 
     class="position-fixed top-0 end-0 mt-4 me-4 px-4 py-3 rounded-4 shadow-lg d-none z-3"
     style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
    <div class="d-flex align-items-center gap-2 text-white">
        <i class="fas fa-check-circle fs-5"></i>
        <span class="fw-semibold">Password copied to clipboard!</span>
    </div>
</div>

{{-- Main Container --}}
<div class="min-vh-100 position-relative overflow-hidden" 
     style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
    
    {{-- Decorative Background Elements --}}
    <div class="position-absolute rounded-circle opacity-50" 
         style="top: -100px; right: -100px; width: 400px; height: 400px; 
                background: radial-gradient(circle, rgba(99,102,241,0.1) 0%, transparent 70%);"></div>
    <div class="position-absolute rounded-circle opacity-50" 
         style="bottom: -150px; left: -150px; width: 500px; height: 500px; 
                background: radial-gradient(circle, rgba(245,158,11,0.08) 0%, transparent 70%);"></div>

    <div class="container-fluid position-relative" style="max-width: 1600px;">
        <div class="row g-4 py-4">
            
            {{-- Header Section --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm" 
                     style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
                    <div class="card-body p-4">
                        <div class="row align-items-center justify-content-between">
                            {{-- Left Side: Back Button and Titles --}}
                            <div class="col-md-8">
                                <div class="d-flex align-items-center gap-3">
                                    <a href="{{ route('admin.dashboard') }}" 
                                    class="btn btn-light bg-white bg-opacity-10 border border-white border-opacity-20 text-white d-flex align-items-center justify-content-center rounded-3 shadow-sm"
                                    style="width: 44px; height: 44px; transition: all 0.3s ease;">
                                        <i class="fas fa-arrow-left"></i>
                                    </a>
                                    <div>
                                        <div class="mb-1">
                                            {{-- Increased opacity for visibility --}}
                                            <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50 rounded-pill px-3 py-1">
                                                <i class="fas fa-circle me-1" style="font-size: 0.4rem;"></i>
                                                <span class="fw-bold" style="font-size: 0.7rem; letter-spacing: 0.8px;">COLLEGE OF MEDICINE</span>
                                            </span>
                                        </div>
                                        <h1 class="h3 fw-bold mb-0 text-white">User Administration</h1>
                                        <p class="text-white text-opacity-75 mb-0 small">Manage system access, roles & credentials</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Extreme Right Side: Add Button --}}
                            <div class="col-md-4 text-md-end">
                                <div class="mt-3 mt-md-0">
                                    <a href="{{ route('admin.users.create') }}"
                                    class="btn btn-warning text-dark fw-bold d-inline-flex align-items-center gap-2 shadow-lg border-0 px-4 py-2 rounded-3">
                                        <i class="fas fa-user-plus"></i>
                                        <span class="text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Add New User</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="col-12">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 56px; height: 56px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                                        <i class="fas fa-users text-white fs-4"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted text-uppercase mb-1 fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Users</p>
                                        <h3 class="h2 fw-bold mb-0 text-dark">{{ $users->total() }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 56px; height: 56px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                        <i class="fas fa-check-circle text-white fs-4"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted text-uppercase mb-1 fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Active</p>
                                        <h3 class="h2 fw-bold mb-0 text-success">{{ $activeCount }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 56px; height: 56px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                                        <i class="fas fa-ban text-white fs-4"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted text-uppercase mb-1 fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Inactive</p>
                                        <h3 class="h2 fw-bold mb-0 text-danger">{{ $inactiveCount }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Password Alert --}}
            @if(session('default_password'))
            <div class="col-12">
                <div id="pwAlert" class="alert alert-warning border-0 shadow-sm mb-0">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-3 bg-warning bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0" 
                             style="width: 48px; height: 48px;">
                            <i class="fas fa-key text-warning fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0">Temporary Password Generated</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <p class="mb-3 text-dark opacity-75">Share this credential with the new user. They will be required to change it on first login.</p>
                            <div class="d-inline-flex align-items-stretch border border-warning border-opacity-25 rounded-3 overflow-hidden bg-white">
                                <code id="defaultPasswordText" class="px-3 py-2 text-warning fw-bold border-0 bg-transparent">{{ session('default_password') }}</code>
                                <button type="button" class="btn btn-warning fw-semibold border-0 rounded-0 px-4" onclick="copyPassword(this)">
                                    <i class="fas fa-copy me-2"></i>Copy Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="col-12">
                <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-3 mb-0" id="flashMsg">
                    <div class="rounded-3 bg-success bg-opacity-10 d-flex align-items-center justify-content-center" 
                         style="width: 40px; height: 40px;">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <span class="fw-semibold flex-grow-1">{{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="col-12">
                <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center gap-3 mb-0" id="flashMsg">
                    <div class="rounded-3 bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" 
                         style="width: 40px; height: 40px;">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                    </div>
                    <span class="fw-semibold flex-grow-1">{{ session('error') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            @endif

            {{-- Search Bar --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3 align-items-center">
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 pe-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" name="q" value="{{ request('q') }}"
                                           class="form-control border-start-0 ps-2"
                                           placeholder="Search by name, username, or email...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex gap-2">
                                    @if(request('q'))
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-danger flex-grow-1">
                                        <i class="fas fa-times me-1"></i>Clear
                                    </a>
                                    @endif
                                    <button type="submit" class="btn btn-dark fw-semibold flex-grow-1">
                                        <i class="fas fa-search me-1"></i>Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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
                                    <div class="fw-bold text-slate-800" style="font-size:0.84rem; white-space: nowrap;">
                                        <i class="fas {{ $userItem->affiliation->icon }} me-1 text-slate-500" style="font-size:0.78rem;"></i>{{ $userItem->affiliation->primary }}
                                    </div>
                                    @endif
                                    <div class="text-slate-700" style="font-size:0.75rem;">{{ $userItem->affiliation->secondary }}</div>
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const flash = document.getElementById('flashMsg');
    if (flash) {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(flash);
            bsAlert.close();
        }, 5000);
    }
});

function copyPassword(btn) {
    const text = document.getElementById('defaultPasswordText').innerText.trim();
    const originalHTML = btn.innerHTML;
    
    navigator.clipboard.writeText(text).then(() => {
        btn.innerHTML = '<i class="fas fa-check me-2"></i>Copied!';
        btn.classList.remove('btn-warning');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-warning');
        }, 2000);
        
        const toast = document.getElementById('copyToast');
        toast.classList.remove('d-none');
        setTimeout(() => toast.classList.add('d-none'), 3000);
    });
}
</script>

@endsection