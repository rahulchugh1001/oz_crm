<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PPCController extends Controller
{
    /**
     * Get the current role that PPC controller manages.
     */
    protected function currentAssignableRole(): string
    {
        return 'PPC';
    }

    /**
     * Display pending requests from SF2 to PPC.
     */
    public function process(): View
    {
        $role = $this->currentAssignableRole();

        $query = DB::table('sf002_to_ppc_transfers as transfers')
            ->select(
                'transfers.id',
                'transfers.quantity',
                'transfers.date',
                'transfers.time',
                'transfers.is_accept',
                'transfers.remark',
                'transfers.created_at',
                'transfers.type',
                'items.code as item_code',
                'items.name as item_name',
                'items.code_sf2 as item_code_sf2',
                'items.name_sf2 as item_name_sf2',
                'items.size as item_size',
                'transfer_by_user.name as transfer_by_name',
                'assign_to_user.name as assign_to_name'
            )
            ->join('items', 'transfers.item_id', '=', 'items.id')
            ->leftJoin('users as transfer_by_user', 'transfers.transfer_by', '=', 'transfer_by_user.id')
            ->leftJoin('users as assign_to_user', 'transfers.assign_to', '=', 'assign_to_user.id')
            ->where('transfers.is_deleted', false)
            ->where('transfers.is_accept', 0); // Only pending

        if (Auth::user()?->role !== 'Admin') {
            $query->where('transfers.assign_role', $role)
                ->where(function ($scoped) {
                    $scoped->whereNull('transfers.assign_to')
                        ->orWhere('transfers.assign_to', Auth::id());
                });
        }

        $pendingTransfers = $query->orderBy('transfers.created_at', 'asc')->get();
        $rejectReasons = DB::table('reject_reasons')
            ->where('status', 1)
            ->where('is_deleted', false)
            ->whereIn('category', ['SF2', 'Both'])
            ->orderBy('name')
            ->get();

        return view('backend.production-reports.ppc.process', compact('pendingTransfers', 'rejectReasons'));
    }

    /**
     * Update the transfer status for incoming stock to PPC.
     */
    public function updateStatus(Request $request, int $transferId): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|integer|in:1,2',
            'ppc_remark' => 'nullable|string|max:500',
            'accept_all_quantity' => 'nullable|boolean',
            'reject_quantity' => 'nullable|numeric|min:0',
            'reject_reason_id' => 'nullable|integer|exists:reject_reasons,id',
        ]);

        $query = DB::table('sf002_to_ppc_transfers')
            ->where('id', $transferId)
            ->where('is_deleted', false);

        if (Auth::user()?->role !== 'Admin') {
            $query->where('assign_role', $this->currentAssignableRole())
                ->where(function ($scoped) {
                    $scoped->whereNull('assign_to')
                        ->orWhere('assign_to', Auth::id());
                });
        }

        $transfer = $query->first();

        if (!$transfer) {
            return back()->with('error', 'Transfer record not found or not assigned to you.');
        }

        $currentQuantity = (float) $transfer->quantity;
        $acceptAllQuantity = (bool) ($validated['accept_all_quantity'] ?? false);
        $rejectQuantity = $acceptAllQuantity ? 0.0 : (float) ($validated['reject_quantity'] ?? 0);

        $rejectReasonId = null;
        if (!$acceptAllQuantity && $rejectQuantity > 0) {
            $rejectReasonId = $validated['reject_reason_id'] ?? null;
            if (!$rejectReasonId) {
                 return back()->withErrors(['reject_reason_id' => 'Please select a reject reason when rejecting partial quantity.'])->withInput();
            }
        }

        if ($rejectQuantity > $currentQuantity) {
            return back()->withErrors(['reject_quantity' => 'Reject quantity cannot exceed the total transfer quantity (' . number_format($currentQuantity, 0) . ').'])->withInput();
        }

        $status = (int) $validated['status'];
        if ($status === 1 && $rejectQuantity == $currentQuantity) {
             return back()->withErrors(['reject_quantity' => 'Cannot accept transfer if rejecting the entire quantity. Please change status to Reject instead.'])->withInput();
        }

        if ($status === 2) {
             $rejectQuantity = $currentQuantity;
             if (!$validated['reject_reason_id']) {
                 return back()->withErrors(['reject_reason_id' => 'Please select a reject reason when rejecting the entire transfer.'])->withInput();
             }
             $rejectReasonId = $validated['reject_reason_id'];
        }

        DB::table('sf002_to_ppc_transfers')
            ->where('id', $transferId)
            ->update([
                'is_accept' => $status,
                'reject_quantity' => $rejectQuantity,
                'reject_reason_id' => $rejectReasonId,
                'ppc_remark' => $validated['ppc_remark'] ?? null,
                'assign_to' => Auth::id(), 
                'updated_at' => now()
            ]);

        $statusText = $status === 1 ? 'accepted' : 'rejected';
        return back()->with('success', "Transfer smoothly $statusText.");
    }

    /**
     * Display PPC stock to transfer to SF3 (Assembly).
     */
    public function stock(): View
    {
        // Accepted inbound from SF2 via PPC
        $ppcInbound = DB::table('sf002_to_ppc_transfers')
            ->where('is_deleted', false)
            ->where('is_accept', 1) // Only accepted
            ->select(
                'item_id',
                'type',
                DB::raw("COALESCE(SUM(GREATEST(quantity - COALESCE(reject_quantity, 0), 0)), 0) as total_accepted")
            )
            ->groupBy('item_id', 'type');

        // Outbound logic to SF3
        $ppcOutbound = DB::table('sf002_stock_transfers')
            ->where('is_deleted', false)
            ->select(
                'item_id',
                'type',
                DB::raw("COALESCE(SUM(CASE
                    WHEN is_accept = 2 THEN 0
                    WHEN is_accept = 1 THEN GREATEST(quantity - COALESCE(reject_quantity, 0), 0)
                    ELSE quantity
                END), 0) as total_transferred") // Includes pending outbound
            )
            ->groupBy('item_id', 'type');

        $stocks = DB::table('items')
            ->select(
                'items.id',
                'items.code',
                'items.name',
                'items.code_sf2',
                'items.name_sf2',
                'items.size',
                'inbound.type',
                DB::raw('COALESCE(inbound.total_accepted, 0) as total_accepted'),
                DB::raw('COALESCE(outbound.total_transferred, 0) as total_transferred'),
                DB::raw('GREATEST(COALESCE(inbound.total_accepted, 0) - COALESCE(outbound.total_transferred, 0), 0) as pending_quantity')
            )
            ->joinSub($ppcInbound, 'inbound', function ($join) {
                $join->on('items.id', '=', 'inbound.item_id');
            })
            ->leftJoinSub($ppcOutbound, 'outbound', function ($join) {
                $join->on('items.id', '=', 'outbound.item_id')
                     ->on('inbound.type', '=', 'outbound.type');
            })
            ->where('items.is_deleted', 0)
            ->having('pending_quantity', '>', 0)
            ->orderBy('items.name')
            ->get();

        $cedStocks = $stocks->where('type', 'ced')->values();
        $zincStocks = $stocks->where('type', 'zinc')->values();
        $ballcageStocks = $stocks->where('type', 'ballcage')->values();

        return view('backend.production-reports.ppc.stock', compact('cedStocks', 'zincStocks', 'ballcageStocks'));
    }

    /**
     * Transfer PPC stock to SF3
     */
    public function storePpcTransfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id'  => 'required|integer|exists:items,id',
            'type'     => 'required|string|in:ced,zinc,ballcage',
            'sf3_process' => 'nullable|string|in:line_1,line_2,line_3,line_4,line_5,line_6',
            'quantity' => 'required|numeric|gt:0',
            'date'     => 'required|date',
            'time'     => 'required|date_format:H:i:s',
            'remark'   => 'nullable|string|max:500',
        ]);

        $totalAccepted = (float) DB::table('sf002_to_ppc_transfers')
            ->where('item_id', $validated['item_id'])
            ->where('type', $validated['type'])
            ->where('is_deleted', false)
            ->where('is_accept', 1)
            ->selectRaw("COALESCE(SUM(GREATEST(quantity - COALESCE(reject_quantity, 0), 0)), 0) as total")
            ->value('total') ?? 0;

        $totalTransferred = (float) DB::table('sf002_stock_transfers')
            ->where('item_id', $validated['item_id'])
            ->where('type', $validated['type'])
            ->where('is_deleted', false)
            ->selectRaw("COALESCE(SUM(CASE
                WHEN is_accept = 2 THEN 0
                WHEN is_accept = 1 THEN GREATEST(quantity - COALESCE(reject_quantity, 0), 0)
                ELSE quantity
            END), 0) as total")
            ->value('total') ?? 0;

        $availableStock = max($totalAccepted - $totalTransferred, 0);

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

        return back()->with('success', 'Stock transferred to Assembly Line successfully.');
    }
}
