<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                'transfers.date',
                'transfers.time',
                'transfers.is_accept',
                'transfers.assign_sf2',
                'transfers.remark',
                'transfers.sf002_remark',
                'items.code as item_code',
                'items.name as item_name',
                'items.size as item_size',
                'transfer_by_user.name as transfer_by_name'
            )
            ->join('items', 'transfers.item_id', '=', 'items.id')
            ->leftJoin('users as transfer_by_user', 'transfers.transfer_by', '=', 'transfer_by_user.id')
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
        $assignedTransfers = $this->assignedTransfersQuery()->get();

        return view('backend.production-reports.sf002.stock', compact('assignedTransfers'));
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

        return view('backend.production-reports.sf002.process', compact('acceptedTransfers'));
    }

    /**
     * Display production report form for a specific accepted transfer.
     */
    public function productionReport(int $transferId): View
    {
        $query = DB::table('sf001_stock_transfers as transfers')
            ->select(
                'transfers.id',
                'transfers.item_id',
                'transfers.quantity',
                'transfers.date',
                'transfers.time',
                'transfers.is_accept',
                'items.code as item_code',
                'items.name as item_name',
                'items.size as item_size'
            )
            ->join('items', 'transfers.item_id', '=', 'items.id')
            ->where('transfers.id', $transferId)
            ->where('transfers.is_deleted', false);

        if (Auth::user()?->role !== 'Admin') {
            $role = $this->currentAssignableRole();

            if (!$role) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where('transfers.assign_role', $role)
                    ->where('transfers.assign_to', Auth::id())
                    ->where('transfers.is_accept', 1);
            }
        }

        $transfer = $query->first();

        if (!$transfer) {
            abort(404, 'Transfer record not found or not assigned to you.');
        }

        return view('backend.production-reports.sf002.production-report', compact('transfer'));
    }

    /**
     * Store production report data.
     */
    public function storeProductionReport(Request $request, int $transferId): RedirectResponse
    {
        // Validate basic transfer existence
        $query = DB::table('sf001_stock_transfers')
            ->where('id', $transferId)
            ->where('is_deleted', false);

        if (Auth::user()?->role !== 'Admin') {
            $role = $this->currentAssignableRole();

            if (!$role) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where('assign_role', $role)
                    ->where('assign_to', Auth::id());
            }
        }

        if (!$query->exists()) {
            return back()->with('error', 'Transfer record not found or not assigned to you.');
        }

        // Store the production report data
        // This is a placeholder - adjust based on your actual database schema
        DB::table('production_reports')->insert([
            'transfer_id' => $transferId,
            'ced_plan' => $request->ced_plan,
            'ced_slot1_set' => $request->ced_slot1_set,
            'ced_slot1_actual' => $request->ced_slot1_actual,
            'ced_slot2_set' => $request->ced_slot2_set,
            'ced_slot2_actual' => $request->ced_slot2_actual,
            'ced_slot3_set' => $request->ced_slot3_set,
            'ced_slot3_actual' => $request->ced_slot3_actual,
            'ced_slot4_set' => $request->ced_slot4_set,
            'ced_slot4_actual' => $request->ced_slot4_actual,
            'ced_slot5_set' => $request->ced_slot5_set,
            'ced_slot5_actual' => $request->ced_slot5_actual,
            'ced_slot6_set' => $request->ced_slot6_set,
            'ced_slot6_actual' => $request->ced_slot6_actual,
            'ced_slot7_set' => $request->ced_slot7_set,
            'ced_slot7_actual' => $request->ced_slot7_actual,
            'ced_slot8_set' => $request->ced_slot8_set,
            'ced_slot8_actual' => $request->ced_slot8_actual,
            'ced_slot9_set' => $request->ced_slot9_set,
            'ced_slot9_actual' => $request->ced_slot9_actual,
            'ced_slot10_set' => $request->ced_slot10_set,
            'ced_slot10_actual' => $request->ced_slot10_actual,
            'ced_slot11_set' => $request->ced_slot11_set,
            'ced_slot11_actual' => $request->ced_slot11_actual,
            'ced_shift' => $request->ced_shift,
            'ced_manpower' => $request->ced_manpower,
            'zinc_plan' => $request->zinc_plan,
            'zinc_slot1_set' => $request->zinc_slot1_set,
            'zinc_slot1_actual' => $request->zinc_slot1_actual,
            'zinc_slot2_set' => $request->zinc_slot2_set,
            'zinc_slot2_actual' => $request->zinc_slot2_actual,
            'zinc_slot3_set' => $request->zinc_slot3_set,
            'zinc_slot3_actual' => $request->zinc_slot3_actual,
            'zinc_slot4_set' => $request->zinc_slot4_set,
            'zinc_slot4_actual' => $request->zinc_slot4_actual,
            'zinc_slot5_set' => $request->zinc_slot5_set,
            'zinc_slot5_actual' => $request->zinc_slot5_actual,
            'zinc_slot6_set' => $request->zinc_slot6_set,
            'zinc_slot6_actual' => $request->zinc_slot6_actual,
            'zinc_slot7_set' => $request->zinc_slot7_set,
            'zinc_slot7_actual' => $request->zinc_slot7_actual,
            'zinc_slot8_set' => $request->zinc_slot8_set,
            'zinc_slot8_actual' => $request->zinc_slot8_actual,
            'zinc_slot9_set' => $request->zinc_slot9_set,
            'zinc_slot9_actual' => $request->zinc_slot9_actual,
            'zinc_slot10_set' => $request->zinc_slot10_set,
            'zinc_slot10_actual' => $request->zinc_slot10_actual,
            'zinc_slot11_set' => $request->zinc_slot11_set,
            'zinc_slot11_actual' => $request->zinc_slot11_actual,
            'zinc_shift' => $request->zinc_shift,
            'zinc_manpower' => $request->zinc_manpower,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.production-reports.sf002.process')
            ->with('success', 'Production report saved successfully.');
    }

    /**
     * Update the transfer status for the assigned SF002 user.
     */
    public function updateStatus(Request $request, int $transferId): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|integer|in:1,2',
            'sf002_remark' => 'nullable|string|max:500',
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

        if ((int) $transfer->is_accept !== 0) {
            return back()->with('error', 'Status already updated. You cannot change the status or remark again.');
        }

        DB::table('sf001_stock_transfers')
            ->where('id', $transferId)
            ->update([
                'is_accept' => $validated['status'],
                'assign_to' => Auth::user()?->role === 'Admin' ? $transfer->assign_to : Auth::id(),
                'sf002_remark' => $validated['sf002_remark'] ?? null,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Transfer status updated successfully.');
    }
}
