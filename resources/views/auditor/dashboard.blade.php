@extends('layouts.auditor')

@section('title', 'Auditor Dashboard')

@section('content')
<div class="container-fluid px-4 py-4">
    
    {{-- 1. Welcome Section --}}
    <div class="relative overflow-hidden rounded-3xl bg-audit-navy p-8 md:p-10 mb-8 border-b-4 border-amber-500 shadow-xl">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white/5 rounded-full"></div>
        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-32 h-32 bg-indigo-500/10 rounded-full"></div>
        
        <div class="row align-items-center relative z-10">
            <div class="col-lg-8">
                <h6 class="text-amber-500 font-black uppercase tracking-[0.2em] text-xs mb-3">Internal Audit Dashboard</h6>
                <h1 class="text-3xl md:text-4xl font-black text-white mb-2">
                    Welcome back, {{ Auth::user()->profile->first_name ?? Auth::user()->username }}
                </h1>
                <p class="text-slate-400 font-medium mb-0 flex items-center">
                    <i class="fas fa-circle-check text-emerald-500 me-2"></i>
                    College of Medicine Inventory System • Compliance Monitoring
                </p>
            </div>
            <div class="col-lg-4 text-lg-end d-none d-lg-block">
                <div class="inline-block bg-white/10 backdrop-blur-md border border-white/20 p-4 rounded-2xl animate-pulse min-w-[200px]">
                    <div class="text-[12px] text-amber-400 font-black uppercase tracking-widest mb-1">
                        Action Required
                    </div>
                    <div class="text-3xl font-black text-white mb-0">
                        {{ number_format($stats['pending'] ?? 0) }} 
                        <span class="text-sm text-slate-300 font-bold uppercase tracking-tighter">Items</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Stats Grid --}}
    <div class="row g-4 mb-8">
        @foreach([
            ['Total Registry', $stats['total'], 'fa-database', 'text-audit-indigo', 'bg-indigo-50'],
            ['Awaiting Review', $stats['pending'], 'fa-hourglass-half', 'text-amber-500', 'bg-amber-50'],
            ['Approved Items', $stats['approved'], 'fa-check-double', 'text-emerald-500', 'bg-emerald-50'],
            ['Rejected', $stats['rejected'], 'fa-ban', 'text-rose-500', 'bg-rose-50']
        ] as [$label, $val, $icon, $textColor, $bgColor])
        <div class="col-6 col-md-3">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm transition-transform hover:-translate-y-1">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">{{ $label }}</span>
                    <div class="w-8 h-8 rounded-lg {{ $bgColor }} flex items-center justify-center">
                        <i class="fas {{ $icon }} {{ $textColor }} text-xs"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-audit-navy">{{ number_format($val ?? 0) }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- 3. Organizational Unit Breakdown (Kept as requested) --}}
    <div class="flex items-center space-x-2 mb-4">
        <div class="h-1 w-8 bg-audit-indigo rounded-full"></div>
        <h6 class="font-black text-audit-navy uppercase tracking-widest text-[11px] mb-0">By Organizational Unit</h6>
    </div>

    <div class="row g-4 mb-8">
        @php
            $entityConfigs = [
                ['Academic Units', 'faculties', 'fa-graduation-cap', 'bg-indigo-50', 'text-indigo-600', 'Faculties & Depts'],
                ['Administrative', 'offices', 'fa-building', 'bg-emerald-50', 'text-emerald-600', 'Offices & Units'],
                ['Research', 'institutes', 'fa-microscope', 'bg-amber-50', 'text-amber-600', 'Institutes']
            ];
        @endphp

        @foreach($entityConfigs as [$title, $key, $icon, $bg, $text, $sub])
        <div class="col-lg-4 col-md-6">
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center space-x-4 mb-4 pb-4 border-b border-slate-100">
                    <div class="w-12 h-12 {{ $bg }} {{ $text }} rounded-xl flex items-center justify-center text-lg shadow-sm">
                        <i class="fas {{ $icon }}"></i>
                    </div>
                    <div>
                        <div class="font-black text-audit-navy leading-none mb-1">{{ $title }}</div>
                        <div class="text-slate-400 text-[10px] uppercase font-bold tracking-tight">{{ $sub }}</div>
                    </div>
                </div>
                
                <div class="space-y-3">
                    @foreach(['pending' => ['text-amber-600', 'Pending'], 'rejected' => ['text-rose-600', 'Rejected'], 'approved' => ['text-emerald-600', 'Approved'], 'total' => ['text-audit-navy', 'Total Items']] as $statKey => $meta)
                        <div class="flex justify-between items-center py-1 border-b border-dashed border-slate-100 last:border-0">
                            <span class="text-slate-500 text-xs font-medium">{{ $meta[1] }}</span>
                            <span class="{{ $meta[0] }} font-black text-sm">{{ $entityBreakdown[$key][$statKey] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- 4. Queue Table (Updated with Item Breakdown) --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-white flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-2 h-6 bg-audit-indigo rounded-full"></div>
                <h5 class="font-black text-audit-navy m-0">Priority Review Queue</h5>
            </div>
            <a href="{{ route('auditor.registry.index') }}" class="btn btn-outline-primary btn-sm rounded-lg px-4 font-bold text-[11px] uppercase tracking-wider">
                Explore Central Registry
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table align-middle m-0 border-0">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="ps-5 text-[10px] font-black text-slate-500 uppercase">Reference</th>
                        <th class="ps-5 text-[10px] font-black text-slate-500 uppercase">Origin Entity</th>
                        <th class="text-[10px] font-black text-slate-500 uppercase">Item Name</th>
                        <th class="text-[10px] font-black text-slate-500 uppercase">Item Breakdown</th>
                        <th class="text-[10px] font-black text-slate-500 uppercase">Total Value</th>
                        <th class="text-[10px] font-black text-slate-500 uppercase text-center">Batch Status</th>
                        <th class="text-[10px] font-black text-slate-500 uppercase">Audit Priority</th>
                        <th class="pe-5 text-end text-[10px] font-black text-slate-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentSubmissions as $sub)
                    @php
                        $items = $sub->items;
                        $itemsCount = $items->count();
                        $approvedCount = $items->where('status', 'approved')->count();
                        $rejectedCount = $items->where('status', 'rejected')->count();
                        $pendingCount = $items->where('status', 'pending')->count();

                        $totalVal = $items->sum(fn($i) => ($i->unit_cost ?? $i->cost ?? 0) * $i->quantity);
                        $daysOld = (int) $sub->created_at->diffInDays(now());

                        $pText = $daysOld >= 7 ? 'High' : ($daysOld >= 3 ? 'Medium' : 'Low');
                        $pClass = $daysOld >= 7 ? 'bg-rose-50 text-rose-700 border-rose-100' : ($daysOld >= 3 ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-blue-50 text-blue-700 border-blue-100');

                        // Entity Logic
                        $eName = 'System'; $eIcon = 'fa-building'; $eType = 'General';
                        if($sub->submittedBy->institute_id) { $eName = $sub->submittedBy->institute->institute_name; $eType = 'Institute'; $eIcon = 'fa-microscope'; }
                        elseif($sub->submittedBy->unit_id) { $eName = $sub->submittedBy->unit->unit_name; $eType = 'Unit'; $eIcon = 'fa-sitemap'; }
                        elseif($sub->submittedBy->office_id) { $eName = $sub->submittedBy->office->office_name; $eType = 'Office'; $eIcon = 'fa-building'; }
                        elseif($sub->submittedBy->department_id) { $eName = $sub->submittedBy->department->dept_name; $eType = 'Dept'; $eIcon = 'fa-university'; }
                        elseif($sub->submittedBy->faculty_id) { $eName = $sub->submittedBy->faculty->faculty_name; $eType = 'Faculty'; $eIcon = 'fa-graduation-cap'; }
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="ps-5 py-4">
                            <div class="font-black text-audit-navy">#{{ str_pad($sub->submission_id, 4, '0', STR_PAD_LEFT) }}</div>
                            <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $sub->created_at->format('d M, Y') }}</div>
                        </td>
                        <td>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center text-slate-400 text-xs">
                                    <i class="fas {{ $eIcon }}"></i>
                                </div>
                                <div>
                                    <div class="text-xs font-black text-slate-700 leading-tight">{{ Str::limit($eName, 22) }}</div>
                                    <div class="text-[9px] text-slate-400 font-bold uppercase">{{ $eType }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-[10px] text-slate-500 font-bold uppercase">
                                {{ $items->first()->item_name ?? 'N/A' }}
                                @if($itemsCount > 1)
                                    <span class="text-indigo-500 font-black">+{{ $itemsCount - 1 }} More</span>
                                @endif
                            </div>
                        <td>
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-xs font-black text-slate-700">{{ $itemsCount }} Items</span>
                            </div>
                            <div class="flex gap-1 items-center">
                                @if($approvedCount > 0)
                                    <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 px-1 rounded">{{ $approvedCount }} ✓</span>
                                @endif
                                @if($rejectedCount > 0)
                                    <span class="text-[9px] font-black text-rose-600 bg-rose-50 px-1 rounded">{{ $rejectedCount }} ✗</span>
                                @endif
                                @if($pendingCount > 0)
                                    <span class="text-[9px] font-black text-amber-600 bg-amber-50 px-1 rounded">{{ $pendingCount }} ?</span>
                                @endif
                            </div>
                            {{-- Mini Progress Bar --}}
                            <div class="w-24 h-1 bg-slate-100 rounded-full mt-1 flex overflow-hidden">
                                <div class="bg-emerald-500 h-full" style="width: {{ ($approvedCount/$itemsCount)*100 }}%"></div>
                                <div class="bg-rose-500 h-full" style="width: {{ ($rejectedCount/$itemsCount)*100 }}%"></div>
                            </div>
                        </td>
                        <td>
                            <div class="text-xs font-black text-audit-navy">₦{{ number_format($totalVal, 2) }}</div>
                        </td>
                        <td class="text-center">
                            @php
                                $statusClasses = [
                                    'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'rejected' => 'bg-rose-50 text-rose-700 border-rose-100',
                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'partially approved' => 'bg-indigo-50 text-indigo-700 border-indigo-100'
                                ];
                                // Logic for Batch Status
                                $displayStatus = $sub->status;
                                if($sub->status == 'pending' && ($approvedCount > 0 || $rejectedCount > 0)) {
                                    $displayStatus = 'In Progress';
                                }
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-full text-[9px] font-black uppercase border {{ $statusClasses[$sub->status] ?? $statusClasses['pending'] }}">
                                {{ $displayStatus }}
                            </span>
                        </td>
                        <td>
                            <div class="px-3 py-1 rounded-lg border {{ $pClass }} inline-block">
                                <div class="text-[9px] font-black uppercase tracking-tight">
                                    {{ $daysOld }} {{ Str::plural('Day', $daysOld) }} • {{ $pText }}
                                </div>
                            </div>
                        </td>
                        <td class="pe-5 text-end">
                            <a href="{{ route('auditor.submissions.show', $sub->submission_id) }}" class="btn btn-dark btn-sm rounded-lg px-3 font-black text-[10px] uppercase shadow-sm">
                                Inspect
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-20 bg-slate-50/50">
                            <i class="fas fa-folder-open text-slate-200 text-4xl mb-3 block"></i>
                            <p class="text-slate-400 font-bold uppercase text-xs tracking-widest">No priority reviews found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection