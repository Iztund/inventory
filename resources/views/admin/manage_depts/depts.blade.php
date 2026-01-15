@extends('layouts.admin')

@section('title', 'Manage Departments | Academic Structure')

@section('content')
<link rel="stylesheet" href="{{ asset('build/assets/css/dashboard_admin/management.css') }}" />
<link rel="stylesheet" href="{{ asset('build/assets/css/dashboard_admin/department/index.css') }}" />

<div class="container-fluid px-4 py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-7">
            <div class="d-flex align-items-center mb-1">
                <a href="{{ route('admin.units-management.index') }}" class="btn btn-sm btn-outline-secondary me-3 rounded-circle shadow-sm" 
                   title="Go Back" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="fw-bold text-dark mb-0 responsive-title">Department Administration</h2>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0 ms-5 responsive-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.units-management.index') }}" class="text-decoration-none text-muted">Structure</a></li>
                    <li class="breadcrumb-item active fw-semibold text-primary">Departments</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.departments.create') }}" class="btn btn-primary shadow-sm px-4 rounded-3 btn-responsive">
                <i class="fas fa-plus-circle me-2"></i><span class="btn-text">Add New Department</span>
            </a>
        </div>
    </div>

    {{-- Orphan Warning Alert (Using Global Count from Controller) --}}
    @if(isset($orphanCount) && $orphanCount > 0)
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-start rounded-4 mb-4 responsive-alert" role="alert">
            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3 alert-icon" style="width: 40px; height: 40px; flex-shrink: 0;">
                <i class="fas fa-ghost"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="alert-heading fw-bold mb-1">Hidden Departments Detected</h6>
                <p class="mb-2 small text-dark">There are <strong>{{ $orphanCount }}</strong> active departments linked to <strong>inactive Faculties</strong>. Inventory staff cannot see these in selection menus.</p>
                @if(request('status') != 'hidden')
                    <a href="{{ route('admin.departments.index', ['status' => 'hidden']) }}" class="btn btn-sm btn-dark rounded-pill px-3">View All</a>
                @endif
            </div>
        </div>
    @endif

    {{-- Search & Status Filter Card --}}
    <div class="card border-0 shadow-sm mb-4 rounded-3">
        <div class="card-body p-3">
            <form action="{{ route('admin.departments.index') }}" method="GET" class="row g-2">
                <div class="col-12 col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                            <i class="fas fa-search"></i>
                        </span>
                        <input class="form-control border-start-0 border-end-0 ps-0 shadow-none" 
                               type="text" placeholder="Search name or code..." name="q" value="{{ request('q') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select name="status" class="form-select shadow-none">
                        <option value="">All Visibility Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Visible & Active</option>
                        <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>Hidden (Orphaned)</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Archived/Inactive</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-grid">
                    <button class="btn btn-primary rounded-3 fw-medium" type="submit">Apply Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center border-bottom gap-2">
            <h6 class="m-0 fw-bold text-dark"><i class="fas fa-sitemap me-2 text-primary"></i>Registry</h6>
            <div class="d-flex flex-wrap gap-2">
                @if(request()->filled('status') || request()->filled('q'))
                    <a href="{{ route('admin.departments.index') }}" class="badge bg-light text-muted border text-decoration-none">Clear Filters</a>
                @endif
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ $departments->total() }} Total</span>
            </div>
        </div>
        <div class="card-body p-0">
            {{-- Desktop Table View --}}
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-subtle text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Department Info</th>
                            <th>Parent Faculty</th>
                            <th>Head of Dept</th>
                            <th class="text-center">Assets</th>
                            <th class="text-center">Visibility</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($departments as $departmentItem)
                        @php
                            $isParentInactive = ($departmentItem->faculty && $departmentItem->faculty->is_active !== 'active');
                            $isHidden = ($departmentItem->is_active == 'active' && $isParentInactive);
                        @endphp
                        <tr class="{{ $isHidden ? 'bg-warning bg-opacity-10' : '' }}">
                            <td class="ps-4">
                                <div>
                                    <div class="fw-bold text-dark mb-0">
                                        {{ $departmentItem->dept_name }}
                                        @if($isHidden) <i class="fas fa-eye-slash text-warning ms-1" title="Hidden from Users"></i> @endif
                                    </div>
                                    <code class="text-primary small fw-semibold">{{ $departmentItem->dept_code ?? 'NO CODE' }}</code>
                                </div>
                            </td>
                            <td>
                                @if($departmentItem->faculty)
                                    <span class="small fw-medium {{ $isParentInactive ? 'text-danger' : 'text-muted' }}">
                                        {{ $departmentItem->faculty->faculty_name }}
                                        @if($isParentInactive)
                                            <span class="d-block text-danger" style="font-size: 0.65rem;">
                                                <i class="fas fa-exclamation-triangle me-1"></i> Faculty Inactive
                                            </span>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted small italic">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    
                                    <span class="small text-dark">
                                        {{ $departmentItem->deptHead->profile->full_name ?? $departmentItem->deptHead->username ?? 'Not Set' }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-white text-dark border px-2">{{ $departmentItem->assets_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                @if($isHidden)
                                    <span class="badge rounded-pill bg-warning text-dark border px-3">Hidden</span>
                                @else
                                    <span class="badge rounded-pill {{ $departmentItem->is_active == 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border px-3">
                                        {{ ucfirst($departmentItem->is_active) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm rounded-2 overflow-hidden border">
                                    <a href="{{ route('admin.departments.edit', $departmentItem->dept_id) }}" class="btn btn-sm btn-white px-3"><i class="fas fa-edit text-primary"></i></a>
                                    <form action="{{ route('admin.departments.destroy', $departmentItem->dept_id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-white border-start px-3" onclick="return confirm('Delete department?')">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-search fa-3x text-light mb-3"></i>
                                <p class="text-muted">No departments found matching your search.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card View --}}
            <div class="d-lg-none mobile-cards">
                @forelse ($departments as $departmentItem)
                @php
                    $isParentInactive = ($departmentItem->faculty && $departmentItem->faculty->is_active !== 'active');
                    $isHidden = ($departmentItem->is_active == 'active' && $isParentInactive);
                @endphp
                <div class="mobile-card {{ $isHidden ? 'mobile-card-warning' : '' }} p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            <h6 class="fw-bold text-dark mb-1">
                                {{ $departmentItem->dept_name }}
                                @if($isHidden) <i class="fas fa-eye-slash text-warning ms-1" title="Hidden from Users"></i> @endif
                            </h6>
                            <code class="text-primary small fw-semibold">{{ $departmentItem->dept_code ?? 'NO CODE' }}</code>
                        </div>
                        <div>
                            @if($isHidden)
                                <span class="badge rounded-pill bg-warning text-dark border px-2 small">Hidden</span>
                            @else
                                <span class="badge rounded-pill {{ $departmentItem->is_active == 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border px-2 small">
                                    {{ ucfirst($departmentItem->is_active) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mobile-info-row mb-2">
                        <span class="mobile-label"><i class="fas fa-building me-1"></i>Faculty:</span>
                        <span class="mobile-value">
                            @if($departmentItem->faculty)
                                <span class="{{ $isParentInactive ? 'text-danger' : 'text-muted' }}">
                                    {{ $departmentItem->faculty->faculty_name }}
                                    @if($isParentInactive)
                                        <span class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Inactive</span>
                                    @endif
                                </span>
                            @else
                                <span class="text-muted">Unassigned</span>
                            @endif
                        </span>
                    </div>

                    <div class="mobile-info-row mb-2">
                        <span class="mobile-label"><i class="fas fa-user-tie me-1"></i>Head:</span>
                        <span class="mobile-value">
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs bg-secondary-subtle text-secondary rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold" 
                                     style="width:24px; height:24px; font-size: 0.65rem;">
                                    {{ strtoupper(substr($departmentItem->deptHead->username ?? 'U', 0, 1)) }}
                                </div>
                                <span class="small">{{ $departmentItem->deptHead->profile->full_name ?? $departmentItem->deptHead->username ?? 'Not Set' }}</span>
                            </div>
                        </span>
                    </div>

                    <div class="mobile-info-row mb-3">
                        <span class="mobile-label"><i class="fas fa-boxes me-1"></i>Assets:</span>
                        <span class="mobile-value">
                            <span class="badge bg-white text-dark border">{{ $departmentItem->assets_count ?? 0 }}</span>
                        </span>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.departments.edit', $departmentItem->dept_id) }}" class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <form action="{{ route('admin.departments.destroy', $departmentItem->dept_id) }}" method="POST" class="flex-fill">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Delete department?')">
                                <i class="fas fa-trash-alt me-1"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-light mb-3"></i>
                    <p class="text-muted">No departments found matching your search.</p>
                </div>
                @endforelse
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $departments->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection