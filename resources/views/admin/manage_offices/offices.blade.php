@extends('layouts.admin')

@section('title', 'Manage Offices | College of Medicine')

@section('content')
<div class="min-vh-100 py-4 px-3 px-lg-5 bg-slate-50">
    <div style="max-width:1600px;" class="mx-auto">

        {{-- Success/Error Alerts with Auto-Hide ID --}}
        @if(session('success') || session('error'))
        <div id="status-alert" class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} border-0 shadow-sm d-flex align-items-center mb-4 animate__animated animate__fadeInDown" 
             role="alert" 
             style="border-left: 5px solid {{ session('success') ? '#10b981' : '#ef4444' }} !important; position: relative; z-index: 1050;">
            <i class="fas {{ session('success') ? 'fa-check-circle' : 'fa-exclamation-circle' }} me-3 fs-4"></i>
            <div>
                <strong class="d-block">{{ session('success') ? 'Success!' : 'Error Occurred' }}</strong>
                <span class="small">{{ session('success') ?? session('error') }}</span>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        {{-- Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-5">
            <div class="d-flex align-items-center gap-3">
                <a href="{{route('admin.units-management.index')}}" 
                class="btn btn-white border border-slate-200 rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                style="width:44px; height:44px;"
                title="Back to Dashboard">
                    <i class="fas fa-arrow-left text-slate-400"></i>
                </a>
                <div>
                    <h1 class="fw-black text-slate-900 mb-1" style="font-size:1.75rem; letter-spacing:-0.02em;">Administrative Offices</h1>
                    <p class="text-slate-600 mb-0" style="font-size:0.88rem;">Manage administrative units and office heads</p>
                </div>
            </div>

            <a href="{{ route('admin.offices.create') }}" class="btn text-white fw-black d-flex align-items-center gap-2 rounded-3 shadow-lg"
               style="background:linear-gradient(135deg, #f59e0b, #d97706); font-size:0.82rem; letter-spacing:0.05em; text-transform:uppercase; padding:0.75rem 1.8rem; border:none;">
                <i class="fas fa-plus-circle"></i> Add Office
            </a>
        </div>

        {{-- Stats Cards with Glassmorphism --}}
        <div class="row g-4 mb-5">
            <div class="col-12 col-md-4">
                <div class="position-relative overflow-hidden rounded-4 p-4 border border-indigo-100 shadow-sm"
                    style="background:linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);">
                    <div class="position-absolute" 
                        style="top:-20px; right:-20px; width:100px; height:100px; background:radial-gradient(circle, rgba(79,70,229,0.15) 0%, transparent 70%); border-radius:50%;"></div>
                    <div class="position-relative d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-indigo-600"
                            style="width:56px; height:56px; box-shadow:0 4px 14px rgba(79,70,229,0.3);">
                            <i class="fas fa-university text-white" style="font-size:1.4rem;"></i>
                        </div>
                        <div>
                            <div class="text-slate-600 text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.1em; font-weight:700;">Total Offices</div>
                            <div class="fw-black text-slate-900" style="font-size:2rem; line-height:1;">{{ $offices->total() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="position-relative overflow-hidden rounded-4 p-4 border border-emerald-100 shadow-sm"
                    style="background:linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
                    <div class="position-absolute" 
                        style="top:-20px; right:-20px; width:100px; height:100px; background:radial-gradient(circle, rgba(5,150,105,0.15) 0%, transparent 70%); border-radius:50%;"></div>
                    <div class="position-relative d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-emerald-600"
                            style="width:56px; height:56px; box-shadow:0 4px 14px rgba(5,150,105,0.3);">
                            <i class="fas fa-check-circle text-white" style="font-size:1.4rem;"></i>
                        </div>
                        <div>
                            <div class="text-slate-600 text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.1em; font-weight:700;">Active</div>
                            <div class="fw-black text-slate-900" style="font-size:2rem; line-height:1;">{{ $offices->where('is_active', 'active')->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="position-relative overflow-hidden rounded-4 p-4 border border-cyan-100 shadow-sm"
                    style="background:linear-gradient(135deg, #ecfeff 0%, #cffafe 100%);">
                    <div class="position-absolute" 
                        style="top:-20px; right:-20px; width:100px; height:100px; background:radial-gradient(circle, rgba(6,182,212,0.15) 0%, transparent 70%); border-radius:50%;"></div>
                    <div class="position-relative d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-cyan-600"
                            style="width:56px; height:56px; box-shadow:0 4px 14px rgba(6,182,212,0.3);">
                            <i class="fas fa-building text-white" style="font-size:1.4rem;"></i>
                        </div>
                        <div>
                            <div class="text-slate-600 text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.1em; font-weight:700;">Units</div>
                            <div class="fw-black text-slate-900" style="font-size:2rem; line-height:1;">{{ $offices->sum('units_count') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search & Filter --}}
        <div class="rounded-4 mb-4 p-3 bg-white border border-slate-200 shadow-sm">
            <form action="{{ route('admin.offices.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="input-group position-relative">
                        <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="form-control border-slate-200 bg-slate-50"
                            style="padding:0.7rem; font-size:0.9rem;"
                            placeholder="Search office name or code...">
                        
                        @if(request('q'))
                            <a href="{{ route('admin.offices.index', request()->except('q')) }}" 
                            class="position-absolute end-0 top-50 translate-middle-y me-3 text-orange-500 hover-orange-600 text-decoration-none"
                            style="z-index: 10;">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <select name="status" class="form-select border-slate-200 bg-slate-50" style="padding:0.7rem; font-size:0.9rem;">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Archived</option>
                        <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>Hidden</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100 fw-black" 
                                style="padding:0.7rem; font-size:0.82rem; background:#0f172a; letter-spacing:0.03em; border:none;">
                            APPLY FILTER
                        </button>

                        @if(request()->anyFilled(['q', 'status']))
                            <a href="{{ route('admin.offices.index') }}" 
                            class="btn d-flex align-items-center justify-content-center px-3"
                            title="Clear All Filters"
                            style="padding:0.7rem; background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; border-radius: 0.5rem;">
                                <i class="fas fa-undo-alt"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Table Card --}}
        <div class="rounded-4 bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="ps-4 py-3 text-slate-500 fw-bold uppercase" style="font-size:0.75rem;">Office Name & Code</th>
                            <th class="py-3 text-slate-500 fw-bold uppercase" style="font-size:0.75rem;">Office Head</th>
                            <th class="py-3 text-slate-500 fw-bold uppercase text-center" style="font-size:0.75rem;">Units</th>
                            <th class="py-3 text-slate-500 fw-bold uppercase text-center" style="font-size:0.75rem;">Status</th>
                            <th class="pe-4 py-3 text-slate-500 fw-bold uppercase text-end" style="font-size:0.75rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($offices as $office)
                        <tr>
                            <td class="ps-4 py-4">
                                <div class="fw-bold text-slate-900 mb-0" style="font-size:0.92rem;">{{ $office->office_name }}</div>
                                <div class="d-flex align-items-center gap-2">
                                    {{-- Leading zeros added to ID --}}
                                    <code class="text-amber-600 fw-medium" style="font-size:0.7rem;">#{{ str_pad($office->office_id, 4, '0', STR_PAD_LEFT) }}</code>
                                    <span class="badge bg-orange-50 text-orange-700 border border-orange-100" style="font-size:0.7rem;">{{ $office->office_code ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-slate-100" style="width:38px; height:38px;">
                                        <i class="fas fa-user-tie text-slate-500"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-slate-800" style="font-size:0.85rem;">{{ $office->head->full_name ?? $office->head->username ?? 'Not Assigned' }}</div>
                                        <div class="text-slate-500" style="font-size:0.72rem;">{{ $office->head->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="fw-black text-slate-700">{{ $office->units_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $office->is_active === 'active' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle' }} border px-3 py-2">
                                    {{ ucfirst($office->is_active) }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('admin.offices.edit', $office->office_id) }}" 
                                    class="btn btn-white btn-sm px-3 border-slate-200">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    {{-- Delete Button --}}
                                    <button type="button" 
                                            onclick="handleDelete('{{ $office->office_id }}', '{{ $office->office_name }}')" 
                                            class="btn btn-white btn-sm px-3 border-slate-200 text-danger hover:bg-red-50">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    {{-- Hidden Form for this specific office --}}
                                    <form id="delete-form-{{ $office->office_id }}" 
                                        action="{{ route('admin.offices.destroy', $office->office_id) }}" 
                                        method="POST" 
                                        class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                                
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-slate-400">No offices registered.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .text-orange-500 { color: #f97316 !important; }
    .hover-orange-600:hover { color: #ea580c !important; }
    .btn-white { background: #fff; color: #64748b; }
    .btn-white:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }
    
    .form-control:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 0.25rem rgba(249, 115, 22, 0.1);
    }
</style>

{{-- Auto-Hide Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.getElementById('status-alert');
        if (alert) {
            setTimeout(() => {
                alert.style.transition = "opacity 0.6s ease, transform 0.6s ease";
                alert.style.opacity = "0";
                alert.style.transform = "translateY(-15px)";
                setTimeout(() => alert.remove(), 200);
            }, 1000);
        }
    });
    function handleDelete(id, name) {
    // Using a standard confirm for now, or you can use SweetAlert2
    if (confirm(`Are you sure you want to delete the "${name}"? This action cannot be undone.`)) {
        // Find the specific form and submit it
        document.getElementById(`delete-form-${id}`).submit();
    }
}
</script>
@endsection