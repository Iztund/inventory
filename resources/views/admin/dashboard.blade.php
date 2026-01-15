@extends('layouts.admin')

@section('title', 'Admin Overview')
@section('active_link', 'dashboard')

@push('styles')
<style>
    :root {
        --med-primary: #2563eb;
        --med-success: #10b981;
        --med-warning: #f59e0b;
        --med-danger: #ef4444;
        --glass-bg: rgba(255, 255, 255, 0.9);
    }

    body { 
        background-color: #f1f5f9;
        background-image: radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.05) 0, transparent 50%), 
                          radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.05) 0, transparent 50%);
        min-height: 100vh;
    }

    .page-title { font-weight: 800; color: #0f172a; letter-spacing: -0.03em; }

    /* Beautiful Glass Card Effect */
    .beauty-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.7);
        border-radius: 24px;
        padding: 1.5rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        height: 100%;
    }
    
    .beauty-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.1);
        border-color: var(--med-primary);
    }

    /* Floating Icon Box */
    .icon-box {
        width: 56px; height: 56px;
        border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
    }

    /* User Access Gradients */
    .bg-gradient-dark { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; }
    .bg-gradient-warning { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #451a03; }

    /* Organizational Units Pills */
    .unit-box {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1rem;
        text-align: center;
        transition: 0.3s ease;
    }
    .unit-box:hover { border-color: var(--med-primary); background: #f8fafc; transform: scale(1.02); }

    .section-label {
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #64748b;
        margin-bottom: 1.25rem;
        display: block;
    }

    /* Sync Button Animation */
    .spin-icon { animation: fa-spin 1s infinite linear; }
</style>
@endpush

@section('content')
<div class="container-fluid py-5 px-lg-5">
    
    {{-- Top Header --}}
    <div class="row align-items-center mb-5">
        <div class="col-md-8">
            <h1 class="page-title mb-1">Intelligence Dashboard</h1>
            <p class="text-muted fw-medium">College of Medicine Central Inventory Hub</p>
        </div>
        <div class="col-md-4 text-md-end">
            <button id="syncButton" class="btn btn-white border-0 shadow-sm rounded-pill px-4 py-2 fw-bold text-primary" onclick="triggerSync()">
                <i class="fas fa-sync-alt me-2" id="syncIcon"></i><span id="syncText">Sync Data</span>
            </button>
        </div>
    </div>

    {{-- CORE METRICS --}}
    <span class="section-label">Inventory Overview</span>
    <div class="row g-4 mb-5">
        <div class="col-xl-4 col-md-6">
            <div class="beauty-card">
                <div class="icon-box" style="background: #eff6ff; color: var(--med-primary);">
                    <i class="fas fa-microscope"></i>
                </div>
                <div class="h6 text-muted fw-bold mb-1">Total Assets</div>
                <div class="h2 fw-bold mb-0 text-dark">{{ $totalAssets ?? 0 }}</div>
                <div class="mt-3 small text-success fw-bold">
                    <i class="fas fa-check-circle me-1"></i> Registry Healthy
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="beauty-card" style="border-bottom: 4px solid var(--med-danger);">
                <div class="icon-box" style="background: #fef2f2; color: var(--med-danger);">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="h6 text-muted fw-bold mb-1">Needs Review</div>
                <div class="h2 fw-bold mb-0 text-danger">{{ $pendingSubmissions ?? 0 }}</div>
                <a href="{{ route('admin.submissions.pending') }}" class="stretched-link mt-3 d-block small fw-bold text-decoration-none text-danger">
                    Process Requests <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <div class="col-xl-4 col-md-12">
            <div class="beauty-card">
                <div class="icon-box" style="background: #f0fdf4; color: var(--med-success);">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="h6 text-muted fw-bold mb-1">Total Requests</div>
                <div class="h2 fw-bold mb-0 text-dark">{{ $totalSubmissions ?? 0 }}</div>
                <div class="mt-3 small text-muted">Historical log entries</div>
            </div>
        </div>
    </div>

    {{-- USER METRICS --}}
    <span class="section-label">User Access Control</span>
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="beauty-card bg-gradient-dark border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="h6 opacity-75 fw-bold mb-1 text-uppercase small">Active Users</div>
                        <div class="display-6 fw-bold">{{ $totalActiveUsers ?? 0 }}</div>
                    </div>
                    <div class="icon-box bg-white bg-opacity-10 text-white mb-0">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="beauty-card bg-gradient-warning border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="h6 opacity-75 fw-bold mb-1 text-uppercase small text-dark">Inactive / Disabled</div>
                        <div class="display-6 fw-bold text-dark">{{ $totalInactiveUsers ?? 0 }}</div>
                    </div>
                    <div class="icon-box bg-black bg-opacity-10 text-dark mb-0">
                        <i class="fas fa-user-lock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ORGANIZATIONAL UNITS --}}
    <span class="section-label">Organizational Structure</span>
    <div class="row row-cols-1 row-cols-md-5 g-3 mb-5">
        <div class="col">
            <div class="unit-box">
                <div class="h4 fw-bold text-primary mb-0">{{ $totalFaculties ?? 0 }}</div>
                <div class="small text-muted fw-bold uppercase" style="font-size: 0.6rem;">Faculties</div>
            </div>
        </div>
        <div class="col">
            <div class="unit-box">
                <div class="h4 fw-bold text-success mb-0">{{ $totalDepartments ?? 0 }}</div>
                <div class="small text-muted fw-bold uppercase" style="font-size: 0.6rem;">Departments</div>
            </div>
        </div>
        <div class="col">
            <div class="unit-box">
                <div class="h4 fw-bold text-warning mb-0">{{ $totalInstitutes ?? 0 }}</div>
                <div class="small text-muted fw-bold uppercase" style="font-size: 0.6rem;">Institutes</div>
            </div>
        </div>
        <div class="col">
            <div class="unit-box border-info">
                <div class="h4 fw-bold text-info mb-0">{{ $totalOffices ?? 0 }}</div>
                <div class="small text-muted fw-bold uppercase" style="font-size: 0.6rem;">Offices</div>
            </div>
        </div>
        <div class="col">
            <div class="unit-box">
                <div class="h4 fw-bold text-dark mb-0">{{ $totalUnits ?? 0 }}</div>
                <div class="small text-muted fw-bold uppercase" style="font-size: 0.6rem;">Admin Units</div>
            </div>
        </div>
    </div>

    {{-- CHARTS & RECENT HISTORY --}}
    <div class="row g-4">
        <div class="col-xl-7">
            <div class="beauty-card">
                <h6 class="fw-800 mb-4 small text-uppercase text-muted">Submission Verification Status</h6>
                <canvas id="submissionsChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="beauty-card p-0 overflow-hidden">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center bg-light bg-opacity-50">
                    <h6 class="fw-800 mb-0 small text-uppercase text-muted">Recent Activity</h6>
                    <a href="{{ route('admin.submissions.pending') }}" class="small fw-bold text-primary text-decoration-none">Review Queue</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            @forelse($recentSubmissions as $r)
                            <tr onclick="window.location='{{ route('admin.submissions.show', $r->submission_id) }}'" style="cursor: pointer;">
                                <td class="ps-4 py-3">
                                    <div class="small fw-bold text-dark">#{{ $r->submission_id }}</div>
                                    <div class="text-muted" style="font-size: 0.65rem;">{{ $r->submittedBy->department->dept_name ?? 'Unit' }}</div>
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <span class="badge rounded-pill {{ $r->status === 'pending' ? 'bg-warning text-dark' : 'bg-success' }}" style="font-size: 0.6rem;">
                                        {{ ucfirst($r->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center py-5 text-muted small">No items pending review</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Setup chart data from PHP
    window.statusCounts = {!! json_encode([
        (int) ($pendingSubmissions ?? 0), 
        (int) ($approvedSubmissions ?? 0), 
        (int) ($rejectedSubmissions ?? 0)
    ]) !!};

    // 2. Sync Button functionality
    function triggerSync() {
        const btn = document.getElementById('syncButton');
        const icon = document.getElementById('syncIcon');
        const text = document.getElementById('syncText');

        // Start animation
        icon.classList.add('spin-icon');
        text.innerText = 'Updating...';
        btn.style.pointerEvents = 'none';
        btn.style.opacity = '0.7';

        // Refresh the page after 1 second
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }
</script>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('build/assets/js/dashboard_admin.js') }}"></script> 
@endpush