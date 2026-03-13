@extends('backend.layout.app')

@php
    $transferHour = !empty($transfer->time) ? (int) date('G', strtotime((string) $transfer->time)) : (int) date('G');
    $defaultShift = ($transferHour >= 8 && $transferHour < 20) ? 'morning' : 'night';
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
                        Item: <span id="selectedItemCode" class="font-medium text-slate-700">{{ $transfer->item_code }}</span> -
                        <span id="selectedItemName" class="font-medium text-slate-700">{{ $transfer->item_name }}</span>
                        (<span id="selectedItemSize" class="font-medium text-slate-700">{{ $transfer->item_size }}</span>)
                    </p>
                </div>
                <a href="{{ route('admin.production-reports.sf003.process', ['line' => $requestedLine, 'tab' => 'production']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors font-medium">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <form id="productionReportForm" method="POST" action="{{ route('admin.production-reports.sf003.production-report.store', ['transferId' => $transfer->id, 'line' => $requestedLine]) }}">
                @csrf
                <input type="hidden" id="selected_transfer_id" name="selected_transfer_id" value="{{ $transfer->id }}">
                <input type="hidden" id="report_id" name="report_id" value="{{ isset($existingReport) && $existingReport ? \Illuminate\Support\Facades\Crypt::encryptString((string) $existingReport->id) : '' }}">

                <div class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="item_selector" class="block text-sm font-medium text-slate-700 mb-2">Select Item</label>
                        <select id="item_selector" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                    <div>
                        <label for="selected_item_quantity" class="block text-sm font-medium text-slate-700 mb-2">Pending Quantity</label>
                        <input type="text" id="selected_item_quantity" value="{{ number_format((float) ($transfer->pending_quantity ?? 0), 0, '.', '') }}" readonly class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-slate-50">
                    </div>
                    <div>
                        <label for="sf3_report_date" class="block text-sm font-medium text-slate-700 mb-2">Report Date</label>
                        <input type="date" id="sf3_report_date" name="sf3_report_date" value="{{ old('sf3_report_date', $existingReport->report_date ?? ($transfer->date ?? date('Y-m-d'))) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
    const shiftSelect = document.getElementById('sf3_shift');
    const hourLabels = document.querySelectorAll('.hour-label');
    const itemSelector = document.getElementById('item_selector');
    const selectedTransferInput = document.getElementById('selected_transfer_id');
    const selectedQuantityInput = document.getElementById('selected_item_quantity');
    const selectedItemCode = document.getElementById('selectedItemCode');
    const selectedItemName = document.getElementById('selectedItemName');
    const selectedItemSize = document.getElementById('selectedItemSize');
    const totalSetShiftInput = form.querySelector('input[name="sf3_total_set_shift"]');
    const setPerHourInput = form.querySelector('input[name="sf3_set_per_hour"]');
    const actualSetShiftInput = form.querySelector('input[name="sf3_actual_set_shift"]');
    const hourlyInputs = [
        'sf3_hour_8_9', 'sf3_hour_9_10', 'sf3_hour_10_11', 'sf3_hour_11_12',
        'sf3_hour_12_1', 'sf3_hour_1_2', 'sf3_hour_2_3', 'sf3_hour_3_4',
        'sf3_hour_4_5', 'sf3_hour_5_6', 'sf3_hour_6_7', 'sf3_hour_7_8'
    ].map(function (name) {
        return form.querySelector('input[name="' + name + '"]');
    }).filter(Boolean);
    let limitWarningTimeout;

    if (!form) return;

    function getSelectedQuantity() {
        return Math.max(parseFloat(selectedQuantityInput ? selectedQuantityInput.value : '0') || 0, 0);
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

    if (shiftSelect) {
        updateShiftLabels(shiftSelect.value || 'morning');
        shiftSelect.addEventListener('change', function () {
            updateShiftLabels(this.value || 'morning');
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
            window.location.href = data.redirect_url || '{{ route('admin.production-reports.sf003.process', ['line' => $requestedLine, 'tab' => 'production']) }}';
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
});
</script>
@endpush
