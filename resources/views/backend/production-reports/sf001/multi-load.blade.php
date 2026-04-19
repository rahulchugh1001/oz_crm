@extends('backend.layout.app')

@section('content')
<div id="fullPageLoader" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-slate-900/40 backdrop-blur-sm transition-all">
    <div class="flex flex-col items-center gap-4">
        <div class="h-16 w-16 animate-spin rounded-full border-4 border-white border-t-indigo-500 shadow-2xl"></div>
        <p class="text-lg font-bold text-white tracking-widest drop-shadow-md">UPDATING STOCK...</p>
    </div>
</div>

<!-- Edit Coil Modal -->
<div id="editCoilModal" class="hidden fixed inset-0 z-[100000] bg-slate-900/50 p-4 overflow-y-auto">
    <div class="mx-auto mt-10 mb-10 w-full max-w-5xl rounded-2xl bg-white shadow-xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Edit Coil Stock</h3>
            <button type="button" onclick="closeEditCoilModal()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="editCoilForm" action="#" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_manufacture_id" class="block text-sm font-semibold text-slate-700 mb-2">Supplier Name <span class="text-rose-500">*</span></label>
                    <select id="edit_manufacture_id" name="manufacture_id" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="edit_process" class="block text-sm font-semibold text-slate-700 mb-2">Process <span class="text-rose-500">*</span></label>
                    <select id="edit_process" name="process" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="available">Available</option>
                        <option value="in_use">In Use</option>
                        <option value="completed">Completed</option>
                        <option value="out_of_stock">Out Of Stock</option>
                    </select>
                </div>

                <div>
                    <label for="edit_coil_no" class="block text-sm font-semibold text-slate-700 mb-2">Coil Name</label>
                    <input type="text" id="edit_coil_no" name="coil_no" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="edit_coil_size" class="block text-sm font-semibold text-slate-700 mb-2">Coil Size <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit_coil_size" name="coil_size" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="edit_thickness" class="block text-sm font-semibold text-slate-700 mb-2">Thickness <span class="text-rose-500">*</span></label>
                    <input type="number" id="edit_thickness" name="thickness" required min="0" step="0.001" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="edit_net_weight_kg" class="block text-sm font-semibold text-slate-700 mb-2">Net Weight (KG) <span class="text-rose-500">*</span></label>
                    <input type="number" id="edit_net_weight_kg" name="net_weight_kg" required min="0" step="0.001" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditCoilModal()" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-medium">Update Coil</button>
            </div>
        </form>
    </div>
</div>

