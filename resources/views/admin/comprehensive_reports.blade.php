@extends('layouts.admin')

@section('title', 'Comprehensive Reports')

@push('styles')
<style>
    /* --- BEAUTIFIED SCREEN STYLES --- */
    body { 
        font-size: 0.8rem; 
        color: #334155; 
        background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); 
        min-height: 100vh;
    }

    /* Card Elevation */
    .report-card { 
        border: 1px solid rgba(226, 232, 240, 0.6); 
        border-radius: 16px; 
        background: #ffffff; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .report-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
    }
    
    /* Header & Org Summary */
    .org-badge { 
        font-size: 0.65rem; 
        padding: 6px 14px; 
        border-radius: 50px; 
        background: rgba(255, 255, 255, 0.8); 
        color: #475569; 
        font-weight: 700; 
        border: 1px solid #e2e8f0;
        backdrop-filter: blur(4px);
    }
    
    /* Elegant Ribbon Button */
    .action-ribbon { 
        display: flex; 
        background: #1e293b; 
        border-radius: 10px; 
        padding: 4px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .ribbon-btn { 
        border: none; background: transparent; padding: 6px 16px; font-size: 0.7rem; 
        font-weight: 600; color: #cbd5e1; text-decoration: none; display: flex; align-items: center;
        border-radius: 8px; transition: all 0.2s;
    }
    .ribbon-btn:hover { background: rgba(255,255,255,0.1); color: #fff; }
    .ribbon-btn i { font-size: 0.8rem; margin-right: 8px; }

    /* KPI Enhancements */
    .stat-icon-box {
        width: 32px; height: 32px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 10px;
    }

    /* Table Design */
    .table-custom { border-radius: 12px; overflow: hidden; }
    .table-custom thead {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-custom th { 
        font-size: 0.6rem; font-weight: 800; text-transform: uppercase; 
        letter-spacing: 0.05em; color: #64748b; padding: 12px;
    }
    .table-custom td { padding: 10px 12px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }

    /* Print Only Header */
    .print-only { display: none; }

    @media print {
        @page { size: A4; margin: 1cm; }
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        body { background: white !important; font-family: 'Times New Roman', serif; }
        .report-card { border: 1px solid #ddd !important; box-shadow: none !important; break-inside: avoid; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 px-lg-5">
    
    {{-- Print View Header --}}
    <div class="print-only mb-4 text-center">
        <h2 class="fw-bold">UNIVERSITY ASSET AUDIT REPORT</h2>
        <p class="text-muted">Generated on {{ date('l, F j, Y') }}</p>
        <hr>
    </div>

    {{-- Dashboard Header --}}
    <div class="d-flex justify-content-between align-items-end mb-4 no-print">
        <div>
            <h4 class="fw-bold text-slate-900 mb-2" style="letter-spacing: -0.02em;">Intelligence Dashboard</h4>
            <div class="d-flex gap-2">
                <span class="org-badge shadow-sm"><i class="fas fa-university me-2 text-primary"></i>{{ $summary['count_institutes'] ?? 0 }} Institutes</span>
                <span class="org-badge shadow-sm"><i class="fas fa-building me-2 text-success"></i>{{ $summary['count_units'] ?? 0 }} Units</span>
                <span class="org-badge shadow-sm"><i class="fas fa-graduation-cap me-2 text-info"></i>{{ $summary['count_departments'] ?? 0 }} Depts</span>
            </div>
        </div>

        <div class="action-ribbon no-print">
            <button onclick="window.print()" class="ribbon-btn">
                <i class="fas fa-print"></i> PRINT
            </button>
            <a href="{{ route('admin.reports.export', request()->all()) }}" class="ribbon-btn">
                <i class="fas fa-download"></i> EXPORT
            </a>
        </div>
    </div>

    {{-- Floating Filter Bar --}}
    <div class="card report-card mb-4 no-print border-0 shadow-lg" style="background: rgba(255,255,255,0.9); backdrop-filter: blur(10px);">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="filter-label">Period Start</label>
                    <input type="date" name="start_date" class="form-control form-control-custom" value="{{ request('start_date', now()->subMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <label class="filter-label">Period End</label>
                    <input type="date" name="end_date" class="form-control form-control-custom" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <label class="filter-label">Admin Unit</label>
                    <select name="unit_id" class="form-select form-control-custom">
                        <option value="">Global View</option>
                        @foreach($units as $unit) <option value="{{ $unit->unit_id }}" {{ request('unit_id') == $unit->unit_id ? 'selected' : '' }}>{{ $unit->unit_name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="filter-label">Department</label>
                    <select name="department" class="form-select form-control-custom">
                        <option value="">All Depts</option>
                        @foreach($departments as $dept) <option value="{{ $dept->dept_id }}" {{ request('department') == $dept->dept_id ? 'selected' : '' }}>{{ $dept->dept_name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="filter-label">Institute</label>
                    <select name="institute_id" class="form-select form-control-custom">
                        <option value="">All Institutes</option>
                        @foreach($institutes as $inst) <option value="{{ $inst->institute_id }}" {{ request('institute_id') == $inst->institute_id ? 'selected' : '' }}>{{ $inst->institute_name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-dark btn-sm w-100 fw-bold rounded-3 shadow-sm" style="height: 32px;">APPLY FILTERS</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Main KPI Row --}}
    <div class="row g-3 mb-4">
        @php
            $kpis = [
                ['Total Assets', number_format($summary['total_assets'] ?? 0), 'bg-blue-50 text-blue-600', 'fa-cubes'],
                ['Total Valuation', '₦' . number_format((float)($summary['total_value'] ?? 0), 2), 'bg-emerald-50 text-emerald-600', 'fa-vault'],
                ['Submissions', number_format($summary['submissions_period'] ?? 0), 'bg-amber-50 text-amber-600', 'fa-file-invoice'],
                ['Avg Asset Price', '₦' . number_format((float)(($summary['total_value'] ?? 0) / max(1, $summary['total_assets'] ?? 0)), 2), 'bg-indigo-50 text-indigo-600', 'fa-tag']
            ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="col-md-3">
            <div class="card report-card p-3 border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="filter-label mb-1">{{ $kpi[0] }}</span>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.1rem;">{{ $kpi[1] }}</h5>
                    </div>
                    <div class="stat-icon-box {{ explode(' ', $kpi[2])[0] }}">
                        <i class="fas {{ $kpi[3] }} {{ explode(' ', $kpi[2])[1] }}"></i>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Visual Analytics --}}
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card report-card p-4 border-0">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0" style="font-size: 0.75rem;"><i class="fas fa-chart-line text-primary me-2"></i>ASSET ENROLLMENT TREND</h6>
                    
                </div>
                <div class="chart-container" style="height: 240px;">
                    <canvas id="enrollmentChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card report-card p-4 border-0">
                <h6 class="fw-bold mb-4" style="font-size: 0.75rem;"><i class="fas fa-chart-pie text-success me-2"></i>STATUS HEALTH</h6>
                <div class="chart-container" style="height: 240px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Inventory Table --}}
    <div class="card report-card border-0 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <span class="fw-bold text-dark" style="font-size: 0.8rem;">Consolidated Inventory Summary</span>
            <span class="badge bg-light text-dark border fw-normal" style="font-size: 0.65rem;">Top Items by Value</span>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Asset Detail</th>
                        <th>Managing Entity</th>
                        <th>Qty</th>
                        <th class="text-end pe-4">Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topAssets as $asset)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark" style="font-size: 0.75rem;">{{ $asset->product_name }}</div>
                            <span class="text-muted" style="font-size: 0.65rem;">{{ $asset->category->category_name ?? 'General' }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge rounded-pill bg-blue-50 text-blue-700 border-blue-100 border" style="font-size: 0.6rem;">
                                    {{ $asset->department->dept_name ?? ($asset->unit->unit_name ?? ($asset->institute->institute_name ?? 'University')) }}
                                </span>
                            </div>
                        </td>
                        <td><span class="fw-semibold">{{ $asset->quantity }}</span></td>
                        <td class="text-end pe-4 fw-bold text-dark">₦{{ number_format((float)$asset->unit_cost * $asset->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shared chart options
    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { display: true, color: '#f1f5f9' }, border: { display: false }, ticks: { font: { size: 9 } } },
            x: { grid: { display: false }, ticks: { font: { size: 9 } } }
        }
    };

    // Enrollment Chart (Line)
    new Chart(document.getElementById('enrollmentChart'), {
        type: 'line',
        data: {
            labels: @json($chartData['enrollment_labels'] ?? []),
            datasets: [{
                data: @json($chartData['enrollment_data'] ?? []),
                borderColor: '#3b82f6',
                borderWidth: 3,
                backgroundColor: (context) => {
                    const ctx = context.chart.ctx;
                    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.1)');
                    gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');
                    return gradient;
                },
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 5,
                pointBackgroundColor: '#3b82f6'
            }]
        },
        options: baseOptions
    });

    // Status Chart (Doughnut)
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: @json($chartData['status_labels'] ?? []),
            datasets: [{
                data: @json($chartData['status_data'] ?? []),
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#6366f1'],
                hoverOffset: 10,
                borderWidth: 4,
                borderColor: '#fff'
            }]
        },
        options: {
            ...baseOptions,
            cutout: '80%',
            plugins: { 
                legend: { 
                    display: true, 
                    position: 'bottom', 
                    labels: { boxWidth: 8, padding: 20, font: { size: 9, weight: '600' } } 
                } 
            },
            scales: { x: { display: false }, y: { display: false } }
        }
    });
});
</script>
@endpush