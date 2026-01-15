@extends('layouts.admin')

@section('title', 'System Structure | Inventory Management')
@section('active_link', 'units-admin')

@section('content')
<link rel="stylesheet" href="{{ asset('build/assets/css/dashboard_admin/academic_admin.css') }}" />
<div class="container-fluid py-4 py-lg-5 px-3 px-lg-5" style="background-color: #f8fafc; min-height: 100vh;">

    {{-- Header Section: Restored exact Desktop positioning --}}
    <div class="d-flex justify-content-between align-items-center mb-4 mb-lg-5">
        <div>
            <h4 class="fw-bold mb-0 text-dark responsive-main-title" style="letter-spacing: -0.02em;">Organizational Structure</h4>
            <nav aria-label="breadcrumb" class="d-none d-sm-block">
                <ol class="breadcrumb mb-0 small text-muted bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-primary">Dashboard</a></li>
                    <li class="breadcrumb-item active">Hierarchy Management</li>
                </ol>
            </nav>
        </div>
        
        <div class="d-flex gap-2 gap-sm-3 align-items-center">
            <a href="{{ route('admin.structure.export') }}" class="btn btn-white shadow-sm px-3 px-sm-4 rounded-3 fw-medium border bg-white d-inline-flex align-items-center">
                <i class="fas fa-file-export me-md-2 text-muted"></i>
                <span class="d-none d-md-inline">Export Structure</span>
                <span class="d-md-none">Export</span>
            </a>
            
            <div class="bg-dark text-white px-3 py-2 rounded-3 shadow-sm d-flex align-items-center">
                <i class="fas fa-calendar-alt me-2 opacity-75 d-none d-sm-inline"></i>
                <span class="small fw-medium">{{ now()->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Top Summary Cards --}}
    <div class="row g-3 g-lg-4 mb-4 mb-lg-5">
        {{-- Academic --}}
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-primary bg-opacity-10 border-0 p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-primary mb-1 text-uppercase small">Academic</h6>
                        <p class="text-muted small mb-0 d-none d-lg-block">Faculties & Departments</p>
                    </div>
                    <i class="fas fa-graduation-cap text-primary opacity-50 fs-4"></i>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.faculties.index') }}" class="nav-unit-link">
                            <span class="small fw-bold">Faculties</span>
                            <span class="badge rounded-pill bg-primary px-3">{{ $summary['total_faculties'] }}</span>
                        </a>
                        <a href="{{ route('admin.departments.index') }}" class="nav-unit-link">
                            <span class="small fw-bold">Departments</span>
                            <span class="badge rounded-pill bg-primary px-3">{{ $summary['total_departments'] }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Administrative --}}
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-success bg-opacity-10 border-0 p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-success mb-1 text-uppercase small">Administrative</h6>
                        <p class="text-muted small mb-0 d-none d-lg-block">Offices & Units</p>
                    </div>
                    <i class="fas fa-user-shield text-success opacity-50 fs-4"></i>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.offices.index') }}" class="nav-unit-link">
                            <span class="small fw-bold">Offices</span>
                            <span class="badge rounded-pill bg-success px-3">{{ $summary['total_offices'] }}</span>
                        </a>
                        <a href="{{ route('admin.units.index') }}" class="nav-unit-link">
                            <span class="small fw-bold">Units</span>
                            <span class="badge rounded-pill bg-success px-3">{{ $summary['total_units'] }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Research --}}
        <div class="col-12 col-md-12 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-info bg-opacity-10 border-0 p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-info mb-1 text-uppercase small">Research</h6>
                        <p class="text-muted small mb-0 d-none d-lg-block">Institutes & Centers</p>
                    </div>
                    <i class="fas fa-microscope text-info opacity-50 fs-4"></i>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.institutes.index') }}" class="nav-unit-link">
                            <span class="small fw-bold">Institutes</span>
                            <span class="badge rounded-pill bg-info px-3">{{ $summary['total_institutes'] }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Breakdown Container --}}
    <div class="bg-white rounded-4 shadow-sm border-0 overflow-hidden">
        {{-- Header of the Breakdown Section --}}
        <div class="p-4 border-bottom d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between bg-white gap-3">
            <div class="d-flex align-items-center">
                <div class="bg-light rounded-3 p-2 me-3">
                    <i class="fas fa-layer-group text-muted"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0 small text-uppercase responsive-table-title" style="letter-spacing: 0.05em;">Structure Breakdown</h6>
                    <p class="text-muted mb-0 d-none d-lg-block" style="font-size: 0.75rem;">Comprehensive count of all college entities</p>
                </div>
            </div>
            
            <div class="d-flex align-items-center bg-light px-3 py-2 rounded-pill border w-100 w-lg-auto justify-content-between">
                <label class="form-check-label small fw-bold text-secondary me-3 mb-0 responsive-toggle-label" for="toggleInactive" style="cursor: pointer;">
                    SHOW INACTIVE DATA
                </label>
                <div class="form-check form-switch mb-0 p-0">
                    <input class="form-check-input custom-switch ms-0" type="checkbox" id="toggleInactive">
                </div>
            </div>
        </div>

        {{-- DESKTOP VIEW: Table structure from original code --}}
        <div class="table-responsive d-none d-lg-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase text-muted fw-bold" style="font-size: 0.65rem; width: 35%;">Classification</th>
                        <th class="py-3 text-uppercase text-muted fw-bold text-center" style="font-size: 0.65rem;">
                            Count <span class="inactive-label text-danger ms-1" style="display:none;">(Inactive)</span>
                        </th>
                        <th class="py-3 text-uppercase text-muted fw-bold text-center" style="font-size: 0.65rem;">Assets Assigned</th>
                        <th class="pe-4 py-3 text-uppercase text-muted fw-bold text-end" style="font-size: 0.65rem;">Status Flag</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- ACADEMIC --}}
                    <tr><td colspan="4" class="ps-4 py-2 bg-light bg-opacity-50 text-primary fw-bold" style="font-size: 0.65rem;">ACADEMIC BRANCH</td></tr>
                    <tr>
                        <td class="ps-5"><span class="fw-bold text-dark small">Faculties</span></td>
                        <td class="text-center">
                            <span class="fw-bold">{{ $summary['total_faculties'] }}</span>
                            <span class="inactive-count text-danger ms-2 small fw-bold" style="display:none;">+ {{ $summary['inactive_faculties'] }}</span>
                        </td>
                        <td class="text-center text-muted small fw-medium">{{ number_format($summary['assets_in_faculties']) }}</td>
                        <td class="text-end pe-4">
                            <span class="badge rounded-pill {{ $summary['inactive_faculties'] > 0 ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success' }} px-3 py-1">
                                {{ $summary['inactive_faculties'] > 0 ? 'Mixed' : 'Active' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-5">
                            <i class="fas fa-level-up-alt fa-rotate-90 text-muted me-2 opacity-50"></i>
                            <span class="fw-bold text-dark small">Departments</span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold">{{ $summary['total_departments'] }}</span>
                            <span class="inactive-count text-danger ms-2 small fw-bold" style="display:none;">+ {{ $summary['inactive_departments'] }}</span>
                        </td>
                        <td class="text-center text-muted small fw-medium">{{ number_format($summary['assets_in_departments']) }}</td>
                        <td class="text-end pe-4">Verfied</td>
                    </tr>

                    {{-- ADMINISTRATIVE --}}
                    <tr><td colspan="4" class="ps-4 py-2 bg-light bg-opacity-50 text-success fw-bold" style="font-size: 0.65rem;">ADMINISTRATIVE BRANCH</td></tr>
                    <tr>
                        <td class="ps-5"><span class="fw-bold text-dark small">Offices</span></td>
                        <td class="text-center">
                            <span class="fw-bold">{{ $summary['total_offices'] }}</span>
                            <span class="inactive-count text-danger ms-2 small fw-bold" style="display:none;">+ {{ $summary['inactive_offices'] }}</span>
                        </td>
                        <td class="text-center text-muted small fw-medium">--</td>
                        <td class="text-end pe-4">Active</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- MOBILE VIEW: Card structure --}}
        <div class="d-lg-none mobile-structure-cards">
            {{-- Academic Section --}}
            <div class="mobile-section-header">ACADEMIC BRANCH</div>
            <div class="mobile-structure-card">
                <div class="mobile-card-title"><span class="fw-bold">Faculties</span><span class="badge bg-primary rounded-pill">{{ $summary['total_faculties'] }}</span></div>
                <div class="mobile-card-row"><span class="mobile-label">Assets:</span><span class="mobile-value">{{ number_format($summary['assets_in_faculties']) }}</span></div>
                <div class="mobile-card-row">
                    <span class="mobile-label">Status:</span>
                    <span class="mobile-value {{ $summary['inactive_faculties'] > 0 ? 'text-warning' : 'text-success' }}">
                        {{ $summary['inactive_faculties'] > 0 ? 'Mixed' : 'Active' }}
                    </span>
                </div>
            </div>

            <div class="mobile-structure-card">
                <div class="mobile-card-title"><span class="fw-bold">Departments</span><span class="badge bg-primary rounded-pill">{{ $summary['total_departments'] }}</span></div>
                <div class="mobile-card-row"><span class="mobile-label">Assets:</span><span class="mobile-value">{{ number_format($summary['assets_in_departments']) }}</span></div>
            </div>

            {{-- Administrative Section --}}
            <div class="mobile-section-header">ADMINISTRATIVE BRANCH</div>
            <div class="mobile-structure-card">
                <div class="mobile-card-title"><span class="fw-bold">Offices</span><span class="badge bg-success rounded-pill">{{ $summary['total_offices'] }}</span></div>
                <div class="mobile-card-row"><span class="mobile-label">Count:</span><span class="mobile-value">{{ $summary['total_offices'] }}</span></div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#toggleInactive').on('change', function() {
            if($(this).is(':checked')) {
                $('.inactive-count, .inactive-label').show();
            } else {
                $('.inactive-count, .inactive-label').hide();
            }
        });
    });
</script>
@endsection