@extends('backend.layout.app')

@section('title', 'Roll Forming (SF1) Process - Create Production Report')

@section('page-title', 'Roll Forming (SF1) Process Management')

@section('breadcrumb')
    <a href="{{ route('admin.production-reports.index') }}" class="text-slate-600 hover:text-slate-900">Production Reports</a>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">Roll Forming (SF1) Process</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Create New</span>
@endsection

@section('content')
<div class="p-3">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="px-4 py-3 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    <i data-lucide="plus" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Roll Forming (SF1) Process - Production Report - Bulk Entry</h2>
                    <p class="text-sm text-slate-500">Enter Roll Forming (SF1) production data for multiple machines at once</p>
                </div>
            </div>
        </div>

        <form id="productionReportCreateForm" action="{{ route('admin.production-reports.store') }}" method="POST" class="p-4">
            @csrf

            <!-- Basic Filters -->
            <div class="mb-4 pb-4 border-b border-slate-200">
                <!-- Report Date | Shift | Total Workman | Total Staff in single row -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <!-- Report Date -->
                    <div>
                        <label for="report_date" class="block text-xs font-medium text-slate-700 mb-1">
                            Report Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            id="report_date"
                            value="{{ date('Y-m-d') }}"
                            class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    <!-- Shift -->
                    <div>
                        <label for="shift" class="block text-xs font-medium text-slate-700 mb-1">
                            Shift <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="shift"
                            class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            onchange="handleShiftChange(this)"
                        >
                            <option value="">Select Shift</option>
                            <option value="Morning" selected>Morning</option>
                            <option value="Night">Night</option>
                        </select>
                    </div>

                    <!-- Total Workman -->
                    <div>
                        <label for="global_workman_count" class="block text-xs font-medium text-slate-700 mb-1">
                            Total Workman <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="global_workman_count"
                            name="workman_count"
                            min="0"
                            step="1"
                            value="0"
                            placeholder="Enter workman count"
                            class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    <!-- Total Staff -->
                    <div>
                        <label for="global_staff_count" class="block text-xs font-medium text-slate-700 mb-1">
                            Total Staff <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="global_staff_count"
                            name="staff_count"
                            min="0"
                            step="1"
                            value="0"
                            placeholder="Enter staff count"
                            class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                </div>
            </div>

            <!-- Production Table -->
            <div class="mb-4">
                <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-xs text-blue-800">
                        <strong>How to use:</strong> Check the checkbox next to each machine to enable data entry. The selected machines will be included when you save the form. All fields must be filled for selected rows.
                    </p>
                </div>
                <div class="mb-2 flex items-center justify-end gap-2">
                    <!-- Horizontal Scroll Buttons -->
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
                <div id="topScrollContainer" class="overflow-x-auto mb-2">
                    <div id="topScrollContent" class="h-1"></div>
                </div>
                <div id="tableScrollContainer" class="overflow-x-auto overflow-y-auto max-h-[65vh]">
                <table class="w-full border-collapse" id="productionTable">
                    <thead style="background: linear-gradient(to right, #141d30, #2d3a52);">
                        <tr>
                            <th class="border border-slate-300 px-3 py-2 text-center text-[10px] font-semibold text-white min-w-20 sticky top-0 z-20 whitespace-nowrap" style="background: #141d30;">
                                <div class="flex flex-col items-center gap-1">
                                    <span>Select</span>
                                    <label class="inline-flex items-center cursor-pointer" title="Select All Machines">
                                        <div class="relative">
                                            <input
                                                type="checkbox"
                                                id="select_all"
                                                onclick="toggleAllRows(this.checked)"
                                                class="sr-only machine-toggle"
                                            >
                                            <div class="toggle-bg"></div>
                                        </div>
                                    </label>
                                </div>
                            </th>
                            <th class="border border-slate-300 px-3 py-2 text-left text-[10px] font-semibold text-white min-w-44 sticky top-0 z-20 whitespace-nowrap" style="background: #1a2538;">Machine</th>
                            <th class="border border-slate-300 px-3 py-2 text-left text-[10px] font-semibold text-white min-w-40 sticky top-0 z-20 whitespace-nowrap" style="background: #1e2d42;">Slide Size</th>
                            <th class="border border-slate-300 px-3 py-2 text-center text-[10px] font-semibold text-white min-w-24 sticky top-0 z-20 whitespace-nowrap" style="background: #22334a;">Total Set/Shift</th>
                            <th class="border border-slate-300 px-3 py-2 text-center text-[10px] font-semibold text-white min-w-24 sticky top-0 z-20 whitespace-nowrap" style="background: #263950;">Set/Hour</th>
                            <!-- Hourly Columns -->
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label sticky top-0 z-20 whitespace-nowrap" style="background: #2d3a52;" data-hour="8-9">8AM-9AM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label sticky top-0 z-20 whitespace-nowrap" style="background: #2d3a52;" data-hour="9-10">9AM-10AM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label sticky top-0 z-20 whitespace-nowrap" style="background: #2d3a52;" data-hour="10-11">10AM-11AM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label sticky top-0 z-20 whitespace-nowrap" style="background: #2d3a52;" data-hour="11-12">11AM-12PM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label sticky top-0 z-20 whitespace-nowrap" style="background: #2d3a52;" data-hour="12-1">12PM-1PM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label sticky top-0 z-20 whitespace-nowrap" style="background: #2d3a52;" data-hour="1-2">1PM-2PM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label sticky top-0 z-20 whitespace-nowrap" style="background: #2d3a52;" data-hour="2-3">2PM-3PM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label sticky top-0 z-20 whitespace-nowrap" style="background: #2d3a52;" data-hour="3-4">3PM-4PM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label sticky top-0 z-20 whitespace-nowrap" style="background: #2d3a52;" data-hour="4-5">4PM-5PM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label sticky top-0 z-20 whitespace-nowrap" style="background: #2d3a52;" data-hour="5-6">5PM-6PM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label sticky top-0 z-20 whitespace-nowrap" style="background: #2d3a52;" data-hour="6-7">6PM-7PM</th>
                            <th class="border border-slate-300 px-2 py-2 text-center text-[10px] font-semibold text-white min-w-20 hour-label sticky top-0 z-20 whitespace-nowrap" style="background: #2d3a52;" data-hour="7-8">7PM-8PM</th>
                            <th class="border border-slate-300 px-3 py-2 text-center text-[10px] font-semibold text-white min-w-20 sticky top-0 z-20 whitespace-nowrap" style="background: #2d3a52;">Actual Set</th>
                            <!-- Workman and Staff columns hidden - entered globally at top -->
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Rows will be added here by JavaScript -->
                    </tbody>
                </table>
                </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.production-reports.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">
                    Cancel
                </a>
                <button type="button" id="saveDraftBtn" onclick="saveAsDraft()" class="px-5 py-2 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-300 rounded-lg hover:bg-amber-100 transition-all">
                    <i data-lucide="save" class="w-4 h-4 inline-block mr-1 -mt-0.5"></i>
                    Save as Draft
                </button>
                <button type="submit" id="createSubmitBtn" class="px-6 py-2 text-sm font-medium text-white rounded-lg hover:shadow-lg transition-all" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    Create Selected Reports
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const machines = @json($machines);
    const slideSizes = @json($slideSizes);
    const duplicateCheckUrl = "{{ route('admin.production-reports.check-duplicate') }}";

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
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            return {
                allowed: false,
                message: data.message || 'Unable to validate machine selection right now.',
            };
        }

        return {
            allowed: !data.exists,
            message: data.message,
            filled_hours: data.filled_hours || {},
        };
    }

    async function canSelectMachineForCurrentFilters(row, showErrorPopup = true) {
        const machineId = row.dataset.machineId;
        const machineName = (row.querySelector('td:nth-child(2)')?.textContent || '').trim() || 'Selected machine';
        const reportDate = document.getElementById('report_date')?.value;
        const shift = document.getElementById('shift')?.value;

        if (!reportDate || !shift) {
            if (showErrorPopup) {
                Swal.fire({
                    title: 'Missing Filters',
                    text: 'Please select report date and shift first.',
                    icon: 'warning',
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText: 'OK',
                });
            }

            return { allowed: false, reason: 'missing' };
        }

        const slideSizeSelect = row.querySelector('select[name="slide_size_id[]"]');
        const slideSizeId = slideSizeSelect ? slideSizeSelect.value : null;
        const coilIdInput = row.querySelector('input[name="coil_id[]"]');
        const coilId = coilIdInput ? coilIdInput.value : null;

        const result = await validateMachineDuplicate(machineId, reportDate, shift, slideSizeId, coilId);
        if (!result.allowed) {
            if (showErrorPopup) {
                Swal.fire({
                    title: 'Duplicate Not Allowed',
                    text: `${machineName}: A report with the same date, shift, machine, coil, and item already exists.`,
                    icon: 'error',
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText: 'OK',
                });
            }

            return { allowed: false, reason: 'duplicate' };
        }

        return { allowed: true, reason: null, filled_hours: result.filled_hours };
    }

    async function onSlideSizeChange(selectElement) {
        const row = selectElement.closest('tr');
        if (!row) return;

        const checkbox = row.querySelector('.machine-checkbox');
        if (!checkbox || !checkbox.checked) return; // Only validate if row is active

        const previousValue = selectElement.dataset.previousValue || '';
        const result = await canSelectMachineForCurrentFilters(row, true);

        if (!result.allowed) {
            // Revert to previous value
            selectElement.value = previousValue;
        } else {
            // Store the new value as previous
            selectElement.dataset.previousValue = selectElement.value;
            // Unlock all hours first, then re-lock based on new check
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
            // Re-lock hours filled by other reports
            if (result.filled_hours) {
                lockFilledHours(row, result.filled_hours);
            }
        }
    }

    function addMachineRow(machine) {
        const tableBody = document.getElementById('tableBody');
        const reportDate = document.getElementById('report_date').value;
        const shift = document.getElementById('shift').value || 'Morning';
        
        const row = document.createElement('tr');
        row.className = 'hover:bg-slate-50 border-b border-slate-200 machine-row';
        row.id = `row-${machine.id}`;
        row.dataset.machineId = machine.id;
        row.dataset.hasCoil = machine.coil && machine.coil.id ? '1' : '0';
        
        let sizeOptions = '<option value="">Select Size</option>';
        slideSizes.forEach(size => {
            const sizeLabel = `${size.name} (${size.size || '-'})`;
            sizeOptions += `<option value="${size.id}">${sizeLabel}</option>`;
        });

        const hourFields = [
            'hour_8_9', 'hour_9_10', 'hour_10_11', 'hour_11_12',
            'hour_12_1', 'hour_1_2', 'hour_2_3', 'hour_3_4',
            'hour_4_5', 'hour_5_6', 'hour_6_7', 'hour_7_8'
        ];

        let hourInputs = '';
        hourFields.forEach(field => {
            hourInputs += `<td class="border border-slate-300 px-2 py-2">
                <input type="text" name="${field}[]" value="" placeholder="-" class="w-full px-1 py-1 border border-slate-200 rounded text-center text-sm focus:ring-1 focus:ring-blue-400 hour-input" oninput="sanitizeHourInput(this)" onchange="calculateActualSet(this)" onfocus="this.select()" disabled>
            </td>`;
        });

        row.innerHTML = `
            <td class="border border-slate-300 px-3 py-2 text-center">
                <label class="inline-flex items-center cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" class="sr-only machine-checkbox machine-toggle" onchange="toggleRowInputs(this)">
                        <div class="toggle-bg"></div>
                    </div>
                </label>
                <input type="hidden" name="selected_machines[]" value="${machine.id}" class="selected-machine-input" disabled>
                <input type="hidden" name="coil_id[]" value="${machine.coil && machine.coil.id ? machine.coil.id : ''}" class="row-input" disabled>
                <input type="hidden" name="coil_number_id[]" value="${machine.load_coil_number_id || ''}" class="row-input" disabled>
            </td>
            <td class="border border-slate-300 px-3 py-2 font-medium text-slate-900">
                <div class="leading-tight">
                    <div>${machine.name}</div>
                    ${machine.load_coil_no ? `<small class="text-slate-500 font-normal">${machine.load_coil_no}</small>` : ''}
                </div>
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <select name="slide_size_id[]" class="w-full px-2 py-1 border border-slate-200 rounded text-sm focus:ring-1 focus:ring-blue-400 row-input slide-size-select" onchange="onSlideSizeChange(this)" disabled required>
                    ${sizeOptions}
                </select>
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="total_set_shift[]" step="1" min="0" value="" placeholder="-" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm focus:ring-1 focus:ring-blue-400 row-input total-set-shift" onchange="calculateSetPerHour(this)" onfocus="this.select()" disabled>
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="set_per_hour[]" step="1" min="0" value="" placeholder="-" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm bg-slate-50 set-per-hour calc-input" style="pointer-events: none;" disabled>
            </td>
            ${hourInputs}
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="actual_set_shift[]" step="1" min="0" value="" placeholder="-" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm bg-slate-50 actual-set calc-input" style="pointer-events: none;" disabled>
            </td>
            <!-- Workman and Staff per-row inputs hidden - entered globally at top -->
            <input type="hidden" name="machine_id[]" value="${machine.id}">
            <input type="hidden" name="report_date[]" value="${reportDate}">
            <input type="hidden" name="shift[]" value="${shift}">
        `;

        tableBody.appendChild(row);

        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    async function toggleRowInputs(checkbox, filledHoursOverride) {
        const row = checkbox.closest('tr');
        if (!row) return;

        let selectionCheck = null;

        if (checkbox.checked) {
            if (row.dataset.hasCoil !== '1') {
                const machineName = (row.querySelector('td:nth-child(2)')?.textContent || '').trim() || 'this machine';
                let proceedWithoutCoil = false;

                if (typeof Swal !== 'undefined' && Swal && typeof Swal.fire === 'function') {
                    const result = await Swal.fire({
                        title: 'Machine Has No Loaded Coil',
                        text: 'Are you sure you want to run this machine without loaded coil?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3b82f6',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'No',
                    });
                    proceedWithoutCoil = !!result.isConfirmed;
                } else {
                    proceedWithoutCoil = window.confirm(`Are you sure you want to run ${machineName} without loaded coil?`);
                }

                if (!proceedWithoutCoil) {
                    checkbox.checked = false;
                    updateSelectAllCheckbox();
                    return;
                }
            }

            checkbox.disabled = true;
            selectionCheck = await canSelectMachineForCurrentFilters(row, true);
            checkbox.disabled = false;

            if (!selectionCheck.allowed) {
                checkbox.checked = false;
                updateSelectAllCheckbox();
                return;
            }
        }

        const inputs = row.querySelectorAll('.row-input, .hour-input, .calc-input');
        const selectedInput = row.querySelector('.selected-machine-input');
        const isChecked = checkbox.checked;

        inputs.forEach(input => {
            input.disabled = !isChecked;
            // Clear locked state when unchecking
            if (!isChecked) {
                input.removeAttribute('data-locked');
                input.readOnly = false;
                input.style.pointerEvents = '';
                input.tabIndex = 0;
                input.classList.remove('bg-slate-200', 'cursor-not-allowed');
                input.title = '';
            }
        });

        if (selectedInput) {
            selectedInput.disabled = !isChecked;
        }

        // Lock hours already filled by other reports for the same machine+date+shift
        const filledHours = filledHoursOverride || (selectionCheck ? selectionCheck.filled_hours : null);
        if (isChecked && filledHours) {
            lockFilledHours(row, filledHours);
        }

        // Track slide size previous value for duplicate re-validation
        const slideSizeSelect = row.querySelector('.slide-size-select');
        if (slideSizeSelect) {
            if (isChecked) {
                slideSizeSelect.dataset.previousValue = slideSizeSelect.value;
            } else {
                delete slideSizeSelect.dataset.previousValue;
            }
        }

        updateSelectAllCheckbox();
        updateTotals();
        evaluateActualSetConstraint(false);
    }

    function updateTotals() {
        // Workman and Staff are now entered globally at the top, no per-row totals needed
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

        if (lockedCount === 0) return;

        const machineName = (row.querySelector('td:nth-child(2) div > div')?.textContent || '').trim();

        Swal.fire({
            title: 'Some Hours Already Reported',
            html: `<p><strong>${machineName}</strong> already has data for ${lockedCount} hour slot(s) on this date and shift.</p>
                   <p class="text-sm text-slate-500 mt-1">Those hour slots are locked. Remaining slots are editable.</p>`,
            icon: 'info',
            confirmButtonColor: '#3b82f6',
            confirmButtonText: 'OK',
        });
    }

    async function toggleAllRows(selectAll) {
        const checkboxes = document.querySelectorAll('.machine-checkbox');

        if (!selectAll) {
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
                toggleRowInputs(checkbox);
            });
            return;
        }

        let skippedCount = 0;

        for (const checkbox of checkboxes) {
            const row = checkbox.closest('tr');
            if (!row) continue;

            const selectionCheck = await canSelectMachineForCurrentFilters(row, false);
            if (!selectionCheck.allowed) {
                checkbox.checked = false;
                await toggleRowInputs(checkbox, null);
                skippedCount++;
                continue;
            }

            checkbox.checked = true;
            await toggleRowInputs(checkbox, selectionCheck.filled_hours || null);
        }

        if (skippedCount > 0) {
            Swal.fire({
                title: 'Some Machines Were Skipped',
                text: `${skippedCount} machine(s) were not selected because report date, shift and machine data already exists.`,
                icon: 'warning',
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'OK',
            });
        }

        evaluateActualSetConstraint(false);
    }

    function updateSelectAllCheckbox() {
        const totalCheckboxes = document.querySelectorAll('.machine-checkbox').length;
        const checkedCheckboxes = document.querySelectorAll('.machine-checkbox:checked').length;
        const selectAllCheckbox = document.getElementById('select_all');
        
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes;
            selectAllCheckbox.indeterminate = checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes;
        }
    }

    function calculateSetPerHour(input) {
        const row = input.closest('tr');
        if (!row) return;

        const totalSetShift = Math.max(parseFloat(input.value) || 0, 0);
        const setPerHour = totalSetShift / 12; // Divide by 12 hours

        const setPerHourInput = row.querySelector('.set-per-hour');
        if (setPerHourInput) {
            setPerHourInput.value = Math.round(setPerHour);
        }

        evaluateActualSetConstraint(false);
    }

    function sanitizeHourInput(input) {
        let val = input.value.trim();
        if (val === '-' || val === '') return;
        val = val.replace(/[^0-9]/g, '');
        input.value = val;
    }

    function calculateActualSet(input) {
        const row = input.closest('tr');
        if (!row) return;

        const hourInputs = row.querySelectorAll('.hour-input');
        let total = 0;
        hourInputs.forEach(inp => {
            const val = (inp.value || '').trim();
            if (val !== '-' && val !== '') {
                total += Math.max(parseFloat(val) || 0, 0);
            }
        });

        const actualSetInput = row.querySelector('.actual-set');
        if (actualSetInput) {
            actualSetInput.value = Math.round(total);
        }

        evaluateActualSetConstraint(false);
    }

    function getCheckedMachineRows() {
        return Array.from(document.querySelectorAll('tr.machine-row')).filter(row => {
            const checkbox = row.querySelector('.machine-checkbox');
            return !!(checkbox && checkbox.checked);
        });
    }

    function showAlertMessage(options) {
        if (typeof Swal !== 'undefined' && Swal && typeof Swal.fire === 'function') {
            return Swal.fire(options);
        }

        const fallbackText = options?.text || options?.title || 'Action required.';
        window.alert(fallbackText);
        return Promise.resolve();
    }

    function evaluateActualSetConstraint(showAlert = false) {
        const checkedRows = getCheckedMachineRows();
        const submitBtn = document.getElementById('createSubmitBtn');
        let invalidRowInfo = null;

        document.querySelectorAll('tr.machine-row.actual-set-invalid').forEach(row => {
            row.classList.remove('actual-set-invalid', 'bg-rose-50');
        });

        checkedRows.forEach(row => {
            const totalSetInput = row.querySelector('input[name="total_set_shift[]"]');
            const actualSetInput = row.querySelector('input[name="actual_set_shift[]"]');
            const machineName = row.querySelector('td:nth-child(2) div > div')?.textContent?.trim() || 'Selected machine';

            const totalSet = Math.max(parseFloat(totalSetInput?.value) || 0, 0);
            const actualSet = Math.max(parseFloat(actualSetInput?.value) || 0, 0);

            if (actualSet > totalSet) {
                row.classList.add('actual-set-invalid', 'bg-rose-50');

                if (!invalidRowInfo) {
                    invalidRowInfo = { machineName, totalSet, actualSet };
                }
            }
        });

        const hasInvalidRow = !!invalidRowInfo;
        if (submitBtn) {
            submitBtn.disabled = hasInvalidRow;
            submitBtn.classList.toggle('opacity-60', hasInvalidRow);
            submitBtn.classList.toggle('cursor-not-allowed', hasInvalidRow);
        }

        if (showAlert && hasInvalidRow) {
            showAlertMessage({
                title: 'Invalid Actual Set',
                text: `${invalidRowInfo.machineName}: Actual Set (${Math.round(invalidRowInfo.actualSet)}) cannot be greater than Total Set/Shift (${Math.round(invalidRowInfo.totalSet)}).`,
                icon: 'error',
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'OK',
            });
        }

        return !hasInvalidRow;
    }

    let previousShiftValue = '';

    function handleShiftChange(selectElement) {
        const newShift = selectElement.value;
        
        // If there's a previous value and it's different, show confirmation
        if (previousShiftValue && previousShiftValue !== newShift) {
            Swal.fire({
                title: 'Change Shift?',
                text: 'Are you sure you want to change the shift? This will update all row shifts.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, change it',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    updateShiftLabels(newShift);
                    updateAllRowShifts(newShift);
                    previousShiftValue = newShift;
                } else {
                    // Revert to previous value
                    selectElement.value = previousShiftValue;
                }
            });
            return;
        }
        
        // Update the labels and all row shifts
        updateShiftLabels(newShift);
        updateAllRowShifts(newShift);
        
        // Store the new value as previous for next change
        previousShiftValue = newShift;
    }

    function updateShiftLabels(shift) {
        const labels = document.querySelectorAll('.hour-label');
        const timePeriod = shift === 'Night' ? 'PM' : 'AM';
        
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

    function updateAllRowShifts(newShift) {
        // Update all hidden shift inputs in table rows
        const tableBody = document.getElementById('tableBody');
        if (!tableBody) return;

        const rows = tableBody.querySelectorAll('tr.machine-row');
        rows.forEach(row => {
            const shiftInput = row.querySelector('input[name="shift[]"]');
            if (shiftInput) {
                shiftInput.value = newShift;
            }
        });
    }

    function updateAllRowDates(newReportDate) {
        const tableBody = document.getElementById('tableBody');
        if (!tableBody) return;

        const rows = tableBody.querySelectorAll('tr.machine-row');
        rows.forEach(row => {
            const reportDateInput = row.querySelector('input[name="report_date[]"]');
            if (reportDateInput) {
                reportDateInput.value = newReportDate;
            }
        });
    }

    let negativeWarningTimeout;
    function showNegativeWarning() {
        let warning = document.getElementById('negativeValueWarning');

        if (!warning) {
            warning = document.createElement('div');
            warning.id = 'negativeValueWarning';
            warning.className = 'negative-warning-toast';
            warning.textContent = 'Negative values are not allowed. Value reset to 0.';
            document.body.appendChild(warning);
        }

        warning.classList.add('show');
        clearTimeout(negativeWarningTimeout);
        negativeWarningTimeout = setTimeout(() => {
            warning.classList.remove('show');
        }, 1800);
    }

    function enforceNonNegative(input) {
        if (!input || input.type !== 'number') return;

        if (input.value === '') return;

        const numericValue = parseFloat(input.value);
        if (Number.isNaN(numericValue)) return;

        if (numericValue < 0) {
            input.value = '0';
            showNegativeWarning();
            if (typeof input.onchange === 'function') {
                input.onchange();
            }
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function enforceWholeNumber(input) {
        if (!input || input.type !== 'number') return;

        if (input.value === '') return;

        const numericValue = parseFloat(input.value);
        if (Number.isNaN(numericValue)) return;

        const roundedValue = Math.round(numericValue);
        if (numericValue !== roundedValue) {
            input.value = String(roundedValue);
            if (typeof input.onchange === 'function') {
                input.onchange();
            }
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function scrollTableHorizontal(direction) {
        const tableScrollContainer = document.getElementById('tableScrollContainer');
        if (!tableScrollContainer) return;

        const amount = 450;
        const delta = direction === 'left' ? -amount : amount;

        tableScrollContainer.scrollBy({
            left: delta,
            behavior: 'auto'
        });
    }

    function initializeTopScroll() {
        const topScrollContainer = document.getElementById('topScrollContainer');
        const topScrollContent = document.getElementById('topScrollContent');
        const tableScrollContainer = document.getElementById('tableScrollContainer');
        const productionTable = document.getElementById('productionTable');

        if (!topScrollContainer || !topScrollContent || !tableScrollContainer || !productionTable) {
            return;
        }

        let syncingFromTop = false;
        let syncingFromTable = false;

        const syncTopWidth = () => {
            topScrollContent.style.width = `${productionTable.scrollWidth}px`;
        };

        topScrollContainer.addEventListener('scroll', () => {
            if (syncingFromTable) return;
            syncingFromTop = true;
            tableScrollContainer.scrollLeft = topScrollContainer.scrollLeft;
            syncingFromTop = false;
        });

        tableScrollContainer.addEventListener('scroll', () => {
            if (syncingFromTop) return;
            syncingFromTable = true;
            topScrollContainer.scrollLeft = tableScrollContainer.scrollLeft;
            syncingFromTable = false;
        });

        syncTopWidth();
        window.addEventListener('resize', syncTopWidth);
    }

    // Initialize with all machines as rows
    window.addEventListener('load', function() {
        // Auto-select shift based on current time (8 AM to 8 PM = Morning, else Night)
        const currentHour = new Date().getHours();
        const shiftDropdown = document.getElementById('shift');
        if (shiftDropdown) {
            if (currentHour >= 8 && currentHour < 20) {
                shiftDropdown.value = 'Morning';
            } else {
                shiftDropdown.value = 'Night';
            }
            // Set the initial value so confirmation triggers on first change
            previousShiftValue = shiftDropdown.value;
        }

        const reportDateInput = document.getElementById('report_date');
        if (reportDateInput) {
            reportDateInput.addEventListener('change', function () {
                updateAllRowDates(this.value);
            });
        }

        machines.forEach(machine => {
            addMachineRow(machine);
        });

        const productionForm = document.getElementById('productionReportCreateForm');
        if (productionForm) {
            productionForm.addEventListener('input', function (event) {
                // Prevent changes on locked hour inputs
                if (event.target && event.target.getAttribute('data-locked') === '1') {
                    event.target.value = '';
                    return;
                }

                if (event.target && event.target.matches('input[type="number"]')) {
                    enforceNonNegative(event.target);
                }

                if (event.target && (event.target.matches('input[name="total_set_shift[]"]') || event.target.matches('input[name^="hour_"]'))) {
                    evaluateActualSetConstraint(false);
                }
            });

            productionForm.addEventListener('blur', function (event) {
                if (event.target && event.target.matches('input[type="number"]')) {
                    enforceNonNegative(event.target);
                    enforceWholeNumber(event.target);
                }
            }, true);
        }

        initializeTopScroll();
        evaluateActualSetConstraint(false);
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });

    function validateFormSubmission() {
        const checkedCheckboxes = document.querySelectorAll('.machine-checkbox:checked');
        
        if (checkedCheckboxes.length === 0) {
            return { valid: false, message: 'Please select at least one machine to create production reports.' };
        }

        // Validate that slide size is selected for each checked row
        let slideSizeSelected = true;
        checkedCheckboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const slideSize = row.querySelector('select[name="slide_size_id[]"]');
            if (!slideSize.value) {
                slideSizeSelected = false;
            }
        });

        if (!slideSizeSelected) {
            return { valid: false, message: 'Please select slide size for all selected machines.' };
        }

        if (!evaluateActualSetConstraint(false)) {
            return { valid: false, message: 'Actual Set cannot be greater than Total Set/Shift for selected machines.' };
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

    function recalculateAllFields() {
        // Get all checked machines (only those will be submitted)
        const checkedRows = getCheckedMachineRows();
        
        checkedRows.forEach(row => {
            // Get total_set_shift value
            const totalSetShiftInput = row.querySelector('.total-set-shift');
            if (totalSetShiftInput) {
                const totalValue = parseFloat(totalSetShiftInput.value) || 0;
                const setPerHourInput = row.querySelector('.set-per-hour');
                if (setPerHourInput) {
                    const calculatedValue = totalValue / 12;
                    setPerHourInput.value = Math.round(calculatedValue);
                    setPerHourInput.disabled = false;  // Ensure it's not disabled for submission
                }
            }

            // Get sum of all hour inputs for actual_set_shift
            const hourInputs = row.querySelectorAll('input[name^="hour_"]');
            let totalHours = 0;
            hourInputs.forEach(input => {
                totalHours += parseFloat(input.value) || 0;
            });
            const actualSetInput = row.querySelector('.actual-set');
            if (actualSetInput) {
                actualSetInput.value = Math.round(totalHours);
                actualSetInput.disabled = false;  // Ensure it's not disabled for submission
            }
        });
    }

    document.getElementById('productionReportCreateForm').addEventListener('submit', async function (event) {
        event.preventDefault();

        // Recalculate all fields before submission
        recalculateAllFields();

        if (!evaluateActualSetConstraint(true)) {
            return;
        }

        clearValidationErrors();

        const validation = validateFormSubmission();
        if (!validation.valid) {
            showAlertMessage({
                title: 'Invalid Form',
                text: validation.message,
                icon: 'error',
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'OK',
            });
            return;
        }

        const submitBtn = document.getElementById('createSubmitBtn');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';

        try {
            // Create form data and explicitly verify calculated fields are included
            const formData = new FormData(this);
            
            // Verify set_per_hour and actual_set_shift are in FormData
            const setPerHourValues = formData.getAll('set_per_hour[]');
            const actualSetValues = formData.getAll('actual_set_shift[]');
            
            console.log('set_per_hour values:', setPerHourValues);
            console.log('actual_set_shift values:', actualSetValues);

            const response = await fetch(this.action, {
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
                    title: 'Success!',
                    text: 'Production reports created successfully.',
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

            Swal.fire({
                title: 'Error',
                text: data.message || 'Something went wrong. Please try again.',
                icon: 'error',
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'OK',
            });
        } catch (error) {
            Swal.fire({
                title: 'Network Error',
                text: 'Please check your connection and try again.',
                icon: 'error',
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'OK',
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });

    // ── Draft Feature ──
    let formSubmitted = false;
    let draftSaving = false;

    function isFormDirty() {
        const checkedBoxes = document.querySelectorAll('.machine-checkbox:checked');
        if (checkedBoxes.length > 0) return true;
        const hourInputs = document.querySelectorAll('input[name^="hour_"]');
        for (const input of hourInputs) {
            if (input.value && parseFloat(input.value) > 0) return true;
        }
        const totalSetInputs = document.querySelectorAll('.total-set-shift');
        for (const input of totalSetInputs) {
            if (input.value && parseFloat(input.value) > 0) return true;
        }
        return false;
    }

    async function saveAsDraft() {
        const checkedCheckboxes = document.querySelectorAll('.machine-checkbox:checked');
        if (checkedCheckboxes.length === 0) {
            Swal.fire('No Machines Selected', 'Please select at least one machine to save as draft.', 'warning');
            return;
        }

        draftSaving = true;
        const draftBtn = document.getElementById('saveDraftBtn');
        const originalHTML = draftBtn.innerHTML;
        draftBtn.disabled = true;
        draftBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 inline-block mr-1 -mt-0.5 animate-spin"></i> Saving...';

        try {
            recalculateAllFields();
            const form = document.getElementById('productionReportCreateForm');
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

    // Mark form as submitted on successful create
    const origSubmitHandler = document.getElementById('productionReportCreateForm');
    origSubmitHandler.addEventListener('formSubmitted', () => { formSubmitted = true; });

    // Intercept navigation away (back button, links, closing tab)
    window.addEventListener('beforeunload', function (e) {
        if (formSubmitted || draftSaving) return;
        if (!isFormDirty()) return;
        e.preventDefault();
        e.returnValue = '';
    });

    // Intercept link clicks within the page for SweetAlert draft prompt
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
    .negative-warning-toast {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 9999;
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        border-radius: 0.5rem;
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
        font-weight: 500;
        box-shadow: 0 10px 20px -10px rgba(185, 28, 28, 0.35);
        opacity: 0;
        transform: translateY(-8px);
        pointer-events: none;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .negative-warning-toast.show {
        opacity: 1;
        transform: translateY(0);
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

    #topScrollContainer,
    #tableScrollContainer {
        scrollbar-width: thin;
        scrollbar-color: #64748b #e2e8f0;
    }

    #topScrollContainer::-webkit-scrollbar,
    #tableScrollContainer::-webkit-scrollbar {
        height: 12px;
    }

    #topScrollContainer::-webkit-scrollbar-track,
    #tableScrollContainer::-webkit-scrollbar-track {
        background: linear-gradient(90deg, #f1f5f9 0%, #e2e8f0 100%);
        border-radius: 9999px;
        border: 1px solid #cbd5e1;
    }

    #topScrollContainer::-webkit-scrollbar-thumb,
    #tableScrollContainer::-webkit-scrollbar-thumb {
        background: linear-gradient(90deg, #64748b 0%, #475569 100%);
        border-radius: 9999px;
        border: 2px solid #e2e8f0;
    }

    #topScrollContainer::-webkit-scrollbar-thumb:hover,
    #tableScrollContainer::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(90deg, #475569 0%, #334155 100%);
    }

    #topScrollContainer {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        background: #f8fafc;
        padding: 2px;
    }

    #tableScrollContainer {
        border-radius: 0.5rem;
    }

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

    tr.machine-row:not(:has(.machine-checkbox:checked)) {
        background-color: #f1f5f9 !important;
        opacity: 0.6;
    }
    tr.machine-row:not(:has(.machine-checkbox:checked)):hover {
        background-color: #e2e8f0 !important;
    }
</style>
@endsection
