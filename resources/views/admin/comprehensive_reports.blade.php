@extends('layouts.admin')

@section('title', 'Registry Intelligence & Analytics')

@section('content')
<div class="min-vh-100 bg-slate-50">

    {{-- Sticky Header with Filters --}}
    <header class="bg-white border-bottom border-slate-200 sticky-top" style="z-index:1030;">
        <div style="max-width:1600px;" class="mx-auto px-4 py-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ url()->previous() }}" 
                       class="btn btn-white border border-slate-200 rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                       style="width:40px; height:40px; transition:all 0.2s;">
                        <i class="fas fa-arrow-left text-slate-400"></i>
                    </a>
                    <div>
                        <h1 class="fw-black text-slate-900 mb-0" style="font-size:1.3rem; letter-spacing:-0.02em;">Registry Intelligence</h1>
                        <div class="d-flex align-items-center gap-3 mt-1" style="font-size:0.72rem;">
                            <span class="text-slate-500 fw-bold"><i class="fas fa-university text-amber-600 me-1"></i>College of Medicine</span>
                            <span class="text-slate-200">•</span>
                            <span class="text-slate-500 fw-bold">{{ now()->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-sm btn-white border border-slate-200 rounded-2 fw-bold d-flex align-items-center gap-2 px-3" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                    <a href="{{ route('admin.reports.export', request()->all()) }}" 
                       class="btn btn-sm text-white fw-black rounded-2 d-flex align-items-center gap-2 shadow-sm px-3"
                       style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-file-export"></i> Export Data
                    </a>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="mt-3 pt-3 border-top border-slate-100">
                <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2">
                    <div class="col-12 col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-slate-50 border-slate-200 text-slate-500"><i class="fas fa-filter"></i></span>
                            <select name="entity_type" class="form-select border-slate-200" onchange="this.form.submit()">
                                <option value="">All Organizational Units</option>
                                <option value="faculty" {{ request('entity_type') == 'faculty' ? 'selected' : '' }}>Faculties</option>
                                <option value="department" {{ request('entity_type') == 'department' ? 'selected' : '' }}>Departments</option>
                                <option value="office" {{ request('entity_type') == 'office' ? 'selected' : '' }}>Offices</option>
                                <option value="unit" {{ request('entity_type') == 'unit' ? 'selected' : '' }}>Units</option>
                                <option value="institute" {{ request('entity_type') == 'institute' ? 'selected' : '' }}>Institutes</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-2">
                        @if(request()->filled('entity_type'))
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-link text-slate-500 text-decoration-none fw-bold">
                                <i class="fas fa-times-circle"></i> Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </header>

    <main style="max-width:1600px;" class="mx-auto px-4 py-5">

        {{-- Executive Summary Cards --}}
        <div class="row g-4 mb-5">
            @php
                $metrics = [
                    ['label' => 'Total Assets', 'value' => number_format($summary['total_assets']), 'icon' => 'fa-boxes-stacked', 'color' => '#4f46e5', 'bg' => '#eef2ff'],
                    ['label' => 'Registry Value', 'value' => '₦' . number_format($summary['total_value'], 2), 'icon' => 'fa-sack-dollar', 'color' => '#059669', 'bg' => '#f0fdf4'],
                    ['label' => 'Period Submissions', 'value' => number_format($summary['submissions_period']), 'icon' => 'fa-file-invoice', 'color' => '#0891b2', 'bg' => '#ecfeff'],
                    ['label' => 'Avg Asset Cost', 'value' => '₦' . number_format($summary['total_value'] / max(1, $summary['total_assets']), 0), 'icon' => 'fa-chart-pie', 'color' => '#f59e0b', 'bg' => '#fffbeb'],
                ];
            @endphp

            @foreach($metrics as $m)
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-4" style="background: {{ $m['bg'] }}; border-left: 4px solid {{ $m['color'] }} !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-slate-500 text-uppercase fw-bold mb-1" style="font-size:0.65rem; letter-spacing:0.05em;">{{ $m['label'] }}</p>
                            <h3 class="fw-black text-slate-900 mb-0" style="font-size:1.5rem;">{{ $m['value'] }}</h3>
                        </div>
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width:45px; height:45px; background: {{ $m['color'] }};">
                            <i class="fas {{ $m['icon'] }}"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Charts Section --}}
        <div class="row g-4 mb-5">
            <div class="col-12 col-lg-8">
                <div class="bg-white rounded-4 border border-slate-200 shadow-sm p-4 h-100">
                    <h5 class="fw-black text-slate-900 mb-1" style="font-size:0.95rem;">Asset Valuation by Entity</h5>
                    <p class="text-slate-500 mb-4" style="font-size:0.75rem;">Top 10 High-Value Organizational Units (in Millions ₦)</p>
                    <div style="height:350px;">
                        <canvas id="valuationChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="bg-white rounded-4 border border-slate-200 shadow-sm p-4 h-100">
                    <h5 class="fw-black text-slate-900 mb-1" style="font-size:0.95rem;">Asset Status Breakdown</h5>
                    <p class="text-slate-500 mb-4" style="font-size:0.75rem;">Inventory Availability</p>
                    <div style="height:300px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top 10 High Value Entities Table --}}
        <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden mb-5">
            <div class="p-4 border-bottom border-slate-100 bg-slate-50">
                <h5 class="fw-black text-slate-900 mb-0" style="font-size:0.95rem;">Top 10 Entities by Valuation</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="ps-4 py-3 text-slate-500 fw-bold uppercase" style="font-size:0.7rem;">Entity Name</th>
                            <th class="py-3 text-slate-500 fw-bold uppercase text-end" style="font-size:0.7rem;">Total Assets</th>
                            <th class="pe-4 py-3 text-slate-500 fw-bold uppercase text-end" style="font-size:0.7rem;">Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allEntities as $entity)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="ps-4 py-3">
                                <span class="fw-bold text-slate-800">{{ $entity->label }}</span>
                            </td>
                            <td class="py-3 text-end">
                                <span class="badge bg-slate-100 text-slate-600 px-3">Registry Record</span>
                            </td>
                            <td class="pe-4 py-3 text-end fw-black text-slate-900">
                                ₦{{ number_format($entity->total, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top Assets Registry --}}
        <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-bottom border-slate-100 bg-slate-50">
                <h5 class="fw-black text-slate-900 mb-0" style="font-size:0.95rem;">High-Value Asset Registry</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="ps-4 py-3 text-slate-500 fw-bold uppercase" style="font-size:0.7rem;">Asset Details</th>
                            <th class="py-3 text-slate-500 fw-bold uppercase" style="font-size:0.7rem;">Category</th>
                            <th class="py-3 text-slate-500 fw-bold uppercase text-center" style="font-size:0.7rem;">Qty</th>
                            <th class="pe-4 py-3 text-slate-500 fw-bold uppercase text-end" style="font-size:0.7rem;">Valuation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topAssets as $asset)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-slate-900">{{ $asset->item_name }}</div>
                                <small class="text-slate-400">ID: #{{ $asset->asset_id }}</small>
                            </td>
                            <td class="py-3">
                                <span class="badge border border-slate-200 text-slate-600 bg-white fw-bold">{{ $asset->category->category_name ?? 'N/A' }}</span>
                            </td>
                            <td class="py-3 text-center fw-bold">{{ $asset->quantity }}</td>
                            <td class="pe-4 py-3 text-end fw-black text-indigo-700">
                                ₦{{ number_format($asset->total_value, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-5 text-slate-400">No assets found in registry.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

{{-- Chart.js Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Valuation Bar Chart
    const valuationCtx = document.getElementById('valuationChart');
    new Chart(valuationCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['enrollment_labels']) !!},
            datasets: [{
                label: 'Value (Millions ₦)',
                data: {!! json_encode($chartData['enrollment_data']) !!},
                backgroundColor: '#6366f1',
                borderRadius: 6,
                barThickness: 35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Status Doughnut Chart
    const statusCtx = document.getElementById('statusChart');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($chartData['status_labels']) !!},
            datasets: [{
                data: {!! json_encode($chartData['status_data']) !!},
                backgroundColor: ['#10b981', '#6366f1', '#f59e0b', '#ef4444'],
                hoverOffset: 4,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            }
        }
    });
});
</script>
@endsection