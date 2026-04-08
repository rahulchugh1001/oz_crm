@extends('backend.layout.app')

@php
    $sf2Type = strtolower((string) request()->query('type', 'ced'));
    $sf2Prefix = $sf2Type === 'zinc' ? 'zinc' : 'ced';
    $sf2Label = strtoupper($sf2Prefix);
    $currentHour = (int) date('G');
    $defaultShift = ($currentHour >= 8 && $currentHour < 20) ? 'morning' : 'night';
    $hasPersistedShift = old($sf2Prefix . '_shift') !== null || !empty($existingReport->shift ?? null);
@endphp

@section('title', $sf2Label . ' SF2 Production Report - Hourly')

@section('page-title', $sf2Label . ' SF2 Production Report Entry')

@section('breadcrumb')
    <span class="text-slate-600">Production Reports</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">{{ $sf2Label }} SF2</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">{{ $sf2Label }} Production</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Report</span>
@endsection

@section('content')
<div class="p-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $sf2Label }} Production Report - Hourly</h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Item: <span id="selectedItemCode" class="font-medium text-slate-700">{{ $transfer->item_code }}</span> -
                        <span id="selectedItemName" class="font-medium text-slate-700">{{ $transfer->item_name }}</span>
                        (<span id="selectedItemSize" class="font-medium text-slate-700">{{ $transfer->item_size }}</span>)
                    </p>
                </div>
                <a href="{{ route('admin.production-reports.sf002.process', ['type' => request()->query('type', 'ced')]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors font-medium">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <form id="productionReportForm" method="POST" action="{{ route('admin.production-reports.sf002.production-report.store', ['transferId' => $transfer->id, 'type' => request()->query('type', 'ced')]) }}">
                @csrf
                <input type="hidden" id="selected_transfer_id" name="selected_transfer_id" value="{{ $transfer->id }}">
                <input type="hidden" id="report_id" name="report_id" value="{{ isset($existingReport) && $existingReport ? \Illuminate\Support\Facades\Crypt::encryptString((string) $existingReport->id) : '' }}">

                @if(!(isset($existingReport) && $existingReport))
                <div class="mb-4 flex items-center gap-3">
                    <span class="text-sm font-semibold text-slate-700">Bulk Production</span>
                    <button type="button" id="bulkModeToggle" class="relative inline-flex h-6 w-11 items-center rounded-full bg-slate-300 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" role="switch" aria-checked="false">
                        <span id="bulkToggleKnob" class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform translate-x-1"></span>
                    </button>
                </div>
                @endif

                <div class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div id="singleItemCol">
                        <label for="item_selector" class="block text-sm font-medium text-slate-700 mb-2">Select Item</label>
                        <select
                            id="item_selector"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            @foreach($availableTransfers as $row)
                                <option
                                    value="{{ $row->id }}"
                                    data-item-id="{{ $row->item_id }}"
                                    data-item-code="{{ $row->item_code }}"
                                    data-item-name="{{ $row->item_name }}"
                                    data-item-size="{{ $row->item_size }}"
                                    data-quantity="{{ number_format((float) ($row->pending_quantity ?? 0), 0, '.', '') }}"
                                    {{ (int) $row->id === (int) $transfer->id ? 'selected' : '' }}
                                >
                                    {{ $row->item_code }} - {{ $row->item_name }} ({{ $row->item_size }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="singlePendingCol">
                        <label for="selected_item_quantity" class="block text-sm font-medium text-slate-700 mb-2">Pending Quantity</label>
                        <input
                            type="text"
                            id="selected_item_quantity"
                            value="{{ number_format((float) ($transfer->pending_quantity ?? 0), 0, '.', '') }}"
                            readonly
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-slate-50"
                        >
                    </div>
                    <div id="multiItemCol" class="hidden md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Add Items</label>
                        <div class="flex gap-2">
                            <select id="bulkItemSelector" class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach($availableTransfers as $row)
                                    <option
                                        value="{{ $row->id }}"
                                        data-item-id="{{ $row->item_id }}"
                                        data-item-code="{{ $row->item_code }}"
                                        data-item-name="{{ $row->item_name }}"
                                        data-item-size="{{ $row->item_size }}"
                                        data-quantity="{{ number_format((float) ($row->pending_quantity ?? 0), 0, '.', '') }}"
                                    >
                                        {{ $row->item_code }} - {{ $row->item_name }} ({{ $row->item_size }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" id="addBulkItemBtn" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg text-white text-sm font-medium hover:opacity-90 transition-opacity" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                                <i data-lucide="plus" class="w-4 h-4"></i> Add
                            </button>
                        </div>
                        <div id="bulkSelectedItems" class="flex flex-wrap gap-2 mt-2"></div>
                    </div>
                    <div>
                        <label for="{{ $sf2Prefix }}_report_date" class="block text-sm font-medium text-slate-700 mb-2">Report Date</label>
                        <input
                            type="date"
                            id="{{ $sf2Prefix }}_report_date"
                            name="{{ $sf2Prefix }}_report_date"
                            value="{{ old($sf2Prefix . '_report_date', $existingReport->report_date ?? ($transfer->date ?? date('Y-m-d'))) }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                    <div>
                        <label for="{{ $sf2Prefix }}_shift" class="block text-sm font-medium text-slate-700 mb-2">Shift</label>
                        <select
                            id="{{ $sf2Prefix }}_shift"
                            name="{{ $sf2Prefix }}_shift"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="morning" {{ old($sf2Prefix . '_shift', $existingReport->shift ?? $defaultShift) === 'morning' ? 'selected' : '' }}>Morning</option>
                            <option value="night" {{ old($sf2Prefix . '_shift', $existingReport->shift ?? $defaultShift) === 'night' ? 'selected' : '' }}>Night</option>
                        </select>
                    </div>
                </div>

                @php
                    $slots = [
                        '8AM to 9AM',
                        '9AM to 10AM',
                        '10AM to 11AM',
                        '11AM to 12PM',
                        '12PM to 1PM',
                        '1PM to 2PM',
                        '2PM to 3PM',
                        '3PM to 4PM',
                        '4PM to 5PM',
                        '5PM to 6PM',
                        '6PM to 7PM',
                        '7PM to 8PM',
                    ];
                @endphp

                <div class="mb-2 flex items-center justify-end gap-2">
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="scrollTableHorizontal('left')" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-700 bg-slate-100 border border-slate-300 rounded-lg hover:bg-slate-200 transition-all">
                            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Left
                        </button>
                        <button type="button" onclick="scrollTableHorizontal('right')" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-700 bg-slate-100 border border-slate-300 rounded-lg hover:bg-slate-200 transition-all">
                            Right <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

                <div id="tableScrollContainer" class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-[1500px] w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-100">
                                <th class="border border-slate-300 px-3 py-2 text-left font-semibold text-slate-900">Type</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">Total Set/Shift</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">Set/Hour</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label" data-hour="8-9">8AM to 9AM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label" data-hour="9-10">9AM to 10AM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label" data-hour="10-11">10AM to 11AM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label" data-hour="11-12">11AM to 12PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label" data-hour="12-1">12PM to 1PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label" data-hour="1-2">1PM to 2PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label" data-hour="2-3">2PM to 3PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label" data-hour="3-4">3PM to 4PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label" data-hour="4-5">4PM to 5PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label" data-hour="5-6">5PM to 6PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label" data-hour="6-7">6PM to 7PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900 hour-label" data-hour="7-8">7PM to 8PM</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">Actual / Set / Shift</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">Manpower / Workman</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">Staff Count</th>
                            </tr>
                        </thead>
                        <tbody id="singleModeBody">
                            <tr class="hover:bg-slate-50">
                                <td class="border border-slate-300 px-3 py-2 font-medium text-slate-900">{{ $sf2Label }}</td>
                                <td class="border border-slate-300 px-3 py-2">
                                    <input type="number" name="{{ $sf2Prefix }}_total_set_shift" value="{{ old($sf2Prefix . '_total_set_shift', isset($existingReport) ? (int) $existingReport->total_set_shift : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0">
                                </td>
                                <td class="border border-slate-300 px-3 py-2">
                                    <input type="number" name="{{ $sf2Prefix }}_set_per_hour" value="{{ old($sf2Prefix . '_set_per_hour', isset($existingReport) ? number_format((float) $existingReport->set_per_hour, 2, '.', '') : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm bg-slate-50" placeholder="-" step="0.01" min="0" readonly>
                                </td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="{{ $sf2Prefix }}_hour_8_9" value="{{ old($sf2Prefix . '_hour_8_9', isset($existingReport) ? (int) $existingReport->hour_8_9 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="{{ $sf2Prefix }}_hour_9_10" value="{{ old($sf2Prefix . '_hour_9_10', isset($existingReport) ? (int) $existingReport->hour_9_10 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="{{ $sf2Prefix }}_hour_10_11" value="{{ old($sf2Prefix . '_hour_10_11', isset($existingReport) ? (int) $existingReport->hour_10_11 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="{{ $sf2Prefix }}_hour_11_12" value="{{ old($sf2Prefix . '_hour_11_12', isset($existingReport) ? (int) $existingReport->hour_11_12 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="{{ $sf2Prefix }}_hour_12_1" value="{{ old($sf2Prefix . '_hour_12_1', isset($existingReport) ? (int) $existingReport->hour_12_1 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="{{ $sf2Prefix }}_hour_1_2" value="{{ old($sf2Prefix . '_hour_1_2', isset($existingReport) ? (int) $existingReport->hour_1_2 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="{{ $sf2Prefix }}_hour_2_3" value="{{ old($sf2Prefix . '_hour_2_3', isset($existingReport) ? (int) $existingReport->hour_2_3 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="{{ $sf2Prefix }}_hour_3_4" value="{{ old($sf2Prefix . '_hour_3_4', isset($existingReport) ? (int) $existingReport->hour_3_4 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="{{ $sf2Prefix }}_hour_4_5" value="{{ old($sf2Prefix . '_hour_4_5', isset($existingReport) ? (int) $existingReport->hour_4_5 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="{{ $sf2Prefix }}_hour_5_6" value="{{ old($sf2Prefix . '_hour_5_6', isset($existingReport) ? (int) $existingReport->hour_5_6 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="{{ $sf2Prefix }}_hour_6_7" value="{{ old($sf2Prefix . '_hour_6_7', isset($existingReport) ? (int) $existingReport->hour_6_7 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2"><input type="number" name="{{ $sf2Prefix }}_hour_7_8" value="{{ old($sf2Prefix . '_hour_7_8', isset($existingReport) ? (int) $existingReport->hour_7_8 : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0"></td>
                                <td class="border border-slate-300 px-3 py-2">
                                    <input type="number" name="{{ $sf2Prefix }}_actual_set_shift" value="{{ old($sf2Prefix . '_actual_set_shift', isset($existingReport) ? (int) $existingReport->actual_set_shift : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm bg-slate-50" placeholder="-" step="1" min="0" readonly>
                                </td>
                                <td class="border border-slate-300 px-3 py-2">
                                    <input type="number" name="{{ $sf2Prefix }}_manpower" value="{{ old($sf2Prefix . '_manpower', isset($existingReport) ? (int) $existingReport->manpower_workman : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0">
                                </td>
                                <td class="border border-slate-300 px-3 py-2">
                                    <input type="number" name="{{ $sf2Prefix }}_staff_count" value="{{ old($sf2Prefix . '_staff_count', isset($existingReport) ? (int) $existingReport->staff_count : '') }}" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0">
                                </td>
                            </tr>
                        </tbody>
                        <tbody id="multiModeBody" class="hidden"></tbody>
                    </table>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.production-reports.sf002.process', ['type' => request()->query('type', 'ced')]) }}" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 rounded-lg text-white hover:opacity-90 font-medium" style="background: linear-gradient(to right, #141d30, #2d3a52);">
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
    const shiftSelect = document.getElementById('{{ $sf2Prefix }}_shift');
    const hourLabels = document.querySelectorAll('.hour-label');
    const itemSelector = document.getElementById('item_selector');
    const selectedTransferInput = document.getElementById('selected_transfer_id');
    const selectedQuantityInput = document.getElementById('selected_item_quantity');
    const selectedItemCode = document.getElementById('selectedItemCode');
    const selectedItemName = document.getElementById('selectedItemName');
    const selectedItemSize = document.getElementById('selectedItemSize');
    const totalSetShiftInput = form.querySelector('input[name="{{ $sf2Prefix }}_total_set_shift"]');
    const setPerHourInput = form.querySelector('input[name="{{ $sf2Prefix }}_set_per_hour"]');
    const actualSetShiftInput = form.querySelector('input[name="{{ $sf2Prefix }}_actual_set_shift"]');
    const hourlyInputs = [
        '{{ $sf2Prefix }}_hour_8_9',
        '{{ $sf2Prefix }}_hour_9_10',
        '{{ $sf2Prefix }}_hour_10_11',
        '{{ $sf2Prefix }}_hour_11_12',
        '{{ $sf2Prefix }}_hour_12_1',
        '{{ $sf2Prefix }}_hour_1_2',
        '{{ $sf2Prefix }}_hour_2_3',
        '{{ $sf2Prefix }}_hour_3_4',
        '{{ $sf2Prefix }}_hour_4_5',
        '{{ $sf2Prefix }}_hour_5_6',
        '{{ $sf2Prefix }}_hour_6_7',
        '{{ $sf2Prefix }}_hour_7_8',
    ].map(function (name) {
        return form.querySelector('input[name="' + name + '"]');
    }).filter(Boolean);
    let limitWarningTimeout;
    if (!form) return;

    function showLimitWarning(message) {
        let warning = document.getElementById('sf2LimitWarning');

        if (!warning) {
            warning = document.createElement('div');
            warning.id = 'sf2LimitWarning';
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

    function getSelectedQuantity() {
        return Math.max(parseFloat(selectedQuantityInput ? selectedQuantityInput.value : '0') || 0, 0);
    }

    function clampToSelectedQuantity(input) {
        if (!input) return;

        const selectedQuantity = getSelectedQuantity();
        input.max = String(selectedQuantity);

        const currentValue = parseFloat(input.value || '0');
        if (!Number.isNaN(currentValue) && currentValue > selectedQuantity) {
            input.value = String(selectedQuantity);
            showLimitWarning('Value cannot be greater than pending quantity (' + selectedQuantity + ').');
        }
    }

    function normalizeWholeNumber(input) {
        if (!input) return;
        if (input.value === '') return;

        const numericValue = parseFloat(input.value);
        if (Number.isNaN(numericValue)) return;

        const roundedValue = Math.round(Math.max(numericValue, 0));
        input.value = String(roundedValue);
    }

    function updateSetPerHour() {
        if (!totalSetShiftInput || !setPerHourInput) return;

        normalizeWholeNumber(totalSetShiftInput);
        const totalSetShift = Math.max(parseFloat(totalSetShiftInput.value || '0') || 0, 0);
        const perHour = totalSetShift / 12;
        setPerHourInput.value = perHour.toFixed(2);
    }

    function updateActualSetShiftFromHours() {
        if (!actualSetShiftInput) return;

        let totalHours = 0;
        hourlyInputs.forEach(function (input) {
            const value = Math.max(parseFloat(input.value || '0') || 0, 0);
            totalHours += value;
        });

        actualSetShiftInput.value = String(Math.round(totalHours));
        clampToSelectedQuantity(actualSetShiftInput);
    }

    function updateSelectedItemMeta() {
        if (!itemSelector) return;
        const selectedOption = itemSelector.options[itemSelector.selectedIndex];
        if (!selectedOption) return;

        if (selectedTransferInput) {
            selectedTransferInput.value = selectedOption.value || '';
        }
        if (selectedQuantityInput) {
            selectedQuantityInput.value = selectedOption.getAttribute('data-quantity') || '0';
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

        clampToSelectedQuantity(totalSetShiftInput);
        clampToSelectedQuantity(actualSetShiftInput);
        updateSetPerHour();
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

    updateSelectedItemMeta();
    if (itemSelector) {
        itemSelector.addEventListener('change', updateSelectedItemMeta);
    }

    if (totalSetShiftInput) {
        totalSetShiftInput.addEventListener('input', function () {
            normalizeWholeNumber(totalSetShiftInput);
            clampToSelectedQuantity(totalSetShiftInput);
            updateSetPerHour();
        });

        totalSetShiftInput.addEventListener('blur', function () {
            normalizeWholeNumber(totalSetShiftInput);
            clampToSelectedQuantity(totalSetShiftInput);
            updateSetPerHour();
        });
    }

    if (actualSetShiftInput) {
        actualSetShiftInput.addEventListener('input', function () {
            normalizeWholeNumber(actualSetShiftInput);
            clampToSelectedQuantity(actualSetShiftInput);
        });

        actualSetShiftInput.addEventListener('blur', function () {
            normalizeWholeNumber(actualSetShiftInput);
            clampToSelectedQuantity(actualSetShiftInput);
        });
    }

    form.querySelectorAll('input[type="number"]').forEach(function (input) {
        if (input === setPerHourInput) return;

        input.addEventListener('blur', function () {
            normalizeWholeNumber(input);
            if (input === totalSetShiftInput || input === actualSetShiftInput) {
                clampToSelectedQuantity(input);
            }
        });
    });

    hourlyInputs.forEach(function (input) {
        input.addEventListener('input', function () {
            normalizeWholeNumber(input);
            updateActualSetShiftFromHours();
        });

        input.addEventListener('blur', function () {
            normalizeWholeNumber(input);
            updateActualSetShiftFromHours();
        });
    });

    updateActualSetShiftFromHours();

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

    form.addEventListener('submit', async function (event) {
        // Bulk mode — delegate to bulk handler
        const _bulkToggle = document.getElementById('bulkModeToggle');
        if (_bulkToggle && _bulkToggle.getAttribute('aria-checked') === 'true') {
            event.preventDefault();
            await handleBulkSubmit();
            return;
        }

        const selectedQuantity = getSelectedQuantity();
        const totalSetShiftValue = Math.max(parseFloat(totalSetShiftInput ? totalSetShiftInput.value : '0') || 0, 0);
        const actualSetShiftValue = Math.max(parseFloat(actualSetShiftInput ? actualSetShiftInput.value : '0') || 0, 0);

        if (totalSetShiftValue > selectedQuantity) {
            event.preventDefault();
            showSubmitError('Total Set/Shift cannot be greater than pending quantity.');
            if (totalSetShiftInput) totalSetShiftInput.focus();
            return;
        }

        if (actualSetShiftValue > selectedQuantity) {
            event.preventDefault();
            showSubmitError('Actual Set/Shift cannot be greater than pending quantity.');
            if (actualSetShiftInput) actualSetShiftInput.focus();
            return;
        }

        event.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        if (!submitBtn) return;

        const defaultSubmitHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
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
            window.location.href = data.redirect_url || '{{ route('admin.production-reports.sf002.process', ['type' => request()->query('type', 'ced'), 'tab' => 'production']) }}';
        } catch (error) {
            showSubmitError('Network error while saving. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = defaultSubmitHtml;
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    });

    // ===== BULK MODE LOGIC =====
    const bulkToggle = document.getElementById('bulkModeToggle');
    const bulkToggleKnob = document.getElementById('bulkToggleKnob');
    const singleItemCol = document.getElementById('singleItemCol');
    const singlePendingCol = document.getElementById('singlePendingCol');
    const multiItemCol = document.getElementById('multiItemCol');
    const singleModeBody = document.getElementById('singleModeBody');
    const multiModeBody = document.getElementById('multiModeBody');
    const bulkItemSelectorEl = document.getElementById('bulkItemSelector');
    const addBulkItemBtn = document.getElementById('addBulkItemBtn');
    const bulkSelectedItemsEl = document.getElementById('bulkSelectedItems');
    let bulkRowIndex = 0;
    const addedTransferIds = new Set();

    if (bulkToggle) {
        bulkToggle.addEventListener('click', function () {
            const isOn = this.getAttribute('aria-checked') !== 'true';
            this.setAttribute('aria-checked', isOn ? 'true' : 'false');

            if (isOn) {
                this.classList.remove('bg-slate-300');
                this.classList.add('bg-blue-600');
                bulkToggleKnob.classList.remove('translate-x-1');
                bulkToggleKnob.classList.add('translate-x-6');
                if (singleItemCol) singleItemCol.classList.add('hidden');
                if (singlePendingCol) singlePendingCol.classList.add('hidden');
                if (multiItemCol) multiItemCol.classList.remove('hidden');
                if (singleModeBody) singleModeBody.classList.add('hidden');
                if (multiModeBody) multiModeBody.classList.remove('hidden');
                // Auto-add all available items
                autoAddAllItems();
            } else {
                this.classList.remove('bg-blue-600');
                this.classList.add('bg-slate-300');
                bulkToggleKnob.classList.remove('translate-x-6');
                bulkToggleKnob.classList.add('translate-x-1');
                if (singleItemCol) singleItemCol.classList.remove('hidden');
                if (singlePendingCol) singlePendingCol.classList.remove('hidden');
                if (multiItemCol) multiItemCol.classList.add('hidden');
                if (singleModeBody) singleModeBody.classList.remove('hidden');
                if (multiModeBody) multiModeBody.classList.add('hidden');
                // Clear all bulk rows when toggling off
                clearAllBulkItems();
            }
        });
    }

    function clearAllBulkItems() {
        if (multiModeBody) multiModeBody.innerHTML = '';
        if (bulkSelectedItemsEl) bulkSelectedItemsEl.innerHTML = '';
        addedTransferIds.clear();
        bulkRowIndex = 0;
    }

    function autoAddAllItems() {
        clearAllBulkItems();
        if (!bulkItemSelectorEl) return;
        const options = bulkItemSelectorEl.options;
        for (let i = 0; i < options.length; i++) {
            bulkItemSelectorEl.selectedIndex = i;
            addBulkItem();
        }
    }

    function addBulkItem() {
        if (!bulkItemSelectorEl) return;
        const opt = bulkItemSelectorEl.options[bulkItemSelectorEl.selectedIndex];
        if (!opt) return;

        const transferId = opt.value;
        if (addedTransferIds.has(transferId)) {
            showLimitWarning('This item is already added.');
            return;
        }

        addedTransferIds.add(transferId);
        const itemCode = opt.getAttribute('data-item-code');
        const itemName = opt.getAttribute('data-item-name');
        const itemSize = opt.getAttribute('data-item-size');
        const pendingQty = opt.getAttribute('data-quantity');
        const idx = bulkRowIndex++;

        // Tag badge
        const tag = document.createElement('span');
        tag.id = 'bulkTag_' + idx;
        tag.className = 'inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200';
        tag.innerHTML = itemCode + ' <button type="button" class="text-blue-400 hover:text-rose-600" onclick="removeBulkItem(' + idx + ', \'' + transferId + '\')"><i data-lucide="x" class="w-3 h-3"></i></button>';
        if (bulkSelectedItemsEl) bulkSelectedItemsEl.appendChild(tag);

        // Table row
        const hourFields = ['hour_8_9','hour_9_10','hour_10_11','hour_11_12','hour_12_1','hour_1_2','hour_2_3','hour_3_4','hour_4_5','hour_5_6','hour_6_7','hour_7_8'];
        let hourCells = '';
        hourFields.forEach(function (field) {
            hourCells += '<td class="border border-slate-300 px-3 py-2"><input type="number" name="items[' + idx + '][' + field + ']" class="w-full px-2 py-1 border border-slate-200 rounded text-sm bulk-hourly" data-row="' + idx + '" placeholder="-" step="1" min="0" value="0"></td>';
        });

        const row = document.createElement('tr');
        row.id = 'bulkRow_' + idx;
        row.className = 'hover:bg-slate-50';
        row.innerHTML =
            '<td class="border border-slate-300 px-3 py-2 font-medium text-slate-900 whitespace-nowrap">' +
                '<div class="flex items-center gap-2">' +
                    '<button type="button" onclick="removeBulkItem(' + idx + ', \'' + transferId + '\')" class="text-rose-400 hover:text-rose-600 flex-shrink-0"><i data-lucide="x-circle" class="w-4 h-4"></i></button>' +
                    '<div>' +
                        '<div class="text-xs font-semibold">' + itemCode + '</div>' +
                        '<div class="text-[10px] text-slate-500">' + itemName + ' (' + itemSize + ')</div>' +
                        '<div class="text-[10px] text-blue-600">Pending: ' + pendingQty + '</div>' +
                    '</div>' +
                '</div>' +
                '<input type="hidden" name="items[' + idx + '][transfer_id]" value="' + transferId + '">' +
            '</td>' +
            '<td class="border border-slate-300 px-3 py-2"><input type="number" name="items[' + idx + '][total_set_shift]" class="w-full px-2 py-1 border border-slate-200 rounded text-sm bulk-total-set" data-row="' + idx + '" data-pending="' + pendingQty + '" placeholder="-" step="1" min="0" max="' + pendingQty + '" value="0"></td>' +
            '<td class="border border-slate-300 px-3 py-2"><input type="number" name="items[' + idx + '][set_per_hour]" class="w-full px-2 py-1 border border-slate-200 rounded text-sm bg-slate-50" placeholder="-" step="0.01" min="0" readonly value="0.00"></td>' +
            hourCells +
            '<td class="border border-slate-300 px-3 py-2"><input type="number" name="items[' + idx + '][actual_set_shift]" class="w-full px-2 py-1 border border-slate-200 rounded text-sm bg-slate-50 bulk-actual-set" data-row="' + idx + '" placeholder="-" step="1" min="0" readonly value="0"></td>' +
            '<td class="border border-slate-300 px-3 py-2"><input type="number" name="items[' + idx + '][manpower]" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0" value="0"></td>' +
            '<td class="border border-slate-300 px-3 py-2"><input type="number" name="items[' + idx + '][staff_count]" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="1" min="0" value="0"></td>';

        if (multiModeBody) multiModeBody.appendChild(row);
        attachBulkRowListeners(idx, parseFloat(pendingQty) || 0);

        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    window.removeBulkItem = function (idx, transferId) {
        const row = document.getElementById('bulkRow_' + idx);
        const tag = document.getElementById('bulkTag_' + idx);
        if (row) row.remove();
        if (tag) tag.remove();
        addedTransferIds.delete(transferId);
    };

    function attachBulkRowListeners(idx, pendingQtyNum) {
        const row = document.getElementById('bulkRow_' + idx);
        if (!row) return;

        const totalSetInput = row.querySelector('.bulk-total-set');
        const setPerHourInput = row.querySelector('input[name="items[' + idx + '][set_per_hour]"]');
        const actualSetInput = row.querySelector('.bulk-actual-set');
        const hourlyInputsInRow = row.querySelectorAll('.bulk-hourly');

        function updateRowCalc() {
            const totalSet = parseFloat(totalSetInput?.value || '0') || 0;
            if (setPerHourInput) setPerHourInput.value = (totalSet / 12).toFixed(2);

            let hourlySum = 0;
            hourlyInputsInRow.forEach(function (inp) {
                hourlySum += Math.max(parseFloat(inp.value || '0') || 0, 0);
            });
            if (actualSetInput) actualSetInput.value = String(Math.round(hourlySum));
        }

        function clampBulkValue(input) {
            const val = parseFloat(input.value || '0') || 0;
            if (val > pendingQtyNum) {
                input.value = String(pendingQtyNum);
                showLimitWarning('Value cannot exceed pending quantity (' + pendingQtyNum + ').');
            }
        }

        if (totalSetInput) {
            totalSetInput.addEventListener('input', function () {
                normalizeWholeNumber(this);
                clampBulkValue(this);
                updateRowCalc();
            });
        }

        hourlyInputsInRow.forEach(function (input) {
            input.addEventListener('input', function () {
                normalizeWholeNumber(this);
                updateRowCalc();
            });
        });
    }

    if (addBulkItemBtn) {
        addBulkItemBtn.addEventListener('click', addBulkItem);
    }

    async function handleBulkSubmit() {
        const rows = multiModeBody ? multiModeBody.querySelectorAll('tr') : [];
        if (rows.length === 0) {
            showSubmitError('Please add at least one item.');
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        if (!submitBtn) return;

        const defaultSubmitHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="flex items-center gap-2"><i data-lucide="loader" class="w-4 h-4 animate-spin"></i>Saving...</span>';

        try {
            const formData = new FormData(form);
            formData.append('bulk_mode', '1');

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            const data = await response.json().catch(function () { return {}; });

            if (!response.ok) {
                const validationErrors = data.errors || {};
                const firstFieldKey = Object.keys(validationErrors)[0];
                const firstFieldError = firstFieldKey ? validationErrors[firstFieldKey][0] : null;
                const message = firstFieldError || data.message || 'Unable to save production reports.';
                showSubmitError(message);
                return;
            }

            await showSubmitSuccess(data.message || 'Production reports saved successfully.');
            window.location.href = data.redirect_url || '{{ route('admin.production-reports.sf002.process', ['type' => request()->query('type', 'ced'), 'tab' => 'production']) }}';
        } catch (error) {
            showSubmitError('Network error while saving. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = defaultSubmitHtml;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }
});

function scrollTableHorizontal(direction) {
    const tableScrollContainer = document.getElementById('tableScrollContainer');
    if (!tableScrollContainer) return;
    const amount = 450;
    const delta = direction === 'left' ? -amount : amount;
    tableScrollContainer.scrollBy({ left: delta, behavior: 'auto' });
}
</script>
@endpush
