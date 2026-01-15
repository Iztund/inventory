@extends('layouts.admin')

@section('title', 'User Administration')

@section('content')
<div class="container-fluid py-5 px-lg-5" style="background-color: #f8fafc; min-height: 100vh;">

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.02em;">User Administration</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small text-muted bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Access Management</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.create') }}" class="btn btn-dark shadow-sm px-4 rounded-3 fw-medium">
                <i class="fas fa-user-plus me-2"></i>Add New User
            </a>
        </div>
    </div>

    {{-- Temporary Password Alert (Persistent until closed) --}}
    @if(session('default_password'))
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 p-4 fade show" role="alert">
            <div class="d-flex align-items-start">
                <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3">
                    <i class="fas fa-key text-warning fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <h6 class="fw-bold text-dark mb-1">Temporary Password Generated</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <p class="text-muted small mb-3">Please share this secure credential with the user. They will be forced to change it on login.</p>
                    <div class="d-flex align-items-center bg-white p-2 border rounded-3 w-auto d-inline-flex">
                        <code id="defaultPasswordText" class="fs-5 fw-bold px-3 text-primary">{{ session('default_password') }}</code>
                        <button type="button" class="btn btn-sm btn-light border ms-2" onclick="copyToClipboard(event)">
                            <i class="fas fa-copy me-1"></i> Copy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Feedback Messages (Auto-hiding) --}}
    @if (session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} border-0 shadow-sm rounded-4 mb-4 p-3 d-flex justify-content-between align-items-center fade show" role="alert">
            <div>
                <i class="fas {{ session('success') ? 'fa-check-circle' : 'fa-exclamation-triangle' }} me-2"></i>
                {{ session('success') ?? session('error') }}
            </div>
            <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.75rem;"></button>
        </div>
    @endif

    <div class="bg-white rounded-4 shadow-sm border-0 mb-4 p-3">
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2">
            <div class="col-md-11">
                <div class="input-group bg-light rounded-3 px-3">
                    <span class="input-group-text bg-transparent border-0 text-muted">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="q" class="form-control bg-transparent border-0 py-2 ps-0 shadow-none" 
                           placeholder="Search by name, username, or email..." value="{{ request('q') }}">
                    @if(request('q'))
                        <a href="{{ route('admin.users.index') }}" class="input-group-text bg-transparent border-0 text-danger">
                            <i class="fas fa-times-circle"></i>
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-dark rounded-3 fw-bold border-0">Go</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-4 shadow-sm border-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #fcfcfd;">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">User Identification</th>
                        <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Role</th>
                        <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Affiliation</th>
                        <th class="py-3 text-uppercase text-muted fw-bold text-center" style="font-size: 0.7rem; letter-spacing: 0.05em;">Status</th>
                        <th class="pe-4 py-3 text-end text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Manage</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse ($users as $userItem)
                    <tr style="transition: all 0.2s ease;">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center text-primary fw-bold me-3 shadow-sm" style="width: 42px; height: 42px;">
                                    {{ strtoupper(substr($userItem->profile->full_name ?? $userItem->username, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">{{ $userItem->profile->full_name ?? 'No Name' }}</div>
                                    <div class="text-muted small">{{ $userItem->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-light text-dark border-0 fw-medium px-3 py-2" style="font-size: 0.75rem;">
                                {{ $userItem->role->role_name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="py-3">
                            @if ($userItem->office)
                                <div class="fw-bold text-primary small"><i class="fas fa-building me-1"></i> {{ $userItem->office->office_code }}</div>
                            @elseif ($userItem->institute)
                                <div class="fw-bold text-success small"><i class="fas fa-university me-1"></i> {{ $userItem->institute->institute_code }}</div>
                            @elseif ($userItem->faculty)
                                <div class="fw-bold text-info small"><i class="fas fa-landmark me-1"></i> {{ $userItem->faculty->faculty_code }}</div>
                            @endif
                            <div class="text-muted" style="font-size: 0.7rem;">
                                {{ $userItem->department->dept_name ?? $userItem->unit->unit_name ?? 'No Specific Unit' }}
                            </div>
                        </td>
                        <td class="py-3 text-center">
                            @if($userItem->status == 'active')
                                <span class="badge rounded-pill bg-success-subtle text-success px-3" style="font-size: 0.7rem;">Active</span>
                            @else
                                <span class="badge rounded-pill bg-light text-muted px-3" style="font-size: 0.7rem;">Inactive</span>
                            @endif
                        </td>
                        <td class="pe-4 py-3 text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle border-0" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v text-muted" style="font-size: 0.8rem;"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                    <li><a class="dropdown-item small py-2" href="{{ route('admin.users.edit', $userItem->user_id) }}"><i class="fas fa-edit me-2 text-muted"></i>Edit User</a></li>
                                    <li><hr class="dropdown-divider opacity-50"></li>
                                    <li>
                                        <form action="{{ route('admin.users.destroy', $userItem->user_id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item small py-2 text-danger" onclick="return confirm('Delete user?')">
                                                <i class="fas fa-trash-alt me-2"></i>Delete User
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-4 opacity-50">
                                <i class="fas fa-users-slash fa-3x mb-3"></i>
                                <h6 class="fw-normal">No users found.</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-top bg-light-subtle">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries</span>
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<style>
    .table thead th { border-bottom: 1px solid #f1f5f9; }
    .table tbody tr:hover { background-color: #f8fafc !important; }
    .bg-primary-subtle { background-color: #eff6ff !important; color: #2563eb !important; }
    .bg-success-subtle { background-color: #f0fdf4 !important; color: #16a34a !important; }
    .pagination { margin-bottom: 0; gap: 4px; }
    .page-link { border: none; border-radius: 8px !important; color: #64748b; padding: 8px 14px; }
    .page-item.active .page-link { background-color: #1e293b; color: white; }
    .btn-close:focus { box-shadow: none; }
</style>

<script>
    // Auto-hide alerts after 4 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-success, .alert-danger');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 4000);
        });
    });

    function copyToClipboard(event) {
        var passwordText = document.getElementById("defaultPasswordText").innerText;
        navigator.clipboard.writeText(passwordText).then(() => {
            const btn = event.currentTarget;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            btn.classList.replace('btn-light', 'btn-success');
            setTimeout(() => { 
                btn.innerHTML = originalHtml; 
                btn.classList.replace('btn-success', 'btn-light');
            }, 2000);
        });
    }
</script>
@endsection