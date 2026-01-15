@extends('layouts.auditor')

@section('title', 'Entity Audit Coverage')

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #f8fafc;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Entity Breakdown 🏢</h2>
            <p class="text-muted small">Monitor verified inventory distribution across the College of Medicine.</p>
        </div>
        <div class="text-end">
            <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-2">
                <i class="fas fa-check-circle me-1"></i> Showing Verified Items Only
            </span>
        </div>
    </div>

    <div class="row g-4">
        {{-- Faculties & Departments --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="fas fa-graduation-cap me-2"></i> Academic Entities
                    </h5>
                </div>
                <div class="card-body px-4">
                    @forelse($entityBreakdown['faculties_list'] as $faculty)
                    <div class="p-3 mb-3 border rounded-4 bg-white hover-card transition">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-0">{{ $faculty->faculty_name }}</h6>
                                <small class="text-muted">{{ $faculty->faculty_code }}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary">₦{{ number_format($faculty->total_value ?? 0, 0) }}</div>
                                <small class="text-muted x-small text-uppercase fw-bold">Verified Value</small>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center gap-3 mt-3">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between x-small mb-1">
                                    <span class="text-muted">Verified Assets</span>
                                    <span class="fw-bold">{{ $faculty->verified_assets_count }} Items</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: {{ $faculty->verified_assets_count > 0 ? '100%' : '0%' }}"></div>
                                </div>
                            </div>
                            <a href="{{ route('auditor.assets.index', ['faculty' => $faculty->faculty_id]) }}" 
                               class="btn btn-sm btn-light rounded-circle shadow-sm" title="View Registry">
                                <i class="fas fa-arrow-right text-primary"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                        <p class="text-center text-muted">No faculty data available.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Administrative Offices --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-success">
                        <i class="fas fa-building me-2"></i> Administrative Entities
                    </h5>
                </div>
                <div class="card-body px-4">
                    @forelse($entityBreakdown['offices_list'] as $office)
                    <div class="p-3 mb-3 border rounded-4 bg-white hover-card transition">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-0">{{ $office->office_name }}</h6>
                                <small class="text-muted">{{ $office->office_code }}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">₦{{ number_format($office->total_value ?? 0, 0) }}</div>
                                <small class="text-muted x-small text-uppercase fw-bold">Verified Value</small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 mt-3">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between x-small mb-1">
                                    <span class="text-muted">Verified Assets</span>
                                    <span class="fw-bold">{{ $office->verified_assets_count }} Items</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $office->verified_assets_count > 0 ? '100%' : '0%' }}"></div>
                                </div>
                            </div>
                            <a href="{{ route('auditor.assets.index', ['office' => $office->office_id]) }}" 
                               class="btn btn-sm btn-light rounded-circle shadow-sm">
                                <i class="fas fa-arrow-right text-success"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                        <p class="text-center text-muted">No office data available.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: #eef2ff; }
    .hover-card:hover { border-color: #4338ca !important; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .transition { transition: all 0.3s ease; }
    .x-small { font-size: 0.7rem; }
</style>
@endsection