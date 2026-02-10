@extends('layouts.admin')

@section('title', 'Institute Administration | College of Medicine')

@section('content')
<link rel="stylesheet" href="{{ asset('build/assets/css/dashboard_admin/management.css') }}" />

{{-- Success/Error Messages --}}
@if(session('success') || session('error'))
    <div id="status-alert" class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} border-0 shadow-sm d-flex align-items-center mt-4 mb-0 animate__animated animate__fadeInDown" 
         role="alert" 
         style="border-left: 5px solid {{ session('success') ? '#10b981' : '#ef4444' }} !important; position: relative; z-index: 1050; margin-left: 1.5rem; margin-right: 1.5rem;">
        <i class="fas {{ session('success') ? 'fa-check-circle' : 'fa-exclamation-circle' }} me-3 fs-4"></i>
        <div>
            <strong class="d-block">{{ session('success') ? 'Success!' : 'Error Occurred' }}</strong>
            <span class="small">{{ session('success') ?? session('error') }}</span>
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="min-vh-100 py-4">
    <div style="max-width:1600px;" class="mx-auto px-4">

        {{-- Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-5">
            <div class="d-flex align-items-center gap-3">
                {{-- Back Button --}}
                <a href="{{ route('admin.units-management.index') }}" 
                class="btn btn-white border border-slate-200 rounded-circle d-flex align-items-center justify-content-center shadow-sm hover-translate-x" 
                style="width: 40px; height: 40px; background: #fff; color: #64748b; transition: all 0.2s;"
                title="Go Back">
                    <i class="fas fa-arrow-left"></i>
                </a>

                <div class="rounded-3 d-flex align-items-center justify-content-center bg-white border border-slate-200 shadow-sm" style="width:44px; height:44px;">
                    <i class="fas fa-microscope text-orange-600"></i>
                </div>
                <div>
                    <h1 class="fw-black text-slate-900 mb-1" style="font-size:1.75rem; letter-spacing:-0.02em;">Research Institutes</h1>
                    <p class="text-slate-600 mb-0" style="font-size:0.88rem;">Manage specialized medical research and academic institutes</p>
                </div>
            </div>

            <a href="{{ route('admin.institutes.create') }}" 
            class="btn text-white fw-black d-flex align-items-center gap-2 rounded-3 shadow-lg"
            style="background:linear-gradient(135deg, #f59e0b, #d97706); font-size:0.82rem; letter-spacing:0.05em; text-transform:uppercase; padding:0.75rem 1.8rem; border:none; transition:transform 0.2s;">
                <i class="fas fa-flask"></i> Add Institute
            </a>
        </div>

        {{-- Stats Cards --}}
        {{-- Stats Cards --}}
        <div class="row g-4 mb-5">
            {{-- Total Institutes --}}
            <div class="col-12 col-md-4">
                <div class="position-relative overflow-hidden rounded-4 p-4 border border-indigo-100 shadow-sm"
                    style="background:linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);">
                    <div class="position-relative d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-indigo-600" style="width:56px; height:56px;">
                            <i class="fas fa-university text-white" style="font-size:1.4rem;"></i>
                        </div>
                        <div>
                            <div class="text-slate-600 text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.1em; font-weight:700;">Total Institutes</div>
                            <div class="fw-black text-slate-900" style="font-size:2rem; line-height:1;">{{ $institutes->total() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Active Staff (Total across all institutes) --}}
            <div class="col-12 col-md-4">
                <div class="position-relative overflow-hidden rounded-4 p-4 border border-emerald-100 shadow-sm"
                    style="background:linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
                    <div class="position-relative d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-emerald-600" style="width:56px; height:56px;">
                            <i class="fas fa-users text-white" style="font-size:1.4rem;"></i>
                        </div>
                        <div>
                            <div class="text-slate-600 text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.1em; font-weight:700;">Active Staffs in Institutes</div>
                            <div class="fw-black text-slate-900" style="font-size:2rem; line-height:1;">{{ $activeStaffCount }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Assigned Institute Directors --}}
            <div class="col-12 col-md-4">
                <div class="position-relative overflow-hidden rounded-4 p-4 border border-cyan-100 shadow-sm"
                    style="background:linear-gradient(135deg, #ecfeff 0%, #cffafe 100%);">
                    <div class="position-relative d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-cyan-600" style="width:56px; height:56px;">
                            <i class="fas fa-user-shield text-white" style="font-size:1.4rem;"></i>
                        </div>
                        <div>
                            <div class="text-slate-600 text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.1em; font-weight:700;">Assigned Directors</div>
                            <div class="fw-black text-slate-900" style="font-size:2rem; line-height:1;">{{ $assignedHeadsCount }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="rounded-4 bg-white border border-slate-200 shadow-sm p-3 mb-4">
            <form action="{{ request()->url() }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-slate-200 bg-slate-50" placeholder="Search Institute name or code...">
                    </div>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select border-slate-200 bg-slate-50">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100 fw-black uppercase" style="letter-spacing:0.05em; font-size:0.8rem; padding:0.7rem;">
                            Filter
                        </button>
                        
                        @if(request()->anyFilled(['q', 'status']))
                            <a href="{{ request()->url() }}" class="btn btn-orange-light d-flex align-items-center justify-content-center px-3" 
                            style="background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa;" title="Clear All">
                                <i class="fas fa-undo"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Table Card --}}
        <div class="rounded-4 bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="ps-4 py-3 text-slate-600 fw-bold uppercase" style="font-size:0.75rem;">Institute Identity</th>
                            <th class="py-3 text-slate-600 fw-bold uppercase" style="font-size:0.75rem;">Director</th>
                            <th class="py-3 text-slate-600 fw-bold uppercase text-center" style="font-size:0.75rem;">Staffing</th>
                            <th class="py-3 text-slate-600 fw-bold uppercase text-center" style="font-size:0.75rem;">Status</th>
                            <th class="pe-4 py-3 text-slate-600 fw-bold uppercase text-end" style="font-size:0.75rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($institutes as $institute)
                        <tr>
                            <td class="ps-4 py-4">
                                <div class="fw-bold text-slate-900 mb-0" style="font-size:0.92rem;">{{ $institute->institute_name }}</div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <code class="text-orange-600 fw-bold" style="font-size:0.75rem;">{{ $institute->institute_code }}</code>
                                    <span class="text-slate-300">|</span>
                                    <span class="text-slate-500 uppercase fw-bold" style="font-size:0.65rem; letter-spacing: 0.05em;">Research Institute</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-orange-50 border border-orange-100" style="width:38px; height:38px;">
                                        <i class="fas fa-user-md text-orange-600"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-slate-800" style="font-size:0.85rem;">{{ $institute->director->full_name ?? 'Pending Appointment' }}</div>
                                        <div class="text-slate-500" style="font-size:0.72rem;">{{ $institute->director->email ?? "" }} </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="badge bg-indigo-50 text-indigo-700 border border-indigo-100 px-3 py-2">
                                    <i class="fas fa-users me-1"></i> {{ $institute->users_count }} Staff
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $institute->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }} border px-3 py-2">
                                    {{ $institute->is_active ? 'ACTIVE' : 'ARCHIVED' }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('admin.institutes.edit', $institute->institute_id) }}" class="btn btn-white btn-sm px-3 border-slate-200">
                                        <i class="fas fa-edit text-slate-600"></i>
                                    </a>
                                    <form action="{{ route('admin.institutes.destroy', $institute->institute_id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-white btn-sm px-3 border-slate-200 text-danger" onclick="return confirm('Delete Institute? This action is permanent.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-slate-500 italic">No institutes found matches your search.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($institutes->hasPages())
                <div class="p-3 bg-slate-50 border-top border-slate-100">
                    {{ $institutes->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900 !important; }
    .btn-white { background: #fff; }
    .btn-white:hover { background: #f8fafc; border-color: #cbd5e1; }
    .text-orange-600 { color: #ea580c !important; }
    .bg-orange-50 { background-color: #fffbeb !important; }
    .border-orange-100 { border-color: #fef3c7 !important; }
    .bg-emerald-100 { background-color: #d1fae5 !important; }
    .text-emerald-700 { color: #047857 !important; }
    .bg-indigo-50 { background-color: #eef2ff !important; }
    .text-indigo-700 { color: #4338ca !important; }
    .uppercase { text-transform: uppercase; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.getElementById('status-alert');
        if (alert) {
            setTimeout(() => {
                alert.style.transition = "opacity 0.6s ease, transform 0.6s ease";
                alert.style.opacity = "0";
                alert.style.transform = "translateY(-20px)";
                setTimeout(() => { alert.remove(); }, 600);
            }, 4000);
        }
    });
</script>
@endsection