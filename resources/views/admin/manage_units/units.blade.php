@extends('layouts.admin')

@section('title', 'Manage Units | Admin')

@section('content')
<link rel="stylesheet" href="{{ asset('build/assets/css/dashboard_admin/management.css') }}" />

<div class="container-fluid px-4 py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-7">
            <div class="d-flex align-items-center mb-1">
                <a href="javascript:void(0);" 
                   onclick="window.history.back();" 
                   class="btn btn-sm btn-outline-secondary me-3 rounded-circle shadow-sm" 
                   title="Go Back"
                   style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="fw-bold text-dark mb-0">Unit Administration</h2>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0 ms-5">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.units-management.index') }}">Academic Structure</a></li>
                    <li class="breadcrumb-item active">Manage Units</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.units.create') }}" class="btn btn-primary shadow-sm px-4">
                <i class="fas fa-plus-circle me-2"></i>Add New Unit
            </a>
        </div>
    </div>

    {{-- Feedback Messages --}}
    @if (session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} border-0 shadow-sm rounded-4 mb-4 p-3 d-flex justify-content-between align-items-center fade show" role="alert" id="status-alert">
            <div>
                <i class="fas {{ session('success') ? 'fa-check-circle' : 'fa-exclamation-triangle' }} me-2"></i>
                {{ session('success') ?? session('error') }}
            </div>
            <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <script>
            // Automatically hide the alert after 4 seconds
            setTimeout(function() {
                let alert = document.getElementById('status-alert');
                if (alert) {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 4000);
        </script>
    @endif

    {{-- Search/Filter Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ request()->url() }}" method="GET" class="row g-2">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input class="form-control border-start-0 border-end-0 ps-0" 
                               type="text" 
                               placeholder="Search by unit name or code..." 
                               name="q" 
                               value="{{ request('q') }}">
                        
                        @if(request()->filled('q'))
                            <a href="{{ request()->url() }}" 
                               class="input-group-text bg-white border-start-0 text-danger" 
                               style="text-decoration: none;" 
                               title="Clear Search">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary" type="submit">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-microchip me-2"></i>Unit List</h6>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ $units->total() }} Units Registered</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Unit Identity</th>
                            <th>Parent (Office/Dept)</th>
                            <th>Unit Head</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($units as $unitItem)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $unitItem->unit_name }}</div>
                                <code class="small text-primary">{{ $unitItem->unit_code ?? 'N/A' }}</code>
                            </td>
                            <td>
                                {{-- Office Relationship check --}}
                                @if($unitItem->office)
                                    <span class="badge bg-success-subtle text-success border fw-normal">
                                        <i class="fas fa-building-user me-1"></i> {{ $unitItem->office->office_name }}
                                    </span>
                                @elseif($unitItem->department)
                                    <span class="badge bg-info-subtle text-info border fw-normal">
                                        <i class="fas fa-graduation-cap me-1"></i> {{ $unitItem->department->dept_name }}
                                    </span>
                                @else
                                    <span class="text-muted small">Independent Unit</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs bg-light text-dark rounded-circle me-2 d-flex align-items-center justify-content-center border" style="width:28px; height:28px; font-size: 0.7rem;">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <span class="small fw-medium">
                                        {{-- Updated to use the 'supervisor' relationship from your model --}}
                                        {{ $unitItem->supervisor->profile->full_name ?? $unitItem->supervisor->username ?? 'Not Assigned' }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill {{ $unitItem->is_active == 'active' ? 'bg-success text-white' : 'bg-secondary text-white' }} px-3" style="font-size: 0.7rem;">
                                    {{ strtoupper($unitItem->is_active) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('admin.units.edit', $unitItem->unit_id) }}" 
                                       class="btn btn-sm btn-outline-info border-0" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.units.destroy', $unitItem->unit_id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0" 
                                                onclick="return confirm('Delete {{ $unitItem->unit_name }}?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <p>No functional units found.</p>
                                    <a href="{{ route('admin.units.index') }}" class="btn btn-sm btn-link">Reset Search</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <p class="small text-muted mb-0">
                    Showing {{ $units->firstItem() }} to {{ $units->lastItem() }} of {{ $units->total() }} records
                </p>
                {{ $units->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<style>
    .bg-success-subtle { background-color: #e1f7ef !important; color: #0d6832 !important; }
    .bg-info-subtle { background-color: #e0f2fe !important; color: #0369a1 !important; }
    .table thead th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6c757d; border-top: 0; }
    .avatar-xs { flex-shrink: 0; }
</style>
@endsection