@extends('backend.layout.app')

@section('title', 'Edit Production Report')

@section('page-title', 'Production Reports Management')

@section('breadcrumb')
    <a href="{{ route('admin.production-reports.index') }}" class="text-slate-600 hover:text-slate-900">Production Reports</a>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Edit Report #{{ $productionReport->id }}</span>
@endsection

@section('content')
<div class="p-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center">
                    <i data-lucide="edit" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Edit Production Report</h2>
                    <p class="text-sm text-slate-500">Update production report data</p>
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
            <div class="mb-8 overflow-x-auto">
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>How to use:</strong> The current report's machine is already checked and filled. You can check other machines to add more reports, or update the existing one.
                    </p>
                </div>
                <table class="w-full border-collapse" id="productionTable">
                    <thead class="bg-slate-100">
                        <tr>
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

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.production-reports.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">
                    Cancel
                </a>
                <button type="submit" id="editSubmitBtn" class="px-6 py-2 text-sm font-medium text-white gradient-primary rounded-lg hover:shadow-lg transition-all">
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

    function syncCommonFields() {
        const reportDate = document.getElementById('report_date').value;
        const shift = document.getElementById('shift').value || 'Morning';

        const reportDateInput = document.querySelector('input[name="report_date[]"]');
        const shiftInput = document.querySelector('input[name="shift[]"]');

        if (reportDateInput) reportDateInput.value = reportDate;
        if (shiftInput) shiftInput.value = shift;
    }

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

        let hourInputs = '';
        hourFields.forEach(field => {
            const value = prefillData ? parseFloat(prefillData[field]) || 0 : 0;
            const disabled = prefillData ? '' : 'disabled';
            hourInputs += `<td class="border border-slate-300 px-2 py-2">
                <input type="number" name="${field}[]" step="0.01" value="${value}" class="w-full px-1 py-1 border border-slate-200 rounded text-center text-sm focus:ring-1 focus:ring-blue-400 hour-input" onchange="calculateActualSet(this)" onfocus="this.select()" ${disabled}>
            </td>`;
        });

        const totalSetShift = prefillData ? parseFloat(prefillData.total_set_shift) || 0 : 0;
        const setPerHour = prefillData ? parseFloat(prefillData.set_per_hour) || 0 : 0;
        const actualSet = prefillData ? parseFloat(prefillData.actual_set_shift) || 0 : 0;
        const workmanCount = prefillData ? parseInt(prefillData.workman_count) || 0 : 0;
        const staffCount = prefillData ? parseInt(prefillData.staff_count) || 0 : 0;

        row.innerHTML = `
            <td class="border border-slate-300 px-3 py-2 font-medium text-slate-900">
                ${machine.name}
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <select name="slide_size_id[]" class="w-full px-2 py-1 border border-slate-200 rounded text-sm focus:ring-1 focus:ring-blue-400 row-input" required>
                    ${sizeOptions}
                </select>
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="total_set_shift[]" step="0.01" value="${totalSetShift}" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm focus:ring-1 focus:ring-blue-400 row-input total-set-shift" onchange="calculateSetPerHour(this)" onfocus="this.select()">
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="set_per_hour[]" step="0.01" value="${setPerHour.toFixed(2)}" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm bg-slate-50 set-per-hour" readonly>
            </td>
            ${hourInputs}
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="actual_set_shift[]" step="0.01" value="${actualSet.toFixed(2)}" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm bg-slate-50 actual-set" readonly>
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="workman_count[]" step="1" min="0" value="${workmanCount}" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm focus:ring-1 focus:ring-blue-400 row-input" onfocus="this.select()">
            </td>
            <td class="border border-slate-300 px-3 py-2">
                <input type="number" name="staff_count[]" step="1" min="0" value="${staffCount}" class="w-full px-2 py-1 border border-slate-200 rounded text-center text-sm focus:ring-1 focus:ring-blue-400 row-input" onfocus="this.select()">
            </td>
            <input type="hidden" name="selected_machines[]" value="${machine.id}">
            <input type="hidden" name="machine_id[]" value="${machine.id}">
            <input type="hidden" name="report_date[]" value="${reportDate}">
            <input type="hidden" name="shift[]" value="${shift}">
        `;

        tableBody.appendChild(row);

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
    window.addEventListener('load', function() {
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
            alert(validation.message);
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
                window.location.href = data.redirect || "{{ route('admin.production-reports.index') }}";
                return;
            }

            if (response.status === 422) {
                markValidationErrors(data.errors);
                const firstError = data.message || (data.errors ? Object.values(data.errors).flat()[0] : 'Validation failed.');
                alert(firstError);
                return;
            }

            alert(data.message || 'Something went wrong. Please try again.');
        } catch (error) {
            alert('Network error. Please check your connection and try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
</script>

<style>
    table {
        border-collapse: collapse;
    }
</style>
@endsection
