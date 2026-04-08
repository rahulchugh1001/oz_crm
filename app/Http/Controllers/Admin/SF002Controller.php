<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SF002Controller extends Controller
{
    protected function currentAssignableRole(): ?string
    {
        $role = Auth::user()?->role;

        return in_array($role, ['SF002', 'SF003'], true) ? $role : null;
    }

    /**
     * Build base query for SF002 assigned transfers.
     */
    protected function assignedTransfersQuery()
    {
        $query = DB::table('sf001_stock_transfers as transfers')
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
                'transfers.assign_sf2',
                'transfers.assign_role',
                'transfers.assign_to',
                'transfers.transfer_by',
                'transfers.remark',
                'transfers.sf002_remark',
                'transfers.reject_reason_id',
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
     * Display stock transfers assigned to the logged-in SF002 user.
     */
    public function index(): View
    {
        $assignedTransfers = $this->assignedTransfersQuery()
            ->addSelect(DB::raw('CASE WHEN sf2_usage.id IS NOT NULL THEN 1 ELSE 0 END as is_used_in_sf2'))
            ->leftJoin('sf2_production_reports as sf2_usage', function ($join) {
                $join->on('transfers.id', '=', 'sf2_usage.transfered_id')
                    ->where('sf2_usage.is_deleted', false);
            })
            ->get();

        $rejectReasons = DB::table('reject_reasons')
            ->select('id', 'name')
            ->where('is_deleted', 0)
            ->where('status', 1)
            ->whereIn('category', ['SF1', 'Both'])
            ->orderBy('name')
            ->get();

        return view('backend.production-reports.sf002.stock', compact('assignedTransfers', 'rejectReasons'));
    }

    /**
     * Display accepted transfers assigned to the logged-in SF002 user.
     */
    public function process(Request $request): View
    {
        $sf2Type = strtoupper((string) $request->query('type', 'CED'));
        if (!in_array($sf2Type, ['CED', 'ZINC'], true)) {
            $sf2Type = 'CED';
        }

        if (Auth::user()?->role === 'Admin') {
            $acceptedTransfers = $this->assignedTransfersQuery()
                ->addSelect('transfers.assign_to', 'accepted_by_user.name as accepted_by_name')
                ->addSelect(DB::raw('GREATEST(transfers.quantity - COALESCE(transfers.reject_quantity, 0), 0) as accepted_quantity'))
                ->leftJoin('users as accepted_by_user', 'transfers.assign_to', '=', 'accepted_by_user.id')
                ->where('transfers.is_accept', 1)
                ->where('transfers.assign_sf2', $sf2Type)
                ->get();
        } else {
            $role = $this->currentAssignableRole();

            $acceptedTransfers = DB::table('sf001_stock_transfers as transfers')
                ->select(
                    'transfers.id',
                    'transfers.item_id',
                    'transfers.quantity',
                    'transfers.date',
                    'transfers.time',
                    'transfers.is_accept',
                    'transfers.assign_sf2',
                    'transfers.reject_quantity',
                    DB::raw('GREATEST(transfers.quantity - COALESCE(transfers.reject_quantity, 0), 0) as accepted_quantity'),
                    'transfers.remark',
                    'transfers.sf002_remark',
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
                ->where('transfers.assign_sf2', $sf2Type)
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

        $sf2ProductionReportsQuery = DB::table('sf2_production_reports as reports')
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
            ->where('reports.type', strtolower($sf2Type))
            ->orderByDesc('reports.report_date')
            ->orderByDesc('reports.created_at');

        if (Auth::user()?->role !== 'Admin') {
            $sf2ProductionReportsQuery->where('reports.created_by', Auth::id());
        }

        $sf2ProductionReports = $sf2ProductionReportsQuery->get();

        return view('backend.production-reports.sf002.process', compact('acceptedTransfers', 'sf2ProductionReports'));
    }

    /**
     * Display production report form for a specific accepted transfer.
     */
    public function productionReport(int $transferId): View
    {
        $sf2Type = strtoupper((string) request()->query('type', 'CED'));
        if (!in_array($sf2Type, ['CED', 'ZINC'], true)) {
            $sf2Type = 'CED';
        }

        $encryptedReportId = (string) request()->query('report_id', '');
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
            $existingQuery = DB::table('sf2_production_reports')
                ->where('id', $reportId)
                ->where('type', strtolower($sf2Type))
                ->where('is_deleted', 0);

            if (Auth::user()?->role !== 'Admin') {
                $existingQuery->where('created_by', Auth::id());
            }

            $existingReport = $existingQuery->first();
            if ($existingReport) {
                $transferId = (int) $existingReport->transfered_id;
            }
        }

        $query = DB::table('sf001_stock_transfers as transfers')
            ->select(
                'transfers.id',
                'transfers.item_id',
                DB::raw('GREATEST(transfers.quantity - COALESCE(transfers.reject_quantity, 0), 0) as quantity'),
                'transfers.date',
                'transfers.time',
                'transfers.is_accept',
                'transfers.assign_sf2',
                'items.code as item_code',
                'items.name as item_name',
                'items.size as item_size'
            )
            ->join('items', 'transfers.item_id', '=', 'items.id')
            ->where('transfers.is_deleted', false)
            ->where('transfers.is_accept', 1)
            ->where('transfers.assign_sf2', $sf2Type)
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

        $usedByTransfer = DB::table('sf2_production_reports')
            ->select('transfered_id', DB::raw('COALESCE(SUM(actual_set_shift), 0) as used_quantity'))
            ->where('type', strtolower($sf2Type))
            ->where('is_deleted', 0)
            ->groupBy('transfered_id')
            ->pluck('used_quantity', 'transfered_id');

        $currentReportActualSet = (float) ($existingReport->actual_set_shift ?? 0);

        $availableTransfers = $availableTransfers
            ->map(function ($row) use ($usedByTransfer, $existingReport, $currentReportActualSet) {
                $baseQuantity = max((float) ($row->quantity ?? 0), 0);
                $usedQuantity = (float) ($usedByTransfer[$row->id] ?? 0);

                // In edit mode, exclude current report quantity from "already used" for that transfer.
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
            abort(404, 'No accepted transfer found for selected SF2 type.');
        }

        return view('backend.production-reports.sf002.production-report', compact('transfer', 'availableTransfers', 'existingReport'));
    }

    /**
     * Store production report data.
     */
    public function storeProductionReport(Request $request, int $transferId): RedirectResponse|JsonResponse
    {
        $sf2Type = strtolower((string) $request->query('type', 'ced'));
        if (!in_array($sf2Type, ['ced', 'zinc'], true)) {
            $sf2Type = 'ced';
        }

        $fieldPrefix = $sf2Type;

        // Handle bulk mode
        if ($request->boolean('bulk_mode')) {
            return $this->storeBulkProductionReport($request, $sf2Type);
        }

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
            $fieldPrefix . '_report_date' => 'required|date',
            $fieldPrefix . '_shift' => 'required|in:morning,night',
            $fieldPrefix . '_set_per_hour' => 'required|numeric|min:0',
            $fieldPrefix . '_total_set_shift' => 'required|numeric|min:0',
            $fieldPrefix . '_actual_set_shift' => 'required|numeric|min:0',
            $fieldPrefix . '_manpower' => 'required|numeric|min:0',
            $fieldPrefix . '_staff_count' => 'required|integer|min:0',
            $fieldPrefix . '_hour_8_9' => 'required|numeric|min:0',
            $fieldPrefix . '_hour_9_10' => 'required|numeric|min:0',
            $fieldPrefix . '_hour_10_11' => 'required|numeric|min:0',
            $fieldPrefix . '_hour_11_12' => 'required|numeric|min:0',
            $fieldPrefix . '_hour_12_1' => 'required|numeric|min:0',
            $fieldPrefix . '_hour_1_2' => 'required|numeric|min:0',
            $fieldPrefix . '_hour_2_3' => 'required|numeric|min:0',
            $fieldPrefix . '_hour_3_4' => 'required|numeric|min:0',
            $fieldPrefix . '_hour_4_5' => 'required|numeric|min:0',
            $fieldPrefix . '_hour_5_6' => 'required|numeric|min:0',
            $fieldPrefix . '_hour_6_7' => 'required|numeric|min:0',
            $fieldPrefix . '_hour_7_8' => 'required|numeric|min:0',
        ]);

        $selectedTransferId = (int) ($request->input('selected_transfer_id') ?: $transferId);
        $sf2TypeDbValue = strtoupper($sf2Type);

        // Validate basic transfer existence
        $query = DB::table('sf001_stock_transfers')
            ->where('id', $selectedTransferId)
            ->where('is_deleted', false)
            ->where('is_accept', 1)
            ->where('assign_sf2', $sf2TypeDbValue);

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

        $requestShift = strtolower((string) $request->input($fieldPrefix . '_shift', ''));
        $reportShift = in_array($requestShift, ['morning', 'night'], true) ? $requestShift : null;

        $requestReportDate = (string) $request->input($fieldPrefix . '_report_date', '');
        $reportDate = $requestReportDate !== '' ? $requestReportDate : ($transfer->date ?? now()->toDateString());

        $hourlyTotal =
            (float) ($request->input($fieldPrefix . '_hour_8_9') ?? 0) +
            (float) ($request->input($fieldPrefix . '_hour_9_10') ?? 0) +
            (float) ($request->input($fieldPrefix . '_hour_10_11') ?? 0) +
            (float) ($request->input($fieldPrefix . '_hour_11_12') ?? 0) +
            (float) ($request->input($fieldPrefix . '_hour_12_1') ?? 0) +
            (float) ($request->input($fieldPrefix . '_hour_1_2') ?? 0) +
            (float) ($request->input($fieldPrefix . '_hour_2_3') ?? 0) +
            (float) ($request->input($fieldPrefix . '_hour_3_4') ?? 0) +
            (float) ($request->input($fieldPrefix . '_hour_4_5') ?? 0) +
            (float) ($request->input($fieldPrefix . '_hour_5_6') ?? 0) +
            (float) ($request->input($fieldPrefix . '_hour_6_7') ?? 0) +
            (float) ($request->input($fieldPrefix . '_hour_7_8') ?? 0);

        $actualSetShift = round($hourlyTotal);

        $baseAvailableQuantity = max((float) $transfer->quantity - (float) ($transfer->reject_quantity ?? 0), 0);
        $alreadyUsedQuantityQuery = DB::table('sf2_production_reports')
            ->where('type', $sf2Type)
            ->where('transfered_id', $selectedTransferId)
            ->where('is_deleted', 0);

        if ($reportId > 0) {
            $alreadyUsedQuantityQuery->where('id', '!=', $reportId);
        }

        $alreadyUsedQuantity = (float) ($alreadyUsedQuantityQuery->sum('actual_set_shift') ?? 0);
        $availableQuantity = max($baseAvailableQuantity - $alreadyUsedQuantity, 0);
        $totalSetShift = (float) ($request->input($fieldPrefix . '_total_set_shift') ?? 0);

        if ($totalSetShift > $availableQuantity) {
            $message = 'Total Set/Shift cannot be greater than pending quantity (' . number_format($availableQuantity, 0, '.', '') . ').';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        if ($actualSetShift > $availableQuantity) {
            $message = 'Actual Set/Shift cannot be greater than pending quantity (' . number_format($availableQuantity, 0, '.', '') . ').';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $payload = [
            'type' => $sf2Type,
            'created_by' => Auth::id(),
            'report_date' => $reportDate,
            'shift' => $reportShift,
            'transfered_id' => $selectedTransferId,
            'item_id' => $transfer->item_id,
            'set_per_hour' => (float) ($request->input($fieldPrefix . '_set_per_hour') ?? 0),
            'total_set_shift' => $totalSetShift,
            'hour_8_9' => (float) ($request->input($fieldPrefix . '_hour_8_9') ?? 0),
            'hour_9_10' => (float) ($request->input($fieldPrefix . '_hour_9_10') ?? 0),
            'hour_10_11' => (float) ($request->input($fieldPrefix . '_hour_10_11') ?? 0),
            'hour_11_12' => (float) ($request->input($fieldPrefix . '_hour_11_12') ?? 0),
            'hour_12_1' => (float) ($request->input($fieldPrefix . '_hour_12_1') ?? 0),
            'hour_1_2' => (float) ($request->input($fieldPrefix . '_hour_1_2') ?? 0),
            'hour_2_3' => (float) ($request->input($fieldPrefix . '_hour_2_3') ?? 0),
            'hour_3_4' => (float) ($request->input($fieldPrefix . '_hour_3_4') ?? 0),
            'hour_4_5' => (float) ($request->input($fieldPrefix . '_hour_4_5') ?? 0),
            'hour_5_6' => (float) ($request->input($fieldPrefix . '_hour_5_6') ?? 0),
            'hour_6_7' => (float) ($request->input($fieldPrefix . '_hour_6_7') ?? 0),
            'hour_7_8' => (float) ($request->input($fieldPrefix . '_hour_7_8') ?? 0),
            'actual_set_shift' => $actualSetShift,
            'manpower_workman' => (float) ($request->input($fieldPrefix . '_manpower') ?? 0),
            'staff_count' => (int) ($request->input($fieldPrefix . '_staff_count') ?? 0),
            'status' => 1,
            'is_deleted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($reportId > 0) {
            $editableQuery = DB::table('sf2_production_reports')
                ->where('id', $reportId)
                ->where('is_deleted', 0)
                ->where('type', $sf2Type);

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
            DB::table('sf2_production_reports')->insert($payload);
        }

        $successMessage = $reportId > 0 ? 'Production report updated successfully.' : 'Production report saved successfully.';
        $redirectUrl = route('admin.production-reports.sf002.process', ['type' => $sf2Type, 'tab' => 'production']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $successMessage,
                'redirect_url' => $redirectUrl,
            ]);
        }

        return redirect()->to($redirectUrl)->with('success', $successMessage);
    }

    /**
     * Handle bulk production report creation (multiple items at once).
     */
    private function storeBulkProductionReport(Request $request, string $sf2Type): JsonResponse
    {
        $fieldPrefix = $sf2Type;

        $request->validate([
            $fieldPrefix . '_report_date' => 'required|date',
            $fieldPrefix . '_shift'       => 'required|in:morning,night',
            'items'                       => 'required|array|min:1',
            'items.*.transfer_id'         => 'required|integer|min:1',
            'items.*.total_set_shift'     => 'required|numeric|min:0',
            'items.*.set_per_hour'        => 'required|numeric|min:0',
            'items.*.hour_8_9'            => 'required|numeric|min:0',
            'items.*.hour_9_10'           => 'required|numeric|min:0',
            'items.*.hour_10_11'          => 'required|numeric|min:0',
            'items.*.hour_11_12'          => 'required|numeric|min:0',
            'items.*.hour_12_1'           => 'required|numeric|min:0',
            'items.*.hour_1_2'            => 'required|numeric|min:0',
            'items.*.hour_2_3'            => 'required|numeric|min:0',
            'items.*.hour_3_4'            => 'required|numeric|min:0',
            'items.*.hour_4_5'            => 'required|numeric|min:0',
            'items.*.hour_5_6'            => 'required|numeric|min:0',
            'items.*.hour_6_7'            => 'required|numeric|min:0',
            'items.*.hour_7_8'            => 'required|numeric|min:0',
            'items.*.manpower'            => 'required|numeric|min:0',
            'items.*.staff_count'         => 'required|integer|min:0',
        ]);

        $sf2TypeDbValue = strtoupper($sf2Type);
        $requestShift = strtolower((string) $request->input($fieldPrefix . '_shift', ''));
        $reportShift  = in_array($requestShift, ['morning', 'night'], true) ? $requestShift : null;
        $reportDate   = (string) $request->input($fieldPrefix . '_report_date', now()->toDateString());

        $items    = $request->input('items', []);
        $errors   = [];
        $payloads = [];

        foreach ($items as $index => $item) {
            $transferId = (int) ($item['transfer_id'] ?? 0);

            $query = DB::table('sf001_stock_transfers')
                ->where('id', $transferId)
                ->where('is_deleted', false)
                ->where('is_accept', 1)
                ->where('assign_sf2', $sf2TypeDbValue);

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
                $errors[] = "Item #" . ($index + 1) . ": Transfer not found or not assigned to you.";
                continue;
            }

            $hourlyTotal =
                (float) ($item['hour_8_9'] ?? 0) +
                (float) ($item['hour_9_10'] ?? 0) +
                (float) ($item['hour_10_11'] ?? 0) +
                (float) ($item['hour_11_12'] ?? 0) +
                (float) ($item['hour_12_1'] ?? 0) +
                (float) ($item['hour_1_2'] ?? 0) +
                (float) ($item['hour_2_3'] ?? 0) +
                (float) ($item['hour_3_4'] ?? 0) +
                (float) ($item['hour_4_5'] ?? 0) +
                (float) ($item['hour_5_6'] ?? 0) +
                (float) ($item['hour_6_7'] ?? 0) +
                (float) ($item['hour_7_8'] ?? 0);

            $actualSetShift = round($hourlyTotal);

            $baseAvailableQuantity = max((float) $transfer->quantity - (float) ($transfer->reject_quantity ?? 0), 0);
            $alreadyUsedQuantity = (float) (DB::table('sf2_production_reports')
                ->where('type', $sf2Type)
                ->where('transfered_id', $transferId)
                ->where('is_deleted', 0)
                ->sum('actual_set_shift') ?? 0);
            $availableQuantity = max($baseAvailableQuantity - $alreadyUsedQuantity, 0);
            $totalSetShift = (float) ($item['total_set_shift'] ?? 0);

            if ($totalSetShift > $availableQuantity) {
                $errors[] = "Item #" . ($index + 1) . " (" . ($transfer->item_code ?? '') . "): Total Set/Shift exceeds pending quantity (" . number_format($availableQuantity, 0, '.', '') . ").";
                continue;
            }

            if ($actualSetShift > $availableQuantity) {
                $errors[] = "Item #" . ($index + 1) . " (" . ($transfer->item_code ?? '') . "): Actual Set/Shift exceeds pending quantity (" . number_format($availableQuantity, 0, '.', '') . ").";
                continue;
            }

            $payloads[] = [
                'type'             => $sf2Type,
                'created_by'       => Auth::id(),
                'report_date'      => $reportDate,
                'shift'            => $reportShift,
                'transfered_id'    => $transferId,
                'item_id'          => $transfer->item_id,
                'set_per_hour'     => (float) ($item['set_per_hour'] ?? 0),
                'total_set_shift'  => $totalSetShift,
                'hour_8_9'         => (float) ($item['hour_8_9'] ?? 0),
                'hour_9_10'        => (float) ($item['hour_9_10'] ?? 0),
                'hour_10_11'       => (float) ($item['hour_10_11'] ?? 0),
                'hour_11_12'       => (float) ($item['hour_11_12'] ?? 0),
                'hour_12_1'        => (float) ($item['hour_12_1'] ?? 0),
                'hour_1_2'         => (float) ($item['hour_1_2'] ?? 0),
                'hour_2_3'         => (float) ($item['hour_2_3'] ?? 0),
                'hour_3_4'         => (float) ($item['hour_3_4'] ?? 0),
                'hour_4_5'         => (float) ($item['hour_4_5'] ?? 0),
                'hour_5_6'         => (float) ($item['hour_5_6'] ?? 0),
                'hour_6_7'         => (float) ($item['hour_6_7'] ?? 0),
                'hour_7_8'         => (float) ($item['hour_7_8'] ?? 0),
                'actual_set_shift' => $actualSetShift,
                'manpower_workman' => (float) ($item['manpower'] ?? 0),
                'staff_count'      => (int) ($item['staff_count'] ?? 0),
                'status'           => 1,
                'is_deleted'       => 0,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        }

        if (!empty($errors)) {
            return response()->json(['message' => implode(' | ', $errors)], 422);
        }

        if (empty($payloads)) {
            return response()->json(['message' => 'No valid items to save.'], 422);
        }

        DB::table('sf2_production_reports')->insert($payloads);

        $redirectUrl = route('admin.production-reports.sf002.process', ['type' => $sf2Type, 'tab' => 'production']);

        return response()->json([
            'message'      => count($payloads) . ' production report(s) saved successfully.',
            'redirect_url' => $redirectUrl,
        ]);
    }

    public function destroyProductionReport(int $id): RedirectResponse
    {
        $query = DB::table('sf2_production_reports')
            ->where('id', $id)
            ->where('is_deleted', 0);

        if (Auth::user()?->role !== 'Admin') {
            $query->where('created_by', Auth::id());
        }

        $record = $query->first();
        if (!$record) {
            return back()->with('error', 'Production report not found or not allowed.');
        }

        if ((int) ($record->is_transfered ?? 0) === 1) {
            return back()->with('error', 'sf2 is transfered');
        }

        DB::table('sf2_production_reports')
            ->where('id', $id)
            ->update([
                'is_deleted' => 1,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Production report deleted successfully.');
    }

    public function showProductionReport(Request $request, string $encryptedId): View
    {
        try {
            $reportId = (int) Crypt::decryptString($encryptedId);
        } catch (\Exception $e) {
            abort(404, 'Production report not found.');
        }

        $sf2Type = strtolower((string) $request->query('type', 'ced'));
        if (!in_array($sf2Type, ['ced', 'zinc'], true)) {
            $sf2Type = 'ced';
        }

        $query = DB::table('sf2_production_reports as reports')
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
            ->where('reports.type', $sf2Type)
            ->where('reports.is_deleted', 0);

        if (Auth::user()?->role !== 'Admin') {
            $query->where('reports.created_by', Auth::id());
        }

        $report = $query->first();
        if (!$report) {
            abort(404, 'Production report not found or not allowed.');
        }

        return view('backend.production-reports.sf002.show', compact('report', 'sf2Type'));
    }

    /**
     * Display SF2 item-wise stock split by CED and ZINC.
     */
    public function sf2Stock(): View
    {
        $cedTransferStats = DB::table('sf002_stock_transfers')
            ->where('is_deleted', false)
            ->where('type', 'ced')
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
                END), 0) as rejected_quantity"),
                DB::raw("GROUP_CONCAT(DISTINCT sf3_process ORDER BY sf3_process) as sf3_process_lines")
            )
            ->groupBy('item_id');

        $cedStocks = DB::table('sf2_production_reports as reports')
            ->select(
                'items.id',
                'items.code',
                'items.name',
                'items.code_sf2',
                'items.name_sf2',
                'items.size',
                DB::raw('COALESCE(SUM(reports.actual_set_shift), 0) as total_produced_stock'),
                DB::raw('COALESCE(MAX(ced_transfers.transferred_quantity), 0) as transferred_quantity'),
                DB::raw('COALESCE(MAX(ced_transfers.rejected_quantity), 0) as rejected_quantity'),
                DB::raw('GREATEST(COALESCE(SUM(reports.actual_set_shift), 0) - COALESCE(MAX(ced_transfers.transferred_quantity), 0), 0) as pending_quantity'),
                DB::raw('MAX(reports.created_at) as last_stock_update'),
                DB::raw('MAX(ced_transfers.sf3_process_lines) as sf3_process_lines')
            )
            ->join('items', 'reports.item_id', '=', 'items.id')
            ->leftJoinSub($cedTransferStats, 'ced_transfers', function ($join) {
                $join->on('items.id', '=', 'ced_transfers.item_id');
            })
            ->where('reports.is_deleted', 0)
            ->where('reports.type', 'ced')
            ->groupBy('items.id', 'items.code', 'items.name', 'items.code_sf2', 'items.name_sf2', 'items.size')
            ->orderBy('items.name')
            ->get();

        $zincTransferStats = DB::table('sf002_stock_transfers')
            ->where('is_deleted', false)
            ->where('type', 'zinc')
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
                END), 0) as rejected_quantity"),
                DB::raw("GROUP_CONCAT(DISTINCT sf3_process ORDER BY sf3_process) as sf3_process_lines")
            )
            ->groupBy('item_id');

        $zincStocks = DB::table('sf2_production_reports as reports')
            ->select(
                'items.id',
                'items.code',
                'items.name',
                'items.code_sf2',
                'items.name_sf2',
                'items.size',
                DB::raw('COALESCE(SUM(reports.actual_set_shift), 0) as total_produced_stock'),
                DB::raw('COALESCE(MAX(zinc_transfers.transferred_quantity), 0) as transferred_quantity'),
                DB::raw('COALESCE(MAX(zinc_transfers.rejected_quantity), 0) as rejected_quantity'),
                DB::raw('GREATEST(COALESCE(SUM(reports.actual_set_shift), 0) - COALESCE(MAX(zinc_transfers.transferred_quantity), 0), 0) as pending_quantity'),
                DB::raw('MAX(reports.created_at) as last_stock_update'),
                DB::raw('MAX(zinc_transfers.sf3_process_lines) as sf3_process_lines')
            )
            ->join('items', 'reports.item_id', '=', 'items.id')
            ->leftJoinSub($zincTransferStats, 'zinc_transfers', function ($join) {
                $join->on('items.id', '=', 'zinc_transfers.item_id');
            })
            ->where('reports.is_deleted', 0)
            ->where('reports.type', 'zinc')
            ->groupBy('items.id', 'items.code', 'items.name', 'items.code_sf2', 'items.name_sf2', 'items.size')
            ->orderBy('items.name')
            ->get();

        return view('backend.production-reports.sf002.sf2-stock', compact('cedStocks', 'zincStocks'));
    }

    /**
     * Store a new SF2 → SF3 stock transfer.
     */
    public function storeSf2Transfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id'  => 'required|integer|exists:items,id',
            'type'     => 'required|string|in:ced,zinc',
            'sf3_process' => 'required|string|in:line_1,line_2,line_3,line_4,line_5,line_6',
            'quantity' => 'required|numeric|gt:0',
            'date'     => 'required|date',
            'time'     => 'required|date_format:H:i:s',
            'remark'   => 'nullable|string|max:500',
        ]);

        $totalProduced = (float) DB::table('sf2_production_reports')
            ->where('item_id', $validated['item_id'])
            ->where('type', $validated['type'])
            ->where('is_deleted', 0)
            ->sum('actual_set_shift');

        $totalTransferred = (float) DB::table('sf002_stock_transfers')
            ->where('item_id', $validated['item_id'])
            ->where('type', $validated['type'])
            ->where('is_deleted', false)
            ->selectRaw("COALESCE(SUM(CASE
                WHEN is_accept = 2 THEN 0
                WHEN is_accept = 1 THEN GREATEST(quantity - COALESCE(reject_quantity, 0), 0)
                ELSE quantity
            END), 0) as transferred_quantity")
            ->value('transferred_quantity') ?? 0;

        $availableStock = max($totalProduced - $totalTransferred, 0);

        if ((float) $validated['quantity'] > $availableStock) {
            return back()->withErrors([
                'quantity' => 'Transfer quantity cannot be greater than available stock (' . number_format($availableStock, 0) . ').',
            ])->withInput();
        }

        DB::table('sf002_stock_transfers')->insert([
            'item_id'      => $validated['item_id'],
            'type'         => $validated['type'],
            'transfer_by'  => Auth::id(),
            'assign_role'  => 'SF003',
            'sf3_process'  => $validated['sf3_process'],
            'assign_to'    => null,
            'quantity'     => $validated['quantity'],
            'date'         => $validated['date'],
            'time'         => $validated['time'],
            'is_accept'    => 0,
            'remark'       => $validated['remark'] ?? null,
            'is_deleted'   => false,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        DB::table('sf2_production_reports')
            ->where('item_id', $validated['item_id'])
            ->where('type', $validated['type'])
            ->where('is_deleted', 0)
            ->update([
                'is_transfered' => 1,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Stock transferred successfully.');
    }

    /**
     * Display SF2 stock and transfer history for a specific item.
     */
    public function sf2StockHistory(int $itemId): View
    {
        $item = DB::table('items')->where('id', $itemId)->first();

        if (!$item) {
            abort(404, 'Item not found.');
        }

        $cedHistory = DB::table('sf2_production_reports as reports')
            ->select(
                'reports.id',
                'reports.report_date',
                'reports.shift',
                'reports.actual_set_shift',
                'reports.total_set_shift',
                'reports.created_at',
                'users.name as created_by_name'
            )
            ->leftJoin('users', 'reports.created_by', '=', 'users.id')
            ->where('reports.item_id', $itemId)
            ->where('reports.type', 'ced')
            ->where('reports.is_deleted', 0)
            ->orderByDesc('reports.report_date')
            ->orderByDesc('reports.created_at')
            ->get();

        $zincHistory = DB::table('sf2_production_reports as reports')
            ->select(
                'reports.id',
                'reports.report_date',
                'reports.shift',
                'reports.actual_set_shift',
                'reports.total_set_shift',
                'reports.created_at',
                'users.name as created_by_name'
            )
            ->leftJoin('users', 'reports.created_by', '=', 'users.id')
            ->where('reports.item_id', $itemId)
            ->where('reports.type', 'zinc')
            ->where('reports.is_deleted', 0)
            ->orderByDesc('reports.report_date')
            ->orderByDesc('reports.created_at')
            ->get();

        $cedTransferHistory = DB::table('sf002_stock_transfers as transfers')
            ->select(
                'transfers.id',
                'transfers.quantity',
                'transfers.reject_quantity',
                'transfers.reject_reason_id',
                'reject_reasons.name as reject_reason_name',
                'transfers.sf3_process',
                DB::raw("CASE
                    WHEN transfers.is_accept = 2 THEN 0
                    WHEN transfers.is_accept = 1 THEN GREATEST(transfers.quantity - COALESCE(transfers.reject_quantity, 0), 0)
                    ELSE transfers.quantity
                END as accepted_quantity"),
                DB::raw("CASE
                    WHEN transfers.is_accept = 2 THEN transfers.quantity
                    WHEN transfers.is_accept = 1 THEN COALESCE(transfers.reject_quantity, 0)
                    ELSE 0
                END as rejected_quantity"),
                'transfers.date',
                'transfers.time',
                'transfers.is_accept',
                'transfers.remark',
                'transfers.sf003_remark',
                'transfers.created_at',
                'transfer_by_user.name as transfer_by_name',
                'assign_to_user.name as assign_to_name'
            )
            ->leftJoin('users as transfer_by_user', 'transfers.transfer_by', '=', 'transfer_by_user.id')
            ->leftJoin('users as assign_to_user', 'transfers.assign_to', '=', 'assign_to_user.id')
            ->leftJoin('reject_reasons', 'transfers.reject_reason_id', '=', 'reject_reasons.id')
            ->where('transfers.item_id', $itemId)
            ->where('transfers.type', 'ced')
            ->where('transfers.is_deleted', false)
            ->orderByDesc('transfers.date')
            ->orderByDesc('transfers.created_at')
            ->get();

        $zincTransferHistory = DB::table('sf002_stock_transfers as transfers')
            ->select(
                'transfers.id',
                'transfers.quantity',
                'transfers.reject_quantity',
                'transfers.reject_reason_id',
                'reject_reasons.name as reject_reason_name',
                'transfers.sf3_process',
                DB::raw("CASE
                    WHEN transfers.is_accept = 2 THEN 0
                    WHEN transfers.is_accept = 1 THEN GREATEST(transfers.quantity - COALESCE(transfers.reject_quantity, 0), 0)
                    ELSE transfers.quantity
                END as accepted_quantity"),
                DB::raw("CASE
                    WHEN transfers.is_accept = 2 THEN transfers.quantity
                    WHEN transfers.is_accept = 1 THEN COALESCE(transfers.reject_quantity, 0)
                    ELSE 0
                END as rejected_quantity"),
                'transfers.date',
                'transfers.time',
                'transfers.is_accept',
                'transfers.remark',
                'transfers.sf003_remark',
                'transfers.created_at',
                'transfer_by_user.name as transfer_by_name',
                'assign_to_user.name as assign_to_name'
            )
            ->leftJoin('users as transfer_by_user', 'transfers.transfer_by', '=', 'transfer_by_user.id')
            ->leftJoin('users as assign_to_user', 'transfers.assign_to', '=', 'assign_to_user.id')
            ->leftJoin('reject_reasons', 'transfers.reject_reason_id', '=', 'reject_reasons.id')
            ->where('transfers.item_id', $itemId)
            ->where('transfers.type', 'zinc')
            ->where('transfers.is_deleted', false)
            ->orderByDesc('transfers.date')
            ->orderByDesc('transfers.created_at')
            ->get();

        return view('backend.production-reports.sf002.sf2-stock-history', compact(
            'item', 'cedHistory', 'zincHistory', 'cedTransferHistory', 'zincTransferHistory'
        ));
    }

    /**
     * Update the transfer status for the assigned SF002 user.
     */
    public function updateStatus(Request $request, int $transferId): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|integer|in:1,2',
            'sf002_remark' => 'nullable|string|max:500',
            'accept_all_quantity' => 'nullable|boolean',
            'reject_quantity' => 'nullable|numeric|min:0',
            'reject_reason_id' => 'nullable|integer|exists:reject_reasons,id',
        ]);

        $query = DB::table('sf001_stock_transfers')
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

        $isUsedInSf2 = DB::table('sf2_production_reports')
            ->where('transfered_id', $transferId)
            ->where('is_deleted', false)
            ->exists();

        if ($isUsedInSf2) {
            return back()->with('error', 'This stock is already used in SF2 production. You cannot update it.');
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
                ->whereIn('category', ['SF1', 'Both'])
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

        $updateData = [
            'is_accept' => $validated['status'],
            'assign_to' => Auth::user()?->role === 'Admin' ? $transfer->assign_to : Auth::id(),
            'sf002_remark' => $validated['sf002_remark'] ?? null,
            'reject_quantity' => $rejectQuantity,
            'reject_reason_id' => $rejectReasonId,
            'updated_at' => now(),
        ];

        DB::table('sf001_stock_transfers')
            ->where('id', $transferId)
            ->update($updateData);

        return back()->with('success', 'Transfer status updated successfully.');
    }
}
