@extends('backend.layout.app')

@section('title', 'Roll Forming (SF1) Process - Edit Production Report')

@section('page-title', 'Roll Forming (SF1) Process Management')

@section('breadcrumb')
    <a href="{{ route('admin.production-reports.index') }}" class="text-slate-600 hover:text-slate-900">Production Reports</a>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">Roll Forming (SF1) Process</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Edit Report #{{ $productionReport->id }}</span>
@endsection

@section('content')
<div class="p-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    <i data-lucide="edit" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Roll Forming (SF1) Process - Edit Production Report</h2>
                    <p class="text-sm text-slate-500">Update Roll Forming (SF1) production report data</p>
                </div>
            </div>
        </div>

        <form id="productionReportEditForm" action="{{ route('admin.production-reports.update', $productionReport) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="mb-8 pb-8 border-b border-slate-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Report Date -->
                    <div>
                        <label for="report_date" class="block text-sm font-medium text-slate-700 mb-2">
                            Report Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            id="report_date"
                            value="{{ old('report_date', $productionReport->report_date) }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required
                        >
                    </div>

                    <!-- Shift -->
                    <div>
                        <label for="shift" class="block text-sm font-medium text-slate-700 mb-2">
                            Shift <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="shift"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required
                            onchange="updateShiftLabels(this.value); syncCommonFields();"
                        >
                            <option value="">Select Shift</option>
                            <option value="Morning" {{ old('shift', $productionReport->shift) == 'Morning' ? 'selected' : '' }}>Morning</option>
                            <option value="Night" {{ old('shift', $productionReport->shift) == 'Night' ? 'selected' : '' }}>Night</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Machine</label>
                        <div class="w-full px-3 py-2 border border-slate-200 bg-slate-50 rounded-lg text-sm text-slate-800 font-medium">
                            {{ $productionReport->machine->name ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Production Table -->
            <div class="mb-8">
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>How to use:</strong> The current report's machine is already checked and filled. You can check other machines to add more reports, or update the existing one.
                    </p>
                </div>
                <div id="lockedHoursWarning" class="mb-4 p-4 bg-amber-50 border border-amber-300 rounded-lg hidden">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        <div>
                            <p class="text-sm font-semibold text-amber-800" id="lockedHoursTitle"></p>
                            <p class="text-xs text-amber-600 mt-0.5">Those hour slots are locked. Remaining slots are editable.</p>
                        </div>
                    </div>
                </div>
                <div class="mb-2 flex items-center justify-end gap-2">
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="scrollTableHorizontal('left')" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-700 bg-slate-100 border border-slate-300 rounded-lg hover:bg-slate-200 transition-all">
                            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                            Left
                        </button>
                        <button type="button" onclick="scrollTableHorizontal('right')" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-700 bg-slate-100 border border-slate-300 rounded-lg hover:bg-slate-200 transition-all">
                            Right
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto" id="tableScrollContainer">
                <table class="w-full border-collapse" id="productionTable">
                    <thead class="text-white" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                        <tr>
                            <th class="border border-slate-300 px-3 py-2 text-center text-[10px] font-semibold text-white min-w-20 whitespace-nowrap" title="Is Ballcage?">Ballcage</th>
                            <th class="border border-slate-300 px-3 py-2 text-left text-[10px] font-semibold text-white min-w-44 whitespace-nowrap">Machine</th>
                            <th class="border border-slate-300 px-3 py-2 text-left text-[10px] font-semibold text-white min-w-40 whitespace-nowrap">Slide Size</th>
                            <th class="border border-slate-300 px-3 py-2 text-center text-[10px] font-semibold text-white min-w-24 whitespace-nowrap">Total Set/Shift</th>
                            <th class="border border-slate-300 px-3 py-2 text-center text-[10px] font-semibold text-white min-w-24 whitespace-nowrap">Set/Hour</th>
                            <!-- Hourly Columns -->
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label whitespace-nowrap" data-hour="8-9">8AM-9AM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label whitespace-nowrap" data-hour="9-10">9AM-10AM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label whitespace-nowrap" data-hour="10-11">10AM-11AM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label whitespace-nowrap" data-hour="11-12">11AM-12PM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label whitespace-nowrap" data-hour="12-1">12PM-1PM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label whitespace-nowrap" data-hour="1-2">1PM-2PM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label whitespace-nowrap" data-hour="2-3">2PM-3PM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label whitespace-nowrap" data-hour="3-4">3PM-4PM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label whitespace-nowrap" data-hour="4-5">4PM-5PM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label whitespace-nowrap" data-hour="5-6">5PM-6PM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label whitespace-nowrap" data-hour="6-7">6PM-7PM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label whitespace-nowrap" data-hour="7-8">7PM-8PM</th>
                            <th class="border border-slate-300 px-3 py-2 text-center text-[10px] font-semibold text-white min-w-20 whitespace-nowrap">Actual Set</th>
                            <th class="border border-slate-300 px-3 py-2 text-center text-[10px] font-semibold text-white min-w-20 whitespace-nowrap">Workman</th>
                            <th class="border border-slate-300 px-3 py-2 text-center text-[10px] font-semibold text-white min-w-20 whitespace-nowrap">Staff</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Rows will be added here by JavaScript -->
                    </tbody>
                </table>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.production-reports.sf001') }}" class="px-4 py-2 text-sm font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">
                    Cancel
                </a>
                <button type="button" id="saveDraftBtn" onclick="saveAsDraft()" class="px-5 py-2 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-300 rounded-lg hover:bg-amber-100 transition-all">
                    <i data-lucide="save" class="w-4 h-4 inline-block mr-1 -mt-0.5"></i>
                    Save as Draft
                </button>
                <button type="submit" id="editSubmitBtn" class="px-6 py-2 text-sm font-medium text-white rounded-lg hover:shadow-lg transition-all" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    Update Production Report
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const machines = @json($machines);
    const slideSizes = @json($slideSizes);
    const existingReport = @json($productionReport);
    const currentReportId = existingReport.id;
    const duplicateCheckUrl = "{{ route('admin.production-reports.check-duplicate') }}";

    function scrollTableHorizontal(direction) {
        const container = document.getElementById('tableScrollContainer');
        if (!container) return;
        const amount = 450;
        container.scrollBy({ left: direction === 'left' ? -amount : amount, behavior: 'smooth' });
    }

    function syncCommonFields() {
        const reportDate = document.getElementById('report_date').value;
        const shift = document.getElementById('shift').value || 'Morning';

        const reportDateInput = document.querySelector('input[name="report_date[]"]');
        const shiftInput = document.querySelector('input[name="shift[]"]');

        if (reportDateInput) reportDateInput.value = reportDate;
        if (shiftInput) shiftInput.value = shift;
    }

    // ── Duplicate & Hour Validation ──

    async function validateMachineDuplicate(machineId, reportDate, shift, slideSizeId, coilId) {
        const csrfToken = document.querySelector('input[name="_token"]')?.value;

        const response = await fetch(duplicateCheckUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            body: JSON.stringify({
                machine_id: machineId,
                report_date: reportDate,
                shift: shift,
                slide_size_id: slideSizeId || null,
                coil_id: coilId || null,
                exclude_id: currentReportId || null,
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            return {
                allowed: false,
                message: data.message || 'Unable to validate.',
            };
        }

        return {
            allowed: !data.exists,
            message: data.message,
            filled_hours: data.filled_hours || {},
        };
    }

    function lockFilledHours(row, filledHours) {
        const hourFields = [
            'hour_8_9', 'hour_9_10', 'hour_10_11', 'hour_11_12',
            'hour_12_1', 'hour_1_2', 'hour_2_3', 'hour_3_4',
            'hour_4_5', 'hour_5_6', 'hour_6_7', 'hour_7_8'
        ];

        const hourLabels = {
            'hour_8_9': '8AM–9AM', 'hour_9_10': '9AM–10AM', 'hour_10_11': '10AM–11AM',
            'hour_11_12': '11AM–12PM', 'hour_12_1': '12PM–1PM', 'hour_1_2': '1PM–2PM',
            'hour_2_3': '2PM–3PM', 'hour_3_4': '3PM–4PM', 'hour_4_5': '4PM–5PM',
            'hour_5_6': '5PM–6PM', 'hour_6_7': '6PM–7PM', 'hour_7_8': '7PM–8PM'
        };

        let lockedCount = 0;
        hourFields.forEach(field => {
            const input = row.querySelector(`input[name="${field}[]"]`);
            if (input && filledHours[field]) {
                input.value = '';
                input.setAttribute('data-locked', '1');
                input.readOnly = true;
                input.tabIndex = -1;
                input.classList.add('bg-slate-200', 'cursor-not-allowed');
                input.title = `${hourLabels[field]} is locked — already reported for this machine, date, and shift.`;
                input.style.cursor = 'not-allowed';
                lockedCount++;
            }
        });

        if (lockedCount === 0) {
            document.getElementById('lockedHoursWarning')?.classList.add('hidden');
            return;
        }

        const machineName = (row.querySelector('td:first-child')?.textContent || '').trim();
        const warningDiv = document.getElementById('lockedHoursWarning');
        const titleEl = document.getElementById('lockedHoursTitle');
        if (warningDiv && titleEl) {
            titleEl.textContent = `${machineName} already has data for ${lockedCount} hour slot(s) on this date and shift from other reports.`;
            warningDiv.classList.remove('hidden');
        }
    }

    function unlockAllHours(row) {
        const hourInputs = row.querySelectorAll('.hour-input');
        hourInputs.forEach(input => {
            if (input.getAttribute('data-locked') === '1') {
                input.removeAttribute('data-locked');
                input.readOnly = false;
                input.tabIndex = 0;
                input.classList.remove('bg-slate-200', 'cursor-not-allowed');
                input.title = '';
                input.style.cursor = '';
            }
        });
    }

    async function onSlideSizeChange(selectElement) {
        const row = selectElement.closest('tr');
        if (!row) return;

        const previousValue = selectElement.dataset.previousValue || '';
        const machineId = row.dataset.machineId;
        const reportDate = document.getElementById('report_date')?.value;
        const shift = document.getElementById('shift')?.value;
        const slideSizeId = selectElement.value;
        const coilIdInput = row.querySelector('input[name="coil_id[]"]');
        const coilId = coilIdInput ? coilIdInput.value : null;

        if (!reportDate || !shift || !slideSizeId) return;

        const result = await validateMachineDuplicate(machineId, reportDate, shift, slideSizeId, coilId);

        if (!result.allowed) {
            Swal.fire({
                title: 'Duplicate Not Allowed',
                text: 'A report with the same date, shift, machine, coil, and item already exists.',
                icon: 'error',
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'OK',
            });
            selectElement.value = previousValue;
        } else {
            selectElement.dataset.previousValue = selectElement.value;
            unlockAllHours(row);
            if (result.filled_hours) {
                lockFilledHours(row, result.filled_hours);
            }
        }
    }

    async function checkAndLockHoursOnLoad(row) {
        const machineId = row.dataset.machineId;
        const reportDate = document.getElementById('report_date')?.value;
        const shift = document.getElementById('shift')?.value;
        const slideSizeSelect = row.querySelector('select[name="slide_size_id[]"]');
        const slideSizeId = slideSizeSelect ? slideSizeSelect.value : null;
        const coilIdInput = row.querySelector('input[name="coil_id[]"]');
        const coilId = coilIdInput ? coilIdInput.value : null;

        if (!machineId || !reportDate || !shift) return;

        const result = await validateMachineDuplicate(machineId, reportDate, shift, slideSizeId, coilId);

        if (!result.allowed) {
            // This exact combo already exists (shouldn't happen for current report), no locking needed
            return;
        }

        if (result.filled_hours) {
            lockFilledHours(row, result.filled_hours);
        }
    }

    // ── End Duplicate & Hour Validation ──

    function addMachineRow(machine, prefillData = null) {
        const tableBody = document.getElementById('tableBody');
        const reportDate = document.getElementById('report_date').value;
        const shift = document.getElementById('shift').value || 'Morning';
        
        const row = document.createElement('tr');
        row.className = 'hover:bg-slate-50 border-b border-slate-200 machine-row';
        row.id = `row-${machine.id}`;
        row.dataset.machineId = machine.id;
        
        let sizeOptions = '<option value="">Select Size</option>';
        slideSizes.forEach(size => {
            const selected = prefillData && size.id === prefillData.slide_size_id ? 'selected' : '';
            sizeOptions += `<option value="${size.id}" ${selected}>${size.name} (${size.size})</option>`;
        });

        const hourFields = [
            'hour_8_9', 'hour_9_10', 'hour_10_11', 'hour_11_12',
            'hour_12_1', 'hour_1_2', 'hour_2_3', 'hour_3_4',
            'hour_4_5', 'hour_5_6', 'hour_6_7', 'hour_7_8'
        ];

        function toVal(v) { return v !== null && v !== undefined && v !== '' ? v : ''; }

        let hourInputs = '';
        hourFields.forEach(field => {
            const value = prefillData ? toVal(prefillData[field]) : '';
            const disabled = prefillData ? '' : 'disabled';
            hourInputs += `<td class="border border-slate-300 px-2 py-2">
                <input type="number" name="${field}[]" step="0.01" value="${value}" placeholder="-" class="w-full px-1 py-1 border border-slate-200 rounded text-center text-sm focus:ring-1 focus:ring-blue-400 hour-input" onchange="calculateActualSet(this)" onfocus="this.select()" ${disabled}>
            </td>`;
        });

        const totalSetShift = prefillData ? toVal(prefillData.total_set_shift) : '';
        const setPerHour = prefillData && prefillData.set_per_hour !== null && prefillData.set_per_hour !== undefined ? parseFloat(prefillData.set_per_hour).toFixed(2) : '';
        const actualSet = prefillData && prefillData.actual_set_shift !== null && prefillData.actual_set_shift !== undefined ? parseFloat(prefillData.actual_set_shift).toFixed(2) : '';
        const workmanCount = prefillData ? toVal(prefillData.workman_count) : '';
        const staffCount = prefillData ? toVal(prefillData.staff_count) : '';

        const isBallcageChecked = prefillData && prefillData.is_ballcage ? 'checked' : '';

        row.innerHTML = `
            <td class="border border-slate-300 px-3 py-2 text-center">
                <label class="inline-flex items-center cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" name="is_ballcage[]" value="1" class="sr-only machine-toggle is-ballcage-checkbox" ${isBallcageChecked}>
                        <div class="toggle-bg"></div>
                        <input type="hidden" name="is_ballcage_hidden[]" value="0">
                    </div>
                </label>
            </td>
            <td class="border border-slate-300 px-3 py-2 font-medium text-slate-900">
                ${machine.name}
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <select name="slide_size_id[]" class="w-full px-2 py-1 border border-slate-200 rounded text-sm focus:ring-1 focus:ring-blue-400 row-input slide-size-select" onchange="onSlideSizeChange(this)" required>
                    ${sizeOptions}
                </select>
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="total_set_shift[]" step="0.01" value="${totalSetShift}" placeholder="-" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm focus:ring-1 focus:ring-blue-400 row-input total-set-shift" onchange="calculateSetPerHour(this)" onfocus="this.select()">
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="set_per_hour[]" step="0.01" value="${setPerHour}" placeholder="-" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm bg-slate-50 set-per-hour" readonly>
            </td>
            ${hourInputs}
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="actual_set_shift[]" step="0.01" value="${actualSet}" placeholder="-" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm bg-slate-50 actual-set" readonly>
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="workman_count[]" step="1" min="0" value="${workmanCount}" placeholder="-" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm focus:ring-1 focus:ring-blue-400 row-input" onfocus="this.select()">
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="staff_count[]" step="1" min="0" value="${staffCount}" placeholder="-" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm focus:ring-1 focus:ring-blue-400 row-input" onfocus="this.select()">
            </td>
            <input type="hidden" name="selected_machines[]" value="${machine.id}">
            <input type="hidden" name="machine_id[]" value="${machine.id}">
            <input type="hidden" name="coil_id[]" value="${prefillData && prefillData.coil_id ? prefillData.coil_id : ''}">
            <input type="hidden" name="coil_number_id[]" value="${prefillData && prefillData.coil_number_id ? prefillData.coil_number_id : ''}">
            <input type="hidden" name="report_date[]" value="${reportDate}">
            <input type="hidden" name="shift[]" value="${shift}">
        `;

        tableBody.appendChild(row);

        // Store initial slide size value for revert on duplicate
        const slideSizeSelect = row.querySelector('.slide-size-select');
        if (slideSizeSelect) {
            slideSizeSelect.dataset.previousValue = slideSizeSelect.value;
        }

        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    function calculateSetPerHour(input) {
        const row = input.closest('tr');
        if (!row) return;

        const totalSetShift = parseFloat(input.value) || 0;
        const setPerHour = totalSetShift / 12;

        const setPerHourInput = row.querySelector('.set-per-hour');
        if (setPerHourInput) {
            setPerHourInput.value = setPerHour.toFixed(2);
        }
    }

    function calculateActualSet(input) {
        const row = input.closest('tr');
        if (!row) return;

        const hourInputs = row.querySelectorAll('input[name^="hour_"]');
        let total = 0;
        hourInputs.forEach(inp => {
            total += parseFloat(inp.value) || 0;
        });

        const actualSetInput = row.querySelector('.actual-set');
        if (actualSetInput) {
            actualSetInput.value = total.toFixed(2);
        }
    }

    function updateShiftLabels(shift) {
        const labels = document.querySelectorAll('.hour-label');
        
        const timeLabels = {
            'Morning': ['8AM-9AM', '9AM-10AM', '10AM-11AM', '11AM-12PM', '12PM-1PM', '1PM-2PM', '2PM-3PM', '3PM-4PM', '4PM-5PM', '5PM-6PM', '6PM-7PM', '7PM-8PM'],
            'Night': ['8PM-9PM', '9PM-10PM', '10PM-11PM', '11PM-12AM', '12AM-1AM', '1AM-2AM', '2AM-3AM', '3AM-4AM', '4AM-5AM', '5AM-6AM', '6AM-7AM', '7AM-8AM']
        };

        const selectedLabels = timeLabels[shift] || timeLabels['Morning'];
        
        labels.forEach((label, index) => {
            if (selectedLabels[index]) {
                label.textContent = selectedLabels[index];
            }
        });
    }

    // Initialize with all machines as rows
    window.addEventListener('load', async function() {
        const reportMachine = machines.find(machine => Number(machine.id) === Number(existingReport.machine_id));
        if (reportMachine) {
            addMachineRow(reportMachine, existingReport);
        }
        
        // Update shift labels based on current shift
        const currentShift = document.getElementById('shift').value;
        if (currentShift) {
            updateShiftLabels(currentShift);
        }

        syncCommonFields();

        document.getElementById('report_date').addEventListener('change', syncCommonFields);
        document.getElementById('shift').addEventListener('change', syncCommonFields);

        // Check and lock hours filled by other reports on page load
        const row = document.querySelector('.machine-row');
        if (row) {
            await checkAndLockHoursOnLoad(row);
        }

        // Prevent changes on locked hour inputs
        const editForm = document.getElementById('productionReportEditForm');
        if (editForm) {
            editForm.addEventListener('input', function (event) {
                if (event.target && event.target.getAttribute('data-locked') === '1') {
                    event.target.value = '';
                    return;
                }
            });
        }
        
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });

    function validateFormSubmission() {
        const slideSize = document.querySelector('select[name="slide_size_id[]"]');
        if (!slideSize || !slideSize.value) {
            return { valid: false, message: 'Please select slide size for all selected machines.' };
        }

        return { valid: true, message: '' };
    }

    function clearValidationErrors() {
        document.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500', 'ring-1', 'ring-red-400');
        });
        document.querySelectorAll('.machine-row.bg-red-50').forEach(row => {
            row.classList.remove('bg-red-50');
        });
    }

    function markValidationErrors(errors) {
        if (!errors) return;

        Object.keys(errors).forEach(key => {
            const match = key.match(/^(\w+)\.(\d+)$/);
            if (!match) return;

            const field = match[1];
            const index = parseInt(match[2], 10);
            const inputs = document.querySelectorAll(`[name="${field}[]"]`);
            const input = inputs[index];

            if (input) {
                input.classList.add('border-red-500', 'ring-1', 'ring-red-400');
                const row = input.closest('.machine-row');
                if (row) row.classList.add('bg-red-50');
            }
        });
    }

    document.getElementById('productionReportEditForm').addEventListener('submit', async function (event) {
        event.preventDefault();

        clearValidationErrors();

        const validation = validateFormSubmission();
        if (!validation.valid) {
            Swal.fire({
                title: 'Invalid Form',
                text: validation.message,
                icon: 'error',
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'OK',
            });
            return;
        }

        const submitBtn = document.getElementById('editSubmitBtn');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Updating...';

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(this)
            });

            const data = await response.json();

            if (response.ok) {
                formSubmitted = true;
                Swal.fire({
                    title: 'Success!',
                    text: 'Production report updated successfully.',
                    icon: 'success',
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText: 'OK',
                }).then(() => {
                    window.location.href = data.redirect || "{{ route('admin.production-reports.sf001') }}";
                });
                return;
            }

            if (response.status === 422) {
                markValidationErrors(data.errors);
                const firstError = data.message || (data.errors ? Object.values(data.errors).flat()[0] : 'Validation failed.');
                Swal.fire({
                    title: 'Validation Error',
                    text: firstError,
                    icon: 'error',
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText: 'OK',
                });
                return;
            }

            Swal.fire('Error', data.message || 'Something went wrong. Please try again.', 'error');
        } catch (error) {
            Swal.fire('Network Error', 'Please check your connection and try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });

    // ── Draft Feature ──
    let formSubmitted = false;
    let draftSaving = false;

    function isFormDirty() {
        const hourInputs = document.querySelectorAll('input[name^="hour_"]');
        for (const input of hourInputs) {
            if (input.value && parseFloat(input.value) > 0) return true;
        }
        const totalSetInputs = document.querySelectorAll('.total-set-shift');
        for (const input of totalSetInputs) {
            if (input.value && parseFloat(input.value) > 0) return true;
        }
        const slideSize = document.querySelector('select[name="slide_size_id[]"]');
        if (slideSize && slideSize.value) return true;
        return false;
    }

    async function saveAsDraft() {
        draftSaving = true;
        const draftBtn = document.getElementById('saveDraftBtn');
        const originalHTML = draftBtn.innerHTML;
        draftBtn.disabled = true;
        draftBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 inline-block mr-1 -mt-0.5 animate-spin"></i> Saving...';

        try {
            const form = document.getElementById('productionReportEditForm');
            const formData = new FormData(form);
            formData.append('is_draft', '1');

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                formSubmitted = true;
                Swal.fire({
                    title: 'Draft Saved!',
                    text: 'Your production report has been saved as a draft.',
                    icon: 'success',
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText: 'OK',
                }).then(() => {
                    window.location.href = data.redirect || "{{ route('admin.production-reports.sf001') }}";
                });
                return;
            }

            if (response.status === 422) {
                markValidationErrors(data.errors);
                const firstError = data.message || (data.errors ? Object.values(data.errors).flat()[0] : 'Validation failed.');
                Swal.fire('Validation Error', firstError, 'error');
                return;
            }

            Swal.fire('Error', data.message || 'Something went wrong.', 'error');
        } catch (error) {
            Swal.fire('Network Error', 'Please check your connection and try again.', 'error');
        } finally {
            draftBtn.disabled = false;
            draftBtn.innerHTML = originalHTML;
            draftSaving = false;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    // Intercept tab/browser close
    window.addEventListener('beforeunload', function (e) {
        if (formSubmitted || draftSaving) return;
        if (!isFormDirty()) return;
        e.preventDefault();
        e.returnValue = '';
    });

    // Intercept link clicks for SweetAlert draft prompt
    document.addEventListener('click', function (e) {
        if (formSubmitted || draftSaving) return;
        const link = e.target.closest('a[href]');
        if (!link) return;
        if (link.getAttribute('href') === '#' || link.getAttribute('target') === '_blank') return;
        if (!isFormDirty()) return;

        e.preventDefault();
        const targetUrl = link.href;

        Swal.fire({
            title: 'Unsaved Changes',
            text: 'You have unsaved data. Would you like to save it as a draft?',
            icon: 'question',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: 'Save as Draft',
            denyButtonText: 'Discard',
            cancelButtonText: 'Stay',
            confirmButtonColor: '#d97706',
            denyButtonColor: '#dc2626',
        }).then((result) => {
            if (result.isConfirmed) {
                saveAsDraft();
            } else if (result.isDenied) {
                formSubmitted = true;
                window.location.href = targetUrl;
            }
        });
    });
</script>

<style>
    table {
        border-collapse: collapse;
    }
    /* Toggle Switch Styles */
    .machine-toggle + .toggle-bg {
        display: block;
        width: 44px;
        height: 24px;
        background-color: #cbd5e1;
        border-radius: 9999px;
        position: relative;
        transition: background-color 0.2s ease;
    }

    .machine-toggle + .toggle-bg:after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        background-color: white;
        border-radius: 50%;
        transition: transform 0.2s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .machine-toggle:checked + .toggle-bg {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .machine-toggle:checked + .toggle-bg:after {
        transform: translateX(20px);
    }

    .machine-toggle:focus + .toggle-bg {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0;
    }
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
@endsection
