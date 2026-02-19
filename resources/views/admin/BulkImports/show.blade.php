@extends('layouts.admin')

@section('title', 'Import Details #' . str_pad($import->import_id, 4, '0', STR_PAD_LEFT))

@section('content')

<div class="min-vh-100 py-4 px-3 px-lg-5 bg-slate-50">
<div style="max-width:1600px;" class="mx-auto">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-5">
        <div>
            <div class="d-flex align-items-center gap-3 mb-2">
                <a href="{{ route('admin.bulk-assets.index') }}" 
                   class="btn btn-white border border-slate-200 rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                   style="width:44px; height:44px;">
                    <i class="fas fa-arrow-left text-slate-600"></i>
                </a>
                <div>
                    <h1 class="fw-black text-slate-900 mb-1" style="font-size:1.75rem; letter-spacing:-0.02em;">
                        Import #{{ str_pad($import->import_id, 4, '0', STR_PAD_LEFT) }}
                    </h1>
                    <p class="text-slate-600 mb-0">
                        {{ ucfirst($import->import_type) }} import to {{ ucfirst($import->entity_type) }}: {{ $import->entity_name }}
                    </p>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            {{-- Generate Tags Button --}}
            @php
                $untaggedCount = $import->assets()->whereNull('asset_tag')->count();
            @endphp
            
            @if($untaggedCount > 0)
            <form action="{{ route('admin.bulk-assets.generate-tags', $import->import_id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-amber-600 text-white d-flex align-items-center gap-2 rounded-3 shadow-sm"
                        style="font-size:0.8rem; padding:0.65rem 1.2rem; background:#f59e0b;">
                    <i class="fas fa-tags"></i>
                    <span class="fw-bold">Generate Tags ({{ $untaggedCount }})</span>
                </button>
            </form>
            @endif

            {{-- Status Badge --}}
            @if($import->status === 'completed')
                <span class="badge bg-success px-3 py-2">
                    <i class="fas fa-check-circle me-1"></i> Completed
                </span>
            @elseif($import->status === 'processing')
                <span class="badge bg-warning px-3 py-2">
                    <i class="fas fa-spinner fa-spin me-1"></i> Processing
                </span>
            @elseif($import->status === 'failed')
                <span class="badge bg-danger px-3 py-2">
                    <i class="fas fa-times-circle me-1"></i> Failed
                </span>
            @else
                <span class="badge bg-secondary px-3 py-2">
                    <i class="fas fa-clock me-1"></i> Pending
                </span>
            @endif
        </div>
    </div>

    {{-- Alert for untagged assets --}}
    @if($untaggedCount > 0)
    <div class="alert alert-warning border-0 rounded-3 mb-4 d-flex align-items-start gap-3">
        <i class="fas fa-exclamation-triangle fs-4 text-warning"></i>
        <div>
            <h6 class="fw-bold mb-2">Asset Tags Pending</h6>
            <p class="mb-2">{{ $untaggedCount }} asset(s) in this import don't have asset tags yet. Asset tags are generated automatically after import to ensure proper tracking.</p>
            <p class="mb-0 small"><strong>Asset Tag Format:</strong> <code>ENTITY-CATEGORY-0001</code></p>
        </div>
    </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="rounded-4 bg-white border border-slate-200 shadow-sm p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-blue-100 d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                        <i class="fas fa-list text-blue-600 fs-5"></i>
                    </div>
                    <div>
                        <p class="text-slate-500 text-uppercase mb-1" style="font-size:0.7rem; font-weight:700;">Total Rows</p>
                        <h3 class="fw-black text-slate-900 mb-0">{{ $import->total_rows }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="rounded-4 bg-white border border-slate-200 shadow-sm p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-emerald-100 d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                        <i class="fas fa-check text-emerald-600 fs-5"></i>
                    </div>
                    <div>
                        <p class="text-slate-500 text-uppercase mb-1" style="font-size:0.7rem; font-weight:700;">Successful</p>
                        <h3 class="fw-black text-emerald-600 mb-0">{{ $import->successful_imports }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="rounded-4 bg-white border border-slate-200 shadow-sm p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-red-100 d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                        <i class="fas fa-times text-red-600 fs-5"></i>
                    </div>
                    <div>
                        <p class="text-slate-500 text-uppercase mb-1" style="font-size:0.7rem; font-weight:700;">Failed</p>
                        <h3 class="fw-black text-red-600 mb-0">{{ $import->failed_imports }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="rounded-4 bg-white border border-slate-200 shadow-sm p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-amber-100 d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                        <i class="fas fa-percentage text-amber-600 fs-5"></i>
                    </div>
                    <div>
                        <p class="text-slate-500 text-uppercase mb-1" style="font-size:0.7rem; font-weight:700;">Success Rate</p>
                        <h3 class="fw-black text-slate-900 mb-0">{{ $import->success_rate }}%</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Details --}}
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="rounded-4 bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-bottom border-slate-200">
                    <h5 class="fw-black text-slate-900 mb-0">
                        <i class="fas fa-info-circle me-2 text-slate-600"></i>
                        Import Information
                    </h5>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <p class="text-slate-500 text-uppercase mb-1" style="font-size:0.7rem; font-weight:700;">Import Type</p>
                            <p class="fw-bold text-slate-900 mb-0">{{ ucfirst($import->import_type) }}</p>
                        </div>
                        <div class="col-6">
                            <p class="text-slate-500 text-uppercase mb-1" style="font-size:0.7rem; font-weight:700;">Imported By</p>
                            <p class="fw-bold text-slate-900 mb-0">{{ $import->importedBy->full_name }}</p>
                        </div>
                        @if($import->original_filename)
                        <div class="col-12">
                            <p class="text-slate-500 text-uppercase mb-1" style="font-size:0.7rem; font-weight:700;">Filename</p>
                            <p class="fw-bold text-slate-900 mb-0">{{ $import->original_filename }}</p>
                        </div>
                        @endif
                        <div class="col-6">
                            <p class="text-slate-500 text-uppercase mb-1" style="font-size:0.7rem; font-weight:700;">Started At</p>
                            <p class="fw-bold text-slate-900 mb-0">{{ $import->started_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="col-6">
                            <p class="text-slate-500 text-uppercase mb-1" style="font-size:0.7rem; font-weight:700;">Completed At</p>
                            <p class="fw-bold text-slate-900 mb-0">{{ $import->completed_at?->format('M d, Y h:i A') ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="rounded-4 bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-bottom border-slate-200">
                    <h5 class="fw-black text-slate-900 mb-0">
                        <i class="fas fa-map-marker-alt me-2 text-slate-600"></i>
                        Target Location
                    </h5>
                </div>
                <div class="p-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-indigo-50 border border-indigo-100">
                        <div class="rounded-3 bg-indigo-600 d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                            <i class="fas fa-building text-white"></i>
                        </div>
                        <div>
                            <p class="text-indigo-600 text-uppercase mb-1 fw-bold" style="font-size:0.7rem;">{{ ucfirst($import->entity_type) }}</p>
                            <h6 class="fw-black text-slate-900 mb-0">{{ $import->entity_name }}</h6>
                        </div>
                    </div>
                    
                    {{-- Asset Tag Information --}}
                    <div class="mt-3 p-3 rounded-3 bg-amber-50 border border-amber-100">
                        <p class="text-amber-700 fw-bold text-uppercase mb-2" style="font-size:0.7rem;">
                            <i class="fas fa-tag me-1"></i> Asset Tag Format
                        </p>
                        <div class="d-flex align-items-center gap-2">
                            <code class="bg-white px-2 py-1 rounded border border-amber-200 text-amber-900 fw-bold">
                                {{ strtoupper(substr($import->entity_name, 0, 3)) }}-CAT-0001
                            </code>
                            <span class="text-slate-500 small">Auto-generated after import</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Error Log (if any) --}}
    {{-- Error Log (if any) --}}
    @if($import->failed_imports > 0 && $import->error_log)
    <div class="rounded-4 bg-white border border-red-200 shadow-sm overflow-hidden mb-5">
        <div class="p-4 bg-red-50 border-bottom border-red-100">
            <h5 class="fw-black text-slate-900 mb-0">
                <i class="fas fa-exclamation-triangle me-2 text-red-600"></i>
                Failed Rows ({{ $import->failed_imports }})
            </h5>
        </div>
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-red-50 sticky-top">
                    <tr>
                        <th class="fw-bold text-slate-700 py-3 ps-4">Row #</th>
                        <th class="fw-bold text-slate-700 py-3">Data Samples</th>
                        <th class="fw-bold text-slate-700 py-3">Error Message</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($import->error_log as $error)
                    <tr>
                        <td class="fw-bold text-red-600 ps-4">
                            {{ is_array($error) ? ($error['row'] ?? 'N/A') : '#' }}
                        </td>
                        <td>
                            @if(isset($error['data']) && is_array($error['data']))
                                <code class="bg-slate-100 px-2 py-1 rounded text-xs text-slate-700">
                                    {{ implode(', ', array_slice($error['data'], 0, 3)) }}...
                                </code>
                            @else
                                <span class="text-slate-400 small italic">System error / No row data</span>
                            @endif
                        </td>
                        <td class="text-danger small fw-medium">
                            {{ is_array($error) ? ($error['error'] ?? 'Unknown processing error') : $error }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Imported Assets --}}
    <div class="rounded-4 bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 bg-slate-50 border-bottom border-slate-200">
            <h5 class="fw-black text-slate-900 mb-0">
                <i class="fas fa-boxes me-2 text-slate-600"></i>
                Imported Assets ({{ $import->assets->count() }})
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="fw-bold text-slate-600">Asset ID</th>
                        <th class="fw-bold text-slate-600">Item Name</th>
                        <th class="fw-bold text-slate-600">Category</th>
                        <th class="fw-bold text-slate-600">Serial Number</th>
                        <th class="fw-bold text-slate-600">Asset Tag</th>
                        <th class="fw-bold text-slate-600">Quantity</th>
                        <th class="fw-bold text-slate-600">Price</th>
                        <th class="fw-bold text-slate-600">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($import->assets as $asset)
                    <tr>
                        <td class="fw-bold text-slate-900">#{{ str_pad($asset->asset_id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="fw-bold text-slate-900">{{ $asset->item_name }}</td>
                        <td>
                            <span class="badge bg-indigo-100 text-indigo-700">
                                {{ $asset->category->category_name }}
                            </span>
                        </td>
                        <td class="text-slate-600" style="font-size:0.85rem;">{{ $asset->serial_number }}</td>
                        <td>
                            @if($asset->asset_tag)
                                <code class="bg-emerald-50 text-emerald-700 px-2 py-1 rounded fw-bold border border-emerald-200">
                                    {{ $asset->asset_tag }}
                                </code>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock me-1"></i> Pending
                                </span>
                            @endif
                        </td>
                        <td class="fw-bold">{{ $asset->quantity }}</td>
                        <td class="fw-bold text-emerald-600">₦{{ number_format($asset->purchase_price, 2) }}</td>
                        <td>
                            @if($asset->status === 'available')
                                <span class="badge bg-success">Available</span>
                            @elseif($asset->status === 'assigned')
                                <span class="badge bg-primary">Assigned</span>
                            @elseif($asset->status === 'maintenance')
                                <span class="badge bg-warning">Maintenance</span>
                            @else
                                <span class="badge bg-secondary">Retired</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-slate-500">
                            No assets imported yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>

@endsection