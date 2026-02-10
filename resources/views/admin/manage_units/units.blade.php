@extends('layouts.admin')

@section('title', 'Manage Units | Admin')

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
                <a href="{{ route('admin.units-management.index') }}" 
                   class="btn btn-white border border-slate-200 rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                   style="width:44px; height:44px;"
                   title="Back to Dashboard">
                    <i class="fas fa-arrow-left text-slate-400"></i>
                </a>
                <div>
                    <h1 class="fw-black text-slate-900 mb-1" style="font-size:1.75rem; letter-spacing:-0.02em;">Unit Administration</h1>
                    <p class="text-slate-600 mb-0" style="font-size:0.88rem;">Manage functional units and operational subdivisions</p>
                </div>
            </div>

            <a href="{{ route('admin.units.create') }}" 
               class="btn text-white fw-black d-flex align-items-center gap-2 rounded-3 shadow-lg"
               style="background:linear-gradient(135deg, #f59e0b, #d97706); font-size:0.82rem; letter-spacing:0.05em; text-transform:uppercase; padding:0.75rem 1.8rem; border:none; transition:transform 0.2s;">
                <i class="fas fa-plus-circle"></i> Add New Unit
            </a>
        </div>

        @if(isset($orphanCount) && $orphanCount > 0)
            <div class="rounded-4 border-start border-4 border-warning bg-white shadow-sm p-3 mb-4 d-flex align-items-center justify-content-between animate__animated animate__headShake">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-orange-50 text-orange-600 rounded-circle d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-slate-900 mb-0">{{ $orphanCount }} Hidden Departments</h6>
                        <p class="text-slate-500 mb-0 small">Linked to inactive faculties. Staff cannot select these in inventory menus.</p>
                    </div>
                </div>
                <a href="{{ route('admin.units.index', ['status' => 'hidden']) }}" class="btn btn-sm btn-dark px-3 rounded-pill fw-bold" style="font-size: 0.75rem;">VIEW HIDDEN</a>
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="row g-4 mb-5">
            {{-- Total Units --}}
            <div class="col-12 col-md-4">
                <div class="position-relative overflow-hidden rounded-4 p-4 border border-indigo-100 shadow-sm"
                    style="background:linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);">
                    <div class="position-relative d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-indigo-600" style="width:56px; height:56px;">
                            <i class="fas fa-microchip text-white" style="font-size:1.4rem;"></i>
                        </div>
                        <div>
                            <div class="text-slate-600 text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.1em; font-weight:700;">Total Units</div>
                            <div class="fw-black text-slate-900" style="font-size:2rem; line-height:1;">{{ $units->total() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Active Units --}}
            <div class="col-12 col-md-4">
                <div class="position-relative overflow-hidden rounded-4 p-4 border border-emerald-100 shadow-sm"
                    style="background:linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
                    <div class="position-relative d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-emerald-600" style="width:56px; height:56px;">
                            <i class="fas fa-toggle-on text-white" style="font-size:1.4rem;"></i>
                        </div>
                        <div>
                            <div class="text-slate-600 text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.1em; font-weight:700;">Active Units</div>
                            <div class="fw-black text-slate-900" style="font-size:2rem; line-height:1;">{{ $unit_active_count }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Assigned Unit Heads --}}
            <div class="col-12 col-md-4">
                <div class="position-relative overflow-hidden rounded-4 p-4 border border-cyan-100 shadow-sm"
                    style="background:linear-gradient(135deg, #ecfeff 0%, #cffafe 100%);">
                    <div class="position-relative d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-cyan-600" style="width:56px; height:56px;">
                            <i class="fas fa-user-shield text-white" style="font-size:1.4rem;"></i>
                        </div>
                        <div>
                            <div class="text-slate-600 text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.1em; font-weight:700;">Assigned Heads</div>
                            <div class="fw-black text-slate-900" style="font-size:2rem; line-height:1;">{{ $assignedHeadsCount }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="rounded-4 bg-white border border-slate-200 shadow-sm p-3 mb-4">
            <form action="{{ request()->url() }}" method="GET" class="row g-2 align-items-center">
                {{-- Search Input --}}
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-slate-200 bg-slate-50" placeholder="Search name or code...">
                    </div>
                </div>

                {{-- Office Filter --}}
                <div class="col-md-3">
                    <select name="office_id" class="form-select border-slate-200 bg-slate-50">
                        <option value="">All Parent Offices</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->office_id }}" {{ request('office_id') == $office->office_id ? 'selected' : '' }}>
                                {{ $office->office_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter (Added) --}}
                <div class="col-md-2">
                    <select name="status" class="form-select border-slate-200 bg-slate-50">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>Hidden (Orphaned)</option>
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100 fw-black uppercase" style="letter-spacing:0.05em; font-size:0.8rem; padding:0.7rem;">
                            Filter
                        </button>
                        
                        @if(request()->anyFilled(['q', 'office_id', 'status']))
                            <a href="{{ request()->url() }}" class="btn btn-orange-light border-orange-200 d-flex align-items-center justify-content-center px-3" 
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
                            <th class="ps-4 py-3 text-slate-600 fw-bold uppercase" style="font-size:0.75rem;">Unit Identity</th>
                            <th class="py-3 text-slate-600 fw-bold uppercase text-nowrap" style="font-size:0.75rem;">Parent Entity</th>
                            <th class="px-1 py-3 text-slate-600 fw-bold uppercase text-nowrap" style="font-size:0.75rem;">Unit Head</th>   
                            <th class="text-center py-3 text-slate-600 fw-bold uppercase" style="font-size:0.75rem;">Status</th>
                            <th class="pe-4 py-3 text-slate-600 fw-bold uppercase text-end" style="font-size:0.75rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($units as $unitItem)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-slate-900 mb-0 text-xs">{{ $unitItem->unit_name }}</div>
                                <code class="text-orange-600 small fw-bold">{{ $unitItem->unit_code }}</code>
                            </td>
                            <td>
                                @if($unitItem->office)
                                    <span class="badge bg-slate-100 text-slate-700 border border-slate-200 fw-medium">
                                        <i class="fas fa-building me-1"></i> {{ $unitItem->office->office_name }}
                                    </span>
                                @elseif($unitItem->unit)
                                    <span class="badge bg-blue-50 text-blue-700 border border-blue-100 fw-medium">
                                        <i class="fas fa-university me-1"></i> {{ $unitItem->unit->unit_name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 small italic">Independent</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div>
                                        <div class="fw-bold text-slate-800 d-flex flex-nowrap" style="font-size:0.7rem; white-space: nowrap;">{{ $unitItem->supervisor->full_name ?? $unitItem->supervisor->username ?? 'Not Assigned' }}</div>
                                        <div class="text-slate-500" style="font-size:0.72rem;">{{ $unitItem->supervisor->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $unitItem->is_active == 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }} border px-3 py-2">
                                    {{ strtoupper($unitItem->is_active) }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('admin.units.edit', $unitItem->unit_id) }}" class="btn btn-white btn-sm px-3 border-slate-200" title="Edit Unit">
                                        <i class="fas fa-edit text-slate-600"></i>
                                    </a>
                                    <form action="{{ route('admin.units.destroy', $unitItem->unit_id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-white btn-sm px-3 border-slate-200 text-danger" onclick="return confirm('Delete Unit?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-slate-600 italic">No units registered in the system.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($units->hasPages())
                <div class="p-1 bg-slate-20 border-top border-slate-100">
                    {{ $units->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900 !important; }
    .btn-white { background: #fff; }
    .btn-white:hover { background: #f8fafc; border-color: #cbd5e1; }
    .bg-emerald-100 { background-color: #d1fae5 !important; }
    .text-emerald-700 { color: #047857 !important; }
    .text-orange-600 { color: #ea580c !important; }
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

    function handleDelete(id, name) {
        if (confirm(`Are you sure you want to delete the unit "${name}"?`)) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    }
</script>
@endsection