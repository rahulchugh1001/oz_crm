@extends('backend.layout.app')

@section('title', 'Roll Forming (SF1) Process - Item Stock')

@section('page-title', 'Roll Forming (SF1) Process - Stock Management')

@section('breadcrumb')
    <span class="text-slate-600">Roll Forming (SF1)</span>
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
                        <h2 class="text-lg font-bold text-slate-900">Roll Forming (SF1) Process - Item Wise Stock</h2>
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
            <table class="w-full text-[13px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">
                            #
                        </th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">
                            Item Code
                        </th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">
                            Item Name
                        </th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">
                            Size
                        </th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">
                            Total Quantity
                        </th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">
                            Pending Quantity
                        </th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">
                            Transferred Quantity
                        </th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">
                            Last Stock Update
                        </th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($itemStocks as $index => $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-700">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-slate-900">{{ $item->code }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i data-lucide="box" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span class="font-medium text-slate-900">{{ $item->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-slate-600">{{ $item->size }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700">
                                <i data-lucide="package" class="w-3.5 h-3.5"></i>
                                <span class="font-semibold">{{ number_format($item->total_produced_stock, 2) }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700">
                                <i data-lucide="hourglass" class="w-3.5 h-3.5"></i>
                                <span class="font-semibold">{{ number_format($item->pending_quantity, 2) }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700">
                                <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
                                <span class="font-semibold">{{ number_format($item->transferred_quantity, 2) }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5 text-slate-600">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>{{ $item->last_stock_update ? \Carbon\Carbon::parse($item->last_stock_update)->format('M d, Y h:i A') : 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    type="button"
                                    onclick="openTransferModal(this)"
                                    data-item-id="{{ $item->id }}"
                                    data-item-code="{{ $item->code }}"
                                    data-item-name="{{ $item->name }}"
                                    data-item-size="{{ $item->size }}"
                                    data-available-stock="{{ number_format((float) $item->total_stock, 2, '.', '') }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all"
                                >
                                    <i data-lucide="arrow-right-left" class="w-3 h-3"></i>
                                    Transfer
                                </button>
                                <a href="{{ route('admin.production-reports.sf001.stock.history', $item->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium text-slate-700 bg-slate-100 border border-slate-300 rounded-lg hover:bg-slate-200 transition-all">
                                    <i data-lucide="history" class="w-3 h-3"></i>
                                    History
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
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
                        <span class="text-slate-600">Total Quantity:</span>
                        <span class="ml-2 font-semibold text-slate-900">{{ number_format($itemStocks->sum('total_produced_stock'), 2) }}</span>
                    </div>
                    <div class="text-sm">
                        <span class="text-slate-600">Pending Quantity:</span>
                        <span class="ml-2 font-semibold text-amber-700">{{ number_format($itemStocks->sum('pending_quantity'), 2) }}</span>
                    </div>
                    <div class="text-sm">
                        <span class="text-slate-600">Transferred Quantity:</span>
                        <span class="ml-2 font-semibold text-blue-700">{{ number_format($itemStocks->sum('transferred_quantity'), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Transfer Modal -->
<div id="transferModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-2xl bg-white">
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
            <form id="transferForm" action="{{ route('admin.production-reports.sf001.stock.transfer') }}" method="POST" class="space-y-4">
                @csrf

                <input type="hidden" name="item_id" id="transfer_item_id" value="{{ old('item_id') }}">
                <input type="hidden" name="date" id="transfer_date" value="{{ old('date') }}">
                <input type="hidden" name="time" id="transfer_time" value="{{ old('time') }}">
                <input type="hidden" name="item_code" id="transfer_item_code_hidden" value="{{ old('item_code') }}">
                <input type="hidden" name="item_name" id="transfer_item_name_hidden" value="{{ old('item_name') }}">
                <input type="hidden" name="item_size" id="transfer_item_size_hidden" value="{{ old('item_size') }}">
                <input type="hidden" name="available_quantity" id="transfer_available_quantity_hidden" value="{{ old('available_quantity') }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="transfer_assign_role" class="block text-sm font-semibold text-slate-700 mb-2">Assign Role <span class="text-rose-500">*</span></label>
                        <select id="transfer_assign_role" name="assign_role" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('assign_role') border-rose-500 @enderror">
                            <option value="">Select Role</option>
                            <option value="SF002" {{ old('assign_role') === 'SF002' ? 'selected' : '' }}>CED & Zinc (SF2)</option>
                            <option value="SF003" {{ old('assign_role') === 'SF003' ? 'selected' : '' }}>Assembly (SF3)</option>
                        </select>
                        @error('assign_role')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="transfer_available_quantity" class="block text-sm font-semibold text-slate-700 mb-2">Available Quantity</label>
                        <input type="text" id="transfer_available_quantity" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700" value="{{ old('available_quantity') }}">
                    </div>

                    <div>
                        <label for="transfer_quantity" class="block text-sm font-semibold text-slate-700 mb-2">Quantity to Transfer <span class="text-rose-500">*</span></label>
                        <input type="number" id="transfer_quantity" name="quantity" required min="0.01" step="0.01" value="{{ old('quantity') }}" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('quantity') border-rose-500 @enderror" placeholder="Enter quantity">
                        <p id="transfer_quantity_help" class="mt-1 text-xs text-slate-500"></p>
                        @error('quantity')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="transfer_display_datetime" class="block text-sm font-semibold text-slate-700 mb-2">Date & Time</label>
                        <input type="text" id="transfer_display_datetime" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700">
                    </div>

                    <div>
                        <label for="transfer_item_code" class="block text-sm font-semibold text-slate-700 mb-2">Item Code</label>
                        <input type="text" id="transfer_item_code" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700" value="{{ old('item_code') }}">
                    </div>

                    <div>
                        <label for="transfer_item_name" class="block text-sm font-semibold text-slate-700 mb-2">Item Name</label>
                        <input type="text" id="transfer_item_name" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700" value="{{ old('item_name') }}">
                    </div>

                    <div class="md:col-span-2">
                        <label for="transfer_item_size" class="block text-sm font-semibold text-slate-700 mb-2">Item Size</label>
                        <input type="text" id="transfer_item_size" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700" value="{{ old('item_size') }}">
                    </div>

                    <div class="md:col-span-2">
                        <label for="transfer_remark" class="block text-sm font-semibold text-slate-700 mb-2">Remark (Optional)</label>
                        <textarea id="transfer_remark" name="remark" rows="2" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('remark') border-rose-500 @enderror" placeholder="Add optional remark...">{{ old('remark') }}</textarea>
                        @error('remark')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Save Transfer
                    </button>
                    <button type="button" onclick="closeTransferModal()" class="flex-1 px-4 py-2.5 bg-slate-600 text-white text-sm font-medium rounded-lg hover:bg-slate-700 transition-colors">
                        Close
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script>
    const transferState = {
        available: 0,
    };

    function formatServerDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function formatServerTime(date) {
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');
        return `${hours}:${minutes}:${seconds}`;
    }

    function formatDisplayDateTime(date) {
        return date.toLocaleString('en-IN', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true,
        });
    }

    function setTransferDateTimeNow() {
        const now = new Date();
        document.getElementById('transfer_date').value = formatServerDate(now);
        document.getElementById('transfer_time').value = formatServerTime(now);
        document.getElementById('transfer_display_datetime').value = formatDisplayDateTime(now);
    }

    // Open Transfer Modal
    function openTransferModal(button) {
        const itemId = button.getAttribute('data-item-id');
        const itemCode = button.getAttribute('data-item-code');
        const itemName = button.getAttribute('data-item-name');
        const itemSize = button.getAttribute('data-item-size');
        const availableStock = parseFloat(button.getAttribute('data-available-stock') || '0');

        transferState.available = availableStock;

        document.getElementById('transfer_item_id').value = itemId;
        document.getElementById('transfer_item_code_hidden').value = itemCode;
        document.getElementById('transfer_item_name_hidden').value = itemName;
        document.getElementById('transfer_item_size_hidden').value = itemSize;
        document.getElementById('transfer_available_quantity_hidden').value = availableStock.toFixed(2);
        document.getElementById('transfer_item_code').value = itemCode;
        document.getElementById('transfer_item_name').value = itemName;
        document.getElementById('transfer_item_size').value = itemSize;
        document.getElementById('transfer_available_quantity').value = availableStock.toFixed(2);

        const quantityInput = document.getElementById('transfer_quantity');
        quantityInput.max = availableStock.toFixed(2);
        document.getElementById('transfer_quantity_help').innerText = `Max allowed: ${availableStock.toFixed(2)}`;

        setTransferDateTimeNow();
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

    document.getElementById('transferForm').addEventListener('submit', function(event) {
        const quantity = parseFloat(document.getElementById('transfer_quantity').value || '0');

        if (quantity > transferState.available) {
            event.preventDefault();
            alert(`Transfer quantity cannot be greater than available quantity (${transferState.available.toFixed(2)}).`);
            return;
        }

        // Use the current submit-time date and time.
        setTransferDateTimeNow();
    });
    
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

        @if($errors->has('assign_role') || $errors->has('quantity') || $errors->has('item_id') || $errors->has('date') || $errors->has('time'))
        document.getElementById('transferModal').classList.remove('hidden');

        const oldAvailable = parseFloat(document.getElementById('transfer_available_quantity_hidden').value || '0');
        transferState.available = oldAvailable;

        document.getElementById('transfer_item_code').value = document.getElementById('transfer_item_code_hidden').value;
        document.getElementById('transfer_item_name').value = document.getElementById('transfer_item_name_hidden').value;
        document.getElementById('transfer_item_size').value = document.getElementById('transfer_item_size_hidden').value;
        document.getElementById('transfer_available_quantity').value = oldAvailable.toFixed(2);

        const quantityInput = document.getElementById('transfer_quantity');
        quantityInput.max = oldAvailable.toFixed(2);
        document.getElementById('transfer_quantity_help').innerText = `Max allowed: ${oldAvailable.toFixed(2)}`;

        const oldDate = document.getElementById('transfer_date').value;
        const oldTime = document.getElementById('transfer_time').value;
        if (oldDate && oldTime) {
            document.getElementById('transfer_display_datetime').value = `${oldDate} ${oldTime}`;
        }
        @endif
    });
</script>
@endsection
