@extends('layouts.staff')

@section('title', 'Submission Details')

@section('content')
<style>
    :root {
        --med-primary: #0f172a; --med-accent: #3b82f6; --med-bg: #f8fafc; 
        --side-bg: #0f172a; --side-text: #f1f5f9; --side-label: #7dd3fc;
    }
    body { background-color: var(--med-bg); font-family: 'Plus Jakarta Sans', sans-serif; }
    
    .glass-header { 
        background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);
        border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 1000; padding: 1.2rem 0;
    }

    .ref-no { font-family: monospace; font-size: 0.75rem; color: var(--med-accent); font-weight: 800; }
    .item-status-pill {
        display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 6px;
        font-weight: 700; font-size: 0.65rem; text-transform: uppercase;
        background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa;
    }
    
    .inventory-card { 
        background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin-bottom: 2rem;
    }

    /* Professional Document Link Design */
    .doc-link-card {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 16px; background: #f8fafc; border: 1px solid #e2e8f0;
        border-radius: 12px; text-decoration: none !important; color: var(--med-primary);
        transition: all 0.2s ease;
    }
    .doc-link-card:hover {
        background: #eff6ff; border-color: var(--med-accent); transform: translateY(-2px);
    }

    .sidebar-meta { background: var(--side-bg); color: var(--side-text); border-radius: 24px; padding: 25px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); }
    .side-label { font-size: 0.65rem; font-weight: 800; color: var(--side-label); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
    .side-text-readable { font-size: 0.85rem; line-height: 1.6; color: #e2e8f0; }
    
    .valuation-box { background: linear-gradient(135deg, #2563eb, #1d4ed8); padding: 20px; border-radius: 18px; margin: 20px 0; }

    @media print {
        .no-print { display: none !important; }
        .inventory-card { border: 1px solid #000 !important; border-radius: 0 !important; page-break-inside: avoid; }
        .sidebar-meta { background: #fff !important; color: #000 !important; border: 1px solid #000 !important; border-radius: 0 !important; }
        .side-label, .side-text-readable { color: #000 !important; }
    }
</style>

<div class="glass-header mb-4 no-print">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1" style="font-size: 0.75rem;">
                        <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('staff.submissions.index') }}">Submissions</a></li>
                        <li class="breadcrumb-item active">View #{{ $submission->submission_id }}</li>
                    </ol>
                </nav>
                <h2 class="h5 fw-bold mb-0">Submission Record</h2>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('staff.submissions.edit', $submission->submission_id) }}" class="btn btn-outline-primary rounded-pill px-3 fw-bold">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <button onclick="window.print()" class="btn btn-outline-dark rounded-pill px-3 fw-bold">
                    <i class="fas fa-print me-1"></i> Print
                </button>
                <a href="{{ route('staff.submissions.index') }}" class="btn btn-primary rounded-pill px-4 fw-bold">Done</a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4">
    <div class="row">
        <div class="col-lg-8">
            @foreach($submission->items as $index => $item)
            <div class="inventory-card">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: 800;">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <div class="ref-no">ITEM/{{ str_pad($submission->submission_id, 3, '0', STR_PAD_LEFT) }}/{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                            <h5 class="mb-0 fw-bold">{{ $item->item_name }}</h5>
                            <small class="text-muted fw-bold">{{ $item->category->category_name }}</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="item-status-pill"><i class="fas fa-clock me-1"></i> Pending Audit</span>
                    </div>
                </div>
                
                <div class="p-4">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="card-label">Quantity</div>
                            <div class="card-value">{{ $item->quantity }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-label">Unit Cost</div>
                            <div class="card-value">₦{{ number_format($item->cost, 2) }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="card-label">Funding Source</div>
                            <div class="card-value text-primary fw-bold">{{ $item->funding_source_per_item ?? 'General Fund' }}</div>
                        </div>

                        <div class="col-12 mt-2">
                            <div class="card-label">Asset Condition / Item Notes</div>
                            <div class="p-3 bg-light rounded-3 border-start border-4 border-warning">
                                <p class="mb-0 small text-dark fw-medium">
                                    {{ $item->item_notes ?? 'No specific notes recorded for this individual asset.' }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-12 mt-3">
                            <div class="card-label">Evidence & Attachments</div>
                            <div class="row g-3 mt-1">
                                @php 
                                    $files = is_string($item->document_path) ? json_decode($item->document_path, true) : $item->document_path; 
                                @endphp

                                @if(!empty($files))
                                    @foreach($files as $file)
                                        <div class="col-md-6">
                                            <a href="{{ Storage::url($file) }}" target="_blank" class="doc-link-card">
                                                <div class="d-flex align-items-center overflow-hidden">
                                                    <i class="fas fa-file-pdf text-danger me-3 fa-lg"></i>
                                                    <div class="text-truncate">
                                                        <div class="fw-bold small">{{ $item->item_name }}_Doc_{{ $loop->iteration }}</div>
                                                        <div class="text-muted" style="font-size: 0.65rem;">Click to preview file</div>
                                                    </div>
                                                </div>
                                                <i class="fas fa-external-link-alt text-muted small"></i>
                                            </a>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12"><p class="text-muted small italic">No digital attachments provided.</p></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="col-lg-4">
            <div class="sidebar-meta sticky-top" style="top: 90px;">
                <h6 class="fw-bold mb-4 text-white">Institutional Information</h6>
                
                <div class="mb-4">
                    <div class="side-label">Originating Entity</div>
                    @php $u = auth()->user(); @endphp
                    <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.08); border-left: 4px solid var(--side-accent);">
                        @if($u->office)
                            <div class="fw-bold text-white small">{{ $u->office->office_name }}</div>
                            @if($u->unit) <div class="side-text-readable" style="font-size: 0.75rem; color: var(--side-accent);">{{ $u->unit->unit_name }}</div> @endif
                        @elseif($u->faculty)
                            <div class="fw-bold text-white small">{{ $u->faculty->faculty_name }}</div>
                            @if($u->department) <div class="side-text-readable" style="font-size: 0.75rem; color: var(--side-accent);">{{ $u->department->dept_name }}</div> @endif
                        @endif
                    </div>
                </div>

                <div class="valuation-box">
                    <div class="side-label text-white-50 mb-1">Cumulative Value</div>
                    <div class="h4 fw-bold mb-0 text-white">₦{{ number_format($submission->items->sum(fn($i) => $i->cost * $i->quantity), 2) }}</div>
                </div>

                <div class="mt-4 pt-4 border-top border-secondary">
                    <div class="mb-4">
                        <div class="side-label">General Submission Notes</div>
                        <p class="side-text-readable">{{ $submission->notes ?? 'No general submission notes provided for this record.' }}</p>
                    </div>
                    <div>
                        <div class="side-label">Executive Summary</div>
                        <p class="side-text-readable">{{ $submission->summary ?? 'No overall summary provided for this record.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection