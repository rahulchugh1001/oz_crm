<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoilMachineTrack;
use App\Models\CoilMachineTrackLog;
use App\Models\CoilManufacture;
use App\Models\CoilStock;
use App\Models\Item;
use App\Models\Machine;
use App\Models\ProductionReport;
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
            ->with(['manufacture:id,name'])
            ->where('is_deleted', 0)
            ->orderByDesc('id')
            ->get();

        $loadedMachineNames = Machine::query()
            ->where('is_deleted', 0)
            ->whereNotNull('coil_id')
            ->get(['coil_id', 'name'])
            ->groupBy('coil_id')
            ->map(function ($rows) {
                return $rows->pluck('name')->unique()->implode(', ');
            });

        $loadedMachinesByCoil = Machine::query()
            ->where('is_deleted', 0)
            ->whereNotNull('coil_id')
            ->get(['id', 'coil_id', 'name', 'machine_code'])
            ->map(function ($machine) {
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
                    ->first(['id', 'load_weight']);

                $machine->active_load_weight = $activeLoadTrack ? (float) $activeLoadTrack->load_weight : null;

                return $machine;
            })
            ->groupBy('coil_id')
            ->map(function ($rows) {
                return $rows->map(function ($row) {
                    return [
                        'id' => $row->id,
                        'name' => $row->name,
                        'machine_code' => $row->machine_code,
                        'active_load_weight' => $row->active_load_weight,
                    ];
                })->values()->all();
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

        return view('backend.production-reports.coil-stock-view', compact(
            'coil',
            'loadedMachines',
            'assignedMachines',
            'trackHistory',
            'logHistory',
            'productionReports'
        ));
    }

    /**
     * Store a new coil stock record.
     */
    public function storeCoilStock(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'manufacture_id' => 'required',
            'new_manufacture_name' => 'nullable|string|max:100|unique:coil_manufacture,name',
            'coil_no' => 'required|string|max:120|unique:coil_stock,coil_no',
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
            'coil_no' => trim((string) $validated['coil_no']),
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
            'coil_no' => 'required|string|max:120|unique:coil_stock,coil_no,' . $coilId,
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
            'coil_no' => trim((string) $validated['coil_no']),
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
            'name'       => trim((string) $validated['name']),
            'status'     => 1,
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
            'name'   => 'required|string|max:100|unique:coil_manufacture,name,' . $id,
            'status' => 'required|in:0,1',
        ]);

        $manufacturer->update([
            'name'   => trim((string) $validated['name']),
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
            'load_weight' => 'required_if:form_type,' . $loadAction . '|numeric|gt:0',
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
                        'coil_no' => $coil->coil_no,
                        'load_weight' => $loadWeight,
                        'remaining_net_weight' => $remainingNetWeight,
                        'total_weight' => $coilNetWeightTotal,
                    ],
                    'Coil loaded to machine.'
                );
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
        $transferStatsSubQuery = DB::table('sf001_stock_transfers')
            ->where('is_deleted', false)
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
                DB::raw('GREATEST(COALESCE(SUM(production_reports.actual_set_shift), 0) - COALESCE(MAX(sf001_transfers.transferred_quantity), 0), 0) as pending_quantity'),
                DB::raw('GREATEST(COALESCE(SUM(production_reports.actual_set_shift), 0) - COALESCE(MAX(sf001_transfers.transferred_quantity), 0), 0) as total_stock'),
                DB::raw('MAX(production_reports.created_at) as last_stock_update')
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
     * Store SF001 stock transfer to target role.
     */
    public function storeTransfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|integer|exists:items,id',
            'assign_sf2' => 'required|string|in:CED,ZINC',
            'quantity' => 'required|numeric|gt:0',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i:s',
            'remark' => 'nullable|string|max:500',
        ]);

        $totalProducedStock = ProductionReport::query()
            ->where('slide_size_id', $validated['item_id'])
            ->where('is_deleted', false)
            ->sum('actual_set_shift');

        $totalTransferredStock = DB::table('sf001_stock_transfers')
            ->where('item_id', $validated['item_id'])
            ->where('is_deleted', false)
            ->selectRaw("COALESCE(SUM(CASE
                WHEN is_accept = 2 THEN 0
                WHEN is_accept = 1 THEN GREATEST(quantity - COALESCE(reject_quantity, 0), 0)
                ELSE quantity
            END), 0) as transferred_quantity")
            ->value('transferred_quantity');

        $availableStock = max((float) $totalProducedStock - (float) $totalTransferredStock, 0);

        if ((float) $validated['quantity'] > $availableStock) {
            return back()->withErrors([
                'quantity' => 'Transfer quantity cannot be greater than available quantity (' . number_format($availableStock, 2) . ').',
            ])->withInput();
        }

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

        $stockManageHistory = DB::table('sf001_stock_transfers as transfers')
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
                'transfers.created_at',
                'transfer_by_user.name as transfer_by_name',
                'assign_to_user.name as assign_to_name'
            )
            ->leftJoin('users as transfer_by_user', 'transfers.transfer_by', '=', 'transfer_by_user.id')
            ->leftJoin('users as assign_to_user', 'transfers.assign_to', '=', 'assign_to_user.id')
            ->leftJoin('reject_reasons', 'transfers.reject_reason_id', '=', 'reject_reasons.id')
            ->where('transfers.item_id', $itemId)
            ->where('transfers.is_deleted', false)
            ->orderByDesc('transfers.date')
            ->orderByDesc('transfers.time')
            ->orderByDesc('transfers.created_at')
            ->get();

        return view('backend.production-reports.sf001.stock-history', compact('item', 'history', 'stockManageHistory'));
    }
}
