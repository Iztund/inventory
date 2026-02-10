@extends('layouts.admin')

@section('title', 'Admin Overview')
@section('active_link', 'dashboard')

@section('content')
<div class="container-fluid px-4 py-4">
    
    {{-- 1. Welcome Section --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 to-slate-800 p-8 md:p-10 mb-8 border-b-4 border-emerald-500 shadow-xl">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white/5 rounded-full"></div>
        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-32 h-32 bg-emerald-500/10 rounded-full"></div>
        
        <div class="row align-items-center relative z-10">
            <div class="col-lg-8">
                <h6 class="text-emerald-400 font-black uppercase tracking-[0.2em] text-xs mb-3">System Administration</h6>
                <h1 class="text-3xl md:text-4xl font-black text-white mb-2">
                    Welcome back, {{ Auth::user()->profile->first_name ?? Auth::user()->username }}
                </h1>
                <p class="text-slate-400 font-medium mb-0 flex items-center">
                    <i class="fas fa-shield-check text-emerald-500 me-2"></i>
                    College of Medicine Inventory System • Central Control
                </p>
            </div>
            <div class="col-lg-4 text-lg-end d-none d-lg-block">
                <div class="inline-block bg-white/10 backdrop-blur-md border border-white/20 p-4 rounded-2xl min-w-[200px]">
                    <div class="text-[12px] text-emerald-400 font-black uppercase tracking-widest mb-1">
                        Pending Review
                    </div>
                    <div class="text-3xl font-black text-white mb-0">
                        {{ number_format($pendingSubmissions ?? 0) }} 
                        <span class="text-sm text-slate-300 font-bold uppercase tracking-tighter">Requests</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Core Metrics Grid (6 cards in 2 rows) --}}
    <div class="row g-4 mb-8">
        @php
            $coreMetrics = [
                ['Total Assets Registered', $totalAssets ?? 0, 'fa-microscope', 'text-blue-600', 'bg-blue-50', 'Registry Healthy'],
                ['Needs Review', $pendingSubmissions ?? 0, 'fa-bell', 'text-rose-500', 'bg-rose-50', 'Urgent Action'],
                ['Approved Items', $approvedSubmissions ?? 0, 'fa-check-circle', 'text-emerald-500', 'bg-emerald-50', 'Processed'],
                ['Rejected Items', $rejectedSubmissions ?? 0, 'fa-times-circle', 'text-red-500', 'bg-red-50', 'Denied'],
                ['Total Requests', $totalSubmissions ?? 0, 'fa-clipboard-list', 'text-indigo-500', 'bg-indigo-50', 'Historical Log'],
                ['Asset Categories', $totalCategories ?? 0, 'fa-tags', 'text-amber-500', 'bg-amber-50', 'Classification'],
            ];
        @endphp
        
        @foreach($coreMetrics as [$label, $val, $icon, $textColor, $bgColor, $subtitle])
        <div class="col-6 col-lg-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm transition-transform hover:-translate-y-1">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-[11px] font-black text-slate-800 uppercase tracking-wider">{{ $label }}</span>
                    <div class="w-8 h-8 rounded-lg {{ $bgColor }} flex items-center justify-center">
                        <i class="fas {{ $icon }} {{ $textColor }} text-xs"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900 mb-1">{{ number_format($val) }}</div>
                <div class="text-[9px] font-bold text-slate-600 uppercase">{{ $subtitle }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- 3. User Access Control --}}
    <div class="flex items-center space-x-2 mb-4">
        <div class="h-1 w-8 bg-emerald-600 rounded-full"></div>
        <h6 class="font-black text-slate-900 uppercase tracking-widest text-[11px] mb-0">User Access Control</h6>
    </div>

    <div class="row g-4 mb-8">
        <div class="col-md-6">
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-6 shadow-lg border border-slate-700">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-2">Active Users</div>
                        <div class="text-4xl font-black text-white">{{ $totalActiveUsers ?? 0 }}</div>
                        <div class="text-emerald-400 text-xs font-bold mt-2">
                            <i class="fas fa-check-circle me-1"></i> System Access Enabled
                        </div>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-white/10 flex items-center justify-center text-emerald-400">
                        <i class="fas fa-user-check text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-6 shadow-lg">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-[10px] text-amber-900 font-black uppercase tracking-widest mb-2">Inactive / Disabled</div>
                        <div class="text-4xl font-black text-slate-900">{{ $totalInactiveUsers ?? 0 }}</div>
                        <div class="text-amber-900 text-xs font-bold mt-2">
                            <i class="fas fa-user-lock me-1"></i> Access Restricted
                        </div>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-black/10 flex items-center justify-center text-slate-900">
                        <i class="fas fa-user-slash text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Organizational Structure --}}
    <div class="flex items-center space-x-2 mb-4">
        <div class="h-1 w-8 bg-blue-600 rounded-full"></div>
        <h6 class="font-black text-slate-900 uppercase tracking-widest text-[11px] mb-0">Organizational Structure</h6>
    </div>

    <div class="row row-cols-2 row-cols-md-5 g-3 mb-8">
        @php
            $orgUnits = [
                ['Faculties', $totalFaculties ?? 0, 'bg-blue-50', 'text-blue-600'],
                ['Departments', $totalDepartments ?? 0, 'bg-emerald-50', 'text-emerald-600'],
                ['Institutes', $totalInstitutes ?? 0, 'bg-amber-50', 'text-amber-600'],
                ['Offices', $totalOffices ?? 0, 'bg-cyan-50', 'text-cyan-600'],
                ['Admin Units', $totalUnits ?? 0, 'bg-slate-50', 'text-slate-600'],
            ];
        @endphp
        
        @foreach($orgUnits as [$label, $count, $bg, $text])
        <div class="col">
            <div class="bg-white border border-slate-200 rounded-xl p-4 text-center transition-all hover:border-slate-400 hover:-translate-y-1 shadow-sm">
                <div class="text-2xl font-black {{ $text }} mb-1">{{ $count }}</div>
                <div class="text-[9px] font-black text-slate-400 uppercase tracking-wider">{{ $label }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- 5. Charts & Activity Grid --}}
    <div class="row g-4">
        {{-- Verification Status Chart --}}
        <div class="col-xl-7">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-6 bg-blue-600 rounded-full"></div>
                        <h5 class="font-black text-slate-900 m-0">Submission Verification Status</h5>
                    </div>
                </div>
                <div class="p-5">
                    <canvas id="submissionsChart" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="col-xl-12"> {{-- Increased to full width for table visibility --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-6 bg-indigo-600 rounded-full"></div>
                        <h5 class="font-black text-slate-900 m-0 text-lg">System-Wide Recent Activity</h5>
                    </div>
                    <a href="{{ route('admin.submissions.index') }}" class="btn btn-sm bg-white border-slate-200 text-slate-600 font-bold px-4 py-2 rounded-xl hover:bg-slate-50 transition-all text-xs uppercase tracking-wider">
                        View Full Logs <i class="fas fa-list ms-2"></i>
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="table table-hover align-middle mb-0 d-none d-md-table">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="ps-5 py-3 text-[10px] font-black text-slate-700 uppercase tracking-widest border-0">ID / Item</th>
                                <th class="py-3 text-[10px] font-black text-slate-700 uppercase tracking-widest border-0">Submitter</th>
                                <th class="py-3 text-[10px] font-black text-slate-700 uppercase tracking-widest border-0">Origin</th>
                                <th class="py-3 text-[10px] font-black text-slate-700 uppercase tracking-widest border-0">Status</th>
                                <th class="py-3 text-[10px] font-black text-slate-700 uppercase tracking-widest border-0">Audit Details</th>
                                <th class="pe-5 text-end py-3 text-[10px] font-black text-slate-700 uppercase tracking-widest border-0">Action</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($recentSubmissions as $r)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="ps-5 py-4">
                                    <div class="font-black text-indigo-600 text-sm">#{{ str_pad($r->submission_id, 4, '0', STR_PAD_LEFT) }}</div>
                                    <div class="text-xs text-slate-900 font-bold truncate max-w-[200px]">
                                        {{ $r->items->first()->item_name ?? 'Batch Submission' }}
                                        @if($r->items->count() > 1)
                                            <span class="text-slate-600 font-normal text-[10px] italic">+{{ $r->items->count() - 1 }} others</span>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <div class="text-[12px] text-slate-700">{{ $r->submittedBy->profile->full_name ?? $r->submittedBy->username ?? 'Unknown User' }}</div>
                                </td>

                                <td>
                                    <div class="text-[10px] font-black text-slate-500 uppercase tracking-tighter">
                                        {{ 
                                            $r->submittedBy->unit->unit_name ?? 
                                            $r->submittedBy->department->dept_name ?? 
                                            $r->submittedBy->institute->institute_name ?? 
                                            $r->submittedBy->faculty->faculty_name ?? 
                                            $r->submittedBy->office->office_name ?? 
                                            'COMUI CENTRAL' 
                                        }}
                                    </div>
                                    {{-- Optional: Add a small sub-label if it's a sub-entity --}}
                                    @if($r->submittedBy->unit || $r->submittedBy->department)
                                        <div class="text-[9px] text-slate-600 font-bold italic">
                                            via {{ $r->submittedBy->faculty->faculty_name ?? $r->submittedBy->office->office_name ?? 'Central' }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $statusConfig = [
                                            'pending' => ['bg-amber-100/50', 'text-amber-700', 'border-amber-200'],
                                            'approved' => ['bg-emerald-100/50', 'text-emerald-700', 'border-emerald-200'],
                                            'rejected' => ['bg-rose-100/50', 'text-rose-700', 'border-rose-200'],
                                        ];
                                        $classes = $statusConfig[$r->status] ?? $statusConfig['pending'];
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase border {{ implode(' ', $classes) }}">
                                        {{ $r->status }}
                                    </span>
                                </td>

                                <td>
                                    @if($r->status !== 'pending')
                                        <div class="text-[10px] font-bold text-slate-700">By {{ $r->reviewedby->profile->full_name ?? $r->reviewedby->username ?? 'System' }}</div>
                                        <div class="text-[9px] text-slate-600 uppercase">{{ $r->updated_at->format('M d, Y • h:i A') }}</div>
                                    @else
                                        <span class="text-[10px] text-slate-600 italic">Waiting for Audit...</span>
                                    @endif
                                </td>

                                <td class="pe-5 text-end">
                                    <a href="{{ route('admin.submissions.show', $r->submission_id) }}" class="btn btn-sm border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-600 bg-white rounded-lg transition-all shadow-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>

                    <div class="d-md-none">
                        @foreach($recentSubmissions as $r)
                        <div class="p-4 border-b border-slate-100">
                            <div class="flex justify-between mb-2">
                                <span class="font-black text-indigo-600">#{{ str_pad($r->submission_id, 4, '0', STR_PAD_LEFT) }}</span>
                                <span class="text-[10px] text-slate-400 uppercase font-bold">{{ $r->submitted_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-sm font-bold text-slate-900 mb-1">{{ $r->items->first()->item_name ?? 'Inventory Batch' }}</div>
                            <div class="text-xs text-slate-500 mb-3">Submitted by {{ $r->submittedBy->profile->full_name ?? 'Staff' }}</div>
                            <a href="{{ route('admin.submissions.show', $r->submission_id) }}" class="btn btn-sm w-full bg-slate-100 text-slate-600 font-bold">View Details</a>
                        </div>
                        @endforeach
                    </div>

                    @if($recentSubmissions->isEmpty())
                        <div class="py-20 text-center">
                            <div class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-folder-open text-slate-300 text-2xl"></i>
                            </div>
                            <p class="text-slate-400 font-bold uppercase text-xs tracking-widest">No activity found in logs</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 6. Quick Actions (3 cards only) --}}
    <div class="row g-4 mt-6">
        @php
            $quickActions = [
                ['Manage Users', 'admin.users.index', 'fa-users-cog', 'bg-blue-600', 'Control user access and permissions'],
                ['Review Requests', 'admin.submissions.pending', 'fa-tasks', 'bg-rose-600', 'Process pending submissions'],
                ['Generate Reports', 'admin.reports.index', 'fa-chart-bar', 'bg-emerald-600', 'Export inventory reports'],
            ];
        @endphp
        
        @foreach($quickActions as [$title, $route, $icon, $bg, $desc])
        <div class="col-md-6 col-xl-4">
            <a href="{{ route($route) }}" class="block bg-white border border-slate-200 rounded-xl p-4 hover:border-slate-400 transition-all hover:-translate-y-1 shadow-sm no-underline group">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 {{ $bg }} rounded-lg flex items-center justify-center text-white shrink-0 group-hover:scale-110 transition-transform">
                        <i class="fas {{ $icon }}"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="font-black text-slate-900 text-sm mb-1">{{ $title }}</div>
                        <div class="text-[10px] text-slate-500 font-medium">{{ $desc }}</div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>

<script>
    // Chart data from PHP
    window.statusCounts = {!! json_encode([
        (int) ($pendingSubmissions ?? 0), 
        (int) ($approvedSubmissions ?? 0), 
        (int) ($rejectedSubmissions ?? 0)
    ]) !!};
</script>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('build/assets/js/dashboard_admin.js') }}"></script> 
@endpush