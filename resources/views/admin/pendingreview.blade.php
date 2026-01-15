@extends('layouts.admin')

@section('title', 'Pending Review')
@section('active_link', 'submissions')

@push('styles')
<style>
    body { 
        background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); 
        min-height: 100vh;
    }
    .page-title { font-weight: 800; color: #1e293b; letter-spacing: -0.02em; }
    
    .stat-pill {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1rem 1.25rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    
    .stat-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
    }

    .card-table {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
        background: white;
    }
    .table thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.65rem;
        font-weight: 800;
        color: #64748b;
        padding: 15px 20px;
        border: none;
    }
    .table tbody td { padding: 16px 20px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    
    .type-badge {
        font-size: 0.65rem;
        font-weight: 800;
        padding: 5px 12px;
        border-radius: 6px;
        text-transform: uppercase;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 px-lg-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title mb-1">Items Awaiting Review <span class="text-primary">.</span></h3>
            <p class="text-muted small">College of Medicine Central Asset Registry</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-white bg-white border px-3 rounded-3 shadow-sm small fw-bold" onclick="window.location.reload()">
                <i class="fas fa-sync-alt text-primary me-2"></i>Refresh List
            </button>
        </div>
    </div>

    {{-- Simplified Stats Bar --}}
    <div class="row g-3 mb-4">
        {{-- Total --}}
        <div class="col">
            <div class="stat-pill d-flex align-items-center">
                <div class="stat-icon bg-dark text-white me-3"><i class="fas fa-list"></i></div>
                <div>
                    <div class="small fw-bold text-muted text-uppercase" style="font-size: 0.6rem;">Total Pending</div>
                    <div class="h5 mb-0 fw-bold">{{ $submissions->total() }}</div>
                </div>
            </div>
        </div>

        {{-- New Items --}}
        <div class="col">
            <div class="stat-pill d-flex align-items-center">
                <div class="stat-icon bg-success-soft text-success me-3" style="background: #ecfdf5;"><i class="fas fa-plus-circle"></i></div>
                <div>
                    <div class="small fw-bold text-muted text-uppercase" style="font-size: 0.6rem;">New Items</div>
                    <div class="h5 mb-0 fw-bold">{{ $submissions->where('submission_type', 'new_purchase')->count() }}</div>
                </div>
            </div>
        </div>

        {{-- Transfers --}}
        <div class="col">
            <div class="stat-pill d-flex align-items-center">
                <div class="stat-icon bg-blue-soft text-primary me-3" style="background: #eff6ff;"><i class="fas fa-shipping-fast"></i></div>
                <div>
                    <div class="small fw-bold text-muted text-uppercase" style="font-size: 0.6rem;">Moving Items</div>
                    <div class="h5 mb-0 fw-bold">{{ $submissions->where('submission_type', 'transfer')->count() }}</div>
                </div>
            </div>
        </div>

        {{-- Repairs --}}
        <div class="col">
            <div class="stat-pill d-flex align-items-center">
                <div class="stat-icon bg-warning-soft text-warning me-3" style="background: #fffbeb;"><i class="fas fa-tools"></i></div>
                <div>
                    <div class="small fw-bold text-muted text-uppercase" style="font-size: 0.6rem;">Repairs</div>
                    <div class="h5 mb-0 fw-bold">{{ $submissions->where('submission_type', 'maintenance')->count() }}</div>
                </div>
            </div>
        </div>

        {{-- Disposals --}}
        <div class="col">
            <div class="stat-pill d-flex align-items-center text-danger">
                <div class="stat-icon bg-danger-soft text-danger me-3" style="background: #fef2f2;"><i class="fas fa-trash"></i></div>
                <div>
                    <div class="small fw-bold text-uppercase" style="font-size: 0.6rem;">Disposing</div>
                    <div class="h5 mb-0 fw-bold">{{ $submissions->where('submission_type', 'disposal')->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-table mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Ref Number</th>
                            <th>Action Type</th>
                            <th>Description</th>
                            <th>Dept / Lab Unit</th>
                            <th>Date Sent</th>
                            <th class="text-center">Total Items</th>
                            <th class="text-end">Verification</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $submission)
                            <tr>
                                <td><span class="fw-bold text-dark">#{{ $submission->submission_id }}</span></td>
                                <td>
                                    @php
                                        $type_meta = [
                                            'new_purchase' => ['label' => 'New Entry', 'bg' => '#dcfce7', 'text' => '#166534'],
                                            'transfer' => ['label' => 'Moving', 'bg' => '#dbeafe', 'text' => '#1e40af'],
                                            'disposal' => ['label' => 'Removing', 'bg' => '#fee2e2', 'text' => '#991b1b'],
                                            'maintenance' => ['label' => 'Repairing', 'bg' => '#fef3c7', 'text' => '#92400e'],
                                        ][$submission->submission_type] ?? ['label' => 'General', 'bg' => '#f1f5f9', 'text' => '#475569'];
                                    @endphp
                                    <span class="type-badge" style="background: {{ $type_meta['bg'] }}; color: {{ $type_meta['text'] }};">
                                        {{ $type_meta['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark">{{ Str::limit($submission->summary, 45) }}</div>
                                </td>
                                <td>
                                    <div class="small fw-bold">{{ $submission->submittedBy->department->dept_name ?? 'N/A' }}</div>
                                    <div class="text-muted" style="font-size: 0.65rem;">Staff: {{ $submission->submittedBy->username ?? 'Unknown' }}</div>
                                </td>
                                <td>
                                    <div class="small fw-bold">{{ $submission->created_at->format('M d, Y') }}</div>
                                    <div class="text-muted" style="font-size: 0.65rem;">{{ $submission->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-light text-dark border px-3">
                                        {{ $submission->items->count() }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.submissions.show', $submission->submission_id) }}" 
                                       class="btn btn-primary btn-sm rounded-3 px-3 fw-bold">
                                        Open Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">No items currently waiting for review.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection