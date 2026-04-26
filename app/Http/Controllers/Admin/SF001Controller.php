<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoilLoadNumber;
use App\Models\CoilMachineTrack;
use App\Models\CoilMachineTrackLog;
use App\Models\CoilManufacture;
use App\Models\CoilStock;
use App\Models\Item;
use App\Models\Machine;
use App\Models\ProductionReport;
use App\Models\CoilLoadAllocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SF001Controller extends Controller
{
    /**
     * Display Coil Stock page for SF001.
     */
    public function coilStock(): View
    {
        $suppliers = CoilManufacture::query()
            ->where('is_deleted', 0)
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        $machines = Machine::query()
            ->where('is_deleted', 0)
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'machine_code', 'coil_id']);

        $coils = CoilStock::query()
            ->with([
                'manufacture:id,name',
                'machines' => function ($query) {
                    $query->where('machines.is_deleted', 0)
                        ->where('machines.status', 1)
                        ->orderBy('machines.name')
                        ->select('machines.id', 'machines.name', 'machines.machine_code', 'machines.coil_id');
                },
            ])
            ->where('is_deleted', 0)
            ->orderByDesc('id')
            ->get();

        $loadedMachinesByCoil = CoilLoadAllocation::query()
            ->with(['machine:id,name,machine_code'])
            ->where('status', 'active')
            ->get()
            ->groupBy('coil_id')
            ->map(function ($allocations) {
                return $allocations->map(function ($allocation) {
                    return [
                        'id' => $allocation->machine->id,
                        'name' => $allocation->machine->name,
                        'machine_code' => $allocation->machine->machine_code,
                        'allocated_weight' => $allocation->allocated_weight,
                        'remaining_weight' => $allocation->remaining_weight,
                    ];
                });
            });

        $loadedMachineNames = $loadedMachinesByCoil->map(function ($machines) {
            return $machines->pluck('name')->implode(', ');
        });

        $coilTrackLogs = CoilMachineTrack::query()
            ->with([
                'machine:id,name,machine_code',
                'coil:id,coil_no',
                'creator:id,name',
            ])
            ->where('is_deleted', 0)
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $trackActionTabs = CoilMachineTrack::manageActionTabs();

        $manufacturers = CoilManufacture::query()
            ->where('is_deleted', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'status']);

        return view('backend.production-reports.coil-stock', compact('coils', 'suppliers', 'machines', 'loadedMachineNames', 'loadedMachinesByCoil', 'coilTrackLogs', 'trackActionTabs', 'manufacturers'));
    }

    /**
     * Display detailed coil stock view page with reporting and history.
     */
    public function viewCoilStock(int $coilId): View
    {
        $coil = CoilStock::query()
            ->with([
                'manufacture:id,name',
                'machines:id,name,machine_code',
            ])
            ->where('id', $coilId)
            ->where('is_deleted', 0)
            ->firstOrFail();

        $loadedMachines = Machine::query()
            ->where('is_deleted', 0)
            ->where('coil_id', $coil->id)
            ->orderBy('name')
            ->get(['id', 'name', 'machine_code', 'coil_id']);

        $assignedMachines = $coil->machines()
            ->orderBy('name')
            ->get(['machines.id', 'machines.name', 'machines.machine_code']);

        $trackHistory = CoilMachineTrack::query()
            ->with([
                'machine:id,name,machine_code',
                'creator:id,name',
                'referenceTrack:id,load_weight,event_at',
                'loadNumber:id,coil_machine_track_id,coil_no',
            ])
            ->where('coil_id', $coil->id)
            ->where('is_deleted', 0)
            ->orderByDesc('event_at')
            ->orderByDesc('id')
            ->get();

        $logHistory = CoilMachineTrackLog::query()
            ->with([
                'machine:id,name,machine_code',
                'creator:id,name',
            ])
            ->where('coil_id', $coil->id)
            ->where('is_deleted', 0)
            ->orderByDesc('id')
            ->get();

        $productionReports = ProductionReport::query()
            ->with([
                'machine:id,name,machine_code',
                'slideSize:id,name,size',
            ])
            ->where('coil_id', $coil->id)
            ->where('is_deleted', false)
            ->orderByDesc('report_date')
            ->orderByDesc('id')
            ->get();

        $coilLoadNumbers = CoilLoadNumber::query()
            ->with([
                'track:id,machine_id,type,load_weight,event_at',
                'track.machine:id,name,machine_code',
                'creator:id,name',
            ])
            ->where('coil_id', $coil->id)
            ->orderByDesc('id')
            ->get();

        return view('backend.production-reports.coil-stock-view', compact(
            'coil',
            'loadedMachines',
            'assignedMachines',
            'trackHistory',
            'logHistory',
            'productionReports',
            'coilLoadNumbers'
        ));
    }

    /**
     * Display multi-machine loading management page for a specific coil.
     */
    public function multiLoadCoil(int $coilId): View
    {
        $coil = CoilStock::query()
            ->with(['manufacture:id,name'])
            ->where('id', $coilId)
            ->where('is_deleted', 0)
            ->firstOrFail();

        $allocatedMachines = CoilLoadAllocation::query()
            ->with(['machine:id,name,machine_code'])
            ->where('coil_id', $coil->id)
            ->where('status', 'active')
            ->get();

        $allocatedMachineIds = $allocatedMachines->pluck('machine_id')->toArray();

        $allMachines = $coil->machines()
            ->leftJoin('coil_stock as current_coil', 'machines.coil_id', '=', 'current_coil.id')
            ->where('machines.is_deleted', 0)
            ->where('machines.status', 1)
            ->orderBy('machines.name')
            ->get([
                'machines.id', 
                'machines.name', 
                'machines.machine_code', 
                'machines.coil_id as current_coil_id',
                'current_coil.coil_no as current_coil_no'
            ]);

        $transitions = CoilMachineTrack::query()
            ->with(['machine:id,name,machine_code', 'loadNumber'])
            ->where('coil_id', $coil->id)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $unloadedHistory = CoilLoadAllocation::query()
            ->with(['machine:id,name,machine_code'])
            ->where('coil_id', $coil->id)
            ->where('status', 'unloaded')
            ->orderByDesc('updated_at')
            ->get();

        $suppliers = CoilManufacture::query()
            ->where('is_deleted', 0)
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('backend.production-reports.sf001.multi-load', compact('coil', 'allocatedMachines', 'allMachines', 'transitions', 'unloadedHistory', 'suppliers'));
    }

    /**
     * Store weight allocation for a specific machine in multi-load mode.
     */
    public function storeMultiLoadAllocation(Request $request, int $coilId): RedirectResponse
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'coil_no' => 'required|string|max:120',
            'allocated_weight' => 'required|numeric|min:1',
            'remark' => 'nullable|string|max:255',
        ]);



        $coil = CoilStock::query()
            ->where('id', $coilId)
            ->where('is_deleted', 0)
            ->firstOrFail();

        $machine = Machine::query()
            ->where('id', $validated['machine_id'])
            ->where('is_deleted', 0)
            ->where('status', 1)
            ->firstOrFail();

        // 2. Check if machine already has an active load
        if (!empty($machine->coil_id)) {
            return back()->with('error', 'Machine already has a loaded coil. Please unload first.');
        }

        // 3. Check weight availability
        $loadWeight = (float) $validated['allocated_weight'];
        if ($loadWeight > (float) $coil->net_weight_kg) {
            return back()->with('error', 'Allocation weight exceeds available coil weight.');
        }

        try {
            DB::transaction(function () use ($coil, $machine, $loadWeight, $validated) {
                $remainingCoilWeight = max((float) $coil->net_weight_kg - $loadWeight, 0);

                // Update Coil Stock
                $coil->update([
                    'net_weight_kg' => $remainingCoilWeight,
                    'process' => $remainingCoilWeight > 0 ? 'in_use' : 'out_of_stock',
                    'process_type' => 'load',
                ]);

                // Update Machine
                $machine->update(['coil_id' => $coil->id]);

                // Create Track
                $track = CoilMachineTrack::query()->create([
                    'machine_id' => $machine->id,
                    'coil_id' => $coil->id,
                    'load_weight' => $loadWeight,
                    'type' => 'load',
                    'event_at' => now(),
                    'remark' => $validated['remark'] ?? 'Multi-machine allocation',
                    'created_by' => Auth::id(),
                    'status' => 1,
                ]);

                // Create Allocation State
                CoilLoadAllocation::query()->create([
                    'coil_id' => $coil->id,
                    'machine_id' => $machine->id,
                    'coil_no' => trim((string) $validated['coil_no']),
                    'allocated_weight' => $loadWeight,
                    'consumed_weight' => 0,
                    'remaining_weight' => $loadWeight,
                    'status' => 'active',
                    'load_track_id' => $track->id,
                ]);

                // Create Coil Load Number link
                CoilLoadNumber::query()->create([
                    'coil_id' => $coil->id,
                    'coil_machine_track_id' => $track->id,
                    'coil_no' => trim((string) $validated['coil_no']),
                    'created_by' => Auth::id(),
                ]);

                // Optional: Log History
                CoilMachineTrackLog::query()->create([
                    'coil_machine_track_id' => $track->id,
                    'action_type' => 'load',
                    'payload' => json_encode([
                        'machine_id' => $machine->id,
                        'coil_id' => $coil->id,
                        'coil_no' => $validated['coil_no'] ?? null,
                        'load_weight' => $loadWeight,
                    ]),
                    'description' => 'Multi-machine load allocation created.',
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to allocate weight: ' . $e->getMessage());
        }

        return back()->with('success', "Weight allocated to {$machine->name} successfully.");
    }

    /**
     * Update/Adjust weight for an active allocation.
     */
    public function updateMultiLoadAllocation(Request $request, int $coilId, int $allocationId): RedirectResponse
    {
        $validated = $request->validate([
            'allocated_weight' => 'required|numeric|min:1',
        ]);

        $allocation = CoilLoadAllocation::query()
            ->where('id', $allocationId)
            ->where('coil_id', $coilId)
            ->where('status', 'active')
            ->firstOrFail();

        $coil = CoilStock::findOrFail($coilId);
        $diff = (float) $validated['allocated_weight'] - (float) $allocation->allocated_weight;

        // Check if coil has enough stock if we are increasing allocation
        if ($diff > 0 && $coil->net_weight_kg < $diff) {
            return back()->with('error', "Not enough stock in coil to increase allocation by " . number_format($diff, 0) . " KG.");
        }

        DB::transaction(function () use ($allocation, $coil, $diff, $validated) {
            // 1. Update Coil Stock
            $coil->update([
                'net_weight_kg' => (float) $coil->net_weight_kg - $diff,
            ]);

            // 2. Update Allocation
            $newRemaining = (float) $allocation->remaining_weight + $diff;
            $allocation->update([
                'allocated_weight' => $validated['allocated_weight'],
                'remaining_weight' => $newRemaining > 0 ? $newRemaining : 0,
            ]);

            // 3. Log the adjustment
            CoilMachineTrack::query()->create([
                'machine_id' => $allocation->machine_id,
                'coil_id' => $coil->id,
                'load_weight' => abs($diff),
                'type' => $diff > 0 ? 'load' : 'unload',
                'event_at' => now(),
                'remark' => 'Weight allocation adjusted manually',
                'created_by' => Auth::id(),
                'status' => 1,
            ]);
        });

        return back()->with('success', "Allocation weight adjusted successfully.");
    }

    /**
     * Unload a specific machine allocation and return remaining weight to coil stock.
     */
    public function unloadMultiLoadAllocation(Request $request, int $coilId, int $allocationId): RedirectResponse
    {
        $validated = $request->validate([
            'return_weight' => 'required|numeric|min:0',
        ]);

        $allocation = CoilLoadAllocation::query()
            ->where('id', $allocationId)
            ->where('coil_id', $coilId)
            ->where('status', 'active')
            ->firstOrFail();

        $machine = Machine::findOrFail($allocation->machine_id);
        $coil = CoilStock::findOrFail($coilId);

        DB::transaction(function () use ($allocation, $machine, $coil, $validated) {
            $returnWeight = (float) $validated['return_weight'];
            $originalAllocated = (float) $allocation->allocated_weight;
            $consumedInThisProcess = $originalAllocated - $returnWeight;

            // 1. Update Coil Stock (Return specified weight)
            $newCoilWeight = (float) $coil->net_weight_kg + $returnWeight;
            $coil->update([
                'net_weight_kg' => $newCoilWeight,
                'process' => $newCoilWeight > 0 ? 'in_use' : 'out_of_stock',
            ]);

            // 2. Clear Machine
            $machine->update(['coil_id' => null]);

            // 3. Complete Allocation and update final consumed weight
            $allocation->update([
                'status' => 'unloaded',
                'consumed_weight' => $consumedInThisProcess > 0 ? $consumedInThisProcess : 0,
                'remaining_weight' => 0,
                'unload_track_id' => null,
            ]);

            // 4. Create Track entry for Unload
            $track = CoilMachineTrack::query()->create([
                'machine_id' => $machine->id,
                'coil_id' => $coil->id,
                'load_weight' => $returnWeight, // Log how much was returned
                'type' => 'unload',
                'event_at' => now(),
                'remark' => 'Multi-machine allocation unloaded (Manual weight)',
                'created_by' => Auth::id(),
                'status' => 1,
            ]);

            $allocation->update(['unload_track_id' => $track->id]);
        });

        return back()->with('success', "Machine {$machine->name} unloaded. " . number_format($validated['return_weight'], 0) . " KG returned to coil stock.");
    }

    /**
     * Store a new coil stock record.
     */
    public function storeCoilStock(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'manufacture_id' => 'required',
            'new_manufacture_name' => 'nullable|string|max:100|unique:coil_manufacture,name',
            'coil_no' => 'nullable|string|max:120',
            'coil_size' => 'required|string|max:60',
            'thickness' => 'required|numeric|min:0',
            'net_weight_kg' => 'required|numeric|min:0',
            'process' => 'required|in:available,in_use,completed,out_of_stock',
            'status' => 'required|in:0,1',
            'machine_ids' => 'required|array|min:1',
            'machine_ids.*' => 'required|integer|exists:machines,id',
        ]);

        $manufactureId = null;
        $selectedManufactureId = (string) ($validated['manufacture_id'] ?? '');

        if ($selectedManufactureId === '__new__') {
            $newManufactureName = trim((string) ($validated['new_manufacture_name'] ?? ''));

            if ($newManufactureName === '') {
                return back()->withErrors([
                    'new_manufacture_name' => 'Please enter new supplier name.',
                ])->withInput();
            }

            $newManufacturer = CoilManufacture::query()->create([
                'name' => $newManufactureName,
                'status' => 1,
                'is_deleted' => 0,
            ]);

            $manufactureId = (int) $newManufacturer->id;
        } else {
            $manufacture = CoilManufacture::query()
                ->where('id', (int) $selectedManufactureId)
                ->where('is_deleted', 0)
                ->first();

            if (!$manufacture) {
                return back()->withErrors([
                    'manufacture_id' => 'Please select a valid supplier.',
                ])->withInput();
            }

            $manufactureId = (int) $manufacture->id;
        }

        $coil = CoilStock::query()->create([
            'manufacture_id' => $manufactureId,
            'coil_no' => $validated['coil_no'] ?? null,
            'coil_size' => trim((string) $validated['coil_size']),
            'thickness' => (float) $validated['thickness'],
            'net_weight_kg' => (float) $validated['net_weight_kg'],
            'process' => (float) $validated['net_weight_kg'] <= 0 ? 'out_of_stock' : (string) $validated['process'],
            'process_type' => null,
            'status' => (int) $validated['status'],
            'is_deleted' => 0,
        ]);

        // Sync machines
        $coil->machines()->sync($validated['machine_ids']);

        return back()->with('success', 'New coil stock added successfully.');
    }

    /**
     * Update an existing coil stock record.
     */
    public function updateCoilStock(Request $request, int $coilId): RedirectResponse
    {
        $coil = CoilStock::query()
            ->where('id', $coilId)
            ->where('is_deleted', 0)
            ->first();

        if (!$coil) {
            return back()->with('error', 'Coil stock record not found.');
        }

        $validated = $request->validate([
            'edit_id' => 'required|integer',
            'manufacture_id' => 'required|integer|exists:coil_manufacture,id',
            'coil_no' => 'nullable|string|max:120',
            'coil_size' => 'required|string|max:60',
            'thickness' => 'required|numeric|min:0',
            'net_weight_kg' => 'required|numeric|min:0',
            'process' => 'required|in:available,in_use,completed,out_of_stock',
            'status' => 'required|in:0,1',
            'machine_ids' => 'required|array|min:1',
            'machine_ids.*' => 'required|integer|exists:machines,id',
        ]);

        $coil->update([
            'manufacture_id' => (int) $validated['manufacture_id'],
            'coil_no' => $validated['coil_no'] ?? null,
            'coil_size' => trim((string) $validated['coil_size']),
            'thickness' => (float) $validated['thickness'],
            'net_weight_kg' => (float) $validated['net_weight_kg'],
            'process' => (float) $validated['net_weight_kg'] <= 0 ? 'out_of_stock' : (string) $validated['process'],
            'status' => (int) $validated['status'],
        ]);

        // Sync machines
        $coil->machines()->sync($validated['machine_ids']);

        return back()->with('success', 'Coil stock updated successfully.');
    }

    /**
     * Soft delete a coil stock record.
     */
    public function destroyCoilStock(int $coilId): RedirectResponse
    {
        $coil = CoilStock::query()
            ->where('id', $coilId)
            ->where('is_deleted', 0)
            ->first();

        if (!$coil) {
            return back()->with('error', 'Coil stock record not found.');
        }

        $isLoadedToMachine = Machine::query()
            ->where('coil_id', $coil->id)
            ->where('is_deleted', 0)
            ->exists();

        if ($coil->process === 'in_use' || $isLoadedToMachine) {
            return back()->with('info', 'In-use coil cannot be deleted.');
        }

        $coil->update([
            'is_deleted' => 1,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Coil stock deleted successfully.');
    }

    /**
     * Store a new coil manufacturer.
     */
    public function storeManufacturer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:coil_manufacture,name',
        ]);

        CoilManufacture::query()->create([
            'name' => trim((string) $validated['name']),
            'status' => 1,
            'is_deleted' => 0,
        ]);

        return back()->with('success', 'Supplier added successfully.');
    }

    /**
     * Update an existing coil manufacturer.
     */
    public function updateManufacturer(Request $request, int $id): RedirectResponse
    {
        $manufacturer = CoilManufacture::query()
            ->where('id', $id)
            ->where('is_deleted', 0)
            ->first();

        if (!$manufacturer) {
            return back()->with('error', 'Supplier not found.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:coil_manufacture,name,' . $id,
            'status' => 'required|in:0,1',
        ]);

        $manufacturer->update([
            'name' => trim((string) $validated['name']),
            'status' => (int) $validated['status'],
        ]);

        return back()->with('success', 'Supplier updated successfully.');
    }

    /**
     * Soft delete a coil manufacturer.
     */
    public function destroyManufacturer(int $id): RedirectResponse
    {
        $manufacturer = CoilManufacture::query()
            ->where('id', $id)
            ->where('is_deleted', 0)
            ->first();

        if (!$manufacturer) {
            return back()->with('error', 'Supplier not found.');
        }

        $isInUse = CoilStock::query()
            ->where('manufacture_id', $id)
            ->where('is_deleted', 0)
            ->exists();

        if ($isInUse) {
            return back()->with('error', 'Cannot delete a supplier that has associated coil stock.');
        }

        $manufacturer->update(['is_deleted' => 1]);

        return back()->with('success', 'Supplier deleted successfully.');
    }

    /**
     * Load selected coil to selected machine.
     */
    public function loadCoilToMachine(Request $request): RedirectResponse
    {
        $loadAction = CoilMachineTrack::ACTION_LOAD;
        $unloadAction = CoilMachineTrack::ACTION_UNLOAD;
        $validFormTypes = implode(',', array_keys(CoilMachineTrack::manageActionTabs()));

        $validated = $request->validate([
            'form_type' => 'required|in:' . $validFormTypes,
            'coil_id' => 'nullable|integer|exists:coil_stock,id',
            'machine_id' => 'required|integer|exists:machines,id',
            'coil_no' => 'required_if:form_type,' . $loadAction . '|nullable|string|max:120',
            'load_weight' => 'required_if:form_type,' . $loadAction . '|nullable|numeric|gt:0',
            'unload_weight' => 'nullable|numeric|min:0',
            'remark' => 'nullable|string|max:255',
        ]);

        $formType = (string) $validated['form_type'];

        if ($formType === $loadAction && empty($validated['coil_id'])) {
            return back()->withErrors([
                'coil_id' => 'Coil is required for loading.',
            ])->withInput();
        }

        $machine = Machine::query()
            ->where('id', (int) $validated['machine_id'])
            ->where('is_deleted', 0)
            ->where('status', 1)
            ->first();

        if (!$machine) {
            return back()->with('error', 'Selected machine is not active.');
        }

        if ($formType === $loadAction) {
            $coil = CoilStock::query()
                ->where('id', (int) $validated['coil_id'])
                ->where('is_deleted', 0)
                ->where('status', 1)
                ->first();

            if (!$coil) {
                return back()->with('error', 'Selected coil is not available for loading.');
            }

            $isMachineAssignedToCoil = $coil->machines()
                ->where('machines.id', $machine->id)
                ->where('machines.is_deleted', 0)
                ->where('machines.status', 1)
                ->exists();

            if (!$isMachineAssignedToCoil) {
                return back()->with('error', 'Selected machine is not assigned to this coil.');
            }

            if (!empty($machine->coil_id)) {
                return back()->with('error', 'Selected machine already has a loaded coil. Please unload first.');
            }

            $isCoilAlreadyLoaded = Machine::query()
                ->where('is_deleted', 0)
                ->where('coil_id', $coil->id)
                ->exists();

            if ($isCoilAlreadyLoaded) {
                return back()->with('error', 'Selected coil is already loaded on a machine. Please unload first.');
            }

            $loadWeight = (float) $validated['load_weight'];
            $coilNetWeightTotal = (float) $coil->net_weight_kg;

            if ($loadWeight > (float) $coil->net_weight_kg) {
                return back()->withErrors([
                    'load_weight' => 'Load weight cannot be greater than coil net weight (' . number_format((float) $coil->net_weight_kg, 0) . ').',
                ])->withInput();
            }

            $remainingNetWeight = max($coilNetWeightTotal - $loadWeight, 0);

            DB::transaction(function () use ($machine, $coil, $loadWeight, $validated, $coilNetWeightTotal, $remainingNetWeight, $loadAction) {
                $machine->update([
                    'coil_id' => $coil->id,
                ]);

                $coil->update([
                    'net_weight_kg' => $remainingNetWeight,
                    'process' => $remainingNetWeight > 0 ? 'in_use' : 'out_of_stock',
                    'process_type' => $loadAction,
                ]);

                $track = CoilMachineTrack::query()->create([
                    'machine_id' => $machine->id,
                    'coil_id' => $coil->id,
                    'load_weight' => $loadWeight,
                    'unload_weight' => null,
                    'type' => $loadAction,
                    'reference_track_id' => null,
                    'event_at' => now(),
                    'remark' => $validated['remark'] ?? null,
                    'created_by' => Auth::id(),
                    'status' => 1,
                    'is_deleted' => 0,
                ]);

                $this->storeCoilTrackLog(
                    $loadAction,
                    $track,
                    null,
                    [
                        'machine_id' => $machine->id,
                        'machine_name' => $machine->name,
                        'coil_id' => $coil->id,
                        'coil_no' => $validated['coil_no'] ?? null,
                        'load_weight' => $loadWeight,
                        'remaining_net_weight' => $remainingNetWeight,
                        'total_weight' => $coilNetWeightTotal,
                    ],
                    'Coil loaded to machine.'
                );

                CoilLoadNumber::query()->create([
                    'coil_id' => $coil->id,
                    'coil_machine_track_id' => $track->id,
                    'coil_no' => trim((string) $validated['coil_no']),
                    'created_by' => Auth::id(),
                ]);
            });

            return back()->with('success', 'Coil loaded to machine successfully.');
        }

        if (empty($machine->coil_id)) {
            return back()->with('error', 'Selected machine has no loaded coil to unload.');
        }

        $coil = CoilStock::query()
            ->where('id', (int) $machine->coil_id)
            ->where('is_deleted', 0)
            ->where('status', 1)
            ->first();

        if (!$coil) {
            return back()->with('error', 'Loaded coil was not found or is inactive.');
        }

        if (!empty($validated['coil_id']) && (int) $validated['coil_id'] !== (int) $coil->id) {
            return back()->with('error', 'Selected coil does not match the currently loaded coil on this machine.');
        }

        $isMachineAssignedToCoil = $coil->machines()
            ->where('machines.id', $machine->id)
            ->where('machines.is_deleted', 0)
            ->where('machines.status', 1)
            ->exists();

        if (!$isMachineAssignedToCoil) {
            return back()->with('error', 'Selected machine is not assigned to this coil.');
        }

        $latestLoadTrack = CoilMachineTrack::query()
            ->where('machine_id', $machine->id)
            ->where('coil_id', $coil->id)
            ->where('type', $loadAction)
            ->where('is_deleted', 0)
            ->whereNotExists(function ($query) use ($unloadAction) {
                $query->select(DB::raw(1))
                    ->from('coil_machine_track as unload_tracks')
                    ->whereColumn('unload_tracks.reference_track_id', 'coil_machine_track.id')
                    ->where('unload_tracks.type', $unloadAction)
                    ->where('unload_tracks.is_deleted', 0);
            })
            ->orderByDesc('id')
            ->first();

        if (!$latestLoadTrack) {
            return back()->withErrors([
                'unload_weight' => 'Unable to unload: active load entry was not found for this machine/coil.',
            ])->withInput();
        }

        $baseLoadWeight = (float) $latestLoadTrack->load_weight;
        $pendingWeight = isset($validated['unload_weight']) ? (float) $validated['unload_weight'] : 0;

        if ($pendingWeight > $baseLoadWeight) {
            return back()->withErrors([
                'unload_weight' => 'Pending weight cannot be greater than loaded weight (' . number_format($baseLoadWeight, 3) . ').',
            ])->withInput();
        }

        $coilNetWeightBeforeUnload = (float) $coil->net_weight_kg;
        $updatedNetWeight = $coilNetWeightBeforeUnload + $pendingWeight;
        $coilNetWeightTotal = $coilNetWeightBeforeUnload + $baseLoadWeight;

        DB::transaction(function () use ($machine, $coil, $baseLoadWeight, $pendingWeight, $latestLoadTrack, $validated, $coilNetWeightTotal, $updatedNetWeight, $unloadAction) {
            $machine->update([
                'coil_id' => null,
            ]);

            $coil->update([
                'net_weight_kg' => $updatedNetWeight,
                'process' => $updatedNetWeight > 0 ? 'available' : 'out_of_stock',
                'process_type' => $unloadAction,
            ]);

            $track = CoilMachineTrack::query()->create([
                'machine_id' => $machine->id,
                'coil_id' => $coil->id,
                'load_weight' => $baseLoadWeight,
                'unload_weight' => $pendingWeight,
                'type' => $unloadAction,
                'reference_track_id' => $latestLoadTrack?->id,
                'event_at' => now(),
                'remark' => $validated['remark'] ?? null,
                'created_by' => Auth::id(),
                'status' => 1,
                'is_deleted' => 0,
            ]);

            $this->storeCoilTrackLog(
                $unloadAction,
                $track,
                [
                    'machine_id' => $machine->id,
                    'coil_id' => $coil->id,
                    'machine_coil_id' => $coil->id,
                ],
                [
                    'machine_id' => $machine->id,
                    'machine_name' => $machine->name,
                    'coil_id' => $coil->id,
                    'coil_no' => $coil->coil_no,
                    'load_weight' => $baseLoadWeight,
                    'unload_weight' => $pendingWeight,
                    'remaining_net_weight' => $updatedNetWeight,
                    'total_weight' => $coilNetWeightTotal,
                    'coil_process' => $updatedNetWeight > 0 ? 'available' : 'out_of_stock',
                ],
                'Coil unloaded from machine.'
            );
        });

        return back()->with('success', 'Coil unloaded from machine successfully.');
    }

    private function storeCoilTrackLog(
        string $actionType,
        CoilMachineTrack $track,
        ?array $oldData,
        ?array $newData,
        ?string $message = null
    ): void {
        $loadedWeight = isset($newData['load_weight']) ? (float) $newData['load_weight'] : (float) $track->load_weight;
        $pendingWeight = isset($newData['unload_weight']) ? (float) $newData['unload_weight'] : 0;
        $totalWeight = isset($newData['total_weight']) ? (float) $newData['total_weight'] : $loadedWeight;
        $unloadedWeight = $actionType === 'unload'
            ? max($loadedWeight - $pendingWeight, 0)
            : 0;

        CoilMachineTrackLog::query()->create([
            'coil_machine_track_id' => $track->id,
            'machine_id' => $track->machine_id,
            'coil_id' => $track->coil_id,
            'action_type' => $actionType,
            'load_weight' => $loadedWeight,
            'unload_weight' => $unloadedWeight,
            'total_weight' => $totalWeight,
            'old_data' => $oldData,
            'new_data' => $newData,
            'message' => $message,
            'created_by' => Auth::id(),
            'status' => 1,
            'is_deleted' => 0,
        ]);
    }

    /**
     * Display Stock page for SF001 - Item wise stock quantities.
     */
    public function stock(): View
    {
        $sf1Transfers = DB::table('sf001_stock_transfers')
            ->select('item_id', 'quantity', 'reject_quantity', 'is_accept')
            ->where('is_deleted', false);

        $ppcTransfers = DB::table('sf002_to_ppc_transfers')
            ->select('item_id', 'quantity', 'reject_quantity', 'is_accept')
            ->where('type', 'ballcage')
            ->where('is_deleted', false);

        $combinedTransfers = $sf1Transfers->unionAll($ppcTransfers);

        $transferStatsSubQuery = DB::table(DB::raw("({$combinedTransfers->toSql()}) as combined"))
            ->mergeBindings($combinedTransfers)
            ->select(
                'item_id',
                DB::raw("COALESCE(SUM(CASE
                    WHEN is_accept = 2 THEN 0
                    WHEN is_accept = 1 THEN GREATEST(quantity - COALESCE(reject_quantity, 0), 0)
                    ELSE quantity
                END), 0) as transferred_quantity"),
                DB::raw("COALESCE(SUM(CASE
                    WHEN is_accept = 2 THEN quantity
                    WHEN is_accept = 1 THEN COALESCE(reject_quantity, 0)
                    ELSE 0
                END), 0) as rejected_quantity")
            )
            ->groupBy('item_id');

        // Get only items that exist in production reports with aggregated quantities
        $itemStocks = Item::query()
            ->select(
                'items.id',
                'items.name',
                'items.code',
                'items.size',
                'items.weight',
                DB::raw('COALESCE(SUM(production_reports.actual_set_shift), 0) as total_produced_stock'),
                DB::raw('COALESCE(MAX(sf001_transfers.transferred_quantity), 0) as transferred_quantity'),
                DB::raw('COALESCE(MAX(sf001_transfers.rejected_quantity), 0) as rejected_quantity'),
                DB::raw('GREATEST(COALESCE(SUM(production_reports.actual_set_shift), 0) - COALESCE(MAX(sf001_transfers.transferred_quantity), 0) - COALESCE(MAX(sf001_transfers.rejected_quantity), 0), 0) as pending_quantity'),
                DB::raw('GREATEST(COALESCE(SUM(production_reports.actual_set_shift), 0) - COALESCE(MAX(sf001_transfers.transferred_quantity), 0) - COALESCE(MAX(sf001_transfers.rejected_quantity), 0), 0) as total_stock'),
                DB::raw('MAX(production_reports.created_at) as last_stock_update'),
                DB::raw('MAX(CAST(production_reports.is_ballcage AS UNSIGNED)) as has_ballcage')
            )
            ->join('production_reports', function ($join) {
                $join->on('items.id', '=', 'production_reports.slide_size_id')
                    ->where('production_reports.is_deleted', '=', false);
            })
            ->leftJoinSub($transferStatsSubQuery, 'sf001_transfers', function ($join) {
                $join->on('items.id', '=', 'sf001_transfers.item_id');
            })
            ->where('items.is_deleted', false)
            ->where('items.status', true)
            ->groupBy('items.id', 'items.name', 'items.code', 'items.size', 'items.weight')
            ->orderBy('items.name')
            ->get();

        return view('backend.production-reports.sf001.stock', compact('itemStocks'));
    }

    /**
     * Export SF001 item stock to CSV.
     */
    public function exportStock()
    {
        $sf1Transfers = DB::table('sf001_stock_transfers')
            ->select('item_id', 'quantity', 'reject_quantity', 'is_accept')
            ->where('is_deleted', false);

        $ppcTransfers = DB::table('sf002_to_ppc_transfers')
            ->select('item_id', 'quantity', 'reject_quantity', 'is_accept')
            ->where('type', 'ballcage')
            ->where('is_deleted', false);

        $combinedTransfers = $sf1Transfers->unionAll($ppcTransfers);

        $transferStatsSubQuery = DB::table(DB::raw("({$combinedTransfers->toSql()}) as combined"))
            ->mergeBindings($combinedTransfers)
            ->select(
                'item_id',
                DB::raw("COALESCE(SUM(CASE
                    WHEN is_accept = 2 THEN 0
                    WHEN is_accept = 1 THEN GREATEST(quantity - COALESCE(reject_quantity, 0), 0)
                    ELSE quantity
                END), 0) as transferred_quantity"),
                DB::raw("COALESCE(SUM(CASE
                    WHEN is_accept = 2 THEN quantity
                    WHEN is_accept = 1 THEN COALESCE(reject_quantity, 0)
                    ELSE 0
                END), 0) as rejected_quantity")
            )
            ->groupBy('item_id');

        $itemStocks = Item::query()
            ->select(
                'items.id',
                'items.name',
                'items.code',
                'items.size',
                'items.weight',
                DB::raw('COALESCE(SUM(production_reports.actual_set_shift), 0) as total_produced_stock'),
                DB::raw('COALESCE(MAX(sf001_transfers.transferred_quantity), 0) as transferred_quantity'),
                DB::raw('COALESCE(MAX(sf001_transfers.rejected_quantity), 0) as rejected_quantity'),
                DB::raw('GREATEST(COALESCE(SUM(production_reports.actual_set_shift), 0) - COALESCE(MAX(sf001_transfers.transferred_quantity), 0) - COALESCE(MAX(sf001_transfers.rejected_quantity), 0), 0) as pending_quantity'),
                DB::raw('MAX(production_reports.created_at) as last_stock_update'),
                DB::raw('MAX(CAST(production_reports.is_ballcage AS UNSIGNED)) as has_ballcage')
            )
            ->join('production_reports', function ($join) {
                $join->on('items.id', '=', 'production_reports.slide_size_id')
                    ->where('production_reports.is_deleted', '=', false);
            })
            ->leftJoinSub($transferStatsSubQuery, 'sf001_transfers', function ($join) {
                $join->on('items.id', '=', 'sf001_transfers.item_id');
            })
            ->where('items.is_deleted', false)
            ->where('items.status', true)
            ->groupBy('items.id', 'items.name', 'items.code', 'items.size', 'items.weight')
            ->orderBy('items.name')
            ->get();

        $filename = "sf001_item_stock_" . date('Y-m-d_H-i-s') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Item Code', 'Item Name', 'Size', 'Total Production', 'In Stock', 'Transferred', 'Rejected', 'Ballcage', 'Last Stock Update'];

        $callback = function () use ($itemStocks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($itemStocks as $item) {
                fputcsv($file, [
                    $item->code,
                    $item->name,
                    $item->size,
                    $item->total_produced_stock,
                    $item->pending_quantity,
                    $item->transferred_quantity,
                    $item->rejected_quantity,
                    $item->has_ballcage ? 'Yes' : 'No',
                    $item->last_stock_update ? \Carbon\Carbon::parse($item->last_stock_update)->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Store SF001 stock transfer to target role.
     */
    public function storeTransfer(Request $request): RedirectResponse
    {
        $itemId = $request->input('item_id');
        $hasBallcage = ProductionReport::where('slide_size_id', $itemId)
            ->where('is_deleted', false)
            ->where('is_ballcage', true)
            ->exists();

        $allowedProcesses = $hasBallcage ? 'CED,ZINC,PPC' : 'CED,ZINC';

        $validated = $request->validate([
            'item_id' => 'required|integer|exists:items,id',
            'assign_sf2' => 'required|string|in:' . $allowedProcesses,
            'quantity' => 'required|numeric|gt:0',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i:s',
            'remark' => 'nullable|string|max:500',
        ]);

        $totalProducedStock = ProductionReport::query()
            ->where('slide_size_id', $validated['item_id'])
            ->where('is_deleted', false)
            ->sum('actual_set_shift');

        $totalTransferredSF1 = (float) DB::table('sf001_stock_transfers')
            ->where('item_id', $validated['item_id'])
            ->where('is_deleted', false)
            ->selectRaw("COALESCE(SUM(CASE
                WHEN is_accept = 2 THEN 0
                WHEN is_accept = 1 THEN GREATEST(quantity - COALESCE(reject_quantity, 0), 0)
                ELSE quantity
            END), 0) as transferred_quantity")
            ->value('transferred_quantity');

        $totalTransferredPPC = (float) DB::table('sf002_to_ppc_transfers')
            ->where('item_id', $validated['item_id'])
            ->where('type', 'ballcage')
            ->where('is_deleted', false)
            ->selectRaw("COALESCE(SUM(CASE
                WHEN is_accept = 2 THEN 0
                WHEN is_accept = 1 THEN GREATEST(quantity - COALESCE(reject_quantity, 0), 0)
                ELSE quantity
            END), 0) as transferred_quantity")
            ->value('transferred_quantity');

        $totalTransferredStock = $totalTransferredSF1 + $totalTransferredPPC;

        $availableStock = max((float) $totalProducedStock - (float) $totalTransferredStock, 0);

        if ((float) $validated['quantity'] > $availableStock) {
            return back()->withErrors([
                'quantity' => 'Transfer quantity cannot be greater than available quantity (' . number_format($availableStock, 2) . ').',
            ])->withInput();
        }

        if ($validated['assign_sf2'] === 'PPC') {
            DB::table('sf002_to_ppc_transfers')->insert([
                'item_id' => $validated['item_id'],
                'transfer_by' => Auth::id(),
                'assign_role' => 'PPC',
                'type' => 'ballcage',
                'assign_to' => null,
                'quantity' => $validated['quantity'],
                'date' => $validated['date'],
                'time' => $validated['time'],
                'is_accept' => 0,
                'remark' => $validated['remark'] ?? null,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('sf001_stock_transfers')->insert([
                'item_id' => $validated['item_id'],
                'transfer_by' => Auth::id(),
                'assign_role' => 'SF002',
                'assign_sf2' => $validated['assign_sf2'],
                'assign_to' => null,
                'quantity' => $validated['quantity'],
                'date' => $validated['date'],
                'time' => $validated['time'],
                'is_accept' => 0,
                'remark' => $validated['remark'] ?? null,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        ProductionReport::query()
            ->where('slide_size_id', $validated['item_id'])
            ->where('is_deleted', false)
            ->update([
                'is_transfered' => 1,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Stock transferred successfully.');
    }

    /**
     * Display production history for a specific item.
     */
    public function stockHistory($itemId): View
    {
        $item = Item::findOrFail($itemId);

        $history = ProductionReport::query()
            ->select(
                'production_reports.id',
                'production_reports.report_date',
                'production_reports.shift',
                'production_reports.actual_set_shift',
                'machines.name as machine_name',
                'production_reports.created_at'
            )
            ->join('machines', 'production_reports.machine_id', '=', 'machines.id')
            ->where('production_reports.slide_size_id', $itemId)
            ->where('production_reports.is_deleted', false)
            ->orderBy('production_reports.report_date', 'desc')
            ->orderBy('production_reports.created_at', 'desc')
            ->get();

        $sf1Transfers = DB::table('sf001_stock_transfers as transfers')
            ->select(
                'transfers.id',
                DB::raw("CASE
                    WHEN transfers.is_accept = 2 THEN 0
                    WHEN transfers.is_accept = 1 THEN GREATEST(transfers.quantity - COALESCE(transfers.reject_quantity, 0), 0)
                    ELSE transfers.quantity
                END as quantity"),
                DB::raw("CASE
                    WHEN transfers.is_accept = 2 THEN transfers.quantity
                    WHEN transfers.is_accept = 1 THEN COALESCE(transfers.reject_quantity, 0)
                    ELSE 0
                END as rejected_quantity"),
                'transfers.reject_reason_id',
                'reject_reasons.name as reject_reason_name',
                'transfers.date',
                'transfers.time',
                'transfers.is_accept',
                'transfers.assign_role',
                'transfers.assign_sf2',
                'transfers.remark',
                'transfers.sf002_remark',
                'transfers.is_self_transferred',
                'transfers.self_transferred_parent_id',
                'parent_transfer.assign_sf2 as parent_assign_sf2',
                'parent_transfer.quantity as parent_quantity',
                'parent_transfer.date as parent_date',
                'parent_transfer.time as parent_time',
                'parent_transfer_by_user.name as parent_transfer_by_name',
                'transfers.created_at',
                'transfer_by_user.name as transfer_by_name',
                'assign_to_user.name as assign_to_name'
            )
            ->leftJoin('users as transfer_by_user', 'transfers.transfer_by', '=', 'transfer_by_user.id')
            ->leftJoin('users as assign_to_user', 'transfers.assign_to', '=', 'assign_to_user.id')
            ->leftJoin('reject_reasons', 'transfers.reject_reason_id', '=', 'reject_reasons.id')
            ->leftJoin('sf001_stock_transfers as parent_transfer', 'transfers.self_transferred_parent_id', '=', 'parent_transfer.id')
            ->leftJoin('users as parent_transfer_by_user', 'parent_transfer.transfer_by', '=', 'parent_transfer_by_user.id')
            ->where('transfers.item_id', $itemId)
            ->where('transfers.is_deleted', false);

        $ppcTransfers = DB::table('sf002_to_ppc_transfers as transfers')
            ->select(
                'transfers.id',
                DB::raw("CASE
                    WHEN transfers.is_accept = 2 THEN 0
                    WHEN transfers.is_accept = 1 THEN GREATEST(transfers.quantity - COALESCE(transfers.reject_quantity, 0), 0)
                    ELSE transfers.quantity
                END as quantity"),
                DB::raw("CASE
                    WHEN transfers.is_accept = 2 THEN transfers.quantity
                    WHEN transfers.is_accept = 1 THEN COALESCE(transfers.reject_quantity, 0)
                    ELSE 0
                END as rejected_quantity"),
                'transfers.reject_reason_id',
                'reject_reasons.name as reject_reason_name',
                'transfers.date',
                'transfers.time',
                'transfers.is_accept',
                'transfers.assign_role',
                'transfers.type as assign_sf2',
                'transfers.remark',
                'transfers.ppc_remark as sf002_remark',
                DB::raw("NULL as is_self_transferred"),
                DB::raw("NULL as self_transferred_parent_id"),
                DB::raw("NULL as parent_assign_sf2"),
                DB::raw("NULL as parent_quantity"),
                DB::raw("NULL as parent_date"),
                DB::raw("NULL as parent_time"),
                DB::raw("NULL as parent_transfer_by_name"),
                'transfers.created_at',
                'transfer_by_user.name as transfer_by_name',
                'assign_to_user.name as assign_to_name'
            )
            ->leftJoin('users as transfer_by_user', 'transfers.transfer_by', '=', 'transfer_by_user.id')
            ->leftJoin('users as assign_to_user', 'transfers.assign_to', '=', 'assign_to_user.id')
            ->leftJoin('reject_reasons', 'transfers.reject_reason_id', '=', 'reject_reasons.id')
            ->where('transfers.item_id', $itemId)
            ->where('transfers.type', 'ballcage')
            ->where('transfers.is_deleted', false);

        $stockManageHistory = $sf1Transfers->unionAll($ppcTransfers)
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->orderByDesc('created_at')
            ->get();

        return view('backend.production-reports.sf001.stock-history', compact('item', 'history', 'stockManageHistory'));
    }
}
