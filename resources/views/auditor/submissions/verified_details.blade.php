@extends('layouts.auditor')

@section('title', 'Submission Details #' . $submission->submission_id)

@section('content')
<div class="container-fluid px-4 py-4">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 print-hide">
        <div class="animate-fade-in">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('auditor.dashboard') }}" class="text-primary text-decoration-none fw-bold">Dashboard</a></li>
                    <li class="breadcrumb-item active text-muted">Asset Audit</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">Record #AUD-{{ str_pad($submission->submission_id, 4, '0', STR_PAD_LEFT) }}</h2>
        </div>

        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-print-premium">
                <i class="fas fa-print me-2"></i> Print Report
            </button>
            <a href="{{ url()->previous() }}" class="btn btn-glass-dark rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Side Column: Origin Profile --}}
        <div class="col-lg-4 col-xl-3">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100 glass-sidebar">
                <div class="bg-primary p-3 text-white text-center">
                    <small class="text-uppercase fw-bold ls-1">Verified Origin</small>
                </div>
                
                <div class="card-body p-4">
                    @php
                        $user = $submission->submittedBy;
                        
                        // Name Fallback Logic
                        $fullName = trim(($user->profile->first_name ?? '') . ' ' . ($user->profile->last_name ?? ''));
                        $displayName = !empty($fullName) ? $fullName : $user->username;
                        $initial = strtoupper(substr($displayName, 0, 1));

                        // Corrected Categorical Logic: These are distinct paths
                        $isAcademic = $user->faculty || $user->department;
                        $isAdministrative = $user->office || $user->unit;
                        $isInstitute = $user->institute;
                    @endphp

                    {{-- User Profile Section --}}
                    <div class="text-center mb-4">
                        <div class="avatar-circle-ui mb-3 mx-auto">{{ $initial }}</div>
                        <h5 class="fw-bold text-dark mb-1">{{ $displayName }}</h5>
                        <p class="text-muted small mb-0">{{ $user->email }}</p>
                    </div>

                    <hr class="my-4 opacity-50">

                    {{-- Categorized Organizational Information --}}
                    <div class="hierarchy-stack">
                        
                        {{-- Path 1: Academic --}}
                        @if($isAcademic)
                            <div class="mb-4">
                                <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Academic Structure</label>
                                @if($user->faculty)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-university text-primary me-2"></i>
                                        <span class="text-dark fw-bold small">{{ $user->faculty->faculty_name }}</span>
                                    </div>
                                @endif
                                @if($user->department)
                                    <div class="d-flex align-items-center ms-3">
                                        <i class="fas fa-chevron-right text-muted me-2 small"></i>
                                        <span class="text-dark small">{{ $user->department->dept_name }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Path 2: Administrative --}}
                        @if($isAdministrative)
                            <div class="mb-4">
                                <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Administrative Structure</label>
                                @if($user->office)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-building text-primary me-2"></i>
                                        <span class="text-dark fw-bold small">{{ $user->office->office_name }}</span>
                                    </div>
                                @endif
                                @if($user->unit)
                                    <div class="d-flex align-items-center ms-3">
                                        <i class="fas fa-chevron-right text-muted me-2 small"></i>
                                        <span class="text-dark small">{{ $user->unit->unit_name }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Path 3: Institute --}}
                        @if($isInstitute)
                            <div class="mb-4">
                                <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Research Structure</label>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-microscope text-primary me-2"></i>
                                    <span class="text-dark fw-bold small">{{ $user->institute->institute_name }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="status-pill-box p-3 rounded-4 {{ $submission->status == 'approved' ? 'bg-soft-success' : 'bg-soft-danger' }}">
                            <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Audit Status</label>
                            <span class="fw-bold {{ $submission->status == 'approved' ? 'text-success' : 'text-danger' }}">
                                <i class="fas {{ $submission->status == 'approved' ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                {{ strtoupper($submission->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Column: Asset Ledger --}}
        <div class="col-lg-8 col-xl-9">
            <div class="card border-0 shadow-lg rounded-4 h-100 bg-white overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-dark mb-0">Asset Registry Ledger</h5>
                        <span class="badge bg-dark rounded-pill px-3 py-2">{{ $submission->items->count() }} Line Items</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle custom-table">
                            <thead>
                                <tr>
                                    <th class="ps-3">Asset Tag</th>
                                    <th>Specification</th>
                                    <th>Category</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end pe-3">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submission->items as $item)
                                <tr>
                                    <td class="ps-3">
                                        @if($submission->status == 'approved')
                                            <span class="tag-label">{{ $item->asset->asset_tag ?? 'PENDING' }}</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->item_name }}</div>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $item->category->category_name ?? 'Equipment' }}</span>
                                    </td>
                                    <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                    <td class="text-end pe-3 fw-bold text-primary">
                                        ₦{{ number_format($item->cost * $item->quantity, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Total Footer --}}
                <div class="card-footer bg-dark p-4 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-white">
                            <span class="text-uppercase opacity-50 x-small fw-bold d-block ls-1">Total Verified Value</span>
                            <h2 class="fw-bold mb-0">₦{{ number_format($submission->items->sum(fn($i) => $i->cost * $i->quantity), 2) }}</h2>
                        </div>
                        <div class="text-end text-white-50 d-none d-md-block">
                            <i class="fas fa-file-invoice-dollar fa-3x opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling */
    .ls-1 { letter-spacing: 1.5px; }
    .x-small { font-size: 0.7rem; }
    .bg-soft-success { background-color: #ecfdf5; }
    .bg-soft-danger { background-color: #fef2f2; }
    
    /* Springy Premium Print Button */
    .btn-print-premium {
        background: #0d6efd;
        color: white;
        border: none;
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: 700;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
    }

    .btn-print-premium:hover {
        background: #0b5ed7;
        color: white;
        transform: translateY(-4px) scale(1.05);
        box-shadow: 0 12px 25px rgba(13, 110, 253, 0.3);
    }

    .btn-glass-dark {
        background: rgba(15, 23, 42, 0.05);
        border: 1px solid rgba(15, 23, 42, 0.1);
        color: #0f172a;
        font-weight: 600;
        transition: all 0.3s;
    }

    /* Profile UI */
    .avatar-circle-ui {
        width: 64px; height: 64px;
        background: linear-gradient(135deg, #0d6efd 0%, #003d99 100%);
        color: white;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem; font-weight: 800;
    }

    /* Table */
    .custom-table thead th {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #64748b;
        padding: 1.25rem 0.5rem;
        border-bottom: 2px solid #f1f5f9;
    }

    .tag-label {
        font-family: 'Courier New', monospace;
        background: #f8fafc;
        color: #334155;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 700;
        border: 1px solid #e2e8f0;
    }

    @media print {
        .print-hide { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; }
        .card-footer { -webkit-print-color-adjust: exact; }
    }
</style>
@endsection