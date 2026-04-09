<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductionReport;
use App\Models\Machine;
use App\Models\Item;
use App\Models\CoilMachineTrack;
use App\Models\CoilLoadNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ProductionReportController extends Controller
{
    /**
     * Display a listing of production reports.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $mode = $request->query('mode', 'active');

        $query = ProductionReport::query();

        if ($mode === 'deleted') {
            $query->where('is_deleted', true);
        } elseif ($mode === 'draft') {
            $query->where('is_deleted', false)->where('is_draft', true);
        } elseif ($mode === 'all') {
            // no filter
        } else {
            $mode = 'active';
            $query->where('is_deleted', false)->where('is_draft', false);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->whereHas('machine', function ($builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%");
                })->orWhere('report_date', 'like', "%{$search}%")
                  ->orWhere('shift', 'like', "%{$search}%");
            });
        }

        $productionReports = $query->with(['machine', 'slideSize'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('backend.production-reports.index', compact('productionReports', 'mode', 'search'));
    }

    /**
     * Display production reports for SF001.
     */
    public function sf001(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $mode = $request->query('mode', 'active');

        $query = ProductionReport::query();

        if ($mode === 'deleted') {
            $query->where('is_deleted', true);
        } elseif ($mode === 'draft') {
            $query->where('is_deleted', false)->where('is_draft', true);
        } elseif ($mode === 'all') {
            // no filter
        } else {
            $mode = 'active';
            $query->where('is_deleted', false)->where('is_draft', false);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->whereHas('machine', function ($builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%");
                })->orWhere('report_date', 'like', "%{$search}%")
                  ->orWhere('shift', 'like', "%{$search}%");
            });
        }

        $productionReports = $query->with(['machine', 'slideSize'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

             return view('backend.production-reports.index', compact('productionReports', 'mode', 'search'));
    }

    /**
     * Display production reports for SF002.
     */
    public function sf002(Request $request): View
    {
        return view('backend.production-reports.sf002.list');
    }

    /**
     * Display production reports for SF003.
     */
    public function sf003(Request $request): RedirectResponse
    {
        return redirect()->route('admin.production-reports.sf003.stock');
    }

    /**
     * Show the form for creating a new production report.
     */
    public function create(): View
    {
        $machines = Machine::with(['coil:id,coil_no'])
            ->where('is_deleted', false)
            ->where('status', true)
            ->get();

        // Attach active load coil_no from coil_load_numbers for machines with loaded coils
        $machinesWithCoil = $machines->whereNotNull('coil_id');
        if ($machinesWithCoil->isNotEmpty()) {
            foreach ($machinesWithCoil as $machine) {
                $activeLoadTrack = CoilMachineTrack::query()
                    ->where('machine_id', $machine->id)
                    ->where('coil_id', $machine->coil_id)
                    ->where('type', CoilMachineTrack::ACTION_LOAD)
                    ->where('is_deleted', 0)
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('coil_machine_track as unload_tracks')
                            ->whereColumn('unload_tracks.reference_track_id', 'coil_machine_track.id')
                            ->where('unload_tracks.type', CoilMachineTrack::ACTION_UNLOAD)
                            ->where('unload_tracks.is_deleted', 0);
                    })
                    ->orderByDesc('id')
                    ->first(['id']);

                $machine->setAttribute('load_coil_no', null);
                $machine->setAttribute('load_coil_number_id', null);
                if ($activeLoadTrack) {
                    $loadNumber = CoilLoadNumber::query()
                        ->where('coil_machine_track_id', $activeLoadTrack->id)
                        ->first(['id', 'coil_no']);
                    $machine->setAttribute('load_coil_no', $loadNumber ? $loadNumber->coil_no : null);
                    $machine->setAttribute('load_coil_number_id', $loadNumber ? $loadNumber->id : null);
                }
            }
        }

        $slideSizes = Item::where('is_deleted', false)
            ->where('status', true)
            ->where('category', 'SF1-SF2')
            ->get();

        return view('backend.production-reports.create', compact('machines', 'slideSizes'));
    }

    /**
     * Check duplicate for report_date + shift + machine.
     */
    public function checkDuplicate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'report_date' => 'required|date',
            'shift' => 'required|string|in:Morning,Night',
            'slide_size_id' => 'nullable|exists:items,id',
            'coil_id' => 'nullable|exists:coil_stock,id',
        ]);

        $query = ProductionReport::query()
            ->where('machine_id', $validated['machine_id'])
            ->where('report_date', $validated['report_date'])
            ->where('shift', $validated['shift'])
            ->where('is_deleted', false)
            ->where('is_draft', false);

        if (!empty($validated['slide_size_id'])) {
            $query->where('slide_size_id', $validated['slide_size_id']);
        }

        if (!empty($validated['coil_id'])) {
            $query->where('coil_id', $validated['coil_id']);
        } else {
            $query->whereNull('coil_id');
        }

        $exists = $query->exists();

        // Find hours already filled by other reports for the same machine+date+shift (any coil/item)
        $filledHours = [];
        if (!$exists) {
            $hourFields = [
                'hour_8_9', 'hour_9_10', 'hour_10_11', 'hour_11_12',
                'hour_12_1', 'hour_1_2', 'hour_2_3', 'hour_3_4',
                'hour_4_5', 'hour_5_6', 'hour_6_7', 'hour_7_8',
            ];

            $existingReports = ProductionReport::query()
                ->where('machine_id', $validated['machine_id'])
                ->where('report_date', $validated['report_date'])
                ->where('shift', $validated['shift'])
                ->where('is_deleted', false)
                ->where('is_draft', false)
                ->get($hourFields);

            // Check each hour individually: only mark as filled if it has a non-null value
            foreach ($hourFields as $field) {
                $filledHours[$field] = false;
            }
            foreach ($existingReports as $report) {
                foreach ($hourFields as $field) {
                    if ($report->$field !== null) {
                        $filledHours[$field] = true;
                    }
                }
            }
        }

        return response()->json([
            'exists' => $exists,
            'message' => $exists
                ? 'A report with the same date, shift, machine, item, and coil already exists.'
                : 'This combination is available.',
            'filled_hours' => $filledHours,
        ]);
    }

    /**
     * Store a newly created production report in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $isDraft = (bool) $request->input('is_draft', false);

        // Slide size validation: required for normal save, nullable for drafts
        $slideSizeRules = $isDraft
            ? ['nullable', Rule::exists('items', 'id')->where(function ($query) {
                    $query->where('is_deleted', false)->where('status', true)->where('category', 'SF1-SF2');
                })]
            : ['required', Rule::exists('items', 'id')->where(function ($query) {
                    $query->where('is_deleted', false)->where('status', true)->where('category', 'SF1-SF2');
                })];

        // Validate arrays
        $validated = $request->validate([
            'selected_machines' => 'nullable|array',
            'machine_id' => 'required|array',
            'machine_id.*' => 'required|exists:machines,id',
            'coil_id' => 'nullable|array',
            'coil_id.*' => 'nullable|exists:coil_stock,id',
            'coil_number_id' => 'nullable|array',
            'coil_number_id.*' => 'nullable|exists:coil_load_numbers,id',
            'slide_size_id' => $isDraft ? 'nullable|array' : 'required|array',
            'slide_size_id.*' => $slideSizeRules,
            'report_date' => 'required|array',
            'report_date.*' => 'required|date',
            'shift' => 'required|array',
            'shift.*' => 'required|string|in:Morning,Night',
            'total_set_shift' => 'nullable|array',
            'total_set_shift.*' => 'nullable|numeric|min:0',
            'set_per_hour' => 'nullable|array',
            'set_per_hour.*' => 'nullable|numeric|min:0',
            'actual_set_shift' => 'nullable|array',
            'actual_set_shift.*' => 'nullable|numeric|min:0',
            'workman_count' => 'nullable|numeric|min:0',
            'staff_count' => 'nullable|numeric|min:0',
            'hour_8_9' => 'nullable|array',
            'hour_8_9.*' => 'nullable|numeric|min:0',
            'hour_9_10' => 'nullable|array',
            'hour_9_10.*' => 'nullable|numeric|min:0',
            'hour_10_11' => 'nullable|array',
            'hour_10_11.*' => 'nullable|numeric|min:0',
            'hour_11_12' => 'nullable|array',
            'hour_11_12.*' => 'nullable|numeric|min:0',
            'hour_12_1' => 'nullable|array',
            'hour_12_1.*' => 'nullable|numeric|min:0',
            'hour_1_2' => 'nullable|array',
            'hour_1_2.*' => 'nullable|numeric|min:0',
            'hour_2_3' => 'nullable|array',
            'hour_2_3.*' => 'nullable|numeric|min:0',
            'hour_3_4' => 'nullable|array',
            'hour_3_4.*' => 'nullable|numeric|min:0',
            'hour_4_5' => 'nullable|array',
            'hour_4_5.*' => 'nullable|numeric|min:0',
            'hour_5_6' => 'nullable|array',
            'hour_5_6.*' => 'nullable|numeric|min:0',
            'hour_6_7' => 'nullable|array',
            'hour_6_7.*' => 'nullable|numeric|min:0',
            'hour_7_8' => 'nullable|array',
            'hour_7_8.*' => 'nullable|numeric|min:0',
        ]);

        $selectedMachines = $validated['selected_machines'] ?? [];
        
        // Log incoming data to debug set_per_hour and actual_set_shift
        Log::info('ProductionReport store() - Debug data:', [
            'set_per_hour_array' => $validated['set_per_hour'] ?? [],
            'actual_set_shift_array' => $validated['actual_set_shift'] ?? [],
        ]);
        
        if (empty($selectedMachines)) {
            $message = 'Please select at least one machine.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return redirect()->route('admin.production-reports.index')
                ->with('error', $message);
        }

        $this->validateDuplicateCombinations($validated, null, $isDraft);

        // Validate no hour overlap with existing reports for the same machine+date+shift
        if (!$isDraft) {
            $this->validateHourOverlap($validated);
        }

        $createdCount = 0;

        foreach ($selectedMachines as $i => $machineId) {
            $data = [
                'machine_id' => $machineId,
                'coil_id' => $validated['coil_id'][$i] ?? null,                   // coil_stock.id
                'coil_number_id' => $validated['coil_number_id'][$i] ?? null,     // coil_load_numbers.id
                'slide_size_id' => $validated['slide_size_id'][$i] ?? null,
                'report_date' => $validated['report_date'][$i] ?? null,
                'shift' => $validated['shift'][$i] ?? null,
                'total_set_shift' => $validated['total_set_shift'][$i] ?? null,
                'set_per_hour' => $validated['set_per_hour'][$i] ?? null,
                'actual_set_shift' => $validated['actual_set_shift'][$i] ?? null,
                'hour_8_9' => $validated['hour_8_9'][$i] ?? null,
                'hour_9_10' => $validated['hour_9_10'][$i] ?? null,
                'hour_10_11' => $validated['hour_10_11'][$i] ?? null,
                'hour_11_12' => $validated['hour_11_12'][$i] ?? null,
                'hour_12_1' => $validated['hour_12_1'][$i] ?? null,
                'hour_1_2' => $validated['hour_1_2'][$i] ?? null,
                'hour_2_3' => $validated['hour_2_3'][$i] ?? null,
                'hour_3_4' => $validated['hour_3_4'][$i] ?? null,
                'hour_4_5' => $validated['hour_4_5'][$i] ?? null,
                'hour_5_6' => $validated['hour_5_6'][$i] ?? null,
                'hour_6_7' => $validated['hour_6_7'][$i] ?? null,
                'hour_7_8' => $validated['hour_7_8'][$i] ?? null,
                'workman_count' => $validated['workman_count'] ?? null,
                'staff_count' => $validated['staff_count'] ?? null,
                'status' => true,
                'is_deleted' => false,
                'is_draft' => $isDraft,
            ];

            ProductionReport::create($data);
            $createdCount++;
        }

        $message = $isDraft
            ? "$createdCount production report(s) saved as draft."
            : "$createdCount production report(s) created successfully.";

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'redirect' => route('admin.production-reports.index'),
            ]);
        }

        return redirect()->route('admin.production-reports.index')
            ->with('success', $message);
    }

    /**
     * Display the specified production report.
     */
    public function show(ProductionReport $productionReport): View
    {
        $productionReport->loadMissing(['machine', 'slideSize', 'coil']);

        return view('backend.production-reports.show', compact('productionReport'));
    }

    /**
     * Show the form for editing the specified production report.
     */
    public function edit(ProductionReport $productionReport): View
    {
        $machines = Machine::where('is_deleted', false)->where('status', true)->get();
        $slideSizes = Item::where('is_deleted', false)
            ->where('status', true)
            ->where('category', 'SF1-SF2')
            ->get();

        return view('backend.production-reports.edit', compact('productionReport', 'machines', 'slideSizes'));
    }

    /**
     * Update the specified production report in storage.
     */
    public function update(Request $request, ProductionReport $productionReport): RedirectResponse|JsonResponse
    {
        $isDraft = (bool) $request->input('is_draft', false);

        $slideSizeRules = $isDraft
            ? ['nullable', Rule::exists('items', 'id')->where(function ($query) {
                    $query->where('is_deleted', false)->where('status', true)->where('category', 'SF1-SF2');
                })]
            : ['required', Rule::exists('items', 'id')->where(function ($query) {
                    $query->where('is_deleted', false)->where('status', true)->where('category', 'SF1-SF2');
                })];

        $validated = $request->validate([
            'selected_machines' => 'nullable|array',
            'machine_id' => 'required|array',
            'machine_id.*' => 'required|exists:machines,id',
            'coil_id' => 'nullable|array',
            'coil_id.*' => 'nullable|exists:coil_stock,id',
            'coil_number_id' => 'nullable|array',
            'coil_number_id.*' => 'nullable|exists:coil_load_numbers,id',
            'slide_size_id' => $isDraft ? 'nullable|array' : 'required|array',
            'slide_size_id.*' => $slideSizeRules,
            'report_date' => 'required|array',
            'report_date.*' => 'required|date',
            'shift' => 'required|array',
            'shift.*' => 'required|string|in:Morning,Night',
            'total_set_shift' => 'nullable|array',
            'total_set_shift.*' => 'nullable|numeric|min:0',
            'set_per_hour' => 'nullable|array',
            'set_per_hour.*' => 'nullable|numeric|min:0',
            'hour_8_9' => 'nullable|array',
            'hour_8_9.*' => 'nullable|numeric|min:0',
            'hour_9_10' => 'nullable|array',
            'hour_9_10.*' => 'nullable|numeric|min:0',
            'hour_10_11' => 'nullable|array',
            'hour_10_11.*' => 'nullable|numeric|min:0',
            'hour_11_12' => 'nullable|array',
            'hour_11_12.*' => 'nullable|numeric|min:0',
            'hour_12_1' => 'nullable|array',
            'hour_12_1.*' => 'nullable|numeric|min:0',
            'hour_1_2' => 'nullable|array',
            'hour_1_2.*' => 'nullable|numeric|min:0',
            'hour_2_3' => 'nullable|array',
            'hour_2_3.*' => 'nullable|numeric|min:0',
            'hour_3_4' => 'nullable|array',
            'hour_3_4.*' => 'nullable|numeric|min:0',
            'hour_4_5' => 'nullable|array',
            'hour_4_5.*' => 'nullable|numeric|min:0',
            'hour_5_6' => 'nullable|array',
            'hour_5_6.*' => 'nullable|numeric|min:0',
            'hour_6_7' => 'nullable|array',
            'hour_6_7.*' => 'nullable|numeric|min:0',
            'hour_7_8' => 'nullable|array',
            'hour_7_8.*' => 'nullable|numeric|min:0',
            'actual_set_shift' => 'nullable|array',
            'actual_set_shift.*' => 'nullable|numeric|min:0',
            'workman_count' => 'nullable|array',
            'workman_count.*' => 'nullable|numeric|min:0',
            'staff_count' => 'nullable|array',
            'staff_count.*' => 'nullable|numeric|min:0',
        ]);

        $selectedMachines = $validated['selected_machines'] ?? [];
        
        if (empty($selectedMachines)) {
            $message = 'Please select at least one machine.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return redirect()->route('admin.production-reports.index')
                ->with('error', $message);
        }

        $this->validateDuplicateCombinations($validated, $productionReport, $isDraft);

        // Validate no hour overlap with existing reports for the same machine+date+shift
        if (!$isDraft) {
            $this->validateHourOverlap($validated, $productionReport);
        }

        $updatedCount = 0;

        foreach ($selectedMachines as $i => $machineId) {
            $data = [
                'machine_id' => $machineId,
                'coil_id' => $validated['coil_id'][$i] ?? null,                   // coil_stock.id
                'coil_number_id' => $validated['coil_number_id'][$i] ?? null,     // coil_load_numbers.id
                'slide_size_id' => $validated['slide_size_id'][$i] ?? null,
                'report_date' => $validated['report_date'][$i] ?? null,
                'shift' => $validated['shift'][$i] ?? null,
                'total_set_shift' => $validated['total_set_shift'][$i] ?? null,
                'set_per_hour' => $validated['set_per_hour'][$i] ?? null,
                'actual_set_shift' => $validated['actual_set_shift'][$i] ?? null,
                'hour_8_9' => $validated['hour_8_9'][$i] ?? null,
                'hour_9_10' => $validated['hour_9_10'][$i] ?? null,
                'hour_10_11' => $validated['hour_10_11'][$i] ?? null,
                'hour_11_12' => $validated['hour_11_12'][$i] ?? null,
                'hour_12_1' => $validated['hour_12_1'][$i] ?? null,
                'hour_1_2' => $validated['hour_1_2'][$i] ?? null,
                'hour_2_3' => $validated['hour_2_3'][$i] ?? null,
                'hour_3_4' => $validated['hour_3_4'][$i] ?? null,
                'hour_4_5' => $validated['hour_4_5'][$i] ?? null,
                'hour_5_6' => $validated['hour_5_6'][$i] ?? null,
                'hour_6_7' => $validated['hour_6_7'][$i] ?? null,
                'hour_7_8' => $validated['hour_7_8'][$i] ?? null,
                'workman_count' => $validated['workman_count'][$i] ?? null,
                'staff_count' => $validated['staff_count'][$i] ?? null,
                'status' => 1,
                'is_deleted' => false,
                'is_draft' => $isDraft,
            ];

            // If this is the original report's machine, update it
            if ($machineId == $productionReport->machine_id) {
                $productionReport->update($data);
            } else {
                // Create new report for other selected machines
                ProductionReport::create($data);
            }
            
            $updatedCount++;
        }

        $message = "Production report(s) updated successfully. ({$updatedCount} machine(s) updated)";

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'redirect' => route('admin.production-reports.index'),
            ]);
        }

        return redirect()->route('admin.production-reports.index')
            ->with('success', $message);
    }

    private function validateDuplicateCombinations(array $validated, ?ProductionReport $currentReport = null, bool $isDraft = false): void
    {
        // Skip duplicate validation for drafts
        if ($isDraft) {
            return;
        }

        $selectedMachines = $validated['selected_machines'] ?? [];
        $errors = [];

        foreach ($selectedMachines as $i => $machineId) {
            $reportDate = $validated['report_date'][$i] ?? null;
            $shift = $validated['shift'][$i] ?? null;
            $slideSizeId = $validated['slide_size_id'][$i] ?? null;
            $coilId = $validated['coil_id'][$i] ?? null;

            $query = ProductionReport::query()
                ->where('machine_id', $machineId)
                ->where('report_date', $reportDate)
                ->where('shift', $shift)
                ->where('is_deleted', false)
                ->where('is_draft', false);

            if ($slideSizeId) {
                $query->where('slide_size_id', $slideSizeId);
            }

            if ($coilId) {
                $query->where('coil_id', $coilId);
            } else {
                $query->whereNull('coil_id');
            }

            if ($currentReport) {
                $query->where('id', '!=', $currentReport->id);
            }

            if ($query->exists()) {
                $errors["machine_id.$i"] = 'Duplicate entry not allowed: a report with the same date, shift, machine, item, and coil already exists.';
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Validate that submitted hourly values don't overlap with existing reports
     * for the same machine + date + shift (regardless of coil/item).
     */
    private function validateHourOverlap(array $validated, ?ProductionReport $currentReport = null): void
    {
        $selectedMachines = $validated['selected_machines'] ?? [];
        $hourFields = [
            'hour_8_9', 'hour_9_10', 'hour_10_11', 'hour_11_12',
            'hour_12_1', 'hour_1_2', 'hour_2_3', 'hour_3_4',
            'hour_4_5', 'hour_5_6', 'hour_6_7', 'hour_7_8',
        ];
        $errors = [];

        foreach ($selectedMachines as $i => $machineId) {
            $reportDate = $validated['report_date'][$i] ?? null;
            $shift = $validated['shift'][$i] ?? null;

            $query = ProductionReport::query()
                ->where('machine_id', $machineId)
                ->where('report_date', $reportDate)
                ->where('shift', $shift)
                ->where('is_deleted', false)
                ->where('is_draft', false);

            if ($currentReport) {
                $query->where('id', '!=', $currentReport->id);
            }

            $existingReports = $query->get($hourFields);
            if ($existingReports->isEmpty()) {
                continue;
            }

            // If any hour has a value in existing reports, block ALL hours
            $hasAnyFilledHour = false;
            foreach ($existingReports as $report) {
                foreach ($hourFields as $field) {
                    if ($report->$field !== null && (float) $report->$field >= 0) {
                        $hasAnyFilledHour = true;
                        break 2;
                    }
                }
            }

            if ($hasAnyFilledHour) {
                foreach ($hourFields as $field) {
                    $submittedValue = $validated[$field][$i] ?? null;
                    if ($submittedValue !== null) {
                        $errors["$field.$i"] = "This hour is already reported for the same machine, date, and shift.";
                    }
                }
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Delete the specified production report from storage.
     */
    public function destroy(ProductionReport $productionReport): RedirectResponse
    {
        if ((int) ($productionReport->is_transfered ?? 0) === 1) {
            return redirect()->route('admin.production-reports.index')
                ->with('error', 'The stock is transfered, so this report cannot be deleted.');
        }

        $productionReport->update(['is_deleted' => true]);

        return redirect()->route('admin.production-reports.index')
            ->with('success', 'Production report deleted successfully.');
    }
}
