<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        return view('backend.production-reports.coil-stock', compact('coils', 'suppliers', 'machines', 'loadedMachineNames'));
    }

    /**
     * Store a new coil stock record.
     */
    public function storeCoilStock(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'manufacture_id' => 'required|integer|exists:coil_manufacture,id',
            'coil_no' => 'required|string|max:120|unique:coil_stock,coil_no',
            'coil_size' => 'required|string|max:60',
            'thickness' => 'required|numeric|min:0',
            'net_weight_kg' => 'required|numeric|min:0',
            'process' => 'required|in:available,in_use,completed',
            'status' => 'required|in:0,1',
        ]);

        CoilStock::query()->create([
            'manufacture_id' => (int) $validated['manufacture_id'],
            'coil_no' => trim((string) $validated['coil_no']),
            'coil_size' => trim((string) $validated['coil_size']),
            'thickness' => (float) $validated['thickness'],
            'net_weight_kg' => (float) $validated['net_weight_kg'],
            'process' => (string) $validated['process'],
            'status' => (int) $validated['status'],
            'is_deleted' => 0,
        ]);

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
            'process' => 'required|in:available,in_use,completed',
            'status' => 'required|in:0,1',
        ]);

        $coil->update([
            'manufacture_id' => (int) $validated['manufacture_id'],
            'coil_no' => trim((string) $validated['coil_no']),
            'coil_size' => trim((string) $validated['coil_size']),
            'thickness' => (float) $validated['thickness'],
            'net_weight_kg' => (float) $validated['net_weight_kg'],
            'process' => (string) $validated['process'],
            'status' => (int) $validated['status'],
        ]);

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
            return back()->with('error', 'In-use coil cannot be deleted.');
        }

        $coil->update([
            'is_deleted' => 1,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Coil stock deleted successfully.');
    }

    /**
     * Load selected coil to selected machine.
     */
    public function loadCoilToMachine(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'form_type' => 'required|in:load',
            'coil_id' => 'required|integer|exists:coil_stock,id',
            'machine_id' => 'required|integer|exists:machines,id',
        ]);

        $coil = CoilStock::query()
            ->where('id', (int) $validated['coil_id'])
            ->where('is_deleted', 0)
            ->where('status', 1)
            ->first();

        if (!$coil) {
            return back()->with('error', 'Selected coil is not available for loading.');
        }

        $machine = Machine::query()
            ->where('id', (int) $validated['machine_id'])
            ->where('is_deleted', 0)
            ->where('status', 1)
            ->first();

        if (!$machine) {
            return back()->with('error', 'Selected machine is not active.');
        }

        $machine->update([
            'coil_id' => $coil->id,
        ]);

        $coil->update([
            'process' => 'in_use',
        ]);

        return back()->with('success', 'Coil loaded to machine successfully.');
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
            ->where('transfers.item_id', $itemId)
            ->where('transfers.is_deleted', false)
            ->orderByDesc('transfers.date')
            ->orderByDesc('transfers.time')
            ->orderByDesc('transfers.created_at')
            ->get();

        return view('backend.production-reports.sf001.stock-history', compact('item', 'history', 'stockManageHistory'));
    }
}
