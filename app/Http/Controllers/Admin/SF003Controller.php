<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SF003Controller extends Controller
{
    protected function syncSf3StockUsageTracking(int $reportId, int $sf3ItemId, array $rows): void
    {
        if (!Schema::hasTable('sf3_stock_usages')) {
            return;
        }

        DB::table('sf3_stock_usages')
            ->where('report_id', $reportId)
            ->delete();

        if ($rows === []) {
            return;
        }

        $usageByStock = collect($rows)
            ->groupBy(function (array $row) {
                return (int) ($row['product_id'] ?? 0);
            })
            ->map(function ($group) {
                return (float) collect($group)->sum('quantity_used');
            })
            ->filter(function ($usedStock, $stockId) {
                return (int) $stockId > 0 && (float) $usedStock > 0;
            });

        if ($usageByStock->isEmpty()) {
            return;
        }

        $stockItems = DB::table('items')
            ->select('id', 'quantity', 'category')
            ->whereIn('id', $usageByStock->keys()->all())
            ->whereIn('category', ['Store', 'Stock'])
            ->get()
            ->keyBy('id');

        if ($stockItems->isEmpty()) {
            return;
        }

        $now = now();
        $trackingRows = [];

        foreach ($usageByStock as $stockId => $usedStock) {
            $sid = (int) $stockId;
            if (!$stockItems->has($sid)) {
                continue;
            }

            $stockItem = $stockItems->get($sid);
            $trackingRows[] = [
                'report_id' => $reportId,
                'item_id' => $sf3ItemId,
                'stock_id' => $sid,
                'in_stock' => round(max((float) ($stockItem->quantity ?? 0), 0), 2),
                'used_stock' => round((float) $usedStock, 2),
                'status' => 1,
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($trackingRows !== []) {
            DB::table('sf3_stock_usages')->insert($trackingRows);
        }
    }

    protected function syncStoreItemQuantities($previousUsageByProduct, $newUsageByProduct): void
    {
        $previous = collect($previousUsageByProduct)
            ->mapWithKeys(function ($value, $key) {
                return [(int) $key => (float) $value];
            });

        $current = collect($newUsageByProduct)
            ->mapWithKeys(function ($value, $key) {
                return [(int) $key => (float) $value];
            });

        $impactedProductIds = $previous->keys()
            ->merge($current->keys())
            ->map(function ($id) {
                return (int) $id;
            })
            ->filter(function ($id) {
                return $id > 0;
            })
            ->unique()
            ->values();

        if ($impactedProductIds->isEmpty()) {
            return;
        }

        foreach ($impactedProductIds as $productId) {
            $oldUsed = (float) ($previous->get($productId) ?? 0);
            $newUsed = (float) ($current->get($productId) ?? 0);

            if (abs($oldUsed - $newUsed) < 0.00001) {
                continue;
            }

            $currentQuantity = (float) (DB::table('items')->where('id', $productId)->value('quantity') ?? 0);
            $updatedQuantity = max($currentQuantity + $oldUsed - $newUsed, 0);

            DB::table('items')
                ->where('id', $productId)
                ->update([
                    'quantity' => round($updatedQuantity, 2),
                    'updated_at' => now(),
                ]);
        }
    }

    protected function syncProductionReportProducts(int $reportId, int $itemId, string $lineCode, float $actualSetShift): void
    {
        $previousTransferIds = DB::table('sf3_production_report_products')
            ->where('mst_item_id', $reportId)
            ->whereNotNull('transfered_id')
            ->pluck('transfered_id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->filter(function ($id) {
                return $id > 0;
            })
            ->unique()
            ->values();

        $previousStoreUsageByProduct = DB::table('sf3_production_report_products as details')
            ->join('items', 'details.product_id', '=', 'items.id')
            ->where('details.mst_item_id', $reportId)
            ->where('details.is_deleted', 0)
            ->where('items.category', 'Store')
            ->select('details.product_id', DB::raw('COALESCE(SUM(details.quantity_used), 0) as total_used'))
            ->groupBy('details.product_id')
            ->pluck('total_used', 'details.product_id');

        $previousUsageByTransfer = DB::table('sf3_production_report_products')
            ->where('mst_item_id', $reportId)
            ->where('is_deleted', 0)
            ->whereNotNull('transfered_id')
            ->select('transfered_id', DB::raw('COALESCE(SUM(quantity_used), 0) as total_used'))
            ->groupBy('transfered_id')
            ->pluck('total_used', 'transfered_id');

        $products = DB::table('item_sf3_products')
            ->select('product', 'quantity')
            ->where('item_id', $itemId)
            ->orderBy('product')
            ->get();

        $storeProductIds = collect();
        $productIds = $products->pluck('product')
            ->map(function ($id) {
                return (int) $id;
            })
            ->filter(function ($id) {
                return $id > 0;
            })
            ->unique()
            ->values();

        if ($productIds->isNotEmpty()) {
            $storeProductIds = DB::table('items')
                ->whereIn('id', $productIds->all())
                ->where('category', 'Store')
                ->pluck('id')
                ->map(function ($id) {
                    return (int) $id;
                })
                ->filter(function ($id) {
                    return $id > 0;
                })
                ->unique()
                ->values();
        }

        DB::table('sf3_production_report_products')
            ->where('mst_item_id', $reportId)
            ->delete();

        if ($products->isEmpty()) {
            $this->syncStoreItemQuantities($previousStoreUsageByProduct, collect());
            $this->syncSf3StockUsageTracking($reportId, $itemId, []);

            if (Schema::hasTable('sf002_stock_transfers') && Schema::hasColumn('sf002_stock_transfers', 'used_quantity')) {
                $previousTransferIds->each(function ($transferId) {
                    $usedQuantity = (float) (DB::table('sf3_production_report_products')
                        ->where('transfered_id', $transferId)
                        ->where('is_deleted', 0)
                        ->sum('quantity_used') ?? 0);

                    DB::table('sf002_stock_transfers')
                        ->where('id', $transferId)
                        ->update([
                            'used_quantity' => round($usedQuantity, 2),
                            'updated_at' => now(),
                        ]);
                });
            }

            return;
        }

        $transferPoolsByProduct = collect();
        if ($productIds->isNotEmpty()) {
            $transferPoolsByProduct = DB::table('sf002_stock_transfers')
                ->select('id', 'item_id', 'quantity', 'reject_quantity', 'used_quantity')
                ->where('is_deleted', false)
                ->where('is_accept', 1)
                ->where('sf3_process', $lineCode)
                ->whereIn('item_id', $productIds->all())
                ->orderByDesc('date')
                ->orderByDesc('time')
                ->orderByDesc('created_at')
                ->get()
                ->groupBy('item_id')
                ->map(function ($rows) {
                    return $rows->map(function ($transfer) {
                        $approvedQuantity = max((float) ($transfer->quantity ?? 0) - (float) ($transfer->reject_quantity ?? 0), 0);
                        $alreadyUsed = max((float) ($transfer->used_quantity ?? 0), 0);
                        $effectiveAvailable = max($approvedQuantity - $alreadyUsed, 0);

                        return [
                            'id' => (int) $transfer->id,
                            'available' => $effectiveAvailable,
                        ];
                    })->values();
                });

            $previousUsageByTransfer->each(function ($usedQty, $transferId) use (&$transferPoolsByProduct) {
                $tid = (int) $transferId;
                if ($tid <= 0) {
                    return;
                }

                $restoredQty = max((float) $usedQty, 0);
                if ($restoredQty <= 0) {
                    return;
                }

                $transferPoolsByProduct = $transferPoolsByProduct->map(function ($pool) use ($tid, $restoredQty) {
                    return collect($pool)->map(function ($entry) use ($tid, $restoredQty) {
                        if ((int) ($entry['id'] ?? 0) === $tid) {
                            $entry['available'] = max((float) ($entry['available'] ?? 0) + $restoredQty, 0);
                        }

                        return $entry;
                    })->values();
                });
            });
        }

        $timestamp = now();
        $rows = $products->flatMap(function ($product) use ($reportId, $actualSetShift, $timestamp, &$transferPoolsByProduct) {
            $baseQuantity = (float) ($product->quantity ?? 0);
            $productId = (int) ($product->product ?? 0);
            $requiredQuantity = round($baseQuantity, 2);
            $usageToAllocate = round($baseQuantity * $actualSetShift, 2);

            if ($productId <= 0) {
                return [];
            }

            $allocatedRows = [];
            $remainingUsage = max($usageToAllocate, 0);
            $isFirstChunk = true;

            if ($transferPoolsByProduct->has($productId)) {
                $pool = collect($transferPoolsByProduct->get($productId));

                $pool = $pool->map(function ($entry) use (&$remainingUsage, &$allocatedRows, $reportId, $productId, $requiredQuantity, $timestamp, &$isFirstChunk) {
                    $available = max((float) ($entry['available'] ?? 0), 0);
                    if ($remainingUsage <= 0 || $available <= 0) {
                        return $entry;
                    }

                    $consume = min($available, $remainingUsage);
                    if ($consume > 0) {
                        $allocatedRows[] = [
                            'mst_item_id' => $reportId,
                            'transfered_id' => (int) ($entry['id'] ?? 0),
                            'product_id' => $productId,
                            'quantity_required' => $isFirstChunk ? $requiredQuantity : 0,
                            'quantity_used' => round($consume, 2),
                            'status' => 1,
                            'is_deleted' => 0,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ];

                        $remainingUsage = max($remainingUsage - $consume, 0);
                        $entry['available'] = max($available - $consume, 0);
                        $isFirstChunk = false;
                    }

                    return $entry;
                })->values();

                $transferPoolsByProduct->put($productId, $pool);
            }

            if ($remainingUsage > 0 || empty($allocatedRows)) {
                $allocatedRows[] = [
                    'mst_item_id' => $reportId,
                    'transfered_id' => null,
                    'product_id' => $productId,
                    'quantity_required' => $isFirstChunk ? $requiredQuantity : 0,
                    'quantity_used' => round($remainingUsage, 2),
                    'status' => 1,
                    'is_deleted' => 0,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }

            return $allocatedRows;
        })->filter(function (array $row) {
            return $row['product_id'] > 0;
        })->values()->all();

        if ($rows !== []) {
            DB::table('sf3_production_report_products')->insert($rows);
        }

        $newStoreUsageByProduct = collect($rows)
            ->filter(function (array $row) use ($storeProductIds) {
                return $storeProductIds->contains((int) ($row['product_id'] ?? 0));
            })
            ->groupBy(function (array $row) {
                return (int) ($row['product_id'] ?? 0);
            })
            ->map(function ($group) {
                return (float) collect($group)->sum('quantity_used');
            });

        $this->syncStoreItemQuantities($previousStoreUsageByProduct, $newStoreUsageByProduct);
        $this->syncSf3StockUsageTracking($reportId, $itemId, $rows);

        if (Schema::hasTable('sf002_stock_transfers') && Schema::hasColumn('sf002_stock_transfers', 'used_quantity')) {
            $currentTransferIds = collect($rows)
                ->pluck('transfered_id')
                ->map(function ($id) {
                    return (int) $id;
                })
                ->filter(function ($id) {
                    return $id > 0;
                })
                ->unique()
                ->values();

            $impactedTransferIds = $previousTransferIds
                ->merge($currentTransferIds)
                ->unique()
                ->values();

            $impactedTransferIds->each(function ($transferId) {
                $usedQuantity = (float) (DB::table('sf3_production_report_products')
                    ->where('transfered_id', $transferId)
                    ->where('is_deleted', 0)
                    ->sum('quantity_used') ?? 0);

                DB::table('sf002_stock_transfers')
                    ->where('id', $transferId)
                    ->update([
                        'used_quantity' => round($usedQuantity, 2),
                        'updated_at' => now(),
                    ]);
            });
        }
    }

    protected function resolveLineContext(string $requestedLine): array
    {
        $lineMap = [
            'l1' => ['code' => 'line_1', 'label' => 'L1', 'title' => 'Assemble Line 1'],
            'l2' => ['code' => 'line_2', 'label' => 'L2', 'title' => 'Assemble Line 2'],
            'l3' => ['code' => 'line_3', 'label' => 'L3', 'title' => 'Assemble Line 3'],
        ];

        $normalized = strtolower($requestedLine);
        if (!array_key_exists($normalized, $lineMap)) {
            $normalized = 'l1';
        }

        return [
            'requestedLine' => $normalized,
            'lineCode' => $lineMap[$normalized]['code'],
            'lineLabel' => $lineMap[$normalized]['label'],
            'lineTitle' => $lineMap[$normalized]['title'],
        ];
    }

    protected function currentAssignableRole(): ?string
    {
        $role = Auth::user()?->role;

        return $role === 'SF003' ? $role : null;
    }

    /**
     * Build base query for SF003 assigned transfers.
     */
    protected function assignedTransfersQuery()
    {
        $query = DB::table('sf002_stock_transfers as transfers')
            ->select(
                'transfers.id',
                'transfers.item_id',
                'transfers.quantity',
                'transfers.used_quantity',
                'transfers.reject_quantity',
                'transfers.reject_reason_id',
                'transfers.created_at',
                'transfers.updated_at',
                'transfers.date',
                'transfers.time',
                'transfers.is_accept',
                'transfers.type',
                'transfers.sf3_process',
                'transfers.assign_role',
                'transfers.assign_to',
                'transfers.transfer_by',
                'transfers.remark',
                'transfers.sf003_remark',
                'reject_reasons.name as reject_reason_name',
                DB::raw("CASE WHEN items.category = 'sf1-sf2' AND items.code_sf2 IS NOT NULL AND items.code_sf2 != '' THEN items.code_sf2 ELSE items.code END as item_code"),
                DB::raw("CASE WHEN items.category = 'sf1-sf2' AND items.name_sf2 IS NOT NULL AND items.name_sf2 != '' THEN items.name_sf2 ELSE items.name END as item_name"),
                'items.size as item_size',
                'transfer_by_user.name as transfer_by_name',
                'assigned_to_user.name as assigned_to_name'
            )
            ->join('items', 'transfers.item_id', '=', 'items.id')
            ->leftJoin('users as transfer_by_user', 'transfers.transfer_by', '=', 'transfer_by_user.id')
            ->leftJoin('users as assigned_to_user', 'transfers.assign_to', '=', 'assigned_to_user.id')
            ->leftJoin('reject_reasons', 'transfers.reject_reason_id', '=', 'reject_reasons.id')
            ->where('transfers.is_deleted', false)
            ->orderByDesc('transfers.date')
            ->orderByDesc('transfers.time')
            ->orderByDesc('transfers.created_at');

        if (Auth::user()?->role !== 'Admin') {
            $role = $this->currentAssignableRole();

            if (!$role) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where('transfers.assign_role', $role)
                    ->where(function ($scoped) {
                        $scoped->whereNull('transfers.assign_to')
                            ->orWhere('transfers.assign_to', Auth::id());
                    });
            }

        }

        return $query;
    }

    /**
     * Display stock transfers assigned to the logged-in SF003 user.
     */
    public function index(): View
    {
        $assignedTransfers = $this->assignedTransfersQuery()->get();

        $rejectReasons = DB::table('reject_reasons')
            ->select('id', 'name')
            ->where('is_deleted', 0)
            ->where('status', 1)
            ->whereIn('category', ['SF2', 'Both'])
            ->orderBy('name')
            ->get();

        return view('backend.production-reports.sf003.stock', compact('assignedTransfers', 'rejectReasons'));
    }

    /**
     * Display Assemble SF3 line process page with stock and production tabs.
     */
    public function process(Request $request): View
    {
        $lineContext = $this->resolveLineContext((string) $request->query('line', 'l1'));
        $requestedLine = $lineContext['requestedLine'];
        $lineCode = $lineContext['lineCode'];
        $lineLabel = $lineContext['lineLabel'];

        if (Auth::user()?->role === 'Admin') {
            $acceptedTransfers = $this->assignedTransfersQuery()
                ->addSelect('transfers.assign_to', 'accepted_by_user.name as accepted_by_name')
                ->addSelect(DB::raw('GREATEST(transfers.quantity - COALESCE(transfers.reject_quantity, 0), 0) as accepted_quantity'))
                ->leftJoin('users as accepted_by_user', 'transfers.assign_to', '=', 'accepted_by_user.id')
                ->where('transfers.is_accept', 1)
                ->where('transfers.sf3_process', $lineCode)
                ->get();
        } else {
            $role = $this->currentAssignableRole();

            $acceptedTransfers = DB::table('sf002_stock_transfers as transfers')
                ->select(
                    'transfers.id',
                    'transfers.item_id',
                    'transfers.quantity',
                    'transfers.used_quantity',
                    'transfers.date',
                    'transfers.time',
                    'transfers.is_accept',
                    'transfers.type',
                    'transfers.sf3_process',
                    'transfers.reject_quantity',
                    DB::raw('GREATEST(transfers.quantity - COALESCE(transfers.reject_quantity, 0), 0) as accepted_quantity'),
                    'transfers.remark',
                    'transfers.sf003_remark',
                    'transfers.assign_to',
                    'items.code as item_code',
                    'items.name as item_name',
                    'items.size as item_size',
                    'transfer_by_user.name as transfer_by_name',
                    'accepted_by_user.name as accepted_by_name'
                )
                ->join('items', 'transfers.item_id', '=', 'items.id')
                ->leftJoin('users as transfer_by_user', 'transfers.transfer_by', '=', 'transfer_by_user.id')
                ->leftJoin('users as accepted_by_user', 'transfers.assign_to', '=', 'accepted_by_user.id')
                ->where('transfers.is_deleted', false)
                ->where('transfers.is_accept', 1)
                ->where('transfers.sf3_process', $lineCode)
                ->when($role, function ($query, $roleValue) {
                    $query->where('transfers.assign_role', $roleValue);
                }, function ($query) {
                    $query->whereRaw('1 = 0');
                })
                ->orderByDesc('transfers.date')
                ->orderByDesc('transfers.time')
                ->orderByDesc('transfers.created_at')
                ->get();
        }

        if (Schema::hasTable('sf3_production_reports')) {
            $sf3ProductionReportsQuery = DB::table('sf3_production_reports as reports')
                ->select(
                    'reports.*',
                    'items.code as item_code',
                    'items.name as item_name',
                    'items.size as item_size',
                    'users.name as created_by_name'
                )
                ->leftJoin('items', 'reports.item_id', '=', 'items.id')
                ->leftJoin('users', 'reports.created_by', '=', 'users.id')
                ->where('reports.is_deleted', 0)
                ->where('reports.sf3_process', $lineCode)
                ->orderByDesc('reports.report_date')
                ->orderByDesc('reports.created_at');

            if (Auth::user()?->role !== 'Admin') {
                $sf3ProductionReportsQuery->where('reports.created_by', Auth::id());
            }

            $sf3ProductionReports = $sf3ProductionReportsQuery->get();
        } else {
            $sf3ProductionReports = collect();
        }

        return view('backend.production-reports.sf003.process', compact(
            'acceptedTransfers',
            'sf3ProductionReports',
            'requestedLine',
            'lineCode',
            'lineLabel'
        ));
    }

    /**
     * Display final stock list from SF3 production reports.
     */
    public function finalStock(Request $request): View
    {
        $selectedLine = strtolower((string) $request->query('line', 'all'));
        $allowedLines = ['all', 'l1', 'l2', 'l3'];
        if (!in_array($selectedLine, $allowedLines, true)) {
            $selectedLine = 'all';
        }

        if (Schema::hasTable('sf3_production_reports')) {
            $finalStockQuery = DB::table('sf3_production_reports as reports')
                ->select(
                    'reports.*',
                    'items.code as item_code',
                    'items.name as item_name',
                    'items.size as item_size',
                    'users.name as created_by_name'
                )
                ->leftJoin('items', 'reports.item_id', '=', 'items.id')
                ->leftJoin('users', 'reports.created_by', '=', 'users.id')
                ->where('reports.is_deleted', 0)
                ->when($selectedLine !== 'all', function ($query) use ($selectedLine) {
                    $lineContext = $this->resolveLineContext($selectedLine);
                    $query->where('reports.sf3_process', $lineContext['lineCode']);
                })
                ->orderByDesc('reports.report_date')
                ->orderByDesc('reports.created_at');

            if (Auth::user()?->role !== 'Admin') {
                $finalStockQuery->where('reports.created_by', Auth::id());
            }

            $finalStockReports = $finalStockQuery->get();
        } else {
            $finalStockReports = collect();
        }

        return view('backend.production-reports.sf003.final-stock', compact('finalStockReports', 'selectedLine'));
    }

    /**
     * Display final stock details from sf3_production_reports and sf3_production_report_products.
     */
    public function finalStockShow(string $encryptedId): View
    {
        try {
            $reportId = (int) Crypt::decryptString($encryptedId);
        } catch (\Exception $e) {
            abort(404, 'Final stock record not found.');
        }

        if (!Schema::hasTable('sf3_production_reports')) {
            abort(500, 'SF3 production reports table is missing. Please run migrations.');
        }

        $reportQuery = DB::table('sf3_production_reports as reports')
            ->select(
                'reports.*',
                'items.code as item_code',
                'items.name as item_name',
                'items.size as item_size',
                'users.name as created_by_name'
            )
            ->leftJoin('items', 'reports.item_id', '=', 'items.id')
            ->leftJoin('users', 'reports.created_by', '=', 'users.id')
            ->where('reports.id', $reportId)
            ->where('reports.is_deleted', 0);

        if (Auth::user()?->role !== 'Admin') {
            $reportQuery->where('reports.created_by', Auth::id());
        }

        $report = $reportQuery->first();
        if (!$report) {
            abort(404, 'Final stock record not found.');
        }

        $productRows = collect();
        if (Schema::hasTable('sf3_production_report_products')) {
            $productRows = DB::table('sf3_production_report_products as details')
                ->select(
                    'details.*',
                    'product_items.code as product_code',
                    'product_items.name as product_name',
                    'product_items.category as product_category',
                    'transfers.quantity as transfer_quantity',
                    'transfers.used_quantity as transfer_used_quantity',
                    DB::raw('GREATEST(COALESCE(transfers.quantity, 0) - COALESCE(transfers.used_quantity, 0), 0) as transfer_available_quantity')
                )
                ->leftJoin('items as product_items', 'details.product_id', '=', 'product_items.id')
                ->leftJoin('sf002_stock_transfers as transfers', 'details.transfered_id', '=', 'transfers.id')
                ->where('details.mst_item_id', $reportId)
                ->where('details.is_deleted', 0)
                ->orderBy('details.id')
                ->get();
        }

        return view('backend.production-reports.sf003.final-stock-show', compact('report', 'productRows'));
    }

    /**
     * Display production report form for SF3 line process.
     */
    public function productionReport(Request $request, ?int $transferId = null): View
    {
        if (!Schema::hasTable('sf3_production_reports')) {
            abort(500, 'SF3 production reports table is missing. Please run migrations.');
        }

        $lineContext = $this->resolveLineContext((string) $request->query('line', 'l1'));
        $requestedLine = $lineContext['requestedLine'];
        $lineCode = $lineContext['lineCode'];
        $lineLabel = $lineContext['lineLabel'];
        $lineTitle = $lineContext['lineTitle'];

        $encryptedReportId = (string) $request->query('report_id', '');
        $reportId = 0;
        if ($encryptedReportId !== '') {
            try {
                $reportId = (int) Crypt::decryptString($encryptedReportId);
            } catch (\Exception $e) {
                $reportId = 0;
            }
        }

        $existingReport = null;
        if ($reportId > 0) {
            $existingQuery = DB::table('sf3_production_reports')
                ->where('id', $reportId)
                ->where('sf3_process', $lineCode)
                ->where('is_deleted', 0);

            if (Auth::user()?->role !== 'Admin') {
                $existingQuery->where('created_by', Auth::id());
            }

            $existingReport = $existingQuery->first();
        }

        $sf3Items = DB::table('items')
            ->where('is_deleted', false)
            ->where('category', 'SF3')
            ->orderBy('code')
            ->orderBy('name')
            ->get();

        $selectedItem = null;
        if ($existingReport && $existingReport->item_id) {
            $selectedItem = $sf3Items->firstWhere('id', (int) $existingReport->item_id);
        }

        return view('backend.production-reports.sf003.production-report', compact(
            'existingReport',
            'requestedLine',
            'lineCode',
            'lineLabel',
            'lineTitle',
            'sf3Items',
            'selectedItem'
        ));
    }

    /**
     * Store SF3 production report data.
     */
    public function storeProductionReport(Request $request, ?int $transferId = null): RedirectResponse|JsonResponse
    {
        if (!Schema::hasTable('sf3_production_reports')) {
            $message = 'SF3 production reports table is missing. Please run migrations.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 500);
            }

            return back()->with('error', $message);
        }

        $lineContext = $this->resolveLineContext((string) $request->query('line', 'l1'));
        $requestedLine = $lineContext['requestedLine'];
        $lineCode = $lineContext['lineCode'];

        $encryptedReportIdInput = (string) $request->input('report_id', '');
        $reportId = 0;
        if ($encryptedReportIdInput !== '') {
            try {
                $reportId = (int) Crypt::decryptString($encryptedReportIdInput);
            } catch (\Exception $e) {
                $reportId = 0;
            }
        }

        $validated = $request->validate([
            'item_id' => 'required|integer|min:1',
            'sf3_report_date' => 'required|date',
            'sf3_shift' => 'required|in:morning,night',
            'sf3_set_per_hour' => 'required|numeric|min:0',
            'sf3_total_set_shift' => 'required|numeric|min:0',
            'sf3_actual_set_shift' => 'required|numeric|min:0',
            'sf3_manpower' => 'required|numeric|min:0',
            'sf3_staff_count' => 'required|integer|min:0',
            'sf3_hour_8_9' => 'required|numeric|min:0',
            'sf3_hour_9_10' => 'required|numeric|min:0',
            'sf3_hour_10_11' => 'required|numeric|min:0',
            'sf3_hour_11_12' => 'required|numeric|min:0',
            'sf3_hour_12_1' => 'required|numeric|min:0',
            'sf3_hour_1_2' => 'required|numeric|min:0',
            'sf3_hour_2_3' => 'required|numeric|min:0',
            'sf3_hour_3_4' => 'required|numeric|min:0',
            'sf3_hour_4_5' => 'required|numeric|min:0',
            'sf3_hour_5_6' => 'required|numeric|min:0',
            'sf3_hour_6_7' => 'required|numeric|min:0',
            'sf3_hour_7_8' => 'required|numeric|min:0',
        ]);

        $itemId = (int) $validated['item_id'];
        $item = DB::table('items')
            ->select('id')
            ->where('id', $itemId)
            ->where('category', 'SF3')
            ->where('is_deleted', false)
            ->first();

        if (!$item) {
            $message = 'Selected SF3 item was not found.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $hourlyTotal =
            (float) ($request->input('sf3_hour_8_9') ?? 0) +
            (float) ($request->input('sf3_hour_9_10') ?? 0) +
            (float) ($request->input('sf3_hour_10_11') ?? 0) +
            (float) ($request->input('sf3_hour_11_12') ?? 0) +
            (float) ($request->input('sf3_hour_12_1') ?? 0) +
            (float) ($request->input('sf3_hour_1_2') ?? 0) +
            (float) ($request->input('sf3_hour_2_3') ?? 0) +
            (float) ($request->input('sf3_hour_3_4') ?? 0) +
            (float) ($request->input('sf3_hour_4_5') ?? 0) +
            (float) ($request->input('sf3_hour_5_6') ?? 0) +
            (float) ($request->input('sf3_hour_6_7') ?? 0) +
            (float) ($request->input('sf3_hour_7_8') ?? 0);

        $actualSetShift = round($hourlyTotal);
        $totalSetShift = (float) ($request->input('sf3_total_set_shift') ?? 0);

        if ($actualSetShift > $totalSetShift) {
            $message = 'Actual / Set / Shift must not be greater than Total Set / Shift (' . number_format($totalSetShift, 0, '.', '') . ').';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $payload = [
            'created_by' => Auth::id(),
            'report_date' => (string) $request->input('sf3_report_date'),
            'shift' => (string) $request->input('sf3_shift'),
            'sf3_process' => $lineCode,
            'transfered_id' => null,
            'item_id' => $itemId,
            'set_per_hour' => (float) ($request->input('sf3_set_per_hour') ?? 0),
            'total_set_shift' => $totalSetShift,
            'hour_8_9' => (float) ($request->input('sf3_hour_8_9') ?? 0),
            'hour_9_10' => (float) ($request->input('sf3_hour_9_10') ?? 0),
            'hour_10_11' => (float) ($request->input('sf3_hour_10_11') ?? 0),
            'hour_11_12' => (float) ($request->input('sf3_hour_11_12') ?? 0),
            'hour_12_1' => (float) ($request->input('sf3_hour_12_1') ?? 0),
            'hour_1_2' => (float) ($request->input('sf3_hour_1_2') ?? 0),
            'hour_2_3' => (float) ($request->input('sf3_hour_2_3') ?? 0),
            'hour_3_4' => (float) ($request->input('sf3_hour_3_4') ?? 0),
            'hour_4_5' => (float) ($request->input('sf3_hour_4_5') ?? 0),
            'hour_5_6' => (float) ($request->input('sf3_hour_5_6') ?? 0),
            'hour_6_7' => (float) ($request->input('sf3_hour_6_7') ?? 0),
            'hour_7_8' => (float) ($request->input('sf3_hour_7_8') ?? 0),
            'actual_set_shift' => $actualSetShift,
            'manpower_workman' => (float) ($request->input('sf3_manpower') ?? 0),
            'staff_count' => (int) ($request->input('sf3_staff_count') ?? 0),
            'status' => 1,
            'is_deleted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $savedReportId = $reportId;

        if ($reportId > 0) {
            $editableQuery = DB::table('sf3_production_reports')
                ->where('id', $reportId)
                ->where('is_deleted', 0)
                ->where('sf3_process', $lineCode);

            if (Auth::user()?->role !== 'Admin') {
                $editableQuery->where('created_by', Auth::id());
            }

            if (!$editableQuery->exists()) {
                $message = 'Production report not found or not editable.';
                if ($request->expectsJson()) {
                    return response()->json(['message' => $message], 404);
                }

                return back()->with('error', $message);
            }

            $editableQuery->update($payload);
        } else {
            $savedReportId = (int) DB::table('sf3_production_reports')->insertGetId($payload);
        }

        if ($savedReportId > 0 && Schema::hasTable('sf3_production_report_products')) {
            $this->syncProductionReportProducts($savedReportId, $itemId, $lineCode, $actualSetShift);
        }

        $successMessage = $reportId > 0 ? 'Production report updated successfully.' : 'Production report saved successfully.';
        $redirectUrl = route('admin.production-reports.sf003.process', ['line' => $requestedLine, 'tab' => 'production']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $successMessage,
                'redirect_url' => $redirectUrl,
            ]);
        }

        return redirect()->to($redirectUrl)->with('success', $successMessage);
    }

    /**
     * Update transfer status for SF003 stock records.
     */
    public function updateStatus(Request $request, int $transferId): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|integer|in:1,2',
            'sf003_remark' => 'nullable|string|max:500',
            'accept_all_quantity' => 'nullable|boolean',
            'reject_quantity' => 'nullable|numeric|min:0',
            'reject_reason_id' => 'nullable|integer|exists:reject_reasons,id',
        ]);

        $query = DB::table('sf002_stock_transfers')
            ->where('id', $transferId)
            ->where('is_deleted', false);

        if (Auth::user()?->role !== 'Admin') {
            $role = $this->currentAssignableRole();

            if (!$role) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where('assign_role', $role)
                    ->where(function ($scoped) {
                        $scoped->whereNull('assign_to')
                            ->orWhere('assign_to', Auth::id());
                    });
            }
        }

        $transfer = $query->first();

        if (!$transfer) {
            return back()->with('error', 'Transfer record not found or not assigned to you.');
        }

        if ((int) $transfer->is_accept !== 0) {
            return back()->with('error', 'Status already updated. You cannot change the status or remark again.');
        }

        $currentQuantity = (float) $transfer->quantity;
        $acceptAllQuantity = (bool) ($validated['accept_all_quantity'] ?? false);
        $rejectQuantity = $acceptAllQuantity ? 0.0 : (float) ($validated['reject_quantity'] ?? 0);

        $rejectReasonId = null;
        if (!$acceptAllQuantity && $rejectQuantity > 0) {
            $rejectReasonId = (int) ($validated['reject_reason_id'] ?? 0);
            if ($rejectReasonId <= 0) {
                return back()->with('error', 'Please select a reject reason.');
            }

            $rejectReasonExists = DB::table('reject_reasons')
                ->where('id', $rejectReasonId)
                ->where('is_deleted', 0)
                ->where('status', 1)
                ->whereIn('category', ['SF2', 'Both'])
                ->exists();

            if (!$rejectReasonExists) {
                return back()->with('error', 'Selected reject reason is not available.');
            }
        }

        if ($rejectQuantity > $currentQuantity) {
            return back()->with('error', 'Reject quantity cannot be greater than transfer quantity.');
        }

        if ((int) $validated['status'] === 1 && $rejectQuantity >= $currentQuantity) {
            return back()->with('error', 'Accepted quantity must be greater than zero.');
        }

        DB::table('sf002_stock_transfers')
            ->where('id', $transferId)
            ->update([
                'is_accept' => $validated['status'],
                'assign_to' => Auth::user()?->role === 'Admin' ? $transfer->assign_to : Auth::id(),
                'sf003_remark' => $validated['sf003_remark'] ?? null,
                'reject_quantity' => $rejectQuantity,
                'reject_reason_id' => $rejectReasonId,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Transfer status updated successfully.');
    }

    /**
     * Fetch SF3 products for a given item.
     */
    public function getItemProducts(Request $request): JsonResponse
    {
        $itemId = (int) ($request->query('item_id') ?? 0);

        if ($itemId <= 0) {
            return response()->json(['products' => []]);
        }

        $products = DB::table('item_sf3_products as sf3p')
            ->join('items', 'sf3p.product', '=', 'items.id')
            ->select(
                'sf3p.id',
                'sf3p.item_id',
                'sf3p.product',
                'sf3p.quantity',
                DB::raw("CASE WHEN items.category = 'SF1-SF2' AND items.code_sf2 IS NOT NULL AND items.code_sf2 != '' THEN items.code_sf2 ELSE items.code END as product_code"),
                DB::raw("CASE WHEN items.category = 'SF1-SF2' AND items.name_sf2 IS NOT NULL AND items.name_sf2 != '' THEN items.name_sf2 ELSE items.name END as product_name"),
                'items.category as product_category',
                DB::raw('COALESCE(items.quantity, 0) as product_store_quantity')
            )
            ->where('sf3p.item_id', $itemId)
            ->orderBy('sf3p.product')
            ->get();

        return response()->json(['products' => $products]);
    }

    /**
     * Fetch in-stock transfer data from SF002 transfers for a given SF3 line.
     */
    public function getItemProductsStock(Request $request): JsonResponse
    {
        $itemId = (int) ($request->query('item_id') ?? 0);
        $lineCode = (string) $request->query('line_code', '');

        if ($itemId <= 0) {
            return response()->json(['products' => []]);
        }

        // Parent SF3 item -> child product ids from item_sf3_products
        $productIds = DB::table('item_sf3_products')
            ->where('item_id', $itemId)
            ->pluck('product')
            ->map(function ($id) {
                return (int) $id;
            })
            ->filter(function ($id) {
                return $id > 0;
            })
            ->unique()
            ->values();

        if ($productIds->isEmpty()) {
            return response()->json(['products' => []]);
        }

        $stockRows = DB::table('sf002_stock_transfers as transfers')
            ->join('items', 'transfers.item_id', '=', 'items.id')
            ->select(
                'transfers.id',
                'transfers.item_id',
                'transfers.date',
                'transfers.time',
                DB::raw("CASE WHEN items.category = 'SF1-SF2' AND items.code_sf2 IS NOT NULL AND items.code_sf2 != '' THEN items.code_sf2 ELSE items.code END as item_code"),
                DB::raw("CASE WHEN items.category = 'SF1-SF2' AND items.name_sf2 IS NOT NULL AND items.name_sf2 != '' THEN items.name_sf2 ELSE items.name END as item_name"),
                'items.category as item_category',
                DB::raw('GREATEST(COALESCE(transfers.quantity, 0) - COALESCE(transfers.used_quantity, 0), 0) as quantity')
            )
            ->where('transfers.is_deleted', false)
            ->where('transfers.is_accept', 1)
            ->whereIn('transfers.item_id', $productIds->all())
            ->when($lineCode !== '', function ($query) use ($lineCode) {
                $query->where('transfers.sf3_process', $lineCode);
            })
            ->orderByDesc('transfers.date')
            ->orderByDesc('transfers.time')
            ->orderByDesc('transfers.created_at')
            ->get();

        return response()->json(['products' => $stockRows]);
    }
}

