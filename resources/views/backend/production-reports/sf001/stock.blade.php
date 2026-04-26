@extends('backend.layout.app')

@section('title', 'Roll Forming (SF1) Process - Item Stock')

@section('page-title', 'Roll Forming (SF1) Process - Stock Management')

@section('breadcrumb')
    <span class="text-slate-600">Roll Forming (SF1)</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Stock</span>
@endsection

@section('content')
    <div class="p-4">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle mb-4">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                            style="background: linear-gradient(to right, #141d30, #2d3a52);">
                            <i data-lucide="package" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Roll Forming (SF1) Process - Item Wise Stock
                            </h2>
                            <p class="text-xs text-slate-500">View aggregated stock quantities by item from production
                                reports</p>
                        </div>
                    </div>
                    @if($itemStocks->isNotEmpty())
                    <a href="{{ route('admin.production-reports.sf001.stock.export') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700 hover:shadow-lg transition-all hover:scale-105">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        <span>Export to Excel</span>
                    </a>
                    @else
                    <button type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-300 text-slate-500 text-xs font-semibold rounded-lg cursor-not-allowed opacity-70" title="No data available to export" disabled>
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        <span>Export to Excel</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stock Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
            <div class="p-4 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900">Item Stock List</h3>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-500">Total Items:</span>
                        <span class="text-xs font-semibold text-slate-900">{{ $itemStocks->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="border-b border-slate-200"
                        style="background: linear-gradient(to right, #141d30, #2d3a52);">
                        <tr>
                            <th
                                class="px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                #
                            </th>
                            <th
                                class="px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                Item Code
                            </th>
                            <th
                                class="px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                Item Name
                            </th>
                            <th
                                class="px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                Size
                            </th>
                            <th
                                class="px-3 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                Total Production
                            </th>
                            <th
                                class="px-3 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                In Stock
                            </th>
                            <th
                                class="px-3 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                Transferred
                            </th>
                            <th
                                class="px-3 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                Rejected
                            </th>
                            <th
                                class="px-3 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                Ballcage
                            </th>
                            <th
                                class="px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                Last Stock Update
                            </th>
                            <th
                                class="px-3 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($itemStocks as $index => $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-3 py-2.5 text-slate-700">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-3 py-2.5">
                                    <span class="font-medium text-slate-900">{{ $item->code }}</span>
                                </td>
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="box" class="w-3.5 h-3.5 text-slate-400"></i>
                                        <span class="font-medium text-slate-900">{{ $item->name }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5">
                                    <span class="text-slate-600">{{ $item->size }}</span>
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <div
                                        class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-700">
                                        <i data-lucide="package" class="w-3 h-3"></i>
                                        <span class="font-semibold">{{ number_format($item->total_produced_stock, 0) }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <div
                                        class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-amber-50 text-amber-700">
                                        <i data-lucide="hourglass" class="w-3 h-3"></i>
                                        <span class="font-semibold">{{ number_format($item->pending_quantity, 0) }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <div
                                        class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700">
                                        <i data-lucide="check-check" class="w-3 h-3"></i>
                                        <span class="font-semibold">{{ number_format($item->transferred_quantity, 0) }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <div
                                        class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-rose-50 text-rose-700">
                                        <i data-lucide="ban" class="w-3 h-3"></i>
                                        <span class="font-semibold">{{ number_format($item->rejected_quantity, 0) }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    @if($item->has_ballcage)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-700">
                                            <i data-lucide="check" class="w-3 h-3 mr-1"></i> Yes
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-500">
                                            <i data-lucide="minus" class="w-3 h-3 mr-1"></i> No
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center gap-1.5 text-slate-600">
                                        <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                                        <span>{{ $item->last_stock_update ? \Carbon\Carbon::parse($item->last_stock_update)->format('M d, Y h:i A') : 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" onclick="openTransferModal(this)" data-item-id="{{ $item->id }}"
                                            data-item-code="{{ $item->code }}" data-item-name="{{ $item->name }}"
                                            data-item-size="{{ $item->size }}"
                                            data-available-stock="{{ round((float) $item->total_stock) }}"
                                            data-has-ballcage="{{ $item->has_ballcage ?? 0 }}"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-medium text-white rounded-lg transition-all"
                                            style="background: linear-gradient(to right, #141d30, #2d3a52);">
                                            <i data-lucide="arrow-right-left" class="w-3 h-3"></i>
                                            Transfer
                                        </button>
                                        <a href="{{ route('admin.production-reports.sf001.stock.history', $item->id) }}"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-medium text-slate-700 bg-slate-100 border border-slate-300 rounded-lg hover:bg-slate-200 transition-all">
                                            <i data-lucide="history" class="w-3 h-3"></i>
                                            History
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-10 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                            <i data-lucide="package-x" class="w-8 h-8 text-slate-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-slate-900">No items found</p>
                                            <p class="text-xs text-slate-500 mt-1">There are no active items in the system</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($itemStocks->count() > 0)
                <div class="p-4 border-t border-slate-200 bg-slate-50">
                    <div class="flex items-center justify-between">
                        <div class="text-xs text-slate-600">
                            <i data-lucide="info" class="w-3.5 h-3.5 inline-block mr-1"></i>
                            Stock quantities are calculated from production reports (actual_set_shift)
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-xs">
                                <span class="text-slate-600">Total Production:</span>
                                <span
                                    class="ml-2 font-semibold text-slate-900">{{ number_format($itemStocks->sum('total_produced_stock'), 0) }}</span>
                            </div>
                            <div class="text-xs">
                                <span class="text-slate-600">In Stock:</span>
                                <span
                                    class="ml-2 font-semibold text-amber-700">{{ number_format($itemStocks->sum('pending_quantity'), 0) }}</span>
                            </div>
                            <div class="text-xs">
                                <span class="text-slate-600">Transferred:</span>
                                <span
                                    class="ml-2 font-semibold text-blue-700">{{ number_format($itemStocks->sum('transferred_quantity'), 0) }}</span>
                            </div>
                            <div class="text-xs">
                                <span class="text-slate-600">Rejected:</span>
                                <span
                                    class="ml-2 font-semibold text-rose-700">{{ number_format($itemStocks->sum('rejected_quantity'), 0) }}</span>
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
                <form id="transferForm" action="{{ route('admin.production-reports.sf001.stock.transfer') }}" method="POST"
                    class="space-y-4">
                    @csrf

                    <input type="hidden" name="item_id" id="transfer_item_id" value="{{ old('item_id') }}">
                    <input type="hidden" name="date" id="transfer_date" value="{{ old('date') }}">
                    <input type="hidden" name="time" id="transfer_time" value="{{ old('time') }}">
                    <input type="hidden" name="item_code" id="transfer_item_code_hidden" value="{{ old('item_code') }}">
                    <input type="hidden" name="item_name" id="transfer_item_name_hidden" value="{{ old('item_name') }}">
                    <input type="hidden" name="item_size" id="transfer_item_size_hidden" value="{{ old('item_size') }}">
                    <input type="hidden" name="available_quantity" id="transfer_available_quantity_hidden"
                        value="{{ old('available_quantity') }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label id="transfer_assign_sf2_label" for="transfer_assign_sf2" class="block text-sm font-semibold text-slate-700 mb-2">SF2
                                Process <span class="text-rose-500">*</span></label>
                            <select id="transfer_assign_sf2" name="assign_sf2" required
                                class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('assign_sf2') border-rose-500 @enderror">
                                <option value="">Select Process</option>
                                <option value="CED" id="transfer_option_ced" {{ old('assign_sf2') === 'CED' ? 'selected' : '' }}>CED</option>
                                <option value="ZINC" id="transfer_option_zinc" {{ old('assign_sf2') === 'ZINC' ? 'selected' : '' }}>ZINC</option>
                                <option value="PPC" id="transfer_option_ppc" class="hidden" {{ old('assign_sf2') === 'PPC' ? 'selected' : '' }}>PPC</option>
                            </select>
                            @error('assign_sf2')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="transfer_available_quantity"
                                class="block text-sm font-semibold text-slate-700 mb-2">Available Quantity</label>
                            <input type="text" id="transfer_available_quantity" readonly
                                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700"
                                value="{{ old('available_quantity') }}">
                        </div>

                        <div>
                            <label for="transfer_quantity" class="block text-sm font-semibold text-slate-700 mb-2">Quantity
                                to Transfer <span class="text-rose-500">*</span></label>
                            <input type="number" id="transfer_quantity" name="quantity" required min="1" step="1"
                                value="{{ old('quantity') }}"
                                class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('quantity') border-rose-500 @enderror"
                                placeholder="Enter quantity">
                            <p id="transfer_quantity_help" class="mt-1 text-xs text-slate-500"></p>
                            @error('quantity')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="transfer_display_datetime"
                                class="block text-sm font-semibold text-slate-700 mb-2">Date & Time</label>
                            <input type="text" id="transfer_display_datetime" readonly
                                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700">
                        </div>

                        <div>
                            <label for="transfer_item_code" class="block text-sm font-semibold text-slate-700 mb-2">Item
                                Code</label>
                            <input type="text" id="transfer_item_code" readonly
                                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700"
                                value="{{ old('item_code') }}">
                        </div>

                        <div>
                            <label for="transfer_item_name" class="block text-sm font-semibold text-slate-700 mb-2">Item
                                Name</label>
                            <input type="text" id="transfer_item_name" readonly
                                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700"
                                value="{{ old('item_name') }}">
                        </div>

                        <div class="md:col-span-2">
                            <label for="transfer_item_size" class="block text-sm font-semibold text-slate-700 mb-2">Item
                                Size</label>
                            <input type="text" id="transfer_item_size" readonly
                                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700"
                                value="{{ old('item_size') }}">
                        </div>

                        <div class="md:col-span-2">
                            <label for="transfer_remark" class="block text-sm font-semibold text-slate-700 mb-2">Remark
                                (Optional)</label>
                            <textarea id="transfer_remark" name="remark" rows="2"
                                class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('remark') border-rose-500 @enderror"
                                placeholder="Add optional remark...">{{ old('remark') }}</textarea>
                            @error('remark')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-6 flex items-center gap-3">
                        <button type="submit" id="transfer_submit_button"
                            class="flex-1 px-4 py-2.5 text-white text-sm font-medium rounded-lg transition-colors"
                            style="background: linear-gradient(to right, #141d30, #2d3a52);">
                            Save Transfer
                        </button>
                        <button type="button" onclick="closeTransferModal()"
                            class="flex-1 px-4 py-2.5 bg-slate-600 text-white text-sm font-medium rounded-lg hover:bg-slate-700 transition-colors">
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
            const hasBallcage = button.getAttribute('data-has-ballcage') === '1';

            transferState.available = availableStock;

            // Toggle label & options based on ballcage status
            const sf2Label = document.getElementById('transfer_assign_sf2_label');
            const sf2Select = document.getElementById('transfer_assign_sf2');
            const ppcOption = document.getElementById('transfer_option_ppc');
            const cedOption = document.getElementById('transfer_option_ced');
            const zincOption = document.getElementById('transfer_option_zinc');

            if (hasBallcage) {
                // Ballcage = Yes: show PPC label & only PPC option
                sf2Label.innerHTML = 'PPC <span class="text-rose-500">*</span>';
                if (ppcOption) ppcOption.classList.remove('hidden');
                if (cedOption) cedOption.classList.add('hidden');
                if (zincOption) zincOption.classList.add('hidden');
                // Reset selection if CED/ZINC was selected
                if (sf2Select.value === 'CED' || sf2Select.value === 'ZINC') {
                    sf2Select.value = '';
                }
            } else {
                // Ballcage = No: show SF2 Process label with CED/ZINC
                sf2Label.innerHTML = 'SF2 Process <span class="text-rose-500">*</span>';
                if (ppcOption) ppcOption.classList.add('hidden');
                if (cedOption) cedOption.classList.remove('hidden');
                if (zincOption) zincOption.classList.remove('hidden');
                // Reset selection if PPC was selected
                if (sf2Select.value === 'PPC') {
                    sf2Select.value = '';
                }
            }

            document.getElementById('transfer_item_id').value = itemId;
            document.getElementById('transfer_item_code_hidden').value = itemCode;
            document.getElementById('transfer_item_name_hidden').value = itemName;
            document.getElementById('transfer_item_size_hidden').value = itemSize;
            document.getElementById('transfer_available_quantity_hidden').value = Math.round(availableStock);
            document.getElementById('transfer_item_code').value = itemCode;
            document.getElementById('transfer_item_name').value = itemName;
            document.getElementById('transfer_item_size').value = itemSize;
            document.getElementById('transfer_available_quantity').value = Math.round(availableStock);

            const quantityInput = document.getElementById('transfer_quantity');
            quantityInput.max = Math.round(availableStock);
            document.getElementById('transfer_quantity_help').innerText = `Max allowed: ${Math.round(availableStock)}`;

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

        document.getElementById('transferForm').addEventListener('submit', function (event) {
            const quantity = parseFloat(document.getElementById('transfer_quantity').value || '0');

            if (quantity > transferState.available) {
                event.preventDefault();
                alert(`Transfer quantity cannot be greater than available quantity (${Math.round(transferState.available)}).`);
                return;
            }

            // Use the current submit-time date and time.
            setTransferDateTimeNow();

            const submitButton = document.getElementById('transfer_submit_button');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-60', 'cursor-not-allowed');
                submitButton.textContent = 'Saving...';
            }
        });

        // Close modal on outside click
        document.addEventListener('click', function (event) {
            const modal = document.getElementById('transferModal');
            if (event.target === modal) {
                closeTransferModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeTransferModal();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            @if($errors->has('assign_sf2') || $errors->has('quantity') || $errors->has('item_id') || $errors->has('date') || $errors->has('time'))
                document.getElementById('transferModal').classList.remove('hidden');

                const oldAvailable = parseFloat(document.getElementById('transfer_available_quantity_hidden').value || '0');
                transferState.available = oldAvailable;

                document.getElementById('transfer_item_code').value = document.getElementById('transfer_item_code_hidden').value;
                document.getElementById('transfer_item_name').value = document.getElementById('transfer_item_name_hidden').value;
                document.getElementById('transfer_item_size').value = document.getElementById('transfer_item_size_hidden').value;
                document.getElementById('transfer_available_quantity').value = Math.round(oldAvailable);

                const quantityInput = document.getElementById('transfer_quantity');
                quantityInput.max = Math.round(oldAvailable);
                document.getElementById('transfer_quantity_help').innerText = `Max allowed: ${Math.round(oldAvailable)}`;

                const oldDate = document.getElementById('transfer_date').value;
                const oldTime = document.getElementById('transfer_time').value;
                if (oldDate && oldTime) {
                    document.getElementById('transfer_display_datetime').value = `${oldDate} ${oldTime}`;
                }
            @endif
        });
    </script>
@endsection