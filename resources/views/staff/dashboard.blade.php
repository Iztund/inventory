@extends('layouts.staff')

@section('title', 'Staff Dashboard')

@section('content')
<style>
    :root {
        --med-navy: #0f172a;
        --med-blue: #2563eb;
        --med-slate: #f8fafc;
        --med-border: #e2e8f0;
    }

    .dashboard-wrapper { padding: 1.5rem; }
    @media (max-width: 991.98px) { .dashboard-wrapper { padding: 1rem; } }

    /* Welcome Header */
    .welcome-banner {
        background: linear-gradient(135deg, var(--med-navy) 0%, #1e293b 100%);
        border-radius: 24px; padding: 40px; color: white;
        position: relative; overflow: hidden; margin-bottom: 30px;
        border-bottom: 6px solid var(--med-blue);
    }

    .stat-card-clean {
        background: white; border-radius: 20px; border: 1px solid var(--med-border);
        padding: 24px; transition: all 0.2s ease; height: 100%;
    }
    
    /* Tags */
    .tag-pending {
        font-size: 0.7rem; color: #94a3b8; font-style: italic;
        background: #f1f5f9; padding: 4px 12px; border-radius: 6px;
        border: 1px dashed #cbd5e1; display: inline-block;
    }

    .tag-official {
        background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;
        padding: 4px 12px; border-radius: 6px; font-weight: 800;
        font-family: 'Monaco', monospace; font-size: 0.75rem; display: inline-block;
    }

    .card-main { background: white; border-radius: 24px; border: 1px solid var(--med-border); overflow: hidden; }
    
    /* Status Dot */
    .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
    .bg-pending { background-color: #f59e0b; }
    .bg-verified { background-color: #10b981; }

    /* Responsive Stacked Table Logic */
    @media (max-width: 767.98px) {
        .welcome-banner { padding: 25px; border-radius: 16px; }
        
        .responsive-table thead { display: none; } /* Hide headers on mobile */
        
        .responsive-table tbody tr {
            display: block;
            border-bottom: 8px solid #f1f5f9 !important;
            padding: 15px 10px;
        }

        .responsive-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 5px !important;
            border: none !important;
            text-align: right;
            font-size: 0.85rem;
        }

        .responsive-table td::before {
            content: attr(data-label);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.65rem;
            color: #64748b;
            text-align: left;
            flex: 1;
        }

        .responsive-table td > div, .responsive-table td > span {
            flex: 2;
            text-align: right;
        }

        .mobile-btn-container {
            display: block !important;
            width: 100%;
            margin-top: 10px;
        }
        .mobile-btn-container .btn { width: 100%; }
    }
</style>

<div class="dashboard-wrapper">
    
    {{-- 1. Welcome Banner --}}
    <div class="welcome-banner shadow-sm">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h6 class="text-info fw-bold text-uppercase mb-2" style="letter-spacing: 2px;">Inventory Control</h6>
                <h1 class="display-6 fw-bold mb-1">Welcome, {{ Auth::user()->profile->first_name ?? Auth::user()->username }}</h1>
                <p class="opacity-75 mb-0 small">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    Current Assignment: 
                    <span class="fw-bold text-white">
                        @if(Auth::user()->unit_id) {{ Auth::user()->unit->unit_name }}
                        @elseif(Auth::user()->department_id) {{ Auth::user()->department->dept_name }}
                        @elseif(Auth::user()->institute_id) {{ Auth::user()->institute->institute_name }}
                        @elseif(Auth::user()->office_id) {{ Auth::user()->office->office_name }}
                        @elseif(Auth::user()->faculty_id) {{ Auth::user()->faculty->faculty_name }}
                        @else College of Medicine @endif
                    </span>
                </p>
            </div>
            <div class="col-md-5 text-md-end d-none d-md-block">
                <a href="{{ route('staff.submissions.create') }}" class="btn btn-info text-navy rounded-pill px-4 py-2 fw-bold shadow">
                    <i class="fas fa-plus me-1"></i> New Entry
                </a>
            </div>
        </div>
    </div>

    {{-- 2. Stats Grid --}}
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <div class="stat-card-clean shadow-sm">
                <div class="text-muted small fw-bold text-uppercase mb-1">My Submissions</div>
                <h2 class="fw-bold m-0 text-navy">{{ $stats['total'] }}</h2>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-clean shadow-sm">
                <div class="text-muted small fw-bold text-uppercase mb-1">Pending Audit</div>
                <h2 class="fw-bold m-0 text-warning">{{ $stats['pending'] }}</h2>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-clean shadow-sm">
                <div class="text-muted small fw-bold text-uppercase mb-1">Verified Assets</div>
                <h2 class="fw-bold m-0 text-success">{{ $stats['approved'] }}</h2>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-clean shadow-sm">
                <div class="text-muted small fw-bold text-uppercase mb-1">Total Assets</div>
                <h2 class="fw-bold m-0 text-primary">{{ $totalUnitAssets }}</h2>
            </div>
        </div>
    </div>

    {{-- 3. Activity Table --}}
    <div class="card-main shadow-sm">
        <div class="px-4 py-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold m-0 text-navy">Audit Activity Log</h5>
            <a href="{{ route('staff.submissions.index') }}" class="text-primary small fw-bold text-decoration-none">History Archives →</a>
        </div>
        <div class="table-responsive">
            <table class="table m-0 responsive-table">
                <thead>
                    <tr>
                        <th class="ps-4">Reference Number</th>
                        <th>Asset Name</th>
                        <th>Inventory Tag</th>
                        <th>Audit Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSubmissions as $sub)
                    @php
                        $u = Auth::user();
                        $firstItem = $sub->items->first();
                        
                        $prefix = 'COM';
                        if($u->unit_id) $prefix = $u->unit->unit_code;
                        elseif($u->department_id) $prefix = $u->department->dept_code;
                        elseif($u->institute_id) $prefix = $u->institute->institute_code ?? 'INST';
                        elseif($u->office_id) $prefix = $u->office->office_code;
                        elseif($u->faculty_id) $prefix = $u->faculty->faculty_code ?? 'FAC';
                    @endphp
                    <tr>
                        <td data-label="Reference Number" class="ps-md-4">
                            <div class="fw-bold text-navy">#AUD/{{ str_pad($sub->submission_id, 4, '0', STR_PAD_LEFT) }}</div>
                            <div class="text-muted extra-small" style="font-size: 0.7rem;">{{ $sub->submitted_at ? $sub->submitted_at->format('M d, Y') : $sub->created_at->format('M d, Y') }}</div>
                        </td>
                        <td data-label="Asset Name">
                            <div class="fw-bold" style="color: #475569;">{{ Str::limit($firstItem->item_name ?? 'Multiple Items', 30) }}</div>
                            <div class="text-muted small">Batch Total: ₦{{ number_format($sub->items->sum(fn($i) => $i->cost * $i->quantity), 2) }}</div>
                        </td>
                        <td data-label="Inventory Tag">
                            @if($sub->status == 'approved' && $firstItem)
                                <div class="tag-official">
                                    {{ $prefix }}/{{ date('y') }}/{{ str_pad($firstItem->id, 4, '0', STR_PAD_LEFT) }}
                                </div>
                            @else
                                <div class="tag-pending">
                                    <i class="fas fa-hourglass-start me-1"></i> Tag Pending
                                </div>
                            @endif
                        </td>
                        <td data-label="Audit Status">
                            @if($sub->status == 'approved')
                                <span class="small fw-bold text-success"><span class="status-dot bg-verified"></span>Verified</span>
                            @elseif($sub->status == 'rejected')
                                <span class="small fw-bold text-danger"><span class="status-dot bg-danger"></span>Rejected</span>
                            @else
                                <span class="small fw-bold text-warning"><span class="status-dot bg-pending"></span>Under Review</span>
                            @endif
                        </td>
                        <td class="text-end pe-md-4 mobile-btn-container">
                            <a href="{{ route('staff.submissions.show', $sub->submission_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">No audit submissions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection