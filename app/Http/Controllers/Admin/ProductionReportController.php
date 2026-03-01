<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductionReport;
use App\Models\Machine;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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
        } elseif ($mode === 'all') {
            // no filter
        } else {
            $mode = 'active';
            $query->where('is_deleted', false);
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
     * Show the form for creating a new production report.
     */
    public function create(): View
    {
        $machines = Machine::where('is_deleted', false)->where('status', true)->get();
        $slideSizes = Item::where('is_deleted', false)->where('status', true)->get();

        return view('backend.production-reports.create', compact('machines', 'slideSizes'));
    }

    /**
     * Store a newly created production report in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate arrays
        $validated = $request->validate([
            'selected_machines' => 'nullable|array',
            'machine_id' => 'required|array',
            'machine_id.*' => 'required|exists:machines,id',
            'slide_size_id' => 'required|array',
            'slide_size_id.*' => 'required|exists:items,id',
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
            'workman_count' => 'nullable|array',
            'workman_count.*' => 'nullable|numeric|min:0',
            'staff_count' => 'nullable|array',
            'staff_count.*' => 'nullable|numeric|min:0',
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
        
        if (empty($selectedMachines)) {
            return redirect()->route('admin.production-reports.index')
                ->with('error', 'Please select at least one machine.');
        }

        $count = count($validated['machine_id']);
        $createdCount = 0;

        for ($i = 0; $i < $count; $i++) {
            // Only create records for selected machines
            if (!in_array($validated['machine_id'][$i], $selectedMachines)) {
                continue;
            }

            $data = [
                'machine_id' => $validated['machine_id'][$i],
                'slide_size_id' => $validated['slide_size_id'][$i],
                'report_date' => $validated['report_date'][$i],
                'shift' => $validated['shift'][$i],
                'total_set_shift' => $validated['total_set_shift'][$i] ?? 0,
                'set_per_hour' => $validated['set_per_hour'][$i] ?? 0,
                'actual_set_shift' => $validated['actual_set_shift'][$i] ?? 0,
                'hour_8_9' => $validated['hour_8_9'][$i] ?? 0,
                'hour_9_10' => $validated['hour_9_10'][$i] ?? 0,
                'hour_10_11' => $validated['hour_10_11'][$i] ?? 0,
                'hour_11_12' => $validated['hour_11_12'][$i] ?? 0,
                'hour_12_1' => $validated['hour_12_1'][$i] ?? 0,
                'hour_1_2' => $validated['hour_1_2'][$i] ?? 0,
                'hour_2_3' => $validated['hour_2_3'][$i] ?? 0,
                'hour_3_4' => $validated['hour_3_4'][$i] ?? 0,
                'hour_4_5' => $validated['hour_4_5'][$i] ?? 0,
                'hour_5_6' => $validated['hour_5_6'][$i] ?? 0,
                'hour_6_7' => $validated['hour_6_7'][$i] ?? 0,
                'hour_7_8' => $validated['hour_7_8'][$i] ?? 0,
                'workman_count' => $validated['workman_count'][$i] ?? 0,
                'staff_count' => $validated['staff_count'][$i] ?? 0,
                'status' => true,
                'is_deleted' => false,
            ];

            ProductionReport::create($data);
            $createdCount++;
        }

        return redirect()->route('admin.production-reports.index')
            ->with('success', "$createdCount production report(s) created successfully.");
    }

    /**
     * Display the specified production report.
     */
    public function show(ProductionReport $productionReport): View
    {
        return view('backend.production-reports.show', compact('productionReport'));
    }

    /**
     * Show the form for editing the specified production report.
     */
    public function edit(ProductionReport $productionReport): View
    {
        $machines = Machine::where('is_deleted', false)->where('status', true)->get();
        $slideSizes = Item::where('is_deleted', false)->where('status', true)->get();

        return view('backend.production-reports.edit', compact('productionReport', 'machines', 'slideSizes'));
    }

    /**
     * Update the specified production report in storage.
     */
    public function update(Request $request, ProductionReport $productionReport): RedirectResponse
    {

   
        $validated = $request->validate([
            'selected_machines' => 'nullable|array',
            'machine_id' => 'required|array',
            'machine_id.*' => 'required|exists:machines,id',
            'slide_size_id' => 'required|array',
            'slide_size_id.*' => 'required|exists:items,id',
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
            return redirect()->route('admin.production-reports.index')
                ->with('error', 'Please select at least one machine.');
        }

        $updatedCount = 0;

        // Loop through all machine_ids and find matching selected machines
        foreach ($validated['machine_id'] as $i => $machineId) {
            // Only process records for selected machines
            if (!in_array($machineId, $selectedMachines)) {
                continue;
            }

            $data = [
                'machine_id' => $machineId,
                'slide_size_id' => $validated['slide_size_id'][$i] ?? null,
                'report_date' => $validated['report_date'][$i] ?? null,
                'shift' => $validated['shift'][$i] ?? null,
                'total_set_shift' => $validated['total_set_shift'][$i] ?? 0,
                'set_per_hour' => $validated['set_per_hour'][$i] ?? 0,
                'actual_set_shift' => $validated['actual_set_shift'][$i] ?? 0,
                'hour_8_9' => $validated['hour_8_9'][$i] ?? 0,
                'hour_9_10' => $validated['hour_9_10'][$i] ?? 0,
                'hour_10_11' => $validated['hour_10_11'][$i] ?? 0,
                'hour_11_12' => $validated['hour_11_12'][$i] ?? 0,
                'hour_12_1' => $validated['hour_12_1'][$i] ?? 0,
                'hour_1_2' => $validated['hour_1_2'][$i] ?? 0,
                'hour_2_3' => $validated['hour_2_3'][$i] ?? 0,
                'hour_3_4' => $validated['hour_3_4'][$i] ?? 0,
                'hour_4_5' => $validated['hour_4_5'][$i] ?? 0,
                'hour_5_6' => $validated['hour_5_6'][$i] ?? 0,
                'hour_6_7' => $validated['hour_6_7'][$i] ?? 0,
                'hour_7_8' => $validated['hour_7_8'][$i] ?? 0,
                'workman_count' => $validated['workman_count'][$i] ?? 0,
                'staff_count' => $validated['staff_count'][$i] ?? 0,
                'status' => 1,
                'is_deleted' => false,
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

        return redirect()->route('admin.production-reports.index')
            ->with('success', "Production report(s) updated successfully. ({$updatedCount} machine(s) updated)");
    }

    /**
     * Delete the specified production report from storage.
     */
    public function destroy(ProductionReport $productionReport): RedirectResponse
    {
        $productionReport->update(['is_deleted' => true]);

        return redirect()->route('admin.production-reports.index')
            ->with('success', 'Production report deleted successfully.');
    }
}
