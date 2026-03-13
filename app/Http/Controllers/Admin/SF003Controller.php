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
                'transfers.reject_quantity',
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
                'items.code as item_code',
                'items.name as item_name',
                'items.size as item_size',
                'transfer_by_user.name as transfer_by_name',
                'assigned_to_user.name as assigned_to_name'
            )
            ->join('items', 'transfers.item_id', '=', 'items.id')
            ->leftJoin('users as transfer_by_user', 'transfers.transfer_by', '=', 'transfer_by_user.id')
            ->leftJoin('users as assigned_to_user', 'transfers.assign_to', '=', 'assigned_to_user.id')
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

        return view('backend.production-reports.sf003.stock', compact('assignedTransfers'));
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
     * Display production report form for a specific accepted transfer in SF3 line process.
     */
    public function productionReport(Request $request, int $transferId): View
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
            if ($existingReport) {
                $transferId = (int) $existingReport->transfered_id;
            }
        }

        $query = DB::table('sf002_stock_transfers as transfers')
            ->select(
                'transfers.id',
                'transfers.item_id',
                DB::raw('GREATEST(transfers.quantity - COALESCE(transfers.reject_quantity, 0), 0) as quantity'),
                'transfers.date',
                'transfers.time',
                'transfers.is_accept',
                'transfers.sf3_process',
                'items.code as item_code',
                'items.name as item_name',
                'items.size as item_size'
            )
            ->join('items', 'transfers.item_id', '=', 'items.id')
            ->where('transfers.is_deleted', false)
            ->where('transfers.is_accept', 1)
            ->where('transfers.sf3_process', $lineCode)
            ->orderByDesc('transfers.date')
            ->orderByDesc('transfers.time')
            ->orderByDesc('transfers.created_at');

        if (Auth::user()?->role !== 'Admin') {
            $role = $this->currentAssignableRole();

            if (!$role) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where('transfers.assign_role', $role)
                    ->where('transfers.assign_to', Auth::id());
            }
        }

        $availableTransfers = $query->get();

        $usedByTransfer = DB::table('sf3_production_reports')
            ->select('transfered_id', DB::raw('COALESCE(SUM(actual_set_shift), 0) as used_quantity'))
            ->where('sf3_process', $lineCode)
            ->where('is_deleted', 0)
            ->groupBy('transfered_id')
            ->pluck('used_quantity', 'transfered_id');

        $currentReportActualSet = (float) ($existingReport->actual_set_shift ?? 0);

        $availableTransfers = $availableTransfers
            ->map(function ($row) use ($usedByTransfer, $existingReport, $currentReportActualSet) {
                $baseQuantity = max((float) ($row->quantity ?? 0), 0);
                $usedQuantity = (float) ($usedByTransfer[$row->id] ?? 0);

                if ($existingReport && (int) $existingReport->transfered_id === (int) $row->id) {
                    $usedQuantity = max($usedQuantity - $currentReportActualSet, 0);
                }

                $row->total_quantity = $baseQuantity;
                $row->used_quantity = $usedQuantity;
                $row->pending_quantity = max($baseQuantity - $usedQuantity, 0);

                return $row;
            })
            ->filter(function ($row) use ($transferId) {
                return (float) ($row->pending_quantity ?? 0) > 0 || (int) $row->id === (int) $transferId;
            })
            ->values();

        $transfer = $availableTransfers->firstWhere('id', $transferId) ?? $availableTransfers->first();

        if (!$transfer) {
            abort(404, 'No accepted transfer found for selected SF3 process line.');
        }

        return view('backend.production-reports.sf003.production-report', compact(
            'transfer',
            'availableTransfers',
            'existingReport',
            'requestedLine',
            'lineCode',
            'lineLabel',
            'lineTitle'
        ));
    }

    /**
     * Store SF3 production report data.
     */
    public function storeProductionReport(Request $request, int $transferId): RedirectResponse|JsonResponse
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
            'selected_transfer_id' => 'required|integer|min:1',
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

        $selectedTransferId = (int) ($validated['selected_transfer_id'] ?? $transferId);

        $query = DB::table('sf002_stock_transfers')
            ->where('id', $selectedTransferId)
            ->where('is_deleted', false)
            ->where('is_accept', 1)
            ->where('sf3_process', $lineCode);

        if (Auth::user()?->role !== 'Admin') {
            $role = $this->currentAssignableRole();

            if (!$role) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where('assign_role', $role)
                    ->where('assign_to', Auth::id());
            }
        }

        $transfer = $query->first();
        if (!$transfer) {
            return back()->with('error', 'Transfer record not found or not assigned to you.');
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
        $baseAvailableQuantity = max((float) $transfer->quantity - (float) ($transfer->reject_quantity ?? 0), 0);

        $alreadyUsedQuantityQuery = DB::table('sf3_production_reports')
            ->where('sf3_process', $lineCode)
            ->where('transfered_id', $selectedTransferId)
            ->where('is_deleted', 0);

        if ($reportId > 0) {
            $alreadyUsedQuantityQuery->where('id', '!=', $reportId);
        }

        $alreadyUsedQuantity = (float) ($alreadyUsedQuantityQuery->sum('actual_set_shift') ?? 0);
        $availableQuantity = max($baseAvailableQuantity - $alreadyUsedQuantity, 0);
        $totalSetShift = (float) ($request->input('sf3_total_set_shift') ?? 0);

        if ($totalSetShift > $availableQuantity) {
            $message = 'Total Set/Shift should be less than pending quantity (' . number_format($availableQuantity, 0, '.', '') . ').';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        if ($actualSetShift > $availableQuantity) {
            $message = 'Actual / Set / Shift should be less than pending quantity (' . number_format($availableQuantity, 0, '.', '') . ').';
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
            'transfered_id' => $selectedTransferId,
            'item_id' => $transfer->item_id,
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
            DB::table('sf3_production_reports')->insert($payload);
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
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Transfer status updated successfully.');
    }
}
