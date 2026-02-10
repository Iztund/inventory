@extends('layouts.staff')

@section('title', 'Staff Dashboard')

@section('content')
<div class="container-fluid px-4 py-4">
    
    {{-- 1. Welcome Hero Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 to-slate-800 p-8 md:p-10 mb-8 border-b-4 border-emerald-500 shadow-xl">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white/5 rounded-full"></div>
        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-32 h-32 bg-emerald-500/10 rounded-full"></div>
        
        <div class="row align-items-center relative z-10">
            <div class="col-lg-8">
                <h6 class="text-emerald-400 font-black uppercase tracking-[0.2em] text-xs mb-3">Staff Portal</h6>
                <h1 class="text-3xl md:text-4xl font-black text-white mb-2">
                    Welcome back, {{ Auth::user()->profile->first_name ?? Auth::user()->username }}
                </h1>
                <p class="text-slate-400 font-medium mb-0 flex items-center">
                    <i class="fas fa-map-marker-alt text-emerald-500 me-2"></i>
                    Current Assignment: 
                    <span class="text-white fw-bold ms-1">
                        @if(Auth::user()->unit_id) 
                            {{ Auth::user()->unit->unit_name }}
                        @elseif(Auth::user()->department_id) 
                            {{ Auth::user()->department->dept_name }}
                        @elseif(Auth::user()->institute_id) 
                            {{ Auth::user()->institute->institute_name }}
                        @elseif(Auth::user()->office_id) 
                            {{ Auth::user()->office->office_name }}
                        @elseif(Auth::user()->faculty_id) 
                            {{ Auth::user()->faculty->faculty_name }}
                        @else 
                            College of Medicine
                        @endif
                    </span>
                </p>
            </div>
            <div class="col-lg-4 text-lg-end d-none d-lg-block">
                <div class="inline-block bg-white/10 backdrop-blur-md border border-white/20 p-4 rounded-2xl min-w-[200px]">
                    <div class="text-[12px] text-emerald-400 font-black uppercase tracking-widest mb-1">
                        Pending Review
                    </div>
                    <div class="text-3xl font-black text-white mb-0">
                        {{ number_format($stats['pending'] ?? 0) }} 
                        <span class="text-sm text-slate-300 font-bold uppercase tracking-tighter">Items</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Core Metrics Grid (4 cards) --}}
    <div class="row g-4 mb-8">
        @php
            $coreMetrics = [
                ['My Submissions', $stats['total'] ?? 0, 'fa-clipboard-list', 'text-emerald-600', 'bg-emerald-50', 'Total Submitted'],
                ['Pending Audit', $stats['pending'] ?? 0, 'fa-clock', 'text-amber-500', 'bg-amber-50', 'Under Review'],
                ['Verified Assets', $stats['approved'] ?? 0, 'fa-check-circle', 'text-emerald-500', 'bg-emerald-50', 'Approved'],
                ['Unit Assets', $totalUnitAssets ?? 0, 'fa-boxes-stacked', 'text-teal-600', 'bg-teal-50', 'Registry Total'],
            ];
        @endphp
        
        @foreach($coreMetrics as [$label, $val, $icon, $textColor, $bgColor, $subtitle])
        <div class="col-6 col-lg-3">
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

    {{-- 3. Submission Status Overview --}}
    <div class="flex items-center space-x-2 mb-4">
        <div class="h-1 w-8 bg-emerald-600 rounded-full"></div>
        <h6 class="font-black text-slate-900 uppercase tracking-widest text-[11px] mb-0">Submission Status Overview</h6>
    </div>

    <div class="row g-4 mb-8">
        <div class="col-md-4">
            <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-2xl p-6 shadow-lg">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-[10px] text-emerald-100 font-black uppercase tracking-widest mb-2">Approved Items</div>
                        <div class="text-4xl font-black text-white">{{ $stats['approved'] ?? 0 }}</div>
                        <div class="text-emerald-200 text-xs font-bold mt-2">
                            <i class="fas fa-check-double me-1"></i> Successfully Verified
                        </div>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center text-white">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-6 shadow-lg">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-[10px] text-amber-100 font-black uppercase tracking-widest mb-2">Under Review</div>
                        <div class="text-4xl font-black text-white">{{ $stats['pending'] ?? 0 }}</div>
                        <div class="text-amber-100 text-xs font-bold mt-2">
                            <i class="fas fa-hourglass-half me-1"></i> Awaiting Audit
                        </div>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center text-white">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-2xl p-6 shadow-lg border border-slate-600">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-[10px] text-slate-300 font-black uppercase tracking-widest mb-2">Total Value</div>
                        <div class="text-4xl font-black text-white">
                            @php
                                $totalValue = $recentSubmissions->sum(function($sub) {
                                    return $sub->items->sum(fn($i) => ($i->cost ?? 0) * ($i->quantity ?? 0));
                                });
                            @endphp
                            ₦{{ number_format($totalValue, 0) }}
                        </div>
                        <div class="text-slate-400 text-xs font-bold mt-2">
                            <i class="fas fa-sack-dollar me-1"></i> Asset Valuation
                        </div>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-white/10 flex items-center justify-center text-emerald-400">
                        <i class="fas fa-coins text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Activity Log Table --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="w-2 h-6 bg-emerald-600 rounded-full"></div>
                <h5 class="font-black text-slate-900 m-0 text-lg">Recent Submission Activity</h5>
            </div>
            <a href="{{ route('staff.submissions.index') }}" 
               class="btn btn-sm bg-white border-slate-200 text-slate-600 font-bold px-4 py-2 rounded-xl hover:bg-slate-50 transition-all text-xs uppercase tracking-wider">
                View All Submissions <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="table table-hover align-middle mb-0 d-none d-md-table">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="ps-5 py-3 text-[10px] font-black text-slate-700 uppercase tracking-widest border-0">Reference</th>
                        <th class="py-3 text-[10px] font-black text-slate-700 uppercase tracking-widest border-0">Asset Details</th>
                        <th class="py-3 text-[10px] font-black text-slate-700 uppercase tracking-widest border-0">Inventory Tag</th>
                        <th class="py-3 text-[10px] font-black text-slate-700 uppercase tracking-widest border-0">Status</th>
                        <th class="py-3 text-[10px] font-black text-slate-700 uppercase tracking-widest border-0">Submitted</th>
                        <th class="pe-5 text-end py-3 text-[10px] font-black text-slate-700 uppercase tracking-widest border-0">Action</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($recentSubmissions as $index => $sub)
                    @php
                        $u = Auth::user();
                        $firstItem = $sub->items->first();
                        
                        $prefix = 'COM';
                        if($u->unit_id) $prefix = $u->unit->unit_code ?? 'UNIT';
                        elseif($u->department_id) $prefix = $u->department->dept_code ?? 'DEPT';
                        elseif($u->institute_id) $prefix = $u->institute->institute_code ?? 'INST';
                        elseif($u->office_id) $prefix = $u->office->office_code ?? 'OFF';
                        elseif($u->faculty_id) $prefix = $u->faculty->faculty_code ?? 'FAC';
                        
                        $statusConfig = [
                            'pending' => ['bg' => 'bg-amber-100/50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => 'fa-clock'],
                            'approved' => ['bg' => 'bg-emerald-100/50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => 'fa-check-circle'],
                            'rejected' => ['bg' => 'bg-rose-100/50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'icon' => 'fa-times-circle'],
                        ];
                        $status = $statusConfig[$sub->status] ?? $statusConfig['pending'];
                    @endphp

                    <tr class="hover:bg-slate-50/80 transition-colors border-bottom border-slate-100"
                        style="animation:fadeInUp 0.3s ease-out both; animation-delay:{{ $index * 0.05 }}s;">
                        <td class="ps-5 py-4">
                            <div class="font-black text-emerald-600 text-sm">#AUD{{ str_pad($sub->submission_id, 4, '0', STR_PAD_LEFT) }}</div>
                            <div class="text-[10px] text-slate-500 font-bold uppercase">REF-{{ date('Y') }}</div>
                        </td>

                        <td class="py-4">
                            <div class="text-sm text-slate-900 font-bold truncate" style="max-width:250px;">
                                {{ $firstItem->item_name ?? 'Batch Submission' }}
                            </div>
                            <div class="text-[10px] text-slate-600 font-semibold">
                                Value: ₦{{ number_format($sub->items->sum(fn($i) => ($i->cost ?? 0) * ($i->quantity ?? 0)), 2) }}
                                @if($sub->items->count() > 1)
                                    <span class="text-slate-400 italic"> • {{ $sub->items->count() }} items</span>
                                @endif
                            </div>
                        </td>

                        <td class="py-4">
                            @if($sub->status == 'approved' && $firstItem)
                                <code class="px-2.5 py-1.5 rounded-lg text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $prefix }}/{{ date('y') }}/{{ str_pad($firstItem->id, 4, '0', STR_PAD_LEFT) }}
                                </code>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-[9px] font-bold bg-slate-100 text-slate-500 border border-slate-200 italic">
                                    <i class="fas fa-hourglass-start me-1"></i> Pending Tag
                                </span>
                            @endif
                        </td>

                        <td class="py-4">
                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase border {{ $status['bg'] }} {{ $status['text'] }} {{ $status['border'] }}">
                                <i class="fas {{ $status['icon'] }} me-1"></i>
                                {{ $sub->status }}
                            </span>
                        </td>

                        <td class="py-4">
                            <div class="text-[11px] text-slate-700 font-bold">
                                {{ $sub->submitted_at ? $sub->submitted_at->format('M d, Y') : $sub->created_at->format('M d, Y') }}
                            </div>
                            <div class="text-[9px] text-slate-500">
                                {{ $sub->submitted_at ? $sub->submitted_at->format('h:i A') : $sub->created_at->format('h:i A') }}
                            </div>
                        </td>

                        <td class="pe-5 text-end py-4">
                            <a href="{{ route('staff.submissions.show', $sub->submission_id) }}" 
                               class="btn btn-sm border-slate-200 text-slate-400 hover:text-emerald-600 hover:border-emerald-600 bg-white rounded-lg transition-all shadow-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="6" class="py-20 text-center">
                            <div class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-inbox text-slate-300 text-2xl"></i>
                            </div>
                            <p class="text-slate-400 font-bold uppercase text-xs tracking-widest mb-3">No submissions yet</p>
                            <a href="{{ route('staff.submissions.create') }}" 
                               class="btn btn-sm btn-emerald-600 text-white fw-bold rounded-lg px-4">
                                <i class="fas fa-plus me-2"></i> Create First Submission
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Mobile Cards --}}
            <div class="d-md-none">
                @forelse($recentSubmissions as $sub)
                @php
                    $firstItem = $sub->items->first();
                    $statusConfig = [
                        'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700'],
                        'approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700'],
                        'rejected' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700'],
                    ];
                    $status = $statusConfig[$sub->status] ?? $statusConfig['pending'];
                @endphp

                <div class="p-4 border-b border-slate-100">
                    <div class="flex justify-between mb-2">
                        <span class="font-black text-emerald-600 text-sm">#AUD{{ str_pad($sub->submission_id, 4, '0', STR_PAD_LEFT) }}</span>
                        <span class="px-2 py-1 rounded text-[9px] font-bold {{ $status['bg'] }} {{ $status['text'] }}">
                            {{ ucfirst($sub->status) }}
                        </span>
                    </div>
                    <div class="text-sm font-bold text-slate-900 mb-1">{{ $firstItem->item_name ?? 'Batch Submission' }}</div>
                    <div class="text-xs text-slate-500 mb-2">
                        {{ $sub->submitted_at ? $sub->submitted_at->format('M d, Y • h:i A') : $sub->created_at->format('M d, Y • h:i A') }}
                    </div>
                    <div class="text-xs text-slate-600 font-semibold mb-3">
                        Value: ₦{{ number_format($sub->items->sum(fn($i) => ($i->cost ?? 0) * ($i->quantity ?? 0)), 2) }}
                    </div>
                    <a href="{{ route('staff.submissions.show', $sub->submission_id) }}" 
                       class="btn btn-sm w-full bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-emerald-50 hover:text-emerald-700">
                        View Details
                    </a>
                </div>

                @empty
                <div class="py-12 text-center">
                    <i class="fas fa-inbox text-slate-300 text-3xl mb-3"></i>
                    <p class="text-slate-400 font-bold text-sm">No submissions found</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 5. Quick Actions --}}
    <div class="row g-4 mt-6">
        @php
            $quickActions = [
                ['New Submission', 'staff.submissions.create', 'fa-plus-circle', 'bg-emerald-600', 'Submit inventory items for audit'],
                ['View All Assets', 'staff.assets.index', 'fa-boxes-stacked', 'bg-teal-600', 'Browse unit asset registry'],
                ['Guidelines', 'staff.guidelines.index', 'fa-book-open', 'bg-slate-700', 'Read submission guidelines'],
            ];
        @endphp
        
        @foreach($quickActions as [$title, $route, $icon, $bg, $desc])
        <div class="col-md-6 col-xl-4">
            <a href="{{ route($route) }}" 
               class="block bg-white border border-slate-200 rounded-xl p-4 hover:border-slate-400 transition-all hover:-translate-y-1 shadow-sm no-underline group">
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

<style>
@keyframes fadeInUp {
    from { opacity:0; transform:translateY(10px); }
    to { opacity:1; transform:translateY(0); }
}
</style>

@endsection