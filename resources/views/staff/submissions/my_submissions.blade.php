@extends('layouts.staff')

@section('title', 'My Inventory Submissions')

@section('content')
<div class="min-h-screen bg-slate-50 py-8">
    {{-- Standardized container to prevent edge-to-edge stretching --}}
    {{-- SESSION FEEDBACK --}}
    @if(session('success') || session('error'))
        @php 
            $isSuccess = session('success');
            $theme = $isSuccess ? 'emerald' : 'red';
            $icon = $isSuccess ? 'check-circle' : 'exclamation-triangle';
        @endphp
        <div id="session-alert" class="mb-6 flex items-center justify-between p-4 rounded-2xl bg-{{ $theme }}-50 border border-{{ $theme }}-100 shadow-sm transition-all duration-500">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-{{ $theme }}-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-{{ $icon }} text-white"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-{{ $theme }}-900 mb-0.5">{{ $isSuccess ? 'Success' : 'Alert' }}</h4>
                    <p class="text-xs font-bold text-{{ $theme }}-700 opacity-90 mb-0 leading-tight">
                        {{ session('success') ?? session('error') }}
                    </p>
                </div>
            </div>
            <button type="button" class="text-{{ $theme }}-400 hover:text-{{ $theme }}-600 transition-colors" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif
    <div class="container mx-auto px-4 max-w-7xl">
        
        {{-- 1. HEADER & ACTION BAR --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
            <div class="flex items-center gap-4">
                {{-- BACK BUTTON --}}
                <a href="{{ route('staff.dashboard') }}" 
                   class="flex items-center justify-center w-11 h-11 rounded-xl border border-slate-200 bg-white text-slate-400 hover:text-emerald-600 hover:border-emerald-100 transition-all shrink-0 group shadow-sm">
                    <i class="fas fa-chevron-left text-sm group-hover:-translate-x-0.5 transition-transform"></i>
                </a>

                <div class="min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600"></span>
                        </span>
                        <h6 class="text-[10px] font-black uppercase tracking-widest text-slate-500 truncate">
                            College of Medicine Inventory
                        </h6>
                    </div>
                    <h1 class="text-xl md:text-3xl font-black text-slate-900 tracking-tight truncate">
                        My Submissions
                    </h1>
                </div>
            </div>
            
            {{-- NEW ENTRY BUTTON (Auditor-Style CTA) --}}
            <a href="{{ route('staff.submissions.create') }}" 
               class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-0.5 active:scale-95">
                <i class="fas fa-plus-circle me-2"></i>
                <span>New Entry</span>
            </a>
        </div>

        {{-- 2. SEARCH & STATUS FILTERS (Auditor Style) --}}
        <div class="mb-8">
            <div class="bg-white rounded-[2rem] shadow-sm border-t-4 border-emerald-600 overflow-hidden">
                <div class="p-6">
                    <form action="{{ route('staff.submissions.index') }}" method="GET" class="flex flex-col gap-6">
                        {{-- Search Bar --}}
                        <div class="flex items-center bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-emerald-500/20 transition-all">
                            <span class="pl-4 pr-2 text-slate-400"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" 
                                class="w-full border-0 bg-transparent py-3.5 text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:ring-0" 
                                placeholder="Search Reference or Item Name..." 
                                value="{{ request('search') }}">
                            @if(request('search'))
                                <a href="{{ route('staff.submissions.index') }}" class="px-3 text-slate-300 hover:text-rose-500 transition-colors">
                                    <i class="fas fa-times-circle text-lg"></i>
                                </a>
                            @endif
                            <button type="submit" class="bg-slate-900 text-white px-8 py-3.5 font-black text-xs uppercase tracking-widest hover:bg-emerald-600 transition-colors">
                                SEARCH
                            </button>
                        </div>

                        {{-- Status Pill Filter --}}
                        <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest me-2">Filter Status:</span>
                            <a href="{{ route('staff.submissions.index') }}" 
                               class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all {{ !request('status') ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                All
                            </a>
                            @foreach(['pending' => 'amber', 'approved' => 'emerald', 'rejected' => 'rose'] as $status => $color)
                                <a href="{{ route('staff.submissions.index', ['status' => $status]) }}" 
                                   class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all
                                   {{ request('status') == $status 
                                        ? "bg-$color-500 text-white shadow-md shadow-$color-100" 
                                        : "bg-$color-50 text-$color-600 hover:bg-$color-100 border border-$color-100" }}">
                                    {{ $status }}
                                </a>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 3. DATA TABLE --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-900 text-white">
                            <th class="ps-8 py-4 text-[10px] font-black uppercase tracking-widest">Ref Number</th>
                            <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Inventory Item</th>
                            <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest text-center">Asset Tag</th>
                            <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Valuation</th>
                            <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest text-center">Status</th>
                            <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest text-center">Details</th>
                            <th class="pe-8 py-4 text-[10px] font-black uppercase tracking-widest text-end">Manage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($submissions as $sub)
                            @php
                                $firstItem = $sub->items->first();
                                $count = $sub->items->count();
                                $statusStyles = [
                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                ];
                            @endphp
                            <tr class="hover:bg-emerald-50/30 transition-colors group">
                                <td class="ps-8 py-5 align-middle whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="font-black text-emerald-600 text-xs italic leading-none">
                                            #AUD-{{ str_pad($sub->submission_id, 5, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <span class="text-[10px] font-bold text-slate-600 not-italic mt-1 uppercase tracking-tight">
                                            {{ $sub->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-5 align-middle">
                                    <div class="text-[11px] font-black text-slate-800 uppercase leading-none">
                                        {{ Str::limit($firstItem->item_name ?? 'N/A', 35) }}
                                    </div>
                                    @if($count > 1) 
                                        <div class="inline-block mt-1 text-[9px] font-black px-1.5 py-0.5 bg-emerald-50 text-emerald-600 rounded">
                                            + {{ $count - 1 }} OTHER ITEMS
                                        </div> 
                                    @endif
                                </td>
                               <td class="px-4 py-5 align-middle text-center">
                                    @php 
                                        $firstItem = $sub->items->first();
                                        $count = $sub->items->count();
                                        $submissionType = $sub->submission_type;
                                        $tag = $firstItem ? $firstItem->generated_tag : null;
                                    @endphp

                                    @if($firstItem)
                                        <div class="relative inline-block">
                                            
                                            {{-- CASE 1: The Model explicitly returned the Pending string --}}
                                            @if($tag === 'PENDING_ASSET_TAG')
                                                <code class="px-2 py-1 rounded-2 bg-slate-100 text-slate-500 fw-bold border border-slate-300 border-dashed d-inline-block mb-1 shadow-sm" 
                                                    style="font-size:0.65rem; letter-spacing: 0.05em;">
                                                    <i class="fas fa-clock me-1 opacity-50"></i>
                                                    PENDING_ASSET_TAG
                                                </code>

                                            {{-- CASE 2: The Model explicitly returned the Linked error --}}
                                            @elseif($tag === 'ASSET_NOT_LINKED')
                                                <code class="px-2 py-1 rounded-2 bg-amber-50 text-amber-700 fw-bold border border-amber-200 d-inline-block mb-1 shadow-sm" 
                                                    style="font-size:0.65rem;">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    ASSET_TAG_MISSING
                                                </code>

                                            {{-- CASE 3: The Model returned a real generated Tag --}}
                                            @else
                                                <code class="px-2 py-1 rounded-2 {{ $submissionType === 'new_purchase' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-blue-50 text-blue-700 border-blue-200' }} fw-black border d-inline-block mb-1 shadow-sm" 
                                                    style="font-size:0.7rem; border-width: 1px;">
                                                    <i class="fas {{ $submissionType === 'new_purchase' ? 'fa-check-decagram' : 'fa-tools' }} me-1"></i>
                                                    {{ $tag ?? 'UNKNOWN_TAG' }}
                                                </code>
                                            @endif

                                            {{-- Batch Indicator --}}
                                            @if($count > 1)
                                                <span class="absolute -top-2 -right-3 flex items-center justify-center min-w-[18px] h-[18px] px-1 bg-slate-800 text-white text-[8px] font-bold rounded-full border-2 border-white shadow-sm z-10" 
                                                    title="This submission contains {{ $count }} items">
                                                    +{{ $count - 1 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-300 italic text-[10px] tracking-widest">NO_ITEMS_FOUND</span>
                                    @endif
                                </td>
                                <td class="px-4 py-5 align-middle text-xs font-black text-slate-900">
                                    ₦{{ number_format($sub->total_value, 2) }}
                                </td>
                                
                                <td class="px-4 py-5 align-middle text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center gap-1.5">
                                        {{-- Overall Submission Status Badge --}}
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black uppercase border shadow-sm {{ $statusStyles[$sub->status] ?? 'bg-slate-50' }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current me-1.5 {{ $sub->status === 'pending' ? 'animate-pulse' : '' }}"></span>
                                            {{ $sub->status }}
                                        </span>
                                        @if($sub->items->count() > 0)
                                            <div class="flex items-center gap-1">
                                                @php
                                                    $approvedCount = $sub->items->where('status', 'approved')->count();
                                                    $rejectedCount = $sub->items->where('status', 'rejected')->count();
                                                @endphp

                                                @if($approvedCount > 0)
                                                    <span class="flex items-center justify-center bg-emerald-100 text-emerald-700 text-[8px] font-black w-5 h-5 rounded-full border border-emerald-200" title="Approved Items">
                                                        {{ $approvedCount }}
                                                    </span>
                                                @endif

                                                @if($rejectedCount > 0)
                                                    <span class="flex items-center justify-center bg-rose-100 text-rose-700 text-[8px] font-black w-5 h-5 rounded-full border border-rose-200" title="Rejected Items">
                                                        {{ $rejectedCount }}
                                                    </span>
                                                @endif
                                                
                                                {{-- If it's a batch and some are still pending, show a small gray indicator --}}
                                                @php $pendingItems = $sub->items->where('status', 'pending')->count(); @endphp
                                                @if($pendingItems > 0 && $sub->status !== 'pending')
                                                    <span class="text-[8px] font-bold text-slate-400 px-1 italic">
                                                        +{{ $pendingItems }} pending
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-5 align-middle text-center">
                                    <a href="{{ route('staff.submissions.show', $sub->submission_id) }}" 
                                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-emerald-600 hover:text-white transition-all duration-200">
                                        <i class="fas fa-file-alt me-2 text-[10px]"></i>
                                        <span class="text-[10px] font-black uppercase tracking-wider">Details</span>
                                    </a>
                                </td>
                                <td class="pe-8 py-5 align-middle text-right">
                                    @if(in_array($sub->status, ['pending', 'rejected']))
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('staff.submissions.edit', $sub->submission_id) }}" 
                                            class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 text-[10px] font-bold hover:bg-slate-900 hover:text-white transition-all">
                                                EDIT
                                            </a>
                                            <form action="{{ route('staff.submissions.destroy', $sub->submission_id) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 rounded-lg border border-rose-100 bg-rose-50 text-rose-600 text-[10px] font-bold hover:bg-rose-600 hover:text-white transition-all">
                                                    DELETE
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <i class="fas fa-lock text-[9px]"></i>
                                            <span class="text-[9px] font-black uppercase tracking-widest">Read Only</span>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-20 text-center font-black text-slate-300 uppercase text-xs">No inventory records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE VIEW --}}
            <div class="md:hidden divide-y divide-slate-100">
                @foreach($submissions as $sub)
                    @php
                        $firstItem = $sub->items->first();
                        $totalVal = $sub->items->sum(fn($i) => $i->cost * $i->quantity);
                    @endphp
                    <div class="p-5 relative">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="font-black text-emerald-600 text-sm italic">#AUD-{{ str_pad($sub->submission_id, 4, '0', STR_PAD_LEFT) }}</span>
                                <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $sub->created_at->format('d M, Y') }}</div>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[8px] font-black uppercase border {{ $statusStyles[$sub->status] ?? 'bg-slate-50' }}">
                                {{ $sub->status }}
                            </span>
                        </div>
                        
                        <h3 class="text-xs font-black text-slate-800 uppercase mb-4 leading-tight">
                            {{ Str::limit($firstItem->item_name ?? 'N/A', 40) }}
                        </h3>

                        <div class="flex items-center justify-between mb-5 px-3 py-2 bg-slate-50 rounded-xl">
                            <span class="text-[10px] font-black text-slate-900">₦{{ number_format($totalVal, 2) }}</span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $sub->items->count() }} items</span>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('staff.submissions.show', $sub->submission_id) }}" 
                               class="flex-grow text-center bg-slate-900 text-white py-3 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-md">
                                View Details
                            </a>
                            @if(in_array($sub->status, ['pending', 'rejected']))
                                <a href="{{ route('staff.submissions.edit', $sub->submission_id) }}" class="px-4 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-600">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('staff.submissions.destroy', $sub->submission_id) }}" method="POST" onsubmit="return confirm('Confirm deletion?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-4 h-full flex items-center justify-center bg-rose-50 text-rose-600 border border-rose-100 rounded-xl">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-8">
            {{ $submissions->links() }}
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.getElementById('session-alert');
        if (alert) {
            // Wait 5 seconds, then fade and remove
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 100); // Wait for fade animation to finish
            }, 1000);
        }
    });
</script>
@endsection