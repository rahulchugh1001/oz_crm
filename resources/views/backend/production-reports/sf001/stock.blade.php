@extends('backend.layout.app')

@section('title', 'SF001 Process - Item Stock')

@section('page-title', 'SF001 Process - Stock Management')

@section('breadcrumb')
    <span class="text-slate-600">SF001</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Stock</span>
@endsection

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center">
                        <i data-lucide="package" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">SF001 Process - Item Wise Stock</h2>
                        <p class="text-sm text-slate-500">View aggregated stock quantities by item from production reports</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">Item Stock List</h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-500">Total Items:</span>
                    <span class="text-sm font-semibold text-slate-900">{{ $itemStocks->count() }}</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            #
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Item Code
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Item Name
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Size
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Total Stock Quantity
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Last Stock Update
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($itemStocks as $index => $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-slate-700">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-slate-900">{{ $item->code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i data-lucide="box" class="w-4 h-4 text-slate-400"></i>
                                <span class="text-sm font-medium text-slate-900">{{ $item->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-600">{{ $item->size }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $item->total_stock > 0 ? 'bg-green-50 text-green-700' : 'bg-slate-50 text-slate-500' }}">
                                <i data-lucide="package-check" class="w-4 h-4"></i>
                                <span class="text-sm font-semibold">{{ number_format($item->total_stock, 2) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                                <span>{{ $item->last_stock_update ? \Carbon\Carbon::parse($item->last_stock_update)->format('M d, Y h:i A') : 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="openTransferModal()" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all">
                                    <i data-lucide="arrow-right-left" class="w-3.5 h-3.5"></i>
                                    Transfer
                                </button>
                                <a href="{{ route('admin.production-reports.sf001.stock.history', $item->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-700 bg-slate-100 border border-slate-300 rounded-lg hover:bg-slate-200 transition-all">
                                    <i data-lucide="history" class="w-3.5 h-3.5"></i>
                                    History
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="package-x" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">No items found</p>
                                    <p class="text-sm text-slate-500 mt-1">There are no active items in the system</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($itemStocks->count() > 0)
        <div class="p-6 border-t border-slate-200 bg-slate-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-slate-600">
                    <i data-lucide="info" class="w-4 h-4 inline-block mr-1"></i>
                    Stock quantities are calculated from production reports (actual_set_shift)
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-sm">
                        <span class="text-slate-600">Total Stock Across All Items:</span>
                        <span class="ml-2 font-semibold text-slate-900">{{ number_format($itemStocks->sum('total_stock'), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Transfer Modal -->
<div id="transferModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                        <i data-lucide="arrow-right-left" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Transfer Stock</h3>
                </div>
                <button onclick="closeTransferModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="text-center py-8">
                <div class="w-16 h-16 mx-auto rounded-full bg-amber-100 flex items-center justify-center mb-4">
                    <i data-lucide="construction" class="w-8 h-8 text-amber-600"></i>
                </div>
                <h4 class="text-lg font-semibold text-slate-900 mb-2">Coming Soon</h4>
                <p class="text-sm text-slate-600">We are currently working on this part</p>
            </div>
            
            <!-- Modal Footer -->
            <div class="mt-6">
                <button onclick="closeTransferModal()" class="w-full px-4 py-2.5 bg-slate-600 text-white text-sm font-medium rounded-lg hover:bg-slate-700 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Open Transfer Modal
    function openTransferModal() {
        document.getElementById('transferModal').classList.remove('hidden');
        // Re-initialize lucide icons for the modal
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
    
    // Close Transfer Modal
    function closeTransferModal() {
        document.getElementById('transferModal').classList.add('hidden');
    }
    
    // Close modal on outside click
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('transferModal');
        if (event.target === modal) {
            closeTransferModal();
        }
    });
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeTransferModal();
        }
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
