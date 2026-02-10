@extends('layouts.auditor')

@section('title', 'Auditor Dashboard')

@section('content')
<div class="container-fluid px-3 sm:px-4 lg:px-6 py-4 lg:py-6 max-w-7xl mx-auto">

    <div class="relative overflow-hidden rounded-2xl lg:rounded-3xl bg-audit-navy p-6 lg:p-10 mb-6 lg:mb-8 border-b-4 border-amber-500 shadow-2xl">
        <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h6 class="text-amber-400 font-black uppercase tracking-widest text-xs lg:text-sm mb-2">
                    Internal Audit Terminal
                </h6>
                <h1 class="text-2xl lg:text-4xl font-black text-white mb-2">
                    Welcome back, {{ Auth::user()->profile->first_name ?? Auth::user()->username }}
                </h1>
                <p class="text-slate-300 text-sm lg:text-base flex items-center">
                    <i class="fas fa-shield-check text-emerald-400 me-2"></i>
                    COMIS • Real-time Compliance Monitoring
                </p>
            </div>

            <div class="bg-white/10 backdrop-blur-lg border border-white/20 px-6 py-4 rounded-2xl animate-pulse text-center lg:text-right min-w-[180px]">
                <div class="text-xs text-amber-300 font-black uppercase tracking-widest mb-1">
                    Action Required
                </div>
                <div class="text-3xl lg:text-4xl font-black text-white">
                    {{ number_format($stats['pending'] ?? 0) }}
                    <span class="text-sm lg:text-lg font-bold text-slate-300">Pending</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
        @foreach([
            ['Total Assets', $stats['total'] ?? 0, 'fa-database', 'bg-indigo-50 text-audit-indigo'],
            ['Pending Review', $stats['pending'] ?? 0, 'fa-hourglass-half', 'bg-amber-50 text-amber-600'],
            ['Approved', $stats['approved'] ?? 0, 'fa-check-double', 'bg-emerald-50 text-emerald-600'],
            ['Rejected', $stats['rejected'] ?? 0, 'fa-ban', 'bg-rose-50 text-rose-600']
        ] as [$title, $val, $icon, $class])
        <div class="bg-white rounded-xl p-4 lg:p-5 shadow-sm border border-slate-200 hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-3">
                <span class="text-xs lg:text-sm font-bold text-slate-700 uppercase tracking-wide">{{ $title }}</span>
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg {{ $class }} flex items-center justify-center">
                    <i class="fas {{ $icon }} text-lg lg:text-xl"></i>
                </div>
            </div>
            <div class="text-2xl lg:text-3xl font-black text-audit-navy">{{ number_format($val) }}</div>
        </div>
        @endforeach
    </div>

    <div class="mb-6 lg:mb-8">
        <div class="flex items-center mb-4">
            <div class="h-1 w-8 lg:w-10 bg-audit-indigo rounded-full mr-3"></div>
            <h6 class="font-black text-audit-navy uppercase tracking-widest text-sm lg:text-base">By Organizational Unit</h6>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6">
            @foreach([
                ['Academic Units', 'faculties', 'fa-graduation-cap', 'bg-indigo-50', 'text-indigo-600'],
                ['Admin & Support', 'offices', 'fa-building', 'bg-emerald-50', 'text-emerald-600'],
                ['Research Institutes', 'institutes', 'fa-microscope', 'bg-amber-50', 'text-amber-600']
            ] as [$title, $key, $icon, $bg, $color])
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <div class="flex items-center mb-4 pb-3 border-b">
                    <div class="w-12 h-12 {{ $bg }} {{ $color }} rounded-xl flex items-center justify-center mr-3">
                        <i class="fas {{ $icon }} text-xl"></i>
                    </div>
                    <h6 class="font-black text-audit-navy text-base">{{ $title }}</h6>
                </div>
                <div class="grid grid-cols-2 gap-3 text-center">
                    @foreach(['pending' => ['text-amber-600', 'Pending'], 'approved' => ['text-emerald-600', 'Approved'], 'rejected' => ['text-rose-600', 'Rejected'], 'total' => ['text-audit-navy', 'Total']] as $k => $v)
                    <div class="p-3 bg-slate-50 rounded-lg">
                        <div class="text-xs text-slate-600">{{ $v[1] }}</div>
                        <div class="{{ $v[0] }} font-black text-xl">{{ $entityBreakdown[$key][$k] ?? 0 }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 lg:p-5 border-b bg-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h5 class="font-black text-audit-navy text-lg lg:text-xl tracking-tight uppercase">Priority Review Queue</h5>
            <a href="{{ route('auditor.registry.index') }}" class="btn btn-outline-primary btn-sm rounded-full px-5 py-2 text-xs lg:text-sm font-bold uppercase w-full sm:w-auto text-center">
                Central Registry →
            </a>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-md overflow-hidden">
            {{-- Desktop Table View (Visible on md and up) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-900 text-white">
                        <tr>
                            <th class="ps-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Ref ID</th>
                            <th class="py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Inventory Details</th>
                            <th class="py-4 text-[10px] font-black uppercase tracking-widest text-center text-slate-400">Status / Metrics</th>
                            <th class="py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Batch Value</th>
                            <th class="py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Audit Trail</th>
                            <th class="py-4 text-[10px] font-black uppercase tracking-widest text-right pe-6 text-slate-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        
                        @forelse($recentSubmissions as $sub)
                            @php
                                $items = $sub->items;
                                $itemsCount = $items->count();
                                $approved = $items->where('status', 'approved')->count();
                                $rejected = $items->where('status', 'rejected')->count();
                                $pending = $items->where('status', 'pending')->count();
                                $isPendingBatch = $sub->status === 'pending';
                                $totalVal = $items->sum(fn($i) => ($i->unit_cost ?? $i->cost ?? 0) * $i->quantity);
                                $user = $sub->submittedBy;
                                $daysOld = (int) $sub->created_at->diffInDays(now());
                                $priorityClass = $daysOld >= 3 ? 'bg-rose-500' : ($daysOld >= 1 ? 'bg-amber-400' : 'bg-blue-500');
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="ps-6 py-5">
                                    <div class="font-black text-slate-900 text-sm">#{{ str_pad($sub->submission_id, 5, '0', STR_PAD_LEFT) }}</div>
                                    <div class="text-[9px] text-slate-700 font-bold uppercase tracking-tighter">{{ $sub->created_at->format('d M Y') }}</div>
                                </td>
                                <td class="py-5">
                                    <div class="text-xs font-black text-indigo-600 uppercase tracking-tight truncate max-w-[200px]" title="{{ $items->pluck('item_name')->implode(', ') }}">
                                        {{ $items->pluck('item_name')->implode(', ') }}
                                    </div>
                                    <div class="text-[10px] font-bold text-slate-700 mt-0.5">
                                        <i class="fas fa-user text-[8px] me-1 text-slate-400"></i>
                                        {{ $user->name ?? $user->username ?? 'Staff' }}
                                    </div>
                                    @if($isPendingBatch)
                                        <span class="inline-block mt-2 px-2 py-0.5 rounded text-[8px] font-black uppercase text-white {{ $priorityClass }}">
                                            {{ $daysOld >= 15 ? 'Critical' : ($daysOld >= 5 ? 'High' : 'Normal') }} ({{ $daysOld }}days)
                                        </span>
                                    @endif
                                </td>
                                <td class="py-5 text-center">
                                    @if($isPendingBatch)
                                        <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-600 border border-amber-100 px-3 py-1 rounded-full text-[10px] font-black uppercase">
                                            {{ $pending }} Items to Review
                                        </span>
                                    @else
                                        <div class="inline-flex items-center bg-emerald-50 rounded-lg p-1 border border-emerald-100 gap-2 text-[10px] font-black">
                                            <span class="text-emerald-600 px-1.5">{{ $approved }} ✓</span>
                                            <div class="w-px h-3 bg-emerald-200"></div>
                                            <span class="text-rose-600 px-1.5">{{ $rejected }} ✗</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-5">
                                    <div class="text-sm font-black text-slate-900">₦{{ number_format($totalVal, 2) }}</div>
                                    <div class="text-[9px] text-slate-600 font-bold uppercase tracking-widest">{{ $itemsCount }} Items</div>
                                </td>
                                <td class="py-5">
                                    @if(!$isPendingBatch)
                                        <div class="text-[11px] font-black text-slate-800 uppercase">{{ $sub->auditor_display_name }}</div>
                                        <div class="text-[10px] text-slate-600 font-bold mt-1 uppercase">{{ $sub->updated_at->format('d/m/y H:i') }}</div>
                                    @else
                                        <span class="text-[9px] font-black text-slate-600 uppercase italic">Pending Audit</span>
                                    @endif
                                </td>
                                <td class="pe-6 py-5 text-right">
                                    <a href="{{ route($sub->routeName, $sub->routeParam) }}" class="inline-flex items-center {{ $isPendingBatch ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600' }} px-4 py-2 rounded-lg font-black uppercase text-[10px] tracking-widest">
                                        {{ $isPendingBatch ? 'Audit' : 'View' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-10">No data found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

           {{-- Mobile Card View (Separated by Spacing & Status Accents) --}}
            <div class="md:hidden bg-slate-50 p-3 space-y-4"> {{-- Container: light gray bg + vertical spacing --}}
                @foreach($recentSubmissions as $sub)
                    @php
                        $items = $sub->items;
                        $isPendingBatch = $sub->status === 'pending';
                        $daysOld = (int) $sub->created_at->diffInDays(now());
                        
                        // Logic for the color-coded side bar
                        $accentColor = $isPendingBatch 
                            ? ($daysOld >= 3 ? 'border-l-rose-500' : 'border-l-amber-500') 
                            : 'border-l-emerald-500';
                        
                        $priorityText = $daysOld >= 3 ? 'Critical' : ($daysOld >= 1 ? 'High' : 'Normal');
                    @endphp

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 border-l-4 {{ $accentColor }} overflow-hidden">
                        {{-- Header: Ref ID & Price --}}
                        <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center">
                            <div>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Case ID</span>
                                <div class="font-black text-slate-900 text-sm">#{{ str_pad($sub->submission_id, 5, '0', STR_PAD_LEFT) }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs font-black text-indigo-600">₦{{ number_format($items->sum(fn($i) => ($i->unit_cost ?? $i->cost ?? 0) * $i->quantity), 2) }}</div>
                                <div class="text-[8px] font-bold text-slate-400 uppercase">Est. Value</div>
                            </div>
                        </div>

                        {{-- Body: Inventory Items --}}
                        <div class="p-4">
                            <div class="mb-3">
                                <h4 class="text-[11px] font-black text-slate-700 uppercase leading-tight">
                                    {{ Str::limit($items->pluck('item_name')->implode(', '), 70) }}
                                </h4>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-slate-50 p-2 rounded-lg border border-slate-100">
                                    <div class="text-[8px] font-black text-slate-400 uppercase">Originator</div>
                                    <div class="text-[10px] font-bold text-slate-800 truncate">
                                        {{ $sub->submittedBy->full_name ?? $sub->submittedBy->username }}
                                    </div>
                                </div>
                                <div class="bg-slate-50 p-2 rounded-lg border border-slate-100">
                                    <div class="text-[8px] font-black text-slate-400 uppercase">Status</div>
                                    @if($isPendingBatch)
                                        <div class="text-[10px] font-black {{ $daysOld >= 3 ? 'text-rose-600' : 'text-amber-600' }} uppercase">
                                            {{ $priorityText }} ({{ $daysOld }}d)
                                        </div>
                                    @else
                                        <div class="text-[10px] font-black text-emerald-600 uppercase truncate">
                                            {{ $sub->auditor_display_name }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Footer: Timestamp & Action --}}
                        <div class="px-4 py-3 bg-slate-50/50 flex items-center justify-between">
                            <div class="text-[9px] text-slate-500 font-bold uppercase">
                                <i class="far fa-calendar-alt me-1"></i> {{ $sub->created_at->format('d M Y') }}
                            </div>
                            <a href="{{ route($sub->routeName, $sub->routeParam) }}" 
                            class="inline-flex items-center {{ $isPendingBatch ? 'bg-indigo-600 text-white' : 'bg-white border border-slate-300 text-slate-600' }} px-5 py-2 rounded-lg font-black uppercase text-[10px] tracking-widest shadow-sm active:scale-95 transition-all">
                                {{ $isPendingBatch ? 'Audit Batch' : 'View Log' }}
                            </a>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>
@endsection