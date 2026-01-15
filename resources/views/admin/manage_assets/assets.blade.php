@extends('layouts.admin')

@section('title', 'Asset Inventory')
@section('active_link', 'assets')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* 1. Statistics Cards Styling */
    .stat-card { border: none; border-radius: 16px; transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
    .icon-shape { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
    
    /* 2. Justified Filter Grid */
    .grid-filters {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr 1fr 1fr;
        gap: 15px;
        align-items: end;
    }

    /* 3. Input Consitency */
    .form-control-custom, .btn-filter-action, .select2-container--default .select2-selection--single {
        height: 46px !important;
        border: 1px solid #e2e8f0 !important;
        background-color: #f8fafc !important;
        border-radius: 10px !important;
        display: flex;
        align-items: center;
    }

    .btn-filter-action {
        border: none !important;
        font-weight: 600;
    }

    /* 4. Table Styling */
    .pathway-badge { 
        font-size: 0.65rem; 
        text-transform: uppercase; 
        font-weight: 800; 
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-block;
        margin-bottom: 4px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 px-lg-5" style="background-color: #f8fafc; min-height: 100vh;">
    
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Asset Inventory Management</h4>
            <p class="text-muted small mb-0">Track and manage university resources across all departments</p>
        </div>
        <a href="{{ route('admin.assets.create') }}" class="btn btn-dark px-4 py-2 rounded-3 shadow-sm">
            <i class="fas fa-plus me-2"></i>New Asset
        </a>
    </div>

    {{-- 1. Dynamic Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card shadow-sm bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-shape bg-dark bg-opacity-10 text-dark me-3"><i class="fas fa-boxes"></i></div>
                    <div><small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Total Inventory</small><h4 class="fw-bold mb-0">{{ $statCounts['total'] }}</h4></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-shape bg-primary bg-opacity-10 text-primary me-3"><i class="fas fa-building"></i></div>
                    <div><small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Admin Units</small><h4 class="fw-bold mb-0">{{ $statCounts['admin'] }}</h4></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-shape bg-info bg-opacity-10 text-info me-3"><i class="fas fa-university"></i></div>
                    <div><small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Academic Depts</small><h4 class="fw-bold mb-0">{{ $statCounts['academic'] }}</h4></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-shape bg-warning bg-opacity-10 text-warning me-3"><i class="fas fa-microscope"></i></div>
                    <div><small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Institutes</small><h4 class="fw-bold mb-0">{{ $statCounts['institute'] }}</h4></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Visual Analytics Section --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-4 text-dark">Asset Status Breakdown</h6>
                    <div style="height: 250px;"><canvas id="statusDoughnut"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-4 text-dark">Top Departmental Allocation</h6>
                    <div style="height: 250px;"><canvas id="deptBarChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Working Filter Section --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.assets.index') }}" class="grid-filters">
                <div>
                    <label class="small fw-bold mb-2 text-muted text-uppercase" style="font-size: 0.65rem;">Search Assets</label>
                    <input type="text" name="search" class="form-control form-control-custom px-3" placeholder="Description or Serial..." value="{{ request('search') }}">
                </div>
                <div>
                    <label class="small fw-bold mb-2 text-muted text-uppercase" style="font-size: 0.65rem;">Admin Units</label>
                    <select name="unit_id" class="form-select select2-units">
                        <option value="">All Units</option>
                        @if(request('unit_id') && isset($currentUnit))
                            <option value="{{ request('unit_id') }}" selected>{{ $currentUnit->unit_name }}</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label class="small fw-bold mb-2 text-muted text-uppercase" style="font-size: 0.65rem;">Academic Depts</label>
                    <select name="dept_id" class="form-select select2-depts">
                        <option value="">All Departments</option>
                        @if(request('dept_id') && isset($currentDept))
                            <option value="{{ request('dept_id') }}" selected>{{ $currentDept->dept_name }}</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label class="small fw-bold mb-2 text-muted text-uppercase" style="font-size: 0.65rem;">Institutes</label>
                    <select name="institute_id" class="form-select select2-institutes">
                        <option value="">All Institutes</option>
                        @if(request('institute_id') && isset($currentInstitute))
                            <option value="{{ request('institute_id') }}" selected>{{ $currentInstitute->institute_name }}</option>
                        @endif
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-filter-action flex-grow-1 shadow-sm">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.assets.index') }}" class="btn btn-outline-secondary btn-filter-action px-3" title="Clear All">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- 4. Assets Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #f1f5f9;">
                    <tr class="text-muted small">
                        <th class="ps-4 py-3">ITEM DETAILS</th>
                        <th>OWNERSHIP</th>
                        <th>STATUS</th>
                        <th class="text-end pe-4">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $asset->product_name }}</div>
                                <div class="text-muted small font-monospace">SN: {{ $asset->serial_number ?? 'N/A' }}</div>
                            </td>
                            <td>
                                @if($asset->unit)
                                    <span class="pathway-badge bg-primary bg-opacity-10 text-primary">Unit: {{ $asset->unit->unit_name }}</span>
                                @elseif($asset->department)
                                    <span class="pathway-badge bg-info bg-opacity-10 text-info">Dept: {{ $asset->department->dept_name }}</span>
                                @elseif($asset->institute)
                                    <span class="pathway-badge bg-warning bg-opacity-10 text-warning">Institute: {{ $asset->institute->institute_name }}</span>
                                @else
                                    <span class="text-muted small italic">General Asset</span>
                                @endif
                            </td>
                            <td>
                                @php $color = match($asset->status) { 'available' => 'success', 'assigned' => 'primary', 'maintenance' => 'warning', 'retired' => 'danger', default => 'secondary' }; @endphp
                                <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} rounded-pill px-3 py-2">
                                    <i class="fas fa-circle me-1" style="font-size: 0.4rem;"></i> {{ ucfirst($asset->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm border rounded-3 overflow-hidden">
                                    <a href="{{ route('admin.assets.show', $asset->asset_id) }}" class="btn btn-sm btn-white text-info px-3"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('admin.assets.edit', $asset->asset_id) }}" class="btn btn-sm btn-white text-warning px-3"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.assets.destroy', $asset->asset_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete Asset?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-white text-danger px-3"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-5 text-muted">No assets found matching your criteria.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top-0 p-4">{{ $assets->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    // Select2 AJAX Helpers
    const initSelect2 = (selector, url, placeholder) => {
        $(selector).select2({
            ajax: {
                url: url,
                dataType: 'json',
                delay: 250,
                data: params => ({ term: params.term, page: params.page }),
                processResults: data => ({ results: data.results, pagination: data.pagination })
            },
            placeholder: placeholder,
            allowClear: true,
            width: '100%'
        });
    };

    // Initialize searches
    initSelect2('.select2-depts', "{{ route('admin.departments.searchDepartments') }}", "All Departments");
    initSelect2('.select2-institutes', "{{ route('admin.institutes.search') }}", "All Institutes");
    
    // Units uses a slightly different return format from your controller
    $('.select2-units').select2({
        ajax: {
            url: "{{ route('admin.units.searchHeads') }}",
            dataType: 'json',
            delay: 250,
            processResults: data => ({ results: data })
        },
        placeholder: "All Units",
        allowClear: true,
        width: '100%'
    });

    // --- Chart: Status Doughnut ---
    new Chart(document.getElementById('statusDoughnut'), {
        type: 'doughnut',
        data: {
            labels: @json($statusData['labels']),
            datasets: [{
                data: @json($statusData['data']),
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                borderWidth: 0, hoverOffset: 4
            }]
        },
        options: { cutout: '75%', plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } }, maintainAspectRatio: false }
    });

    // --- Chart: Departmental Allocation (Horizontal Bar) ---
    new Chart(document.getElementById('deptBarChart'), {
        type: 'bar',
        data: {
            labels: @json($deptChart['labels']),
            datasets: [{
                label: 'Asset Count',
                data: @json($deptChart['data']),
                backgroundColor: '#6366f1',
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            maintainAspectRatio: false,
            scales: { x: { grid: { display: false } }, y: { grid: { display: false } } }
        }
    });
});
</script>
@endpush