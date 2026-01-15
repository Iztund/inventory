@extends('layouts.admin')

@section('title', 'Manage Faculties | Academic Structure')

@section('content')
<link rel="stylesheet" href="{{ asset('build/assets/css/dashboard_admin/management.css') }}" />

<div class="container-fluid px-4 py-4">
    {{-- Header Section: Solid Desktop & Mobile Layout --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-7 col-12">
            <div class="d-flex align-items-center mb-1">
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary me-3 rounded-circle shadow-sm" 
                    title="Go Back" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="fw-bold text-dark mb-0">Faculty Administration</h2>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0 ms-5 d-none d-md-flex">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.units-management.index') }}" class="text-decoration-none text-muted">Structure</a></li>
                    <li class="breadcrumb-item active fw-semibold text-primary">Faculties</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.faculties.create') }}" class="btn btn-primary shadow-sm px-4 rounded-3 w-100 w-md-auto">
                <i class="fas fa-plus-circle me-2"></i><span class="btn-text">Add New Faculty</span>
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas {{ session('success') ? 'fa-check-circle' : 'fa-exclamation-triangle' }} me-2"></i>
            {{ session('success') ?? session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Search Card: Fixed Grid Columns --}}
    <div class="card border-0 shadow-sm mb-4 rounded-3">
        <div class="card-body p-3">
            <form action="{{ route('admin.faculties.index') }}" method="GET" class="row g-2">
                {{-- Search Input (6 columns on desktop) --}}
                <div class="col-lg-6 col-md-5 col-12">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                            <i class="fas fa-search"></i>
                        </span>
                        <input class="form-control border-start-0 border-end-0 ps-0 shadow-none" 
                               type="text" name="q" placeholder="Search faculty name or code..." value="{{ request('q') }}">
                        @if(request()->filled('q'))
                            <a href="{{ route('admin.faculties.index') }}" class="input-group-text bg-white border-start-0 text-danger pe-3">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        @endif
                    </div>
                </div>
                {{-- Status Dropdown (4 columns on desktop) --}}
                <div class="col-lg-4 col-md-4 col-12">
                    <select name="status" class="form-select shadow-none">
                        <option value="">All Visibility Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Visible & Active</option>
                        <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>Hidden (Orphaned)</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Archived/Inactive</option>
                    </select>
                </div>
                {{-- Filter Button (2 columns on desktop) --}}
                <div class="col-lg-2 col-md-3 col-12 d-grid">
                    <button class="btn btn-dark rounded-3 fw-medium" type="submit">Filter Results</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h6 class="m-0 fw-bold text-dark"><i class="fas fa-university me-2 text-primary"></i>Registered Faculties</h6>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ $faculties->total() }} Total</span>
        </div>
        
        <div class="card-body p-0">
            {{-- DESKTOP VIEW --}}
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-4 py-3" style="width: 30%">Faculty Info</th>
                            <th>Dean of Faculty</th>
                            <th class="text-center">Departments</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($faculties as $facultyItem)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark mb-0 text-truncate" style="max-width: 250px;">{{ $facultyItem->faculty_name }}</div>
                                <code class="text-primary small fw-semibold">{{ $facultyItem->faculty_code ?? 'NO CODE' }}</code>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs bg-primary-subtle text-primary rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold" style="width:32px; height:32px; font-size: 0.75rem;">
                                        {{ strtoupper(substr($facultyItem->dean->username ?? 'D', 0, 1)) }}
                                    </div>
                                    <span class="small fw-semibold text-dark">{{ $facultyItem->dean->profile->full_name ?? $facultyItem->dean->username ?? 'Unassigned' }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark fw-bold border px-3">{{ $facultyItem->departments_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill {{ $facultyItem->is_active == 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border px-3">
                                    {{ ucfirst($facultyItem->is_active) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm rounded-2 overflow-hidden border">
                                    <a href="{{ route('admin.faculties.edit', $facultyItem->faculty_id) }}" class="btn btn-sm btn-white"><i class="fas fa-edit text-primary"></i></a>
                                    <form action="{{ route('admin.faculties.destroy', $facultyItem->faculty_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this faculty?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-white border-start"><i class="fas fa-trash-alt text-danger"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted">No faculties found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE VIEW --}}
            <div class="d-lg-none">
                @forelse ($faculties as $facultyItem)
                    <div class="p-3 border-bottom">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold text-dark">{{ $facultyItem->faculty_name }}</span>
                            <span class="badge rounded-pill {{ $facultyItem->is_active == 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ ucfirst($facultyItem->is_active) }}</span>
                        </div>
                        <div class="row g-0 small text-muted">
                            <div class="col-6">Code: <span class="text-dark fw-medium">{{ $facultyItem->faculty_code ?? 'N/A' }}</span></div>
                            <div class="col-6 text-end">Depts: <span class="text-dark fw-medium">{{ $facultyItem->departments_count ?? 0 }}</span></div>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            <a href="{{ route('admin.faculties.edit', $facultyItem->faculty_id) }}" class="btn btn-sm btn-outline-primary w-50 rounded-3">Edit</a>
                            <form action="{{ route('admin.faculties.destroy', $facultyItem->faculty_id) }}" method="POST" class="w-50">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-3" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">No faculties found.</div>
                @endforelse
            </div>
        </div>

        <div class="card-footer bg-white py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <p class="small text-muted mb-0">Showing <strong>{{ $faculties->firstItem() }}</strong> to <strong>{{ $faculties->lastItem() }}</strong></p>
                <div class="small">{{ $faculties->links('pagination::bootstrap-5') }}</div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-subtle { background-color: #f9fafb !important; }
    .bg-success-subtle { background-color: #ecfdf5 !important; color: #065f46 !important; border: 1px solid #10b98133 !important; }
    .bg-danger-subtle { background-color: #fef2f2 !important; color: #991b1b !important; border: 1px solid #ef444433 !important; }
    .bg-primary-subtle { background-color: #eff6ff !important; color: #1e40af !important; }
    .btn-white { background-color: #ffffff; color: #374151; }
    .btn-white:hover { background-color: #f3f4f6; }
    /* Fix for mobile pagination size */
    .pagination { margin-bottom: 0; flex-wrap: wrap; justify-content: center; }
</style>
@endsection