<div class="px-6 py-4">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Manage Coil: <span class="text-indigo-600">{{ $coil->coil_no ?: 'N/A' }}</span></h1>
                <button 
                    type="button"
                    onclick="openEditCoilModalFromMulti(this)"
                    data-id="{{ $coil->id }}"
                    data-manufacture-id="{{ $coil->manufacture_id }}"
                    data-coil-no="{{ $coil->coil_no }}"
                    data-coil-size="{{ $coil->coil_size }}"
                    data-thickness="{{ $coil->thickness }}"
                    data-net-weight-kg="{{ $coil->net_weight_kg }}"
                    data-process="{{ $coil->process }}"
                    data-update-url="{{ route('admin.production-reports.sf001.coil-stock.update', $coil->id) }}"
                    class="p-2 rounded-lg bg-indigo-50 text-indigo-500 hover:bg-indigo-600 hover:text-white transition-all border border-indigo-100 shadow-sm"
                    title="Edit Coil Metadata"
                >
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </button>
            </div>
            <p class="text-sm text-slate-500 mt-1">Manage weight distribution for Coil: <span class="font-bold text-slate-700">{{ $coil->coil_no ?: 'Unnamed Coil' }}</span></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.production-reports.sf001.coil-stock') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back to Coil Stock
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coil Info Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200">
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                        <i data-lucide="database" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Coil Summary</h3>
                </div>
                
                @php
                    $totalCoilWeight = (float) $coil->net_weight_kg + $allocatedMachines->sum('allocated_weight');
                    $totalAllocated = (float) $allocatedMachines->sum('allocated_weight');
                    $allocationPercentage = $totalCoilWeight > 0 ? ($totalAllocated / $totalCoilWeight) * 100 : 0;
                @endphp

                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-500">Supplier</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $coil->manufacture->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-500">Master Net Weight</span>
                        <span class="text-sm font-bold text-slate-900">{{ number_format($totalCoilWeight, 0) }} KG</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-500">Available Stock</span>
                        <span class="text-sm font-bold text-emerald-600">{{ number_format($coil->net_weight_kg, 0) }} KG</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-slate-500">Distributed Weight</span>
                        <span class="text-sm font-bold text-indigo-600">{{ number_format($totalAllocated, 0) }} KG</span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Weight Distribution</span>
                        <span class="text-xs font-bold text-indigo-600">{{ round($allocationPercentage) }}% Allocated</span>
                    </div>
                    <div class="h-3 w-full rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full bg-indigo-500 transition-all duration-500" style="width: {{ $allocationPercentage }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Add Machine Form -->
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200 relative overflow-hidden">
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Allocate Machine</h3>
                </div>

                @if($coil->net_weight_kg <= 0)
                    <div class="absolute inset-0 z-10 bg-white/60 backdrop-blur-[2px] flex flex-col items-center justify-center p-6 text-center">
                        <div class="h-16 w-16 mb-4 rounded-full bg-rose-50 flex items-center justify-center text-rose-500">
                            <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                        </div>
                        <h4 class="text-lg font-bold text-rose-600 mb-1">Out of Stock</h4>
                        <p class="text-sm text-slate-500 max-w-[200px]">This coil has no remaining weight to allocate.</p>
                    </div>
                @endif

                <form id="allocationForm" action="{{ route('admin.production-reports.sf001.coil-stock.multi-load.store', $coil->id) }}" method="POST" class="space-y-4 {{ $coil->net_weight_kg <= 0 ? 'opacity-20 pointer-events-none select-none' : '' }}">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Target Machine</label>
                        <div class="machine-select-wrapper">
                            <!-- Hidden native select -->
                            <select name="machine_id" id="machine_id_hidden" required class="hidden-machine-select" style="display:none;">
                                <option value="">Select a machine</option>
                                @foreach($allMachines as $machine)
                                    <option value="{{ $machine->id }}">{{ $machine->name }} ({{ $machine->machine_code }})</option>
                                @endforeach
                            </select>
                            
                            <!-- Searchable dropdown UI -->
                            <div class="ss-dropdown" id="machine-search-dropdown" data-open="false">
                                <div class="ss-trigger" tabindex="0">
                                    <span class="ss-display-text ss-placeholder">Select a machine</span>
                                    <i data-lucide="chevron-down" class="ss-arrow w-4 h-4"></i>
                                </div>
                                <div class="ss-panel">
                                    <div class="ss-search-wrap">
                                        <i data-lucide="search" class="ss-search-icon w-3.5 h-3.5"></i>
                                        <input type="text" class="ss-search-input" placeholder="Search machines..." autocomplete="off">
                                    </div>
                                    <ul class="ss-list"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Allocation Weight (KG)</label>
                        <div class="relative">
                            <input type="number" name="allocated_weight" required min="1" max="{{ $coil->net_weight_kg }}" placeholder="Max: {{ number_format($coil->net_weight_kg, 0) }} KG" class="w-full rounded-xl border border-slate-300 pl-4 pr-12 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 outline-none transition-all">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-slate-400">KG</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Remark (Optional)</label>
                        <input type="text" name="remark" placeholder="e.g. Initial load" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 outline-none transition-all">
                    </div>
                    <button type="submit" id="submitBtn" class="flex items-center justify-center gap-2 w-full rounded-xl bg-indigo-600 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-indigo-300 transition-all active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed">
                        <span id="btnText">Confirm Allocation</span>
                        <div id="btnLoader" class="hidden">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>
                </form>
            </div>
        </div>

        <!-- Allocation Table -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <i data-lucide="list" class="w-5 h-5 text-slate-500"></i>
                        <h3 class="text-lg font-bold text-slate-800">Active Machine Loads</h3>
                    </div>
                    <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700 border border-indigo-100">{{ $allocatedMachines->count() }} Machines</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                <th class="px-6 py-4">Machine</th>
                                <th class="px-6 py-4 text-center">Allocated</th>
                                <th class="px-6 py-4 text-center text-indigo-600">Remaining</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($allocatedMachines as $allocation)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-slate-800">{{ $allocation->machine->name }}</p>
                                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">{{ $allocation->machine->machine_code }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-bold text-slate-700">{{ number_format($allocation->allocated_weight, 0) }} KG</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-bold text-indigo-600">{{ number_format($allocation->remaining_weight, 0) }} KG</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 border border-emerald-100">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="{{ route('admin.production-reports.sf001.coil-stock.multi-load.update', [$coil->id, $allocation->id]) }}" method="POST" class="inline js-manage-form">
                                            @csrf
                                            <button type="button" onclick="confirmManage(this, {{ $allocation->allocated_weight }})" class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors text-xs font-bold" title="Adjust Weight">
                                                <i data-lucide="settings-2" class="w-3.5 h-3.5"></i>
                                                Manage
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.production-reports.sf001.coil-stock.multi-load.unload', [$coil->id, $allocation->id]) }}" method="POST" class="inline js-unload-form">
                                            @csrf
                                            <button type="button" onclick="confirmUnload(this)" class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors text-xs font-bold" title="Unload Machine">
                                                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                                Unload
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-300">
                                            <i data-lucide="inbox" class="w-6 h-6"></i>
                                        </div>
                                        <p class="text-slate-400 text-sm font-medium">No active machine allocations for this coil.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Used Logs History -->
            <div class="rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <i data-lucide="archive" class="w-5 h-5 text-slate-500"></i>
                        <h3 class="text-lg font-bold text-slate-800">Used Logs (History)</h3>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                <th class="px-6 py-4">Machine</th>
                                <th class="px-6 py-4 text-center">Was Allocated</th>
                                <th class="px-6 py-4 text-center text-rose-600 font-black">Actual Consumed</th>
                                <th class="px-6 py-4">Unloaded At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($unloadedHistory as $history)
                            <tr class="bg-slate-50/20 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-slate-600">{{ $history->machine->name }}</p>
                                    <p class="text-[9px] font-semibold text-slate-400 uppercase tracking-wider">{{ $history->machine->machine_code }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-bold text-slate-500 line-through decoration-slate-300">{{ number_format($history->allocated_weight, 0) }} KG</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex flex-col">
                                        <span class="text-sm font-black text-rose-600">{{ number_format($history->consumed_weight, 0) }} KG</span>
                                        <span class="text-[9px] font-bold text-slate-300 uppercase">Final Usage</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs font-bold text-slate-600">{{ $history->updated_at->format('d M, Y') }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $history->updated_at->format('h:i A') }}</p>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-400 text-xs italic">No unloaded history yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Activity Logs / History -->
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200">
                <div class="flex items-center gap-2 mb-6">
                    <i data-lucide="history" class="w-5 h-5 text-slate-500"></i>
                    <h3 class="text-lg font-bold text-slate-800">Recent Transitions</h3>
                </div>
                <div class="space-y-4">
                    @forelse($transitions as $track)
                        <div class="flex items-start gap-4 p-3 rounded-xl bg-slate-50/50 border border-slate-100">
                            <div class="mt-1 h-8 w-8 flex-shrink-0 flex items-center justify-center rounded-lg {{ $track->type === 'load' ? 'bg-indigo-50 text-indigo-600' : 'bg-rose-50 text-rose-600' }}">
                                <i data-lucide="{{ $track->type === 'load' ? 'arrow-up-right' : 'arrow-down-left' }}" class="w-4 h-4"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-bold text-slate-800 capitalize">{{ $track->type }}ed on {{ $track->machine->name }}</p>
                                    <span class="text-[10px] font-medium text-slate-400">{{ $track->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Weight: <span class="font-bold {{ $track->type === 'load' ? 'text-indigo-600' : 'text-rose-600' }}">{{ number_format($track->load_weight, 0) }} KG</span>
                                    @if($track->remark)
                                        <span class="mx-2 text-slate-300">|</span>
                                        <span>{{ $track->remark }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">No recent transitions for this multi-load configuration.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Searchable Dropdown Styles */
    .machine-select-wrapper { position: relative; }
    .ss-dropdown { position: relative; width: 100%; font-size: 0.875rem; user-select: none; }
    .ss-trigger { display: flex; align-items: center; justify-between; gap: 8px; width: 100%; padding: 10px 16px; border: 1px solid #cbd5e1; border-radius: 0.75rem; background: #fff; cursor: pointer; min-height: 44px; transition: all 0.2s; outline: none; color: #334155; }
    .ss-trigger:hover { border-color: #94a3b8; }
    .ss-dropdown.ss-open .ss-trigger { border-color: #6366f1; ring: 2px; ring-color: rgba(99,102,241,0.2); }
    .ss-display-text { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .ss-placeholder { color: #94a3b8; }
    .ss-panel { display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 50; mt: 2px; background: #fff; border: 1px solid #cbd5e1; border-radius: 0.75rem; shadow: 0 10px 15px -3px rgba(0,0,0,0.1); overflow: hidden; }
    .ss-dropdown.ss-open .ss-panel { display: block; }
    .ss-search-wrap { display: flex; items-center; gap: 8px; padding: 10px 12px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
    .ss-search-input { width: 100%; border: none; outline: none; background: transparent; font-size: 0.875rem; }
    .ss-list { list-style: none; margin: 0; padding: 4px 0; max-height: 240px; overflow-y: auto; }
    .ss-option { padding: 8px 16px; cursor: pointer; transition: all 0.1s; }
    .ss-option:hover { background: #f1f5f9; color: #6366f1; }
    .ss-selected { background: #eef2ff; color: #6366f1; font-weight: 600; }
    .ss-no-results { padding: 12px; text-center; color: #94a3b8; font-size: 0.875rem; }

    /* Hide number input spin buttons */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdown = document.getElementById('machine-search-dropdown');
        const hiddenSelect = document.getElementById('machine_id_hidden');
        if (!dropdown || !hiddenSelect) return;

        const trigger = dropdown.querySelector('.ss-trigger');
        const displayText = dropdown.querySelector('.ss-display-text');
        const searchInput = dropdown.querySelector('.ss-search-input');
        const list = dropdown.querySelector('.ss-list');

        function populateList(filter = '') {
            list.innerHTML = '';
            const options = Array.from(hiddenSelect.options).filter(opt => opt.value !== "");
            const q = filter.toLowerCase();
            let count = 0;

            options.forEach(opt => {
                if (q && !opt.text.toLowerCase().includes(q)) return;
                const li = document.createElement('li');
                li.className = 'ss-option';
                if (opt.value === hiddenSelect.value) li.classList.add('ss-selected');
                li.textContent = opt.text;
                li.dataset.value = opt.value;
                li.onclick = function() {
                    hiddenSelect.value = opt.value;
                    displayText.textContent = opt.text;
                    displayText.classList.remove('ss-placeholder');
                    closeDropdown();
                };
                list.appendChild(li);
                count++;
            });

            if (count === 0) {
                const li = document.createElement('li');
                li.className = 'ss-no-results';
                li.textContent = 'No machines found';
                list.appendChild(li);
            }
        }

        function openDropdown() {
            dropdown.classList.add('ss-open');
            dropdown.dataset.open = "true";
            populateList(searchInput.value);
            searchInput.focus();
        }

        function closeDropdown() {
            dropdown.classList.remove('ss-open');
            dropdown.dataset.open = "false";
        }

        trigger.onclick = function(e) {
            e.stopPropagation();
            dropdown.dataset.open === "true" ? closeDropdown() : openDropdown();
        };

        searchInput.onclick = (e) => e.stopPropagation();
        searchInput.oninput = (e) => populateList(e.target.value);

        document.onclick = () => closeDropdown();
    });

    function showFullLoader() {
        document.getElementById('fullPageLoader').classList.remove('hidden');
        document.getElementById('fullPageLoader').classList.add('flex');
    }

    document.getElementById('allocationForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const text = document.getElementById('btnText');
        const loader = document.getElementById('btnLoader');
        
        btn.disabled = true;
        text.innerText = 'Allocating...';
        loader.classList.remove('hidden');
        showFullLoader();
    });

    function confirmManage(btn, currentWeight) {
        const row = btn.closest('tr');
        const machineName = row.querySelector('p.text-sm.font-bold').textContent;

        Swal.fire({
            title: '<div class="flex items-center gap-3 pt-2 text-emerald-600"><i data-lucide="settings-2" class="w-6 h-6"></i><span>Adjust Allocation</span></div>',
            html: `
                <div class="text-left mt-4 px-1">
                    <p class="text-sm text-slate-500 mb-6 bg-emerald-50 p-3 rounded-xl border border-emerald-100 flex items-start gap-2">
                        <i data-lucide="info" class="w-4 h-4 mt-0.5 text-emerald-500 flex-shrink-0"></i>
                        <span>Adjusting weight for <b>${machineName}</b>. This will sync automatically with master coil stock.</span>
                    </p>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">New Allocated Weight (KG)</label>
                    <div class="relative">
                        <input type="number" id="swal-new-weight" class="w-full !m-0 !py-3 !px-4 !rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-200/50 outline-none transition-all text-lg font-bold text-slate-700" value="${currentWeight}" min="1">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-slate-300">KG</span>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#cbd5e1',
            confirmButtonText: 'Update Weight',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-2xl border-none p-6',
                confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm shadow-lg shadow-emerald-200',
                cancelButton: 'rounded-xl px-6 py-3 font-bold text-sm text-slate-600'
            },
            didOpen: () => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            },
            preConfirm: () => {
                const val = document.getElementById('swal-new-weight').value;
                if (!val || isNaN(val) || val < 1) {
                    Swal.showValidationMessage('Please enter a valid weight');
                    return false;
                }
                Swal.showLoading();
                return val;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                showFullLoader();
                const form = btn.closest('form');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'allocated_weight';
                input.value = result.value;
                form.appendChild(input);
                form.submit();
            }
        });
    }

    function confirmUnload(btn) {
        const row = btn.closest('tr');
        const remainingWeight = row.querySelector('td:nth-child(4) span').textContent.replace(' KG', '').replace(/,/g, '');
        const machineName = row.querySelector('p.text-sm.font-bold').textContent;
        
        Swal.fire({
            title: '<div class="flex items-center gap-3 pt-2 text-rose-600"><i data-lucide="log-out" class="w-6 h-6"></i><span>Unload Machine</span></div>',
            html: `
                <div class="text-left mt-4 px-1">
                    <p class="text-sm text-slate-500 mb-6 bg-rose-50 p-3 rounded-xl border border-rose-100 flex items-start gap-2">
                        <i data-lucide="info" class="w-4 h-4 mt-0.5 text-rose-500 flex-shrink-0"></i>
                        <span>Unloading <b>${machineName}</b>. Enter the exact weight being returned to stock.</span>
                    </p>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Returned Weight (KG)</label>
                    <div class="relative">
                        <input type="number" id="swal-return-weight" class="w-full !m-0 !py-3 !px-4 !rounded-xl border border-slate-200 focus:border-rose-500 focus:ring-4 focus:ring-rose-200/50 outline-none transition-all text-lg font-bold text-slate-700" value="${Math.round(remainingWeight)}" min="0">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-slate-300">KG</span>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#cbd5e1',
            confirmButtonText: 'Confirm & Return Stock',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-2xl border-none p-6',
                confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm shadow-lg shadow-rose-200',
                cancelButton: 'rounded-xl px-6 py-3 font-bold text-sm text-slate-600'
            },
            didOpen: () => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            },
            preConfirm: () => {
                const val = document.getElementById('swal-return-weight').value;
                if (!val || isNaN(val) || val < 0) {
                    Swal.showValidationMessage('Please enter a valid weight');
                    return false;
                }
                
                // Show loading on the button
                Swal.showLoading();
                return val;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                showFullLoader();
                const form = btn.closest('form');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'return_weight';
                input.value = result.value;
                form.appendChild(input);
                form.submit();
            }
        });
    }
    function openEditCoilModalFromMulti(button) {
        const id = button.getAttribute('data-id');
        const manufactureId = button.getAttribute('data-manufacture-id');
        const coilNo = button.getAttribute('data-coil-no');
        const coilSize = button.getAttribute('data-coil-size');
        const thickness = button.getAttribute('data-thickness');
        const netWeightKg = button.getAttribute('data-net-weight-kg');
        const process = button.getAttribute('data-process');
        const updateUrl = button.getAttribute('data-update-url');

        document.getElementById('edit_manufacture_id').value = manufactureId;
        document.getElementById('edit_coil_no').value = coilNo;
        document.getElementById('edit_coil_size').value = coilSize;
        document.getElementById('edit_thickness').value = thickness;
        document.getElementById('edit_net_weight_kg').value = netWeightKg;
        document.getElementById('edit_process').value = process;
        document.getElementById('editCoilForm').action = updateUrl;

        document.getElementById('editCoilModal').classList.remove('hidden');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeEditCoilModal() {
        document.getElementById('editCoilModal').classList.add('hidden');
    }
</script>
@endsection
