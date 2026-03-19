@extends('backend.layout.app')

@php
    $currentHour = (int) date('G');
    $defaultShift = ($currentHour >= 8 && $currentHour < 20) ? 'morning' : 'night';
    $hasPersistedShift = old('sf3_shift') !== null || !empty($existingReport->shift ?? null);
@endphp

@section('title', $lineTitle . ' SF3 Production Report - Hourly')

@section('page-title', $lineTitle . ' SF3 Production Report Entry')

@section('breadcrumb')
    <span class="text-slate-600">Production Reports</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">Assemble SF3</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">{{ $lineTitle }}</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Report</span>
@endsection

@section('content')
<div class="p-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $lineTitle }} Production Report - Hourly</h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Item: <span id="selectedItemCode" class="font-medium text-slate-700">{{ $selectedItem->code ?? '-' }}</span> -
                        <span id="selectedItemName" class="font-medium text-slate-700">{{ $selectedItem->name ?? '-' }}</span>
                        (<span id="selectedItemSize" class="font-medium text-slate-700">{{ $selectedItem->size ?? '-' }}</span>)
                    </p>
                </div>
                <a href="{{ route('admin.production-reports.sf003.process', ['line' => $requestedLine, 'tab' => 'production']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors font-medium">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <form id="productionReportForm" method="POST" action="{{ route('admin.production-reports.sf003.production-report.store', ['line' => $requestedLine]) }}">
                @csrf
                <input type="hidden" id="report_id" name="report_id" value="{{ isset($existingReport) && $existingReport ? \Illuminate\Support\Facades\Crypt::encryptString((string) $existingReport->id) : '' }}">

                <div class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="item_selector" class="block text-sm font-medium text-slate-700 mb-2">Select Item</label>
                        <select id="item_selector" name="item_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="" disabled {{ old('item_id', $existingReport->item_id ?? '') === '' ? 'selected' : '' }}>-- Select Item --</option>
                            @foreach($sf3Items as $item)
                                <option
                                    value="{{ $item->id }}"
                                    data-item-id="{{ $item->id }}"
                                    data-item-code="{{ $item->code }}"
                                    data-item-name="{{ $item->name }}"
                                    data-item-size="{{ $item->size }}"
                                    {{ (string) old('item_id', $existingReport->item_id ?? '') === (string) $item->id ? 'selected' : '' }}
                                >
                                    {{ $item->code }} - {{ $item->name }} ({{ $item->size }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="sf3_report_date" class="block text-sm font-medium text-slate-700 mb-2">Report Date</label>
                        <input type="date" id="sf3_report_date" name="sf3_report_date" value="{{ old('sf3_report_date', $existingReport->report_date ?? date('Y-m-d')) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="sf3_shift" class="block text-sm font-medium text-slate-700 mb-2">Shift</label>
                        <select id="sf3_shift" name="sf3_shift" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="morning" {{ old('sf3_shift', $existingReport->shift ?? $defaultShift) === 'morning' ? 'selected' : '' }}>Morning</option>
                            <option value="night" {{ old('sf3_shift', $existingReport->shift ?? $defaultShift) === 'night' ? 'selected' : '' }}>Night</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-[1500px] w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-100">
                                <th class="border border-slate-300 px-3 py-2 text-left font-semibold text-slate-900">Type</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">Total Set/Shift</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">Set/Hour</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label">8AM to 9AM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label">9AM to 10AM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label">10AM to 11AM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label">11AM to 12PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label">12PM to 1PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label">1PM to 2PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label">2PM to 3PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label">3PM to 4PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label">4PM to 5PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label">5PM to 6PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label">6PM to 7PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label">7PM to 8PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">Actual / Set / Shift</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">Manpower / Workman</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">Staff Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover:bg-slate-50">
                                <td class="border border-slate-300 px-3 py-2 font-medium text-slate-900">{{ $lineTitle }}</td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_total_set_shift" value="{{ old('sf3_total_set_shift', isset($existingReport) ? (int) $existingReport->total_set_shift : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_set_per_hour" value="{{ old('sf3_set_per_hour', isset($existingReport) ? number_format((float) $existingReport->set_per_hour, 2, '.', '') : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm bg-slate-50" placeholder="-" step="0.01" min="0" readonly></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_hour_8_9" value="{{ old('sf3_hour_8_9', isset($existingReport) ? (int) $existingReport->hour_8_9 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_hour_9_10" value="{{ old('sf3_hour_9_10', isset($existingReport) ? (int) $existingReport->hour_9_10 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_hour_10_11" value="{{ old('sf3_hour_10_11', isset($existingReport) ? (int) $existingReport->hour_10_11 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_hour_11_12" value="{{ old('sf3_hour_11_12', isset($existingReport) ? (int) $existingReport->hour_11_12 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_hour_12_1" value="{{ old('sf3_hour_12_1', isset($existingReport) ? (int) $existingReport->hour_12_1 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_hour_1_2" value="{{ old('sf3_hour_1_2', isset($existingReport) ? (int) $existingReport->hour_1_2 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_hour_2_3" value="{{ old('sf3_hour_2_3', isset($existingReport) ? (int) $existingReport->hour_2_3 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_hour_3_4" value="{{ old('sf3_hour_3_4', isset($existingReport) ? (int) $existingReport->hour_3_4 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_hour_4_5" value="{{ old('sf3_hour_4_5', isset($existingReport) ? (int) $existingReport->hour_4_5 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_hour_5_6" value="{{ old('sf3_hour_5_6', isset($existingReport) ? (int) $existingReport->hour_5_6 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_hour_6_7" value="{{ old('sf3_hour_6_7', isset($existingReport) ? (int) $existingReport->hour_6_7 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_hour_7_8" value="{{ old('sf3_hour_7_8', isset($existingReport) ? (int) $existingReport->hour_7_8 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_actual_set_shift" value="{{ old('sf3_actual_set_shift', isset($existingReport) ? (int) $existingReport->actual_set_shift : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm bg-slate-50" placeholder="-" step="1" min="0" readonly></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_manpower" value="{{ old('sf3_manpower', isset($existingReport) ? (int) $existingReport->manpower_workman : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="sf3_staff_count" value="{{ old('sf3_staff_count', isset($existingReport) ? (int) $existingReport->staff_count : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table id="mergedStockTable" class="w-full border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-100">
                                    <th class="border border-slate-300 px-3 py-2 text-left font-semibold text-slate-900">Product Code</th>
                                    <th class="border border-slate-300 px-3 py-2 text-left font-semibold text-slate-900">Product Name</th>
                                    <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">Category</th>
                                    <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">Quantity Required</th>
                                    <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">Stock Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="mergedTableBody">
                                <tr class="text-center text-slate-500">
                                    <td colspan="5" class="py-4">Select an item to view stock details</td>
                                </tr>
                            </tbody>
                            <tbody id="mergedTableLoader" style="display: none;">
                                <tr class="text-center">
                                    <td colspan="5" class="py-6">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-5 h-5 rounded-full border-2 border-blue-500 border-t-transparent animate-spin"></div>
                                            <span class="text-slate-600 font-medium">Loading stock details...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div id="stockCapacityNote" class="hidden mt-4 rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-800"></div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.production-reports.sf003.process', ['line' => $requestedLine, 'tab' => 'production']) }}" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors font-medium">Cancel</a>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-medium">
                        {{ isset($existingReport) && $existingReport ? 'Update Report' : 'Save Report' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    #productionReportForm input[type="number"]::-webkit-outer-spin-button,
    #productionReportForm input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    #productionReportForm input[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('productionReportForm');
    const saveButton = form ? form.querySelector('button[type="submit"]') : null;
    const shiftSelect = document.getElementById('sf3_shift');
    const hourLabels = document.querySelectorAll('.hour-label');
    const itemSelector = document.getElementById('item_selector');
    const selectedItemCode = document.getElementById('selectedItemCode');
    const selectedItemName = document.getElementById('selectedItemName');
    const selectedItemSize = document.getElementById('selectedItemSize');
    const totalSetShiftInput = form.querySelector('input[name="sf3_total_set_shift"]');
    const setPerHourInput = form.querySelector('input[name="sf3_set_per_hour"]');
    const actualSetShiftInput = form.querySelector('input[name="sf3_actual_set_shift"]');
    const stockCapacityNote = document.getElementById('stockCapacityNote');
    const hourlyInputs = [
        'sf3_hour_8_9', 'sf3_hour_9_10', 'sf3_hour_10_11', 'sf3_hour_11_12',
        'sf3_hour_12_1', 'sf3_hour_1_2', 'sf3_hour_2_3', 'sf3_hour_3_4',
        'sf3_hour_4_5', 'sf3_hour_5_6', 'sf3_hour_6_7', 'sf3_hour_7_8'
    ].map(function (name) {
        return form.querySelector('input[name="' + name + '"]');
    }).filter(Boolean);
    let limitWarningTimeout;
    let isSaving = false;
    let requiredProductsData = [];
    let stockRowsData = [];

    if (!form) return;

    function applySaveButtonState() {
        if (!saveButton) return;

        const mustDisable = isSaving;
        saveButton.disabled = mustDisable;

        if (mustDisable) {
            saveButton.classList.add('opacity-60', 'cursor-not-allowed');
        } else {
            saveButton.classList.remove('opacity-60', 'cursor-not-allowed');
        }
    }

    function updateStockCapabilityNote(message) {
        if (!stockCapacityNote) return;

        if (!message) {
            stockCapacityNote.textContent = '';
            stockCapacityNote.classList.add('hidden');
            return;
        }

        stockCapacityNote.textContent = message;
        stockCapacityNote.classList.remove('hidden');
    }

    function evaluateStockCapability() {
        const selectedValue = itemSelector ? itemSelector.value : '';
        const totalSetShift = Math.max(parseFloat(totalSetShiftInput ? totalSetShiftInput.value : '0') || 0, 0);

        if (!selectedValue || totalSetShift <= 0 || requiredProductsData.length === 0) {
            updateStockCapabilityNote('');
            applySaveButtonState();
            return;
        }

        const stockByProduct = {};
        stockRowsData.forEach(function (row) {
            const productId = parseInt(row.item_id || 0, 10);
            const quantity = Math.max(parseFloat(row.quantity || 0) || 0, 0);

            if (!productId) return;
            stockByProduct[productId] = (stockByProduct[productId] || 0) + quantity;
        });

        const shortProducts = requiredProductsData
            .map(function (product) {
                const productId = parseInt(product.product || 0, 10);
                const requiredPerSet = Math.max(parseFloat(product.quantity || 0) || 0, 0);
                const requiredTotal = requiredPerSet * totalSetShift;
                const inStock = stockByProduct[productId] || 0;

                return {
                    name: product.product_code || product.product_name || 'Product',
                    required: requiredTotal,
                    stock: inStock,
                };
            })
            .filter(function (row) {
                return row.required > row.stock;
            });

        if (shortProducts.length === 0) {
            updateStockCapabilityNote('');
            applySaveButtonState();
            return;
        }

        const summary = shortProducts
            .slice(0, 3)
            .map(function (row) {
                return row.name + ' (Required: ' + Math.round(row.required) + ', In Stock: ' + Math.round(row.stock) + ')';
            })
            .join(', ');

        updateStockCapabilityNote('Stock preview: required quantity is higher than current stock for ' + summary + '.');
        applySaveButtonState();
    }

    function showLimitWarning(message) {
        let warning = document.getElementById('sf3LimitWarning');

        if (!warning) {
            warning = document.createElement('div');
            warning.id = 'sf3LimitWarning';
            warning.className = 'fixed top-5 right-5 z-[60] px-4 py-2 rounded-lg bg-amber-100 border border-amber-300 text-amber-800 text-sm font-medium shadow-lg';
            document.body.appendChild(warning);
        }

        warning.textContent = message;
        warning.classList.remove('hidden');

        clearTimeout(limitWarningTimeout);
        limitWarningTimeout = setTimeout(function () {
            warning.classList.add('hidden');
        }, 2200);
    }

    function validateActualSetShift() {
        if (!actualSetShiftInput) return;

        const totalSetShift = Math.max(parseFloat(totalSetShiftInput ? totalSetShiftInput.value : '0') || 0, 0);
        const actualSetShift = Math.max(parseFloat(actualSetShiftInput.value || '0') || 0, 0);

        if (actualSetShift > totalSetShift) {
            showLimitWarning('Value cannot be greater than Total Set/Shift (' + totalSetShift + ').');
        }
    }

    function clampToSelectedQuantity(input) {
        if (!input) return;

        const totalSetShift = Math.max(parseFloat(totalSetShiftInput ? totalSetShiftInput.value : '0') || 0, 0);
        input.max = String(totalSetShift);

        const currentValue = parseFloat(input.value || '0');
        if (!Number.isNaN(currentValue) && currentValue > totalSetShift) {
            input.value = String(totalSetShift);
            showLimitWarning('Value cannot be greater than Total Set/Shift (' + totalSetShift + ').');
        }
    }

    function validateHourlyInputs() {
        // Calculate sum of all hourly inputs
        let hourlySum = 0;
        hourlyInputs.forEach(function (input) {
            if (input) {
                hourlySum += Math.max(parseFloat(input.value || '0') || 0, 0);
            }
        });

        const totalSetShift = Math.max(parseFloat(totalSetShiftInput ? totalSetShiftInput.value : '0') || 0, 0);

        // Update actual_set_shift with the sum
        if (actualSetShiftInput) {
            actualSetShiftInput.value = String(Math.round(hourlySum));
        }

        // Check if hourly sum exceeds Total Set/Shift
        if (hourlySum > totalSetShift) {
            showLimitWarning('Hourly total (' + Math.round(hourlySum) + ') cannot exceed Total Set/Shift (' + totalSetShift + ').');
        }
    }

    function normalizeWholeNumber(input) {
        if (!input || input.value === '') return;

        const numericValue = parseFloat(input.value);
        if (Number.isNaN(numericValue)) return;

        input.value = String(Math.round(Math.max(numericValue, 0)));
    }

    function updateSetPerHour() {
        if (!totalSetShiftInput || !setPerHourInput) return;

        normalizeWholeNumber(totalSetShiftInput);
        const totalSetShift = Math.max(parseFloat(totalSetShiftInput.value || '0') || 0, 0);
        setPerHourInput.value = (totalSetShift / 12).toFixed(2);
    }

    function updateActualSetShiftFromHours() {
        if (!actualSetShiftInput) return;

        let totalHours = 0;
        hourlyInputs.forEach(function (input) {
            const value = Math.max(parseFloat(input.value || '0') || 0, 0);
            totalHours += value;
        });

        actualSetShiftInput.value = String(Math.round(totalHours));
        validateActualSetShift();
    }

    function updateSelectedItemMeta() {
        if (!itemSelector) return;
        const selectedOption = itemSelector.options[itemSelector.selectedIndex];
        if (!selectedOption) return;

        const selectedValue = selectedOption.value;
        
        // If no item selected, reset everything
        if (!selectedValue) {
            if (selectedItemCode) selectedItemCode.textContent = '-';
            if (selectedItemName) selectedItemName.textContent = '-';
            if (selectedItemSize) selectedItemSize.textContent = '-';
            const mergedTbody = document.getElementById('mergedTableBody');
            if (mergedTbody) {
                mergedTbody.innerHTML = '<tr class="text-center text-slate-500"><td colspan="5" class="py-4">Select an item to view stock details</td></tr>';
            }
            requiredProductsData = [];
            stockRowsData = [];
            evaluateStockCapability();
            return;
        }
        if (selectedItemCode) {
            selectedItemCode.textContent = selectedOption.getAttribute('data-item-code') || '-';
        }
        if (selectedItemName) {
            selectedItemName.textContent = selectedOption.getAttribute('data-item-name') || '-';
        }
        if (selectedItemSize) {
            selectedItemSize.textContent = selectedOption.getAttribute('data-item-size') || '-';
        }

        clampToSelectedQuantity(actualSetShiftInput);
        updateSetPerHour();

        requiredProductsData = [];
        stockRowsData = [];
        evaluateStockCapability();

        // Fetch products for the selected item
        const itemId = selectedOption.getAttribute('data-item-id');
        if (itemId) {
            fetchItemProducts(itemId);
            fetchItemProductsStock(itemId);
        }
    }

    function fetchItemProducts(itemId) {
        const url = '{{ route("admin.production-reports.sf003.item-products") }}?item_id=' + itemId;
        const tbody = document.getElementById('mergedTableBody');
        const loader = document.getElementById('mergedTableLoader');

        // Show loader
        if (tbody) tbody.style.display = 'none';
        if (loader) loader.style.display = '';

        fetch(url)
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                const products = data.products || [];
                requiredProductsData = products;
                
                if (!tbody) return;

                // Hide loader
                if (loader) loader.style.display = 'none';
                tbody.style.display = '';

                if (products.length === 0) {
                    tbody.innerHTML = '<tr class="text-center text-slate-500"><td colspan="5" class="py-4">No required stock found for this item</td></tr>';
                    evaluateStockCapability();
                    return;
                }

                // Render merged table with both required and stock data
                renderMergedTable();
                evaluateStockCapability();
            })
            .catch(function (error) {
                console.error('Error fetching products:', error);
                requiredProductsData = [];
                
                // Hide loader
                if (loader) loader.style.display = 'none';
                if (tbody) {
                    tbody.style.display = '';
                    tbody.innerHTML = '<tr class="text-center text-red-500"><td colspan="5" class="py-4">Error loading required stock</td></tr>';
                }

                evaluateStockCapability();
            });
    }

    function renderMergedTable() {
        const tbody = document.getElementById('mergedTableBody');
        if (!tbody) return;

        // Build a map of stock quantities by product (item_id)
        const stockByProduct = {};
        stockRowsData.forEach(function (row) {
            const productId = parseInt(row.item_id || 0, 10);
            const quantity = Math.max(parseFloat(row.quantity || 0) || 0, 0);

            if (productId > 0) {
                stockByProduct[productId] = (stockByProduct[productId] || 0) + quantity;
            }
        });

        if (requiredProductsData.length === 0) {
            tbody.innerHTML = '<tr class="text-center text-slate-500"><td colspan="5" class="py-4">No required stock found for this item</td></tr>';
            return;
        }

        // Render merged table
        tbody.innerHTML = requiredProductsData.map(function (product) {
            const productId = parseInt(product.product || 0, 10);
            const stockQuantity = stockByProduct[productId] || 0;
            return '<tr class="hover:bg-slate-50">' +
                '<td class="border border-slate-300 px-3 py-2">' + (product.product_code || '-') + '</td>' +
                '<td class="border border-slate-300 px-3 py-2">' + (product.product_name || '-') + '</td>' +
                '<td class="border border-slate-300 px-3 py-2 text-center">' + (product.product_category || '-') + '</td>' +
                '<td class="border border-slate-300 px-3 py-2 text-center">' + Math.round(product.quantity || 0) + '</td>' +
                '<td class="border border-slate-300 px-3 py-2 text-center">' + Math.round(stockQuantity) + '</td>' +
                '</tr>';
        }).join('');
    }

    function updateShiftLabels(shift) {
        const labels = shift === 'night'
            ? ['8PM to 9PM', '9PM to 10PM', '10PM to 11PM', '11PM to 12AM', '12AM to 1AM', '1AM to 2AM', '2AM to 3AM', '3AM to 4AM', '4AM to 5AM', '5AM to 6AM', '6AM to 7AM', '7AM to 8AM']
            : ['8AM to 9AM', '9AM to 10AM', '10AM to 11AM', '11AM to 12PM', '12PM to 1PM', '1PM to 2PM', '2PM to 3PM', '3PM to 4PM', '4PM to 5PM', '5PM to 6PM', '6PM to 7PM', '7PM to 8PM'];

        hourLabels.forEach(function (label, index) {
            if (labels[index]) {
                label.textContent = labels[index];
            }
        });
    }

    function detectShiftByCurrentTime() {
        const hour = new Date().getHours();
        return (hour >= 8 && hour < 20) ? 'morning' : 'night';
    }

    if (shiftSelect) {
        const hasPersistedShift = @json($hasPersistedShift);
        if (!hasPersistedShift) {
            shiftSelect.value = detectShiftByCurrentTime();
        }

        let previousShift = shiftSelect.value || 'morning';
        let ignoreShiftChange = false;

        updateShiftLabels(previousShift);

        shiftSelect.addEventListener('change', async function () {
            if (ignoreShiftChange) return;

            const nextShift = this.value || 'morning';
            if (nextShift === previousShift) return;

            let confirmed = false;

            if (typeof Swal !== 'undefined') {
                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Change Shift?',
                    text: 'Are you sure you want to change the shift?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, change it',
                    cancelButtonText: 'No',
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#64748b',
                });
                confirmed = !!result.isConfirmed;
            } else {
                confirmed = window.confirm('Are you sure you want to change the shift?');
            }

            if (!confirmed) {
                ignoreShiftChange = true;
                this.value = previousShift;
                updateShiftLabels(previousShift);
                ignoreShiftChange = false;
                return;
            }

            previousShift = nextShift;
            updateShiftLabels(nextShift);
        });
    }

    // Initialize with default state
    if (selectedItemCode) selectedItemCode.textContent = '-';
    if (selectedItemName) selectedItemName.textContent = '-';
    if (selectedItemSize) selectedItemSize.textContent = '-';

    if (itemSelector) {
        itemSelector.addEventListener('change', updateSelectedItemMeta);
    }

    if (totalSetShiftInput) {
        totalSetShiftInput.addEventListener('input', function () {
            normalizeWholeNumber(totalSetShiftInput);
            updateSetPerHour();
            evaluateStockCapability();
        });

        totalSetShiftInput.addEventListener('blur', function () {
            normalizeWholeNumber(totalSetShiftInput);
            updateSetPerHour();
            evaluateStockCapability();
        });
    }

    if (actualSetShiftInput) {
        actualSetShiftInput.addEventListener('input', function () {
            normalizeWholeNumber(actualSetShiftInput);
            validateActualSetShift();
        });

        actualSetShiftInput.addEventListener('blur', function () {
            normalizeWholeNumber(actualSetShiftInput);
            validateActualSetShift();
        });
    }

    form.querySelectorAll('input[type="number"]').forEach(function (input) {
        if (input === setPerHourInput) return;

        input.addEventListener('blur', function () {
            normalizeWholeNumber(input);
            if (input === actualSetShiftInput) {
                validateActualSetShift();
            }
        });
    });

    hourlyInputs.forEach(function (input) {
        input.addEventListener('input', function () {
            normalizeWholeNumber(input);
            validateHourlyInputs();
        });

        input.addEventListener('blur', function () {
            normalizeWholeNumber(input);
            validateHourlyInputs();
        });
    });

    validateHourlyInputs();

    function showSubmitError(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: message,
                confirmButtonColor: '#dc2626',
            });
            return;
        }

        alert(message);
    }

    function showSubmitSuccess(message) {
        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                icon: 'success',
                title: 'Saved',
                text: message,
                confirmButtonColor: '#16a34a',
            });
        }

        return Promise.resolve();
    }

    function fetchItemProductsStock(itemId) {
        const url = '{{ route("admin.production-reports.sf003.item-products-stock") }}?item_id=' + itemId + '&line_code={{ $lineCode }}';
        const tbody = document.getElementById('mergedTableBody');
        const loader = document.getElementById('mergedTableLoader');

        if (tbody) tbody.style.display = 'none';
        if (loader) loader.style.display = '';

        fetch(url)
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                const rows = data.products || [];
                const requiredOrderMap = {};

                requiredProductsData.forEach(function (product, index) {
                    const productId = parseInt(product.product || 0, 10);
                    if (productId > 0) {
                        requiredOrderMap[productId] = index;
                    }
                });

                rows.sort(function (a, b) {
                    const aId = parseInt(a.item_id || 0, 10);
                    const bId = parseInt(b.item_id || 0, 10);
                    const aOrder = Object.prototype.hasOwnProperty.call(requiredOrderMap, aId) ? requiredOrderMap[aId] : Number.MAX_SAFE_INTEGER;
                    const bOrder = Object.prototype.hasOwnProperty.call(requiredOrderMap, bId) ? requiredOrderMap[bId] : Number.MAX_SAFE_INTEGER;

                    if (aOrder !== bOrder) {
                        return aOrder - bOrder;
                    }

                    return String(a.item_code || '').localeCompare(String(b.item_code || ''));
                });

                stockRowsData = rows;

                if (!tbody) return;

                if (loader) loader.style.display = 'none';
                tbody.style.display = '';

                if (rows.length === 0) {
                    // Still show required stock even if no stock transfers
                    if (requiredProductsData.length === 0) {
                        tbody.innerHTML = '<tr class="text-center text-slate-500"><td colspan="5" class="py-4">No data found</td></tr>';
                    } else {
                        renderMergedTable();
                    }
                    evaluateStockCapability();
                    return;
                }

                // Render merged table with stock data included
                renderMergedTable();
                evaluateStockCapability();
            })
            .catch(function (error) {
                console.error('Error fetching in-stock transfers:', error);
                stockRowsData = [];
                if (loader) loader.style.display = 'none';
                if (tbody) {
                    tbody.style.display = '';
                    if (requiredProductsData.length === 0) {
                        tbody.innerHTML = '<tr class="text-center text-red-500"><td colspan="5" class="py-4">Error loading data</td></tr>';
                    } else {
                        renderMergedTable();
                    }
                }

                evaluateStockCapability();
            });
    }

    form.addEventListener('submit', async function (event) {
        const totalSetShift = Math.max(parseFloat(totalSetShiftInput ? totalSetShiftInput.value : '0') || 0, 0);
        const actualSetShiftValue = Math.max(parseFloat(actualSetShiftInput ? actualSetShiftInput.value : '0') || 0, 0);

        evaluateStockCapability();

        if (actualSetShiftValue > totalSetShift) {
            event.preventDefault();
            showSubmitError('Actual Set/Shift (' + Math.round(actualSetShiftValue) + ') cannot be greater than Total Set/Shift (' + totalSetShift + ').');
            if (actualSetShiftInput) actualSetShiftInput.focus();
            return;
        }

        event.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        if (!submitBtn) return;

        const defaultSubmitHtml = submitBtn.innerHTML;
        isSaving = true;
        applySaveButtonState();
        submitBtn.innerHTML = '<span class="flex items-center gap-2"><i data-lucide="loader" class="w-4 h-4 animate-spin"></i>Saving...</span>';

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            const data = await response.json().catch(function () {
                return {};
            });

            if (!response.ok) {
                const validationErrors = data.errors || {};
                const firstFieldKey = Object.keys(validationErrors)[0];
                const firstFieldError = firstFieldKey ? validationErrors[firstFieldKey][0] : null;
                const message = firstFieldError || data.message || 'Unable to save production report. Please check your input.';
                showSubmitError(message);
                return;
            }

            await showSubmitSuccess(data.message || 'Production report saved successfully.');
            window.location.href = data.redirect_url || '{{ route('admin.production-reports.sf003.process', ['line' => $requestedLine, 'tab' => 'production']) }}';
        } catch (error) {
            showSubmitError('Network error while saving. Please try again.');
        } finally {
            isSaving = false;
            applySaveButtonState();
            submitBtn.innerHTML = defaultSubmitHtml;
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    });

    if (itemSelector && itemSelector.value) {
        updateSelectedItemMeta();
    }

    applySaveButtonState();
});

</script>
@endpush
