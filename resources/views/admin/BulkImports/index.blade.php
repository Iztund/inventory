@extends('layouts.admin')

@section('title', 'Asset Population Hub')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'com-dark': '#0f172a',
                    'com-slate': '#1e293b',
                }
            }
        }
    }
</script>
<style>
    body { font-family: 'Outfit', sans-serif; }
    /* Smoother glass effect */
    .glass-panel {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    /* Fixed background to give "depth" and move page back */
    .viewport-depth {
        background: radial-gradient(circle at 50% 50%, #1e293b 0%, #0f172a 100%);
        min-height: 100vh;
    }
</style>
@endpush

@section('content')
<div class="viewport-depth pb-12">
    <div class="container-fluid px-6 lg:px-16 py-10 max-w-[1600px] mx-auto">
        
        {{-- Alert System --}}
        @foreach(['success' => 'emerald', 'error' => 'red', 'info' => 'blue'] as $type => $color)
            @if(session($type))
                <div class="alert alert-dismissible fade show border-0 rounded-2xl p-4 mb-8 flex items-center shadow-2xl animate-bounce" 
                     style="background: rgba(255,255,255,0.05); border-left: 5px solid {{ $color }} !important;">
                    <div class="bg-{{ $color }}-500/20 p-3 rounded-xl mr-4 text-{{ $color }}-400">
                        <i class="fas fa-info-circle fa-lg"></i>
                    </div>
                    <div class="text-white">
                        <p class="font-black text-xs uppercase tracking-widest mb-0">{{ $type }}</p>
                        <p class="text-sm font-medium opacity-90 mb-0">{{ session($type) }}</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif
        @endforeach

        {{-- Hero Header --}}
        <div class="text-center mb-16 pt-6">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-amber-500 shadow-[0_0_50px_rgba(245,158,11,0.3)] mb-8 transform hover:rotate-12 transition-transform">
                <i class="fas fa-database text-white text-4xl"></i>
            </div>
            
            <h1 class="text-white font-black text-5xl lg:text-6xl tracking-tighter mb-4 drop-shadow-2xl">
                Asset Population <span class="text-amber-500">Hub</span>
            </h1>
            
            <p class="text-slate-400 font-medium text-lg max-w-2xl mx-auto leading-relaxed">
                Centralized bulk processing for the College of Medicine. Manage institutional assets across Faculties, Departments, and Units with ease.
            </p>

            {{-- Action Buttons --}}
            <div class="flex justify-center gap-4 mt-10 flex-wrap">
                <a href="{{ route('admin.bulk-assets.csv.template') }}" 
                   class="flex items-center gap-3 px-8 py-4 bg-slate-800/50 hover:bg-slate-700 text-white rounded-2xl border border-slate-700 transition-all font-bold tracking-tight">
                    <i class="fas fa-download text-amber-500"></i>
                    Get Template
                </a>
                
                <a href="{{ route('admin.bulk-assets.csv.create') }}" 
                   class="flex items-center gap-3 px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl shadow-lg shadow-emerald-900/20 transition-all font-bold tracking-tight">
                    <i class="fas fa-file-excel"></i>
                    Upload CSV/Excel
                </a>

                <a href="{{ route('admin.bulk-assets.manual.create') }}" 
                   class="flex items-center gap-3 px-8 py-4 bg-amber-600 hover:bg-amber-500 text-white rounded-2xl shadow-lg shadow-amber-900/20 transition-all font-bold tracking-tight">
                    <i class="fas fa-plus-circle"></i>
                    Manual Entry
                </a>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="row g-4 mb-12">
            @php
                $statItems = [
                    ['label' => 'Total Imports', 'val' => $stats['total_imports'], 'icon' => 'upload', 'color' => 'indigo'],
                    ['label' => 'Assets Imported', 'val' => number_format($stats['total_assets_imported']), 'icon' => 'check-double', 'color' => 'emerald'],
                    ['label' => 'Pending Review', 'val' => $stats['pending_imports'], 'icon' => 'clock', 'color' => 'amber'],
                    ['label' => 'Failed Records', 'val' => $stats['failed_imports'], 'icon' => 'exclamation-triangle', 'color' => 'red'],
                ];
            @endphp

            @foreach($statItems as $item)
            <div class="col-md-6 col-lg-3">
                <div class="glass-panel p-6 rounded-3xl group hover:border-{{ $item['color'] }}-500/50 transition-all duration-500">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-{{ $item['color'] }}-500/10 flex items-center justify-center text-{{ $item['color'] }}-400">
                            <i class="fas fa-{{ $item['icon'] }} fa-lg"></i>
                        </div>
                        <span class="text-[10px] font-black tracking-widest text-slate-500 uppercase">{{ $item['label'] }}</span>
                    </div>
                    <h2 class="text-white font-black text-3xl mb-1">{{ $item['val'] }}</h2>
                    <p class="text-{{ $item['color'] }}-400 text-xs font-bold uppercase tracking-tighter">System Analytics</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- History Table --}}
        <div class="glass-panel rounded-3xl overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-white/5 bg-white/5 flex flex-wrap justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-amber-500/10 rounded-2xl">
                        <i class="fas fa-history text-amber-500"></i>
                    </div>
                    <div>
                        <h5 class="text-white font-black text-xl mb-0">Import History</h5>
                        <p class="text-slate-500 text-xs font-medium uppercase tracking-widest">Audit Trail & Processing Logs</p>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead class="bg-black/20 text-slate-400 font-black text-[11px] uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-4 border-0">Batch ID</th>
                            <th class="px-6 py-4 border-0">Method</th>
                            <th class="px-6 py-4 border-0">Entity Context</th>
                            <th class="px-6 py-4 border-0">Successful</th>
                            <th class="px-6 py-4 border-0">Status</th>
                            <th class="px-6 py-4 border-0">Performed By</th>
                            <th class="px-6 py-4 border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-300 font-medium">
                        @forelse($recentImports as $import)
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 text-white font-bold">#{{ str_pad($import->import_id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black {{ $import->import_type === 'csv' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                    {{ strtoupper($import->import_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-100 font-bold text-sm">{{ ucfirst($import->entity_type) }}</div>
                                <div class="text-slate-500 text-xs">{{ $import->entity_name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-emerald-400 font-black">{{ $import->successful_imports }}</span>
                                    <span class="text-slate-600 text-xs italic">of {{ $import->total_rows }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = [
                                        'completed' => 'emerald',
                                        'processing' => 'amber',
                                        'failed' => 'red'
                                    ][$import->status] ?? 'slate';
                                @endphp
                                <span class="flex items-center gap-2 text-{{ $statusColor }}-400 font-bold text-xs uppercase">
                                    <span class="w-2 h-2 rounded-full bg-{{ $statusColor }}-500 {{ $import->status === 'processing' ? 'animate-ping' : '' }}"></span>
                                    {{ $import->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-slate-100 font-bold">{{ $import->importedBy->full_name }}</span>
                                <div class="text-[10px] text-slate-500 uppercase">{{ $import->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.bulk-assets.show', $import->import_id) }}" class="btn btn-sm btn-outline-light border-white/10 rounded-xl hover:bg-white hover:text-black">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.bulk-assets.destroy', $import->import_id) }}" method="POST" onsubmit="return confirm('Confirm deletion?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger border-red-500/20 rounded-xl">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-20 text-center">
                                <i class="fas fa-folder-open text-slate-700 text-5xl mb-4"></i>
                                <p class="text-slate-500 font-bold">No import records found in the registry.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($recentImports->hasPages())
                <div class="p-6 bg-black/10 flex justify-center">
                    {{ $recentImports->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Modern auto-dismiss for alerts
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 4000);
</script>
@endpush
@endsection