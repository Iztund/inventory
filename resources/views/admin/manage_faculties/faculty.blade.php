@extends('layouts.admin')

@section('title', 'Manage Faculties | College of Medicine')

@section('content')
<div class="container-fluid px-4">
    {{-- Success/Error Alerts with Auto-Hide ID --}}
    @if(session('success') || session('error'))
    <div id="status-alert" class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} border-0 shadow-sm d-flex align-items-center mt-4 mb-0 animate__animated animate__fadeInDown" 
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

    <div class="min-vh-100 py-4">
        <div style="max-width:1600px;" class="mx-auto">

            {{-- Header --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-5">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('admin.units-management.index') }}" 
                    class="btn btn-white border border-slate-200 rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                    style="width:44px; height:44px;"
                    title="Back to Dashboard">
                        <i class="fas fa-arrow-left text-slate-400"></i>
                    </a>
                    <div>
                        <h1 class="fw-black text-slate-900 mb-1" style="font-size:1.75rem; letter-spacing:-0.02em;">Faculty Administration</h1>
                        <p class="text-slate-600 mb-0" style="font-size:0.88rem;">Academic structure oversight for College of Medicine</p>
                    </div>
                </div>

                <a href="{{ route('admin.faculties.create') }}" 
                   class="btn text-white fw-black d-flex align-items-center gap-2 rounded-3 shadow-lg"
                   style="background:linear-gradient(135deg, #f59e0b, #d97706); font-size:0.82rem; letter-spacing:0.05em; text-transform:uppercase; padding:0.75rem 1.8rem; border:none; transition:transform 0.2s;">
                    <i class="fas fa-plus-circle"></i> Add Faculty
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
                                <div class="text-slate-600 text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.1em; font-weight:700;">Total Faculties</div>
                                <div class="fw-black text-slate-900" style="font-size:2rem; line-height:1;">{{ $faculties->total() }}</div>
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
                                <div class="fw-black text-slate-900" style="font-size:2rem; line-height:1;">{{ $faculties->where('is_active', 'active')->count() }}</div>
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
                                <div class="text-slate-600 text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.1em; font-weight:700;">Departments</div>
                                <div class="fw-black text-slate-900" style="font-size:2rem; line-height:1;">{{ $faculties->sum('departments_count') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Search & Filter --}}
            <div class="rounded-4 mb-4 p-3 bg-white border border-slate-200 shadow-sm">
                <form action="{{ route('admin.faculties.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="input-group position-relative">
                            <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="q" value="{{ request('q') }}"
                                class="form-control border-slate-200 bg-slate-50"
                                style="padding:0.7rem; font-size:0.9rem;"
                                placeholder="Search faculty name or code...">
                            
                            @if(request('q'))
                                <a href="{{ route('admin.faculties.index', request()->except('q')) }}" 
                                class="position-absolute end-0 top-50 translate-middle-y me-5 text-orange-500 hover-orange-600 text-decoration-none"
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
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-dark w-100 fw-black" 
                                    style="padding:0.7rem; font-size:0.82rem; background:#0f172a; letter-spacing:0.03em;">
                                APPLY FILTER
                            </button>

                            @if(request()->anyFilled(['q', 'status']))
                                <a href="{{ route('admin.faculties.index') }}" 
                                class="btn btn-orange-light d-flex align-items-center justify-content-center px-3 border-orange-200"
                                style="padding:0.7rem; background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa;">
                                    <i class="fas fa-undo-alt"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="rounded-4 bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="ps-4 py-3 text-slate-500 fw-bold uppercase" style="font-size:0.75rem;">Faculty Details</th>
                                <th class="py-3 text-slate-500 fw-bold uppercase" style="font-size:0.75rem;">Dean of Faculty</th>
                                <th class="py-3 text-slate-500 fw-bold uppercase text-center" style="font-size:0.75rem;">Departments</th>
                                <th class="py-3 text-slate-500 fw-bold uppercase text-center" style="font-size:0.75rem;">Status</th>
                                <th class="pe-4 py-3 text-slate-500 fw-bold uppercase text-end" style="font-size:0.75rem;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faculties as $faculty)
                            <tr>
                                <td class="ps-4 py-4">
                                    <div class="fw-bold text-slate-900 mb-0" style="font-size:0.92rem;">{{ $faculty->faculty_name }}</div>
                                    <div class="d-flex align-items-center gap-2">
                                        {{-- ADDED ZEROS HERE --}}
                                        <code class="text-amber-600 fw-medium" style="font-size:0.7rem;">#{{ str_pad($faculty->faculty_id, 4, '0', STR_PAD_LEFT) }}</code>
                                        <span class="badge bg-slate-100 text-slate-600 border border-slate-200" style="font-size:0.7rem;">{{ $faculty->faculty_code ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-orange-50 border border-orange-100" 
                                             style="width:38px; height:38px;">
                                            <i class="fas fa-user-tie text-orange-600" style="color:#d97706;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-slate-800" style="font-size:0.85rem;">{{ $faculty->dean->full_name ?? 'Unassigned' }}</div>
                                            <div class="text-slate-500" style="font-size:0.72rem;">{{ $faculty->dean->email ?? 'no-email@med.edu' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-black text-slate-700">{{ $faculty->departments_count }}</span>
                                </td>
                                <td class="text-center">
                                    @if($faculty->is_active === 'active')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">Active</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">Inactive</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="btn-group shadow-sm">
                                        <a href="{{ route('admin.faculties.edit', $faculty->faculty_id) }}" 
                                        class="btn btn-white btn-sm px-3 border-slate-200">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        {{-- Delete Button --}}
                                        <button type="button" 
                                                onclick="handleDelete('{{ $faculty->faculty_id }}', '{{ $faculty->faculty_name }}')" 
                                                class="btn btn-white btn-sm px-3 border-slate-200 text-danger hover:bg-red-50">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        {{-- Hidden Form for this specific faculty --}}
                                        <form id="delete-form-{{ $faculty->faculty_id }}" 
                                            action="{{ route('admin.faculties.destroy', $faculty->faculty_id) }}" 
                                            method="POST" 
                                            class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-slate-400 italic">No faculties found in the medical registry.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($faculties->hasPages())
                <div class="p-4 bg-slate-50 border-top border-slate-100">
                    {{ $faculties->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .text-orange-500 { color: #f97316 !important; }
    .hover-orange-600:hover { color: #ea580c !important; }
    .btn-orange-light:hover { background-color: #ffedd5 !important; color: #c2410c !important; }
    .text-orange-600 { color: #d97706 !important; }
    .bg-orange-50 { background-color: #fffbeb !important; }
    .border-orange-100 { border-color: #fef3c7 !important; }
    .btn-white { background: #fff; color: #64748b; }
    .btn-white:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }
    .table thead th { font-weight: 800; background: #f8fafc; }
    .table tbody tr:hover { background-color: #fffcf8; }
</style>

{{-- AUTO HIDE SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.getElementById('status-alert');
        if (alert) {
            setTimeout(() => {
                // Using standard Bootstrap transition
                alert.style.transition = "opacity 0.6s ease, transform 0.6s ease";
                alert.style.opacity = "0";
                alert.style.transform = "translateY(-20px)";
                
                // Remove from DOM after transition
                setTimeout(() => {
                    alert.remove();
                }, 600);
            }, 4000); // 4 Seconds display
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