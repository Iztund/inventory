@extends('layouts.admin')

@section('title', 'Import Details #' . str_pad($import->import_id, 4, '0', STR_PAD_LEFT))

@section('content')

<div class="min-vh-100 py-4 px-3 px-lg-5" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);">
<div style="max-width:1600px;" class="mx-auto">

    {{-- Header Section --}}
    <div class="text-center mb-5">
        <a href="{{ route('admin.bulk-assets.index') }}" 
           class="btn btn-lg d-inline-flex align-items-center justify-content-center rounded-circle mb-4 shadow-lg" 
           style="width: 60px; height: 60px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
            <i class="fas fa-arrow-left text-white fs-5"></i>
        </a>
        
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" 
             style="width: 120px; height: 120px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 20px 60px rgba(99,102,241,0.4);">
            <i class="fas fa-file-import text-white" style="font-size: 3rem; filter: drop-shadow(0 0 10px rgba(255,255,255,0.5));"></i>
        </div>
        
        <h1 class="text-white fw-black mb-3" style="font-size: 2.5rem; letter-spacing: -0.02em; text-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            Import #{{ str_pad($import->import_id, 4, '0', STR_PAD_LEFT) }}
        </h1>
        
        <p class="text-white mb-4" style="font-size: 1.1rem; opacity: 0.8;">
            {{ ucfirst($import->import_type) }} import • {{ ucfirst($import->entity_type) }}: <strong>{{ $import->entity_name }}</strong>
        </p>

        {{-- Status Badge --}}
        <div class="d-inline-flex align-items-center gap-3">
            @if($import->status === 'completed')
                <span class="badge rounded-pill px-4 py-3 shadow-lg" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); font-size: 1rem;">
                    <i class="fas fa-check-circle me-2"></i> Completed
                </span>
            @elseif($import->status === 'processing')
                <span class="badge rounded-pill px-4 py-3 shadow-lg" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); font-size: 1rem;">
                    <i class="fas fa-spinner fa-spin me-2"></i> Processing
                </span>
            @elseif($import->status === 'failed')
                <span class="badge rounded-pill px-4 py-3 shadow-lg" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); font-size: 1rem;">
                    <i class="fas fa-times-circle me-2"></i> Failed
                </span>
            @else
                <span class="badge rounded-pill px-4 py-3 shadow-lg" style="background: rgba(255,255,255,0.2); font-size: 1rem;">
                    <i class="fas fa-clock me-2"></i> Pending
                </span>
            @endif

            {{-- Generate Tags Button --}}
            @php
                $untaggedCount = $import->assets()->whereNull('asset_tag')->count();
            @endphp
            
            @if($untaggedCount > 0)
            <form action="{{ route('admin.bulk-assets.generate-tags', $import->import_id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-lg rounded-pill px-4 py-3 fw-bold shadow-lg" 
                        style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none;">
                    <i class="fas fa-tags me-2"></i>
                    Generate Tags ({{ $untaggedCount }})
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Alert for Untagged Assets --}}
    @if($untaggedCount > 0)
    <div class="rounded-4 p-4 mb-4 shadow-lg" style="background: rgba(245, 158, 11, 0.1); backdrop-filter: blur(20px); border: 1px solid rgba(245, 158, 11, 0.3);">
        <div class="d-flex align-items-start gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: rgba(245, 158, 11, 0.2);">
                <i class="fas fa-exclamation-triangle text-white fs-4"></i>
            </div>
            <div class="text-white">
                <h6 class="fw-bold mb-2">Asset Tags Pending</h6>
                <p class="mb-2" style="opacity: 0.9;">{{ $untaggedCount }} asset(s) don't have tags yet. Click the "Generate Tags" button above to create them.</p>
                <p class="mb-0" style="opacity: 0.7; font-size: 0.85rem;"><strong>Format:</strong> <code class="bg-white text-amber-600 px-2 py-1 rounded">COM/ENTITY/CAT/SUBCAT/YY/XXXXXX</code></p>
            </div>
        </div>
    </div>
    @endif

    {{-- Summary Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="rounded-4 overflow-hidden shadow-lg" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1);">
                <div class="p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: rgba(59, 130, 246, 0.2);">
                            <i class="fas fa-list text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <p class="text-white mb-1 text-uppercase" style="font-size: 0.7rem; opacity: 0.7; font-weight: 700;">Total Rows</p>
                            <h3 class="text-white fw-black mb-0" style="font-size: 2rem;">{{ $import->total_rows }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="rounded-4 overflow-hidden shadow-lg" style="background: rgba(5, 150, 105, 0.1); backdrop-filter: blur(20px); border: 1px solid rgba(5, 150, 105, 0.3);">
                <div class="p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: rgba(5, 150, 105, 0.2);">
                            <i class="fas fa-check text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <p class="text-white mb-1 text-uppercase" style="font-size: 0.7rem; opacity: 0.7; font-weight: 700;">Successful</p>
                            <h3 class="text-white fw-black mb-0" style="font-size: 2rem;">{{ $import->successful_imports }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="rounded-4 overflow-hidden shadow-lg" style="background: rgba(220, 38, 38, 0.1); backdrop-filter: blur(20px); border: 1px solid rgba(220, 38, 38, 0.3);">
                <div class="p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: rgba(220, 38, 38, 0.2);">
                            <i class="fas fa-times text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <p class="text-white mb-1 text-uppercase" style="font-size: 0.7rem; opacity: 0.7; font-weight: 700;">Failed</p>
                            <h3 class="text-white fw-black mb-0" style="font-size: 2rem;">{{ $import->failed_imports }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="rounded-4 overflow-hidden shadow-lg" style="background: rgba(245, 158, 11, 0.1); backdrop-filter: blur(20px); border: 1px solid rgba(245, 158, 11, 0.3);">
                <div class="p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: rgba(245, 158, 11, 0.2);">
                            <i class="fas fa-percentage text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <p class="text-white mb-1 text-uppercase" style="font-size: 0.7rem; opacity: 0.7; font-weight: 700;">Success Rate</p>
                            <h3 class="text-white fw-black mb-0" style="font-size: 2rem;">{{ $import->success_rate }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Details & Target Location Cards --}}
    <div class="row g-4 mb-4">
        {{-- Import Information --}}
        <div class="col-md-6">
            <div class="rounded-4 overflow-hidden shadow-lg" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1);">
                <div class="p-4 d-flex align-items-center gap-3" style="background: rgba(99, 102, 241, 0.1); border-bottom: 1px solid rgba(99, 102, 241, 0.2);">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(99, 102, 241, 0.2);">
                        <i class="fas fa-info-circle text-white"></i>
                    </div>
                    <h5 class="text-white fw-black mb-0">Import Information</h5>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <p class="text-white text-uppercase mb-1" style="font-size: 0.7rem; opacity: 0.6; font-weight: 700;">Import Type</p>
                            <p class="text-white fw-bold mb-0">{{ ucfirst($import->import_type) }}</p>
                        </div>
                        <div class="col-6">
                            <p class="text-white text-uppercase mb-1" style="font-size: 0.7rem; opacity: 0.6; font-weight: 700;">Imported By</p>
                            <p class="text-white fw-bold mb-0">{{ $import->importedBy->full_name }}</p>
                        </div>
                        @if($import->original_filename)
                        <div class="col-12">
                            <p class="text-white text-uppercase mb-1" style="font-size: 0.7rem; opacity: 0.6; font-weight: 700;">Filename</p>
                            <code class="bg-white text-indigo-600 px-2 py-1 rounded fw-bold">{{ $import->original_filename }}</code>
                        </div>
                        @endif
                        <div class="col-6">
                            <p class="text-white text-uppercase mb-1" style="font-size: 0.7rem; opacity: 0.6; font-weight: 700;">Started At</p>
                            <p class="text-white fw-bold mb-0">{{ $import->started_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="col-6">
                            <p class="text-white text-uppercase mb-1" style="font-size: 0.7rem; opacity: 0.6; font-weight: 700;">Completed At</p>
                            <p class="text-white fw-bold mb-0">{{ $import->completed_at?->format('M d, Y h:i A') ?? 'In Progress' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Target Location --}}
        <div class="col-md-6">
            <div class="rounded-4 overflow-hidden shadow-lg" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1);">
                <div class="p-4 d-flex align-items-center gap-3" style="background: rgba(5, 150, 105, 0.1); border-bottom: 1px solid rgba(5, 150, 105, 0.2);">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(5, 150, 105, 0.2);">
                        <i class="fas fa-map-marker-alt text-white"></i>
                    </div>
                    <h5 class="text-white fw-black mb-0">Target Location</h5>
                </div>
                <div class="p-4">
                    <div class="p-3 rounded-3 mb-3" style="background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2);">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(99, 102, 241, 0.2);">
                                <i class="fas fa-building text-white"></i>
                            </div>
                            <div>
                                <p class="text-white text-uppercase mb-1 fw-bold" style="font-size: 0.7rem; opacity: 0.7;">{{ ucfirst($import->entity_type) }}</p>
                                <h6 class="text-white fw-black mb-0">{{ $import->entity_name }}</h6>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    {{-- Error Log --}}
    @if($import->failed_imports > 0 && $import->error_log)
    <div class="rounded-4 overflow-hidden shadow-lg mb-4" style="background: rgba(220, 38, 38, 0.1); backdrop-filter: blur(20px); border: 1px solid rgba(220, 38, 38, 0.3);">
        <div class="p-4 d-flex align-items-center gap-3" style="background: rgba(220, 38, 38, 0.1); border-bottom: 1px solid rgba(220, 38, 38, 0.2);">
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(220, 38, 38, 0.2);">
                <i class="fas fa-exclamation-triangle text-white"></i>
            </div>
            <h5 class="text-white fw-black mb-0">Failed Rows ({{ $import->failed_imports }})</h5>
        </div>
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-dark table-hover mb-0" style="background: transparent;">
                <thead style="background: rgba(220, 38, 38, 0.1); position: sticky; top: 0;">
                    <tr>
                        <th class="text-white fw-bold py-3 border-0">Row #</th>
                        <th class="text-white fw-bold py-3 border-0">Data Samples</th>
                        <th class="text-white fw-bold py-3 border-0">Error Message</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($import->error_log as $error)
                    <tr style="border-color: rgba(255,255,255,0.1);">
                        <td class="text-danger fw-bold border-0 py-3">
                            {{ is_array($error) ? ($error['row'] ?? 'N/A') : '#' }}
                        </td>
                        <td class="border-0 py-3">
                            @if(isset($error['data']) && is_array($error['data']))
                                <code class="bg-white text-slate-700 px-2 py-1 rounded" style="font-size: 0.8rem;">
                                    {{ implode(', ', array_slice($error['data'], 0, 3)) }}...
                                </code>
                            @else
                                <span class="text-white" style="opacity: 0.5; font-size: 0.85rem; font-style: italic;">System error / No row data</span>
                            @endif
                        </td>
                        <td class="text-danger border-0 py-3" style="font-size: 0.85rem;">
                            {{ is_array($error) ? ($error['error'] ?? 'Unknown processing error') : $error }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Imported Assets Table --}}
    <div class="rounded-4 overflow-hidden shadow-lg" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1);">
        <div class="p-4 d-flex align-items-center gap-3" style="background: rgba(99, 102, 241, 0.1); border-bottom: 1px solid rgba(99, 102, 241, 0.2);">
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(99, 102, 241, 0.2);">
                <i class="fas fa-boxes text-white"></i>
            </div>
            <h5 class="text-white fw-black mb-0">Imported Assets ({{ $import->assets->count() }})</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0" style="background: transparent;">
                <thead style="background: rgba(255,255,255,0.05);">
                    <tr>
                        <th class="text-white fw-bold border-0 py-3">ID</th>
                        <th class="text-white fw-bold border-0 py-3">Item Name</th>
                        <th class="text-white fw-bold border-0 py-3">Cat/SubCat</th>
                        <th class="text-white fw-bold border-0 py-3">Serial</th>
                        <th class="text-white fw-bold border-0 py-3">Asset Tag</th>
                        <th class="text-white fw-bold border-0 py-3">Qty</th>
                        <th class="text-white fw-bold border-0 py-3">Unit Price</th>
                        <th class="text-white fw-bold border-0 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($import->assets as $asset)
                    <tr style="border-color: rgba(255,255,255,0.1);">
                        <td class="text-white fw-bold border-0 py-3" style="font-family: 'Fira Code', monospace;">#{{ str_pad($asset->asset_id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="text-white fw-bold border-0 py-3" style="white-space: nowrap">{{ $asset->item_name }}</td>
                        <td class="border-0 py-3">
                            <span class="badge rounded-pill px-3 py-2" style="background: rgba(99, 102, 241, 0.2); color: white;">
                                {{ $asset->category->category_name }}
                            </span>
                            <span class="badge rounded-pill px-3 py-2" style="background: rgba(5, 150, 105, 0.2); color: white;">
                                {{ $asset->subcategory->subcategory_name }}
                            </span>
                        </td>
                        <td class="text-white border-0 py-3" style=" font-size: 0.85rem; font-family: 'Fira Code', monospace; white-space: nowrap;">{{ $asset->serial_number }}</td>
                        <td class="border-0 py-3">
                            @if($asset->asset_tag)
                                <code class="bg-emerald-600 text-white px-3 py-2 rounded fw-bold shadow-sm" style="font-family: 'Fira Code', monospace; font-size: 0.85rem;">
                                    {{ $asset->asset_tag }}
                                </code>
                            @else
                                <span class="badge rounded-pill px-3 py-2" style="background: rgba(245, 158, 11, 0.2); color: white;">
                                    <i class="fas fa-clock me-1"></i> Pending
                                </span>
                            @endif
                        </td>
                        <td class="text-white fw-bold border-0 py-3">{{ $asset->quantity }}</td>
                        <td class="fw-bold border-0 py-3" style="color: #059669;">₦{{ number_format($asset->purchase_price, 2) }}</td>
                        <td class="border-0 py-3">
                            @if($asset->status === 'available')
                                <span class="badge rounded-pill px-3 py-2" style="background: rgba(5, 150, 105, 0.2); color: white;">Available</span>
                            @elseif($asset->status === 'assigned')
                                <span class="badge rounded-pill px-3 py-2" style="background: rgba(59, 130, 246, 0.2); color: white;">Assigned</span>
                            @elseif($asset->status === 'maintenance')
                                <span class="badge rounded-pill px-3 py-2" style="background: rgba(245, 158, 11, 0.2); color: white;">Maintenance</span>
                            @else
                                <span class="badge rounded-pill px-3 py-2" style="background: rgba(100, 116, 139, 0.2); color: white;">Retired</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 border-0">
                            <i class="fas fa-inbox text-white mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="text-white mb-0" style="opacity: 0.5;">No assets imported yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>

<style>
@keyframes slideDown {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.table-dark tbody tr:hover {
    background: rgba(255,255,255,0.05) !important;
    transform: scale(1.01);
    transition: all 0.2s;
}

/* Custom scrollbar for tables */
.table-responsive::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.05);
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: rgba(245, 158, 11, 0.5);
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: rgba(245, 158, 11, 0.7);
}

.btn:hover {
    transform: translateY(-2px);
    transition: all 0.3s;
}
</style>

@endsection