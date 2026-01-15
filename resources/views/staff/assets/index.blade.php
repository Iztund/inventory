@extends('layouts.staff')

@section('title', 'Inventory Registry')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    :root {
        --med-navy: #0f172a;
        --med-blue: #2563eb;
        --med-slate: #f8fafc;
        --med-border: #e2e8f0;
    }

    .inventory-header { background: white; border-bottom: 1px solid #e2e8f0; padding: 1.5rem 0; margin-bottom: 2rem; }
    .stat-mini-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; height: 100%; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: transform 0.2s; }
    .stat-mini-card:hover { transform: translateY(-3px); }
    
    .chart-container { position: relative; height: 200px; width: 100%; }
    .asset-card { background: white; border-radius: 24px; border: 1px solid #e2e8f0; overflow: hidden; }
    
    /* Back Button */
    .btn-back { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f8fafc; border: 1px solid #e2e8f0; color: #1e293b; transition: all 0.2s; }
    .btn-back:hover { background: var(--med-navy); color: white; transform: translateX(-3px); }

    /* Export Center Styling */
    .export-pill-wrapper {
        background: #ffffff;
        border: 1px solid var(--med-border);
        padding: 5px;
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .btn-export-csv { 
        background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; 
        font-size: 0.7rem; font-weight: 800; border-radius: 50px; transition: all 0.2s;
    }
    .btn-export-csv:hover { background: #166534; color: white; }

    .btn-export-pdf { 
        background: #fff1f2; color: #991b1b; border: 1px solid #fecdd3; 
        font-size: 0.7rem; font-weight: 800; border-radius: 50px; transition: all 0.2s;
    }
    .btn-export-pdf:hover { background: #991b1b; color: white; }

    /* Table Styling */
    .table thead th { 
        background: #f8fafc; text-transform: uppercase; font-size: 0.65rem; 
        letter-spacing: 1px; font-weight: 700; color: #64748b; padding: 1.2rem 1.5rem; 
    }
    .table tbody td { padding: 1.2rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    
    .tag-active { font-family: 'Monaco', monospace; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; }
    .tag-pending { font-family: 'Monaco', monospace; background: #fff1f2; color: #991b1b; border: 1px solid #fecdd3; padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; }

    /* Custom Scrollbar for Subcategory List */
    .custom-scroll::-webkit-scrollbar { width: 5px; }
    .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<div class="inventory-header shadow-sm">
    <div class="container-fluid px-4">
        <div class="row align-items-center">
            <div class="col-md-5 d-flex align-items-center gap-3">
                <a href="{{ route('staff.dashboard') }}" class="btn-back" title="Back to Dashboard">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1 small">
                            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item active fw-bold text-primary">Asset Registry</li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold text-navy mb-0">Inventory Registry</h2>
                </div>
            </div>
            
            <div class="col-md-7 mt-3 mt-md-0">
                <div class="d-flex justify-content-md-end align-items-center gap-3 flex-wrap">
                    {{-- New Export Center --}}
                    <div class="export-pill-wrapper">
                        <span class="ps-3 d-none d-lg-inline text-muted fw-bold small text-uppercase" style="font-size: 0.6rem;">Export:</span>
                        <a href="{{ route('staff.assets.export-csv', ['search' => request('search')]) }}" class="btn btn-export-csv px-3">
                            <i class="fas fa-file-csv me-1"></i> CSV
                        </a>
                        <a href="{{ route('staff.assets.export-pdf', ['search' => request('search')]) }}" class="btn btn-export-pdf px-3">
                            <i class="fas fa-file-pdf me-1"></i> PDF
                        </a>
                    </div>

                    {{-- Stock Counter --}}
                    <div class="bg-navy p-2 px-3 rounded-4 shadow-sm text-start" style="min-width: 120px; background: #0f172a; border-bottom: 3px solid var(--med-blue);">
                        <div class="text-info small fw-bold text-uppercase" style="font-size: 0.55rem; letter-spacing: 1px;">Total Stock</div>
                        <div class="h4 fw-bold mb-0 text-white">{{ number_format($totalItems ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4">
    <div class="mb-4 d-flex align-items-center">
        <i class="fas fa-map-marker-alt text-primary me-2"></i>
        <span class="text-muted small me-2 text-uppercase fw-bold">Registry Location:</span>
        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 rounded-pill">
            {{ Auth::user()->unit->unit_name ?? Auth::user()->department->dept_name ?? Auth::user()->office->office_name ?? Auth::user()->institute->institute_name ?? Auth::user()->faculty->faculty_name ?? 'Central Registry' }}
        </span>
    </div>

    <div class="row g-4 mb-4">
        {{-- Subcategory Breakdown --}}
        <div class="col-md-4">
            <div class="stat-mini-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h6 class="fw-bold text-navy small mb-0 text-uppercase">Subcategory Breakdown</h6>
                    <i class="fas fa-layer-group text-muted small"></i>
                </div>
                <div class="overflow-auto custom-scroll" style="max-height: 200px; padding-right: 5px;">
                    <ul class="list-group list-group-flush small">
                        @forelse($subcategorySummary as $summary)
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2 bg-transparent">
                            <span class="text-muted pe-2">{{ $summary->name }}</span>
                            <span class="badge rounded-pill px-3 py-2" style="background: #e0e7ff; color: #312e81; font-weight: 800; min-width: 45px;">
                                {{ number_format($summary->total_qty) }}
                            </span>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted border-0 small py-4">No data recorded.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- Condition Overview --}}
        <div class="col-md-4">
            <div class="stat-mini-card p-4 text-center">
                <h6 class="fw-bold text-navy small mb-3 text-uppercase border-bottom pb-2 text-start">Condition Overview</h6>
                <div class="chart-container">
                    <canvas id="lifecycleChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Audit Verification --}}
        <div class="col-md-4">
            <div class="stat-mini-card p-4 text-center">
                <h6 class="fw-bold text-navy small mb-3 text-uppercase border-bottom pb-2 text-start">Audit Verification</h6>
                <div class="chart-container">
                    <canvas id="auditChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Registry Table --}}
    <div class="asset-card shadow-sm mb-5">
        <div class="p-4 border-bottom bg-white d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-bold text-navy mb-1">Detailed Registry Table</h5>
                <p class="text-muted small mb-0">List of all verified assets within your assignment.</p>
            </div>
            <form action="{{ route('staff.assets.index') }}" method="GET" style="min-width: 320px;">
                <div class="input-group border rounded-pill overflow-hidden bg-light">
                    <input type="text" name="search" class="form-control border-0 bg-transparent px-4" placeholder="Search serial or asset name..." value="{{ request('search') }}">
                    <button class="btn btn-primary px-4" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table m-0 align-middle">
                <thead>
                    <tr>
                        <th>Asset Name</th>
                        <th>Asset Tag</th>
                        <th>Serial Number</th>
                        <th class="text-center">Qty</th>
                        <th>Category Info</th>
                        <th class="text-end">Item Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr>
                        <td>
                            <div class="fw-bold text-navy">{{ $asset->item_name }}</div>
                            <div class="text-muted extra-small" style="font-size: 0.65rem;">ID: #AST-{{ str_pad($asset->id, 5, '0', STR_PAD_LEFT) }}</div>
                        </td>
                        <td>
                            @if($asset->asset_tag)
                                <span class="tag-active">{{ $asset->asset_tag }}</span>
                            @else
                                <span class="tag-pending">TAG PENDING</span>
                            @endif
                        </td>
                        <td><code class="text-primary small fw-bold">{{ $asset->serial_number ?? '---' }}</code></td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-2">{{ number_format($asset->quantity) }}</span>
                        </td>
                        <td>
                            <div class="text-primary small fw-bold text-uppercase" style="font-size: 0.65rem;">{{ $asset->category->name ?? 'General' }}</div>
                            <div class="text-muted small">{{ $asset->subcategory->subcategory_name ?? 'None' }}</div>
                        </td>
                        <td class="text-end">
                            @php
                                $c = ['available'=>'success', 'assigned'=>'primary', 'maintenance'=>'warning', 'retired'=>'danger'][$asset->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $c }} bg-opacity-10 text-{{ $c }} border border-{{ $c }} border-opacity-25 rounded-pill px-3 py-2 fw-bold" style="font-size: 0.7rem;">
                                {{ ucfirst($asset->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">No assets found matching your criteria.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top bg-light">
            {{ $assets->appends(request()->input())->links() }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartOptions = {
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: { boxWidth: 8, padding: 12, font: { size: 9, weight: 'bold' } }
                }
            }
        };

        new Chart(document.getElementById('lifecycleChart'), {
            type: 'doughnut',
            data: {
                labels: ['Available', 'Assigned', 'Repair', 'Retired'],
                datasets: [{
                    data: [{{$stats['available']??0}}, {{$stats['assigned']??0}}, {{$stats['maintenance']??0}}, {{$stats['retired']??0}}],
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: chartOptions
        });

        new Chart(document.getElementById('auditChart'), {
            type: 'doughnut',
            data: {
                labels: ['Approved', 'Pending', 'Rejected'],
                datasets: [{
                    data: [{{$auditStats['approved']??0}}, {{$auditStats['pending']??0}}, {{$auditStats['rejected']??0}}],
                    backgroundColor: ['#059669', '#6366f1', '#dc2626'],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: chartOptions
        });
    });
</script>
@endsection