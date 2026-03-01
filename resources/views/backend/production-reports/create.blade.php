@extends('backend.layout.app')

@section('title', 'Create Production Report')

@section('page-title', 'Production Reports Management')

@section('breadcrumb')
    <a href="{{ route('admin.production-reports.index') }}" class="text-slate-600 hover:text-slate-900">Production Reports</a>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Create New</span>
@endsection

@section('content')
<div class="p-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center">
                    <i data-lucide="plus" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Production Report - Bulk Entry</h2>
                    <p class="text-sm text-slate-500">Enter production data for multiple machines at once</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.production-reports.store') }}" method="POST" class="p-6" onsubmit="return validateFormSubmission(event)">
            @csrf

            <!-- Basic Filters -->
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
                            value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                            onchange="updateShiftLabels(this.value)"
                        >
                            <option value="">Select Shift</option>
                            <option value="Morning" selected>Morning</option>
                            <option value="Night">Night</option>
                        </select>
                    </div>

                    <!-- Select All Checkbox -->
                    <div class="flex items-end">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                id="select_all"
                                onclick="toggleAllRows(this.checked)"
                                class="w-4 h-4 rounded border-slate-300"
                            >
                            <span class="text-sm font-medium text-slate-700">Select All Machines</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Production Table -->
            <div class="mb-8 overflow-x-auto">
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>How to use:</strong> Check the checkbox next to each machine to enable data entry. The selected machines will be included when you save the form. All fields must be filled for selected rows.
                    </p>
                </div>
                <table class="w-full border-collapse" id="productionTable">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="border border-slate-300 px-3 py-3 text-center text-xs font-semibold text-slate-700 bg-slate-50 min-w-20">Select</th>
                            <th class="border border-slate-300 px-3 py-3 text-left text-xs font-semibold text-slate-700 min-w-44">Machine</th>
                            <th class="border border-slate-300 px-3 py-3 text-left text-xs font-semibold text-slate-700 min-w-40">Slide Size</th>
                            <th class="border border-slate-300 px-3 py-3 text-center text-xs font-semibold text-slate-700 min-w-24">Total Set/Shift</th>
                            <th class="border border-slate-300 px-3 py-3 text-center text-xs font-semibold text-slate-700 min-w-24">Set/Hour</th>
                            <!-- Hourly Columns -->
                            <th class="border border-slate-300 px-2 py-3 text-center text-xs font-semibold text-slate-700 min-w-20 hour-label" data-hour="8-9">8AM-9AM</th>
                            <th class="border border-slate-300 px-2 py-3 text-center text-xs font-semibold text-slate-700 min-w-20 hour-label" data-hour="9-10">9AM-10AM</th>
                            <th class="border border-slate-300 px-2 py-3 text-center text-xs font-semibold text-slate-700 min-w-20 hour-label" data-hour="10-11">10AM-11AM</th>
                            <th class="border border-slate-300 px-2 py-3 text-center text-xs font-semibold text-slate-700 min-w-20 hour-label" data-hour="11-12">11AM-12PM</th>
                            <th class="border border-slate-300 px-2 py-3 text-center text-xs font-semibold text-slate-700 min-w-20 hour-label" data-hour="12-1">12PM-1PM</th>
                            <th class="border border-slate-300 px-2 py-3 text-center text-xs font-semibold text-slate-700 min-w-20 hour-label" data-hour="1-2">1PM-2PM</th>
                            <th class="border border-slate-300 px-2 py-3 text-center text-xs font-semibold text-slate-700 min-w-20 hour-label" data-hour="2-3">2PM-3PM</th>
                            <th class="border border-slate-300 px-2 py-3 text-center text-xs font-semibold text-slate-700 min-w-20 hour-label" data-hour="3-4">3PM-4PM</th>
                            <th class="border border-slate-300 px-2 py-3 text-center text-xs font-semibold text-slate-700 min-w-20 hour-label" data-hour="4-5">4PM-5PM</th>
                            <th class="border border-slate-300 px-2 py-3 text-center text-xs font-semibold text-slate-700 min-w-20 hour-label" data-hour="5-6">5PM-6PM</th>
                            <th class="border border-slate-300 px-2 py-3 text-center text-xs font-semibold text-slate-700 min-w-20 hour-label" data-hour="6-7">6PM-7PM</th>
                            <th class="border border-slate-300 px-2 py-3 text-center text-xs font-semibold text-slate-700 min-w-20 hour-label" data-hour="7-8">7PM-8PM</th>
                            <th class="border border-slate-300 px-3 py-3 text-center text-xs font-semibold text-slate-700 min-w-20">Actual Set</th>
                            <th class="border border-slate-300 px-3 py-3 text-center text-xs font-semibold text-slate-700 min-w-20">Workman</th>
                            <th class="border border-slate-300 px-3 py-3 text-center text-xs font-semibold text-slate-700 min-w-20">Staff</th>
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
                <a href="{{ route('admin.production-reports.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white gradient-primary rounded-lg hover:shadow-lg transition-all">
                    Create Selected Reports
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const machines = @json($machines);
    const slideSizes = @json($slideSizes);

    function addMachineRow(machine) {
        const tableBody = document.getElementById('tableBody');
        const reportDate = document.getElementById('report_date').value;
        const shift = document.getElementById('shift').value || 'Morning';
        
        const row = document.createElement('tr');
        row.className = 'hover:bg-slate-50 border-b border-slate-200 machine-row';
        row.id = `row-${machine.id}`;
        row.dataset.machineId = machine.id;
        
        let sizeOptions = '<option value="">Select Size</option>';
        slideSizes.forEach(size => {
            sizeOptions += `<option value="${size.id}">${size.name} (${size.size})</option>`;
        });

        const hourFields = [
            'hour_8_9', 'hour_9_10', 'hour_10_11', 'hour_11_12',
            'hour_12_1', 'hour_1_2', 'hour_2_3', 'hour_3_4',
            'hour_4_5', 'hour_5_6', 'hour_6_7', 'hour_7_8'
        ];

        let hourInputs = '';
        hourFields.forEach(field => {
            hourInputs += `<td class="border border-slate-300 px-2 py-2">
                <input type="number" name="${field}[]" step="0.01" value="0" class="w-full px-1 py-1 border border-slate-200 rounded text-center text-sm focus:ring-1 focus:ring-blue-400 hour-input" onchange="calculateActualSet(this)" onfocus="this.select()" disabled>
            </td>`;
        });

        row.innerHTML = `
            <td class="border border-slate-300 px-3 py-2 text-center">
                <input type="checkbox" class="w-4 h-4 rounded border-slate-300 machine-checkbox" onchange="toggleRowInputs(this)">
                <input type="hidden" name="selected_machines[]" value="${machine.id}" class="selected-machine-input" disabled>
            </td>
            <td class="border border-slate-300 px-3 py-2 font-medium text-slate-900">
                ${machine.name}
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <select name="slide_size_id[]" class="w-full px-2 py-1 border border-slate-200 rounded text-sm focus:ring-1 focus:ring-blue-400 row-input" disabled required>
                    ${sizeOptions}
                </select>
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="total_set_shift[]" step="0.01" value="0" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm focus:ring-1 focus:ring-blue-400 row-input total-set-shift" onchange="calculateSetPerHour(this)" onfocus="this.select()" disabled>
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="set_per_hour[]" step="0.01" value="0" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm bg-slate-50 set-per-hour" readonly>
            </td>
            ${hourInputs}
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="actual_set_shift[]" step="0.01" value="0" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm bg-slate-50 actual-set" readonly>
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="workman_count[]" step="1" min="0" value="0" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm focus:ring-1 focus:ring-blue-400 row-input" onfocus="this.select()" disabled>
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="staff_count[]" step="1" min="0" value="0" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm focus:ring-1 focus:ring-blue-400 row-input" onfocus="this.select()" disabled>
            </td>
            <input type="hidden" name="machine_id[]" value="${machine.id}">
            <input type="hidden" name="report_date[]" value="${reportDate}">
            <input type="hidden" name="shift[]" value="${shift}">
        `;

        tableBody.appendChild(row);

        // Update lucide icons for the new row
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    function toggleRowInputs(checkbox) {
        const row = checkbox.closest('tr');
        if (!row) return;

        const inputs = row.querySelectorAll('.row-input, .hour-input');
        const selectedInput = row.querySelector('.selected-machine-input');
        const isChecked = checkbox.checked;

        inputs.forEach(input => {
            input.disabled = !isChecked;
        });

        if (selectedInput) {
            selectedInput.disabled = !isChecked;
        }

        updateSelectAllCheckbox();
    }

    function toggleAllRows(selectAll) {
        document.querySelectorAll('.machine-checkbox').forEach(checkbox => {
            checkbox.checked = selectAll;
            toggleRowInputs(checkbox);
        });
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

        const totalSetShift = parseFloat(input.value) || 0;
        const setPerHour = totalSetShift / 12; // Divide by 12 hours

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

    // Initialize with all machines as rows
    window.addEventListener('load', function() {
        machines.forEach(machine => {
            addMachineRow(machine);
        });
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });

    function validateFormSubmission(event) {
        const checkedCheckboxes = document.querySelectorAll('.machine-checkbox:checked');
        
        if (checkedCheckboxes.length === 0) {
            event.preventDefault();
            alert('Please select at least one machine to create production reports.');
            return false;
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
            event.preventDefault();
            alert('Please select slide size for all selected machines.');
            return false;
        }

        return true;
    }

</script>

<style>
    table {
        border-collapse: collapse;
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
