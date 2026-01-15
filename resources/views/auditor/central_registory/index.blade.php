@extends('layouts.auditor')

@section('title', 'Full Asset Registry')

@section('content')
<div class="container-fluid py-3" style="background: linear-gradient(135deg, #f4f7f6 0%, #e9ecef 100%); min-height: 100vh;">
    
    {{-- 1. RESTORED PREMIUM HEADER --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="glass-card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center p-3 border-start border-5 border-primary bg-white">
                        <div class="d-flex align-items-center">
                            <div class="glass-icon-circle text-primary me-3 shadow-sm">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Full Asset Registry</h5>
                                <div class="d-flex align-items-center">
                                    <div class="status-pointer position-relative me-2">
                                        <div class="pulse-ring"></div>
                                        <div class="pulse-dot"></div>
                                    </div>
                                    <span class="text-uppercase text-muted fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">Live Inventory Audit System</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-dark text-white px-4 py-2 rounded-4 shadow-lg text-center mt-2 mt-md-0" style="min-width: 200px; border-bottom: 3px solid #0d6efd;">
                            <h6 class="mb-0 fw-bold text-primary" style="font-size: 1rem;">₦{{ number_format($total_registry_value, 2) }}</h6>
                            <span class="fw-bold opacity-75" style="font-size: 0.55rem; text-uppercase;">Total Approved Value</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. RESTORED COMMAND CENTER FILTERS --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="glass-card border-0 shadow-sm rounded-4 p-3 bg-white border-top border-4 border-info">
                <form action="{{ route('auditor.registry.index') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden border">
                                <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-0" style="font-size: 0.8rem;" placeholder="Search ID, Staff, or Asset Details..." value="{{ request('search') }}">
                                <button class="btn btn-primary px-4 fw-bold" type="submit" style="font-size: 0.7rem;">SEARCH</button>
                            </div>
                        </div>

                        {{-- Academic Hierarchy --}}
                        <div class="col-lg-5">
                            <div class="p-2 rounded-3 bg-light-glass border border-white shadow-sm">
                                <h6 class="fw-bold text-primary text-uppercase mb-2" style="font-size: 0.55rem;"><i class="fas fa-university me-1"></i>Academic Branch</h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <select name="faculty_id" id="facultySelect" class="form-select form-select-sm border-0 shadow-sm">
                                            <option value="">-- Faculty --</option>
                                            @foreach($faculties as $f)
                                                <option value="{{ $f->faculty_id }}" {{ request('faculty_id') == $f->faculty_id ? 'selected' : '' }}>{{ $f->faculty_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6" id="departmentWrapper" style="{{ request('faculty_id') ? '' : 'display: none;' }}">
                                        <select name="department_id" id="departmentSelect" class="form-select form-select-sm border-0 shadow-sm">
                                            <option value="">-- Dept --</option>
                                            @foreach($departments as $d)
                                                <option value="{{ $d->dept_id }}" data-parent="{{ $d->faculty_id }}" {{ request('department_id') == $d->dept_id ? 'selected' : '' }}>{{ $d->dept_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Admin Hierarchy --}}
                        <div class="col-lg-5">
                            <div class="p-2 rounded-3 bg-light-glass border border-white shadow-sm">
                                <h6 class="fw-bold text-info text-uppercase mb-2" style="font-size: 0.55rem;"><i class="fas fa-building-user me-1"></i>Admin Branch</h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <select name="office_id" id="officeSelect" class="form-select form-select-sm border-0 shadow-sm">
                                            <option value="">-- Office --</option>
                                            @foreach($offices as $o)
                                                <option value="{{ $o->office_id }}" {{ request('office_id') == $o->office_id ? 'selected' : '' }}>{{ $o->office_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6" id="unitWrapper" style="{{ request('office_id') ? '' : 'display: none;' }}">
                                        <select name="unit_id" id="unitSelect" class="form-select form-select-sm border-0 shadow-sm">
                                            <option value="">-- Unit --</option>
                                            @foreach($units as $u)
                                                <option value="{{ $u->unit_id }}" data-parent="{{ $u->office_id }}" {{ request('unit_id') == $u->unit_id ? 'selected' : '' }}>{{ $u->unit_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Institute --}}
                        <div class="col-lg-2">
                            <div class="p-2 rounded-3 bg-white border shadow-sm h-100">
                                <h6 class="fw-bold text-muted text-uppercase mb-2" style="font-size: 0.55rem;">Institute</h6>
                                <select name="institute_id" class="form-select form-select-sm border-0 bg-light">
                                    <option value="">All</option>
                                    @foreach($institutes as $i)
                                        <option value="{{ $i->institute_id }}" {{ request('institute_id') == $i->institute_id ? 'selected' : '' }}>{{ $i->institute_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 text-end">
                            <a href="{{ route('auditor.registry.index') }}" class="btn btn-link text-muted fw-bold text-decoration-none x-small me-3">CLEAR</a>
                            <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm" style="font-size: 0.65rem;">FILTER RESULTS</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 3. FOUR-TAB SYSTEM --}}
    <div class="row mb-2">
        <div class="col-12">
            <ul class="nav nav-pills gap-2" id="registryTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active rounded-pill px-3 py-1 fw-bold border shadow-sm" style="font-size: 0.75rem;" data-bs-toggle="tab" data-bs-target="#pending">
                        Pending <span class="badge bg-warning text-dark ms-2">{{ $pending_submissions->count() }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link rounded-pill px-3 py-1 fw-bold border shadow-sm" style="font-size: 0.75rem;" data-bs-toggle="tab" data-bs-target="#rejected">
                        Rejected <span class="badge bg-danger text-white ms-2">{{ $rejected_submissions->count() }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link rounded-pill px-3 py-1 fw-bold border shadow-sm" style="font-size: 0.75rem;" data-bs-toggle="tab" data-bs-target="#approved">
                        Approved <span class="badge bg-success text-white ms-2">{{ $approved_submissions->count() }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link rounded-pill px-3 py-1 fw-bold border shadow-sm" style="font-size: 0.75rem;" data-bs-toggle="tab" data-bs-target="#all">
                        All History <span class="badge bg-secondary text-white ms-2">{{ $pending_submissions->count() + $approved_submissions->count() + $rejected_submissions->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    {{-- 4. RESTORED DETAILED TABLES --}}
    <div class="tab-content mt-2">
        @php 
            $tabs = [
                'pending'  => ['data' => $pending_submissions, 'color' => 'bg-dark'],
                'rejected' => ['data' => $rejected_submissions, 'color' => 'bg-danger'],
                'approved' => ['data' => $approved_submissions, 'color' => 'bg-success'],
                'all'      => ['data' => $pending_submissions->concat($approved_submissions)->concat($rejected_submissions), 'color' => 'bg-secondary']
            ]; 
        @endphp
        
        @foreach($tabs as $key => $tabInfo)
        <div class="tab-pane fade {{ $key == 'pending' ? 'show active' : '' }}" id="{{ $key }}">
            <div class="glass-card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="{{ $tabInfo['color'] }} text-white">
                            <tr style="font-size: 0.65rem; text-uppercase;">
                                <th class="ps-4 py-2">Ref ID</th>
                                <th>Source Entity</th>
                                <th>Items Contained</th>
                                <th>{{ $key == 'rejected' ? 'Rejection Reason' : 'Parent Branch' }}</th>
                                <th>Qty</th>
                                <th>{{ $key == 'pending' ? 'Est. Value' : 'Verified Value' }}</th>
                                @if($key == 'all') <th>Status</th> @endif
                                <th class="text-center pe-4">Manage</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.8rem;">
                            @forelse($tabInfo['data'] as $sub)
                            <tr>
                                <td class="ps-4 fw-bold">
                                    <span class="{{ $sub->status == 'approved' ? 'text-success' : ($sub->status == 'rejected' ? 'text-danger' : 'text-primary') }}">
                                        {{ $sub->status == 'approved' ? 'AUD-' : '#' }}{{ str_pad($sub->submission_id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td><div class="fw-bold text-dark">{{ $sub->submittedBy->unit->unit_name ?? $sub->submittedBy->department->dept_name ?? 'General' }}</div></td>
                                
                                {{-- Item Names Column --}}
                                <td>
                                    <div class="text-truncate text-muted" style="max-width: 250px; font-size: 0.75rem;">
                                        {{ $sub->items->pluck('item_name')->implode(', ') }}
                                    </div>
                                </td>

                                {{-- Dynamic Column --}}
                                <td>
                                    @if($key == 'rejected')
                                        <div class="x-small text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i>{{ Str::limit($sub->summary, 40) }}</div>
                                    @else
                                        <div class="x-small text-muted text-uppercase fw-bold">{{ $sub->submittedBy->faculty->faculty_name ?? $sub->submittedBy->office->office_name ?? 'COMUI' }}</div>
                                    @endif
                                </td>

                                <td><span class="badge bg-light text-dark border px-2">{{ $sub->items->count() }}</span></td>
                                <td class="fw-bold text-dark">₦{{ number_format($sub->items->sum(fn($i) => ($i->unit_cost ?? $i->cost ?? 0) * $i->quantity), 2) }}</td>
                                
                                @if($key == 'all')
                                <td>
                                    <span class="badge rounded-pill {{ $sub->status == 'approved' ? 'bg-success-subtle text-success border-success' : ($sub->status == 'rejected' ? 'bg-danger-subtle text-danger border-danger' : 'bg-warning-subtle text-warning border-warning') }} border px-2" style="font-size: 0.6rem;">
                                        {{ strtoupper($sub->status) }}
                                    </span>
                                </td>
                                @endif
                                
                                <td class="text-center pe-4">
                                    <a href="{{ route('auditor.submissions.show', $sub->submission_id) }}" class="btn btn-xs {{ $sub->status == 'approved' ? 'btn-outline-success' : ($sub->status == 'rejected' ? 'btn-outline-danger' : 'btn-primary') }} rounded-pill px-3 fw-bold" style="font-size: 0.65rem;">
                                        {{ $sub->status == 'pending' ? 'Process' : 'View Record' }}
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center py-5 text-muted">No {{ $key }} records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>

<style>
    .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(0,0,0,0.05) !important; }
    .glass-icon-circle { width: 35px; height: 35px; background: rgba(13, 110, 253, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(13, 110, 253, 0.2); }
    .bg-light-glass { background: rgba(248, 249, 250, 0.8); }
    .status-pointer { width: 10px; height: 10px; }
    .pulse-dot { width: 6px; height: 6px; background-color: #198754; border-radius: 50%; position: absolute; top: 2px; left: 2px; }
    .pulse-ring { border: 2px solid #198754; border-radius: 50%; height: 16px; width: 16px; position: absolute; animation: pulsate 1.5s ease-out infinite; left: -3px; top: -3px; }
    @keyframes pulsate { 0% { transform: scale(0.1); opacity: 0; } 50% { opacity: 1; } 100% { transform: scale(1.2); opacity: 0; } }
    .nav-pills .nav-link { color: #6c757d; background: #fff; transition: 0.3s; border: 1px solid transparent; }
    .nav-pills .nav-link.active { background: #0d6efd !important; color: white !important; }
    .x-small { font-size: 0.65rem; }
    .btn-xs { padding: 0.2rem 0.6rem; font-size: 0.7rem; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const facultySelect = document.getElementById('facultySelect');
    const departmentWrapper = document.getElementById('departmentWrapper');
    const departmentSelect = document.getElementById('departmentSelect');
    const deptOptions = Array.from(departmentSelect.options);

    const officeSelect = document.getElementById('officeSelect');
    const unitWrapper = document.getElementById('unitWrapper');
    const unitSelect = document.getElementById('unitSelect');
    const unitOptions = Array.from(unitSelect.options);

    facultySelect.addEventListener('change', function () {
        const selectedId = this.value;
        if (selectedId) {
            departmentWrapper.style.display = 'block';
            filterDropdown(departmentSelect, deptOptions, selectedId);
        } else {
            departmentWrapper.style.display = 'none';
            departmentSelect.value = "";
        }
    });

    officeSelect.addEventListener('change', function () {
        const selectedId = this.value;
        if (selectedId) {
            unitWrapper.style.display = 'block';
            filterDropdown(unitSelect, unitOptions, selectedId);
        } else {
            unitWrapper.style.display = 'none';
            unitSelect.value = "";
        }
    });

    function filterDropdown(selectElement, allOptions, parentId) {
        selectElement.innerHTML = '';
        selectElement.appendChild(allOptions[0]);
        allOptions.forEach(option => {
            if (option.getAttribute('data-parent') === parentId) {
                selectElement.appendChild(option);
            }
        });
    }

    if(facultySelect.value) facultySelect.dispatchEvent(new Event('change'));
    if(officeSelect.value) officeSelect.dispatchEvent(new Event('change'));
});
</script>
@endsection