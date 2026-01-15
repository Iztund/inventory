@extends('layouts.staff')

@section('title', 'My Inventory Submissions')

@section('content')
<style>
    :root {
        --med-navy: #0f172a; --med-blue: #3b82f6; --med-border: #e2e8f0;
    }

    /* Restoring your exact Alert Styling */
    .alert-custom {
        border-radius: 12px; border: none; font-size: 0.85rem; font-weight: 600;
        display: flex; align-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .alert-success-custom { background: #ecfdf5; color: #065f46; border-left: 5px solid #10b981; }
    .alert-error-custom { background: #fef2f2; color: #991b1b; border-left: 5px solid #ef4444; }

    .submission-list-item { transition: all 0.2s ease; border-bottom: 1px solid var(--med-border); }
    .submission-list-item:hover { background-color: #f8fafc; }

    .action-container { display: flex; gap: 8px; justify-content: flex-end; }
    .btn-circle {
        width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
        border-radius: 50%; border: none; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }
    .btn-v { background: #eff6ff; color: #3b82f6; } .btn-v:hover { background: #3b82f6; color: #fff; transform: scale(1.1); }
    .btn-e { background: #f1f5f9; color: #0f172a; } .btn-e:hover { background: #0f172a; color: #fff; transform: scale(1.1); }
    .btn-d { background: #fef2f2; color: #ef4444; } .btn-d:hover { background: #ef4444; color: #fff; transform: scale(1.1); }

    .status-pill { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; padding: 5px 12px; border-radius: 50px; border: 1px solid transparent; white-space: nowrap; }
    .bg-pending { background: #fffbeb; color: #92400e; border-color: #fef3c7; }
    .bg-approved { background: #ecfdf5; color: #065f46; border-color: #d1fae5; }
    .bg-rejected { background: #fef2f2; color: #991b1b; border-color: #fee2e2; }
    
    .asset-tag-box { font-family: 'Monaco', 'Consolas', monospace; font-size: 0.75rem; background: #f8fafc; border: 1px solid #dee2e6; padding: 3px 10px; border-radius: 6px; color: #475569; font-weight: 700; }
    .awaiting { border-style: dashed; color: #94a3b8; font-weight: 400; font-style: italic; }

    .search-wrapper { background: #f1f5f9; border-radius: 50px; padding: 4px 20px; display: flex; align-items: center; border: 2px solid transparent; transition: 0.3s; flex: 1; }
    .search-wrapper:focus-within { background: #fff; border-color: var(--med-blue); box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.05); }
    .search-wrapper input { border: none; background: transparent; padding: 8px; width: 100%; outline: none; font-size: 0.9rem; }

    /* Mobile Layout Fixes */
    .mobile-submission-card { background: white; border: 1px solid var(--med-border); border-radius: 12px; padding: 1rem; margin-bottom: 1rem; }
    .mobile-card-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--med-border); }
    .mobile-card-row { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; font-size: 0.875rem; }
    .mobile-label { color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; }
    .mobile-value { color: #1e293b; font-weight: 600; text-align: right; }
    .mobile-actions { display: flex; gap: 0.5rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--med-border); }

    @media (max-width: 991px) {
        .table-responsive { display: none; }
        .mobile-view { display: block !important; }
        .filter-buttons { overflow-x: auto; flex-wrap: nowrap; padding-bottom: 5px; }
    }
</style>

<div class="container-fluid px-3 px-lg-4 py-4">
    {{-- SESSION MESSAGES --}}
    @if(session('success'))
        <div class="alert alert-custom alert-success-custom alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-3 fa-lg"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Fixed Header: Log and Button aligned properly --}}
    <div class="d-flex flex-row justify-content-between align-items-end mb-4">
        <div>
            <h6 class="text-primary fw-bold text-uppercase mb-1 small d-none d-md-block" style="letter-spacing: 1px;">College Inventory Log</h6>
            <h2 class="fw-bold text-dark mb-0 h4">My Submissions</h2>
        </div>
        <a href="{{ route('staff.submissions.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm w-auto">
            <i class="fas fa-plus-circle me-1"></i> <span class="d-none d-sm-inline">New Entry</span><span class="d-inline d-sm-none">New</span>
        </a>
    </div>

    {{-- Search & Filter --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('staff.submissions.index') }}" method="GET" class="d-flex flex-column gap-3">
                <div class="d-flex flex-wrap gap-2 filter-buttons">
                    <a href="{{ route('staff.submissions.index') }}" class="btn btn-sm rounded-pill px-3 {{ !request('status') ? 'btn-dark shadow' : 'btn-light border text-muted' }}">All</a>
                    @foreach(['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'] as $status => $color)
                        <a href="{{ route('staff.submissions.index', ['status' => $status]) }}" 
                           class="btn btn-sm rounded-pill px-3 {{ request('status') == $status ? "btn-$color fw-bold ".($color != 'warning' ? 'text-white' : '') : 'btn-light border text-muted' }}">
                           {{ ucfirst($status) }}
                        </a>
                    @endforeach
                </div>
                <div class="search-wrapper">
                    <i class="fas fa-search text-muted me-2"></i>
                    <input type="text" name="search" placeholder="Search Reference or Item..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3 ms-2">Find</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Desktop Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden d-none d-lg-block">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bolder text-muted">Ref ID</th>
                        <th class="py-3 text-uppercase small fw-bolder text-muted">Inventory Item</th>
                        <th class="py-3 text-uppercase small fw-bolder text-muted">Asset Tag</th>
                        <th class="py-3 text-uppercase small fw-bolder text-muted">Valuation</th>
                        <th class="py-3 text-uppercase small fw-bolder text-muted text-center">Status</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bolder text-muted text-center">Operations</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $sub)
                    @php
                        $firstItem = $sub->items->first();
                        $count = $sub->items->count();
                        $totalVal = $sub->items->sum(fn($i) => $i->cost * $i->quantity);
                    @endphp
                    <tr class="submission-list-item">
                        <td class="ps-4">
                            <span class="fw-bold text-dark">#AUD-{{ str_pad($sub->submission_id, 5, '0', STR_PAD_LEFT) }}</span>
                            <div class="text-muted small">{{ $sub->created_at->format('M d, Y') }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark small">{{ Str::limit($firstItem->item_name ?? 'N/A', 35) }}</div>
                            @if($count > 1) <div class="text-primary fw-bold" style="font-size: 0.6rem;">+ {{ $count - 1 }} Other items</div> @endif
                        </td>
                        <td>
                            @if($sub->status == 'approved' && $firstItem?->asset?->asset_tag)
                                <span class="asset-tag-box">{{ $firstItem->asset->asset_tag }}</span>
                            @else
                                <span class="asset-tag-box awaiting">Awaiting Tag</span>
                            @endif
                        </td>
                        <td><div class="fw-bold text-dark">₦{{ number_format($totalVal, 2) }}</div></td>
                        <td class="text-center"><span class="status-pill bg-{{ $sub->status }}">{{ $sub->status }}</span></td>
                        <td class="pe-4">
                            <div class="action-container">
                                <a href="{{ route('staff.submissions.show', $sub->submission_id) }}" class="btn-circle btn-v"><i class="fas fa-eye small"></i></a>
                                @if(in_array($sub->status, ['pending', 'rejected']))
                                    <a href="{{ route('staff.submissions.edit', $sub->submission_id) }}" class="btn-circle btn-e"><i class="fas fa-pencil-alt small"></i></a>
                                    {{-- RESTORED DELETE BUTTON --}}
                                    <form action="{{ route('staff.submissions.destroy', $sub->submission_id) }}" method="POST" onsubmit="return confirm('Confirm deletion of this record?');" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-circle btn-d"><i class="fas fa-trash-alt small"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5">No inventory records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile View --}}
    <div class="mobile-view d-lg-none" style="display: none;">
        @foreach($submissions as $sub)
        @php
            $firstItem = $sub->items->first();
            $totalVal = $sub->items->sum(fn($i) => $i->cost * $i->quantity);
        @endphp
        <div class="mobile-submission-card shadow-sm">
            <div class="mobile-card-header">
                <div>
                    <div class="fw-bold text-dark">#AUD-{{ str_pad($sub->submission_id, 5, '0', STR_PAD_LEFT) }}</div>
                    <div class="text-muted small">{{ $sub->created_at->format('M d, Y') }}</div>
                </div>
                <span class="status-pill bg-{{ $sub->status }}">{{ $sub->status }}</span>
            </div>
            <div class="mobile-card-row">
                <span class="mobile-label">Item</span>
                <span class="mobile-value">{{ Str::limit($firstItem->item_name ?? 'N/A', 25) }}</span>
            </div>
            <div class="mobile-card-row">
                <span class="mobile-label">Value</span>
                <span class="mobile-value">₦{{ number_format($totalVal, 2) }}</span>
            </div>
            <div class="mobile-actions">
                <a href="{{ route('staff.submissions.show', $sub->submission_id) }}" class="btn btn-sm btn-outline-primary rounded-pill flex-grow-1">View</a>
                @if(in_array($sub->status, ['pending', 'rejected']))
                    <a href="{{ route('staff.submissions.edit', $sub->submission_id) }}" class="btn btn-sm btn-outline-dark rounded-pill flex-grow-1">Edit</a>
                    {{-- MOBILE DELETE --}}
                    <form action="{{ route('staff.submissions.destroy', $sub->submission_id) }}" method="POST" onsubmit="return confirm('Confirm deletion?');" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill"><i class="fas fa-trash-alt"></i></button>
                    </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $submissions->appends(request()->query())->links() }}
    </div>
</div>
@endsection