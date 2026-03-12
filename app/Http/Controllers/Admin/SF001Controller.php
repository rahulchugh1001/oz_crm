<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
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
        return view('backend.production-reports.coil-stock');
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
