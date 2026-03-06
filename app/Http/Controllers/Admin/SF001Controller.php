<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ProductionReport;
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
        // Get only items that exist in production reports with aggregated quantities
        $itemStocks = Item::query()
            ->select(
                'items.id',
                'items.name',
                'items.code',
                'items.size',
                'items.weight',
                DB::raw('COALESCE(SUM(production_reports.actual_set_shift), 0) as total_stock'),
                DB::raw('MAX(production_reports.created_at) as last_stock_update')
            )
            ->join('production_reports', function ($join) {
                $join->on('items.id', '=', 'production_reports.slide_size_id')
                    ->where('production_reports.is_deleted', '=', false);
            })
            ->where('items.is_deleted', false)
            ->where('items.status', true)
            ->groupBy('items.id', 'items.name', 'items.code', 'items.size', 'items.weight')
            ->orderBy('items.name')
            ->get();

        return view('backend.production-reports.sf001.stock', compact('itemStocks'));
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

        return view('backend.production-reports.sf001.stock-history', compact('item', 'history'));
    }
}
