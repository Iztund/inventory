@extends('layouts.auditor')

@section('title', 'Verified Asset Registry')

@section('content')
<div class="container-fluid py-4 bg-light min-vh-100">
    
    {{-- 1. INSTITUTIONAL HEADER (Print Only) --}}
    <div class="d-none d-print-block mb-5 border-bottom border-dark pb-3">
        <div class="row align-items-center">
            <div class="col-8">
                <h2 class="fw-bold mb-0 text-dark">COLLEGE OF MEDICINE</h2>
                <h5 class="text-uppercase fw-light mb-2 text-secondary">Inventory Management System</h5>
                <p class="small mb-0 text-muted">Official Verified Asset Registry Report</p>
            </div>
            <div class="col-4 text-end">
                <div class="small fw-bold text-dark">REF: #REG-{{ date('Ymd') }}</div>
                <div class="small text-muted">Date: {{ now()->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    {{-- 2. ACTION BAR (Web Only) --}}
    <div class="row mb-4 d-print-none">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-3 bg-white border-start border-5 border-success">
                        <div class="d-flex align-items-center">
                            
                            {{-- RESTORED ANIMATED INDICATOR --}}
                            <div class="me-3 position-relative d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                <div class="pulse-ring"></div>
                                <div class="pulse-dot"></div>
                            </div>
                            
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Verified Asset Registry</h5>
                                <div class="d-flex align-items-center">
                                    <span class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 1px;">
                                        Official Audited Inventory
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-3 mt-md-0">
                            <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">
                                <i class="fas fa-print me-2"></i>Print Report
                            </button>
                            <a href="{{ route('auditor.reports.export', request()->all()) }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                                <i class="fas fa-file-excel me-2"></i>Export Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. FILTERS (Web Only) --}}
    <div class="row mb-4 d-print-none">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-white border-top border-primary border-4">
                <div class="card-body p-4">
                    <form action="{{ route('auditor.assets.index') }}" method="GET" id="filterForm">
                        <div class="row g-3">
                            {{-- Search Bar --}}
                            <div class="col-12">
                                <div class="input-group shadow-sm rounded-3 overflow-hidden border">
                                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-0 py-2 shadow-none" placeholder="Search Tag, Serial, or Asset Name..." value="{{ request('search') }}">
                                    <button class="btn btn-dark px-4 fw-bold" type="submit">SEARCH</button>
                                </div>
                            </div>

                            {{-- Academic Hierarchy --}}
                            <div class="col-lg-5">
                                <div class="p-3 rounded-3 bg-light border border-white h-100">
                                    <h6 class="fw-bold text-primary text-uppercase mb-2 small">Academic Branch</h6>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <select name="faculty_id" id="facultySelect" class="form-select form-select-sm border-0 shadow-sm">
                                                <option value="">-- Faculty --</option>
                                                @foreach($faculties as $f)
                                                    <option value="{{ $f->faculty_id }}" {{ request('faculty_id') == $f->faculty_id ? 'selected' : '' }}>{{ $f->faculty_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <div id="departmentWrapper" class="{{ request('faculty_id') ? '' : 'd-none' }}">
                                                <select name="dept_id" id="departmentSelect" class="form-select form-select-sm border-0 shadow-sm">
                                                    <option value="">-- Dept --</option>
                                                    @foreach($departments as $d)
                                                        <option value="{{ $d->dept_id }}" data-parent="{{ $d->faculty_id }}" {{ request('dept_id') == $d->dept_id ? 'selected' : '' }}>{{ $d->dept_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Admin Hierarchy --}}
                            <div class="col-lg-5">
                                <div class="p-3 rounded-3 bg-light border border-white h-100">
                                    <h6 class="fw-bold text-info text-uppercase mb-2 small">Admin Branch</h6>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <select name="office_id" id="officeSelect" class="form-select form-select-sm border-0 shadow-sm">
                                                <option value="">-- Office --</option>
                                                @foreach($offices as $o)
                                                    <option value="{{ $o->office_id }}" {{ request('office_id') == $o->office_id ? 'selected' : '' }}>{{ $o->office_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <div id="unitWrapper" class="{{ request('office_id') ? '' : 'd-none' }}">
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
                            </div>

                            {{-- Category --}}
                            <div class="col-lg-2">
                                <div class="p-3 rounded-3 bg-white border h-100">
                                    <h6 class="fw-bold text-muted text-uppercase mb-2 small">Category</h6>
                                    <select name="category_id" class="form-select form-select-sm bg-light border-0">
                                        <option value="">All</option>
                                        @foreach($categories as $c)
                                            <option value="{{ $c->category_id }}" {{ request('category_id') == $c->category_id ? 'selected' : '' }}>{{ $c->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 text-end mt-3">
                                <a href="{{ route('auditor.assets.index') }}" class="btn btn-link text-muted fw-bold text-decoration-none small me-3">RESET</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">APPLY</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. TABLE --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-dark text-white">
                    <tr class="small text-uppercase">
                        <th class="ps-4 py-3">Asset Tag</th>
                        <th>Item Details</th>
                        <th>Ownership</th>
                        <th class="text-center">Condition</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4 d-print-none">Manage</th>
                    </tr>
                </thead>
                <tbody class="text-dark small">
                    @forelse($assets as $asset)
                    <tr class="border-bottom">
                        <td class="ps-4 font-monospace fw-bold text-primary">{{ $asset->asset_tag ?: 'NO TAG' }}</td>
                        <td>
                            <div class="fw-bold">{{ $asset->item_name }}</div>
                            <small class="text-muted">SN: {{ $asset->serial_number ?: 'N/A' }}</small>
                        </td>
                        <td>
                            <div class="fw-bold">
                                @if($asset->unit) {{ $asset->unit->unit_name }}
                                @elseif($asset->department) {{ $asset->department->dept_name }}
                                @elseif($asset->office) {{ $asset->office->office_name }}
                                @else {{ $asset->faculty->faculty_name ?? 'Registry' }} @endif
                            </div>
                        </td>
                        <td class="text-center">
                            @php $item = $asset->submissionItems()->latest()->first(); @endphp
                            <span class="badge rounded-pill px-3 py-2 {{ ($item?->condition == 'Good') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} border-0 shadow-none">
                                {{ $item?->condition ?? 'Good' }}
                            </span>
                        </td>
                        <td class="text-center fw-bold">{{ strtoupper($asset->status) }}</td>
                        <td class="text-end pe-4 d-print-none">
                            <a href="{{ route('auditor.assets.show', $asset->asset_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Details</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5">No Verified Records.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 5. SIGNATURE FOOTER (Print Only) --}}
    <div class="d-none d-print-block mt-5 pt-5">
        <div class="row text-center mt-5">
            <div class="col-4">
                <hr class="border-dark border-2 mb-2 mx-4">
                <p class="small fw-bold">Inventory Officer</p>
            </div>
            <div class="col-4">
                <hr class="border-dark border-2 mb-2 mx-4">
                <p class="small fw-bold">Auditor-in-Charge</p>
            </div>
            <div class="col-4">
                <hr class="border-dark border-2 mb-2 mx-4">
                <p class="small fw-bold">Date Certified</p>
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS Pulse Animation using modern Bootstrap context */
    .pulse-dot {
        width: 8px;
        height: 8px;
        background-color: #198754; /* Bootstrap success green */
        border-radius: 50%;
        z-index: 2;
    }

    .pulse-ring {
        position: absolute;
        width: 24px;
        height: 24px;
        background-color: rgba(25, 135, 84, 0.4);
        border-radius: 50%;
        animation: ripple 1.5s infinite ease-out;
        z-index: 1;
    }

    @keyframes ripple {
        0% { transform: scale(0.5); opacity: 1; }
        100% { transform: scale(1.5); opacity: 0; }
    }

    @media print {
        .bg-light { background-color: white !important; }
        .card { border: none !important; box-shadow: none !important; }
        .table { border: 1px solid #000 !important; }
    }
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
        departmentWrapper.classList.toggle('d-none', !selectedId);
        filterDropdown(departmentSelect, deptOptions, 'data-parent', selectedId);
    });

    officeSelect.addEventListener('change', function () {
        const selectedId = this.value;
        unitWrapper.classList.toggle('d-none', !selectedId);
        filterDropdown(unitSelect, unitOptions, 'data-parent', selectedId);
    });

    function filterDropdown(selectElement, allOptions, dataAttr, parentId) {
        selectElement.innerHTML = '';
        selectElement.appendChild(allOptions[0]);
        allOptions.forEach(option => {
            if (option.getAttribute(dataAttr) === parentId) {
                selectElement.appendChild(option);
            }
        });
    }

    if(facultySelect.value) facultySelect.dispatchEvent(new Event('change'));
    if(officeSelect.value) officeSelect.dispatchEvent(new Event('change'));
});
</script>
@endsection