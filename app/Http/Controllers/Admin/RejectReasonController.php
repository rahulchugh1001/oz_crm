<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RejectReason;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RejectReasonController extends Controller
{
    public function index(Request $request): View
    {
        $mode = $request->query('mode', 'active');
        $search = trim((string) $request->query('search', ''));

        $query = RejectReason::query()
            ->select('reject_reasons.*')
            ->selectRaw(
                '(
                    (select count(*) from sf001_stock_transfers t1
                        where t1.reject_reason_id = reject_reasons.id
                        and t1.is_deleted = 0
                        and COALESCE(t1.reject_quantity, 0) > 0
                    )
                    +
                    (select count(*) from sf002_stock_transfers t2
                        where t2.reject_reason_id = reject_reasons.id
                        and t2.is_deleted = 0
                        and COALESCE(t2.reject_quantity, 0) > 0
                    )
                ) as usage_count'
            );

        if ($mode === 'deleted') {
            $query->where('is_deleted', true);
        } elseif ($mode === 'all') {
            // no filter
        } else {
            $mode = 'active';
            $query->where('is_deleted', false);
        }

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        $rejectReasons = $query
            ->orderByDesc('usage_count')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('backend.reject-reasons.index', compact('rejectReasons', 'mode', 'search'));
    }

    public function show(string $encryptedId): View
    {
        try {
            $rejectReasonId = (int) Crypt::decryptString($encryptedId);
            $rejectReason = RejectReason::findOrFail($rejectReasonId);
        } catch (\Exception $e) {
            abort(404, 'Reject reason not found.');
        }

        $to = Carbon::today();
        $from = (clone $to)->subDays(29);

        $sf1Daily = DB::table('sf001_stock_transfers as transfers')
            ->select('transfers.date', DB::raw('count(*) as c'))
            ->where('transfers.is_deleted', 0)
            ->where('transfers.reject_reason_id', $rejectReason->id)
            ->whereRaw('COALESCE(transfers.reject_quantity, 0) > 0')
            ->whereBetween('transfers.date', [$from->toDateString(), $to->toDateString()])
            ->groupBy('transfers.date')
            ->pluck('c', 'transfers.date');

        $sf2Daily = DB::table('sf002_stock_transfers as transfers')
            ->select('transfers.date', DB::raw('count(*) as c'))
            ->where('transfers.is_deleted', 0)
            ->where('transfers.reject_reason_id', $rejectReason->id)
            ->whereRaw('COALESCE(transfers.reject_quantity, 0) > 0')
            ->whereBetween('transfers.date', [$from->toDateString(), $to->toDateString()])
            ->groupBy('transfers.date')
            ->pluck('c', 'transfers.date');

        $chartLabels = [];
        $chartSf1 = [];
        $chartSf2 = [];

        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $chartLabels[] = $cursor->format('M d');
            $chartSf1[] = (int) ($sf1Daily[$key] ?? 0);
            $chartSf2[] = (int) ($sf2Daily[$key] ?? 0);
            $cursor->addDay();
        }

        $sf1UsageQuery = DB::table('sf001_stock_transfers as transfers')
            ->select(
                'transfers.id',
                'transfers.quantity',
                'transfers.reject_quantity',
                'transfers.date',
                'transfers.time',
                'transfers.is_accept',
                'transfers.assign_sf2',
                'transfers.assign_role',
                'transfers.remark',
                'transfers.sf002_remark',
                'transfers.created_at',
                'items.code as item_code',
                'items.name as item_name',
                'items.size as item_size',
                'transfer_by_user.name as transfer_by_name',
                'assign_to_user.name as accepted_by_name'
            )
            ->join('items', 'transfers.item_id', '=', 'items.id')
            ->leftJoin('users as transfer_by_user', 'transfers.transfer_by', '=', 'transfer_by_user.id')
            ->leftJoin('users as assign_to_user', 'transfers.assign_to', '=', 'assign_to_user.id')
            ->where('transfers.is_deleted', 0)
            ->where('transfers.reject_reason_id', $rejectReason->id)
            ->whereRaw('COALESCE(transfers.reject_quantity, 0) > 0')
            ->orderByDesc('transfers.date')
            ->orderByDesc('transfers.time')
            ->orderByDesc('transfers.created_at');

        $sf2UsageQuery = DB::table('sf002_stock_transfers as transfers')
            ->select(
                'transfers.id',
                'transfers.quantity',
                'transfers.reject_quantity',
                'transfers.type',
                'transfers.sf3_process',
                'transfers.date',
                'transfers.time',
                'transfers.is_accept',
                'transfers.remark',
                'transfers.sf003_remark',
                'transfers.created_at',
                'items.code as item_code',
                'items.name as item_name',
                'items.size as item_size',
                'transfer_by_user.name as transfer_by_name',
                'assign_to_user.name as accepted_by_name'
            )
            ->join('items', 'transfers.item_id', '=', 'items.id')
            ->leftJoin('users as transfer_by_user', 'transfers.transfer_by', '=', 'transfer_by_user.id')
            ->leftJoin('users as assign_to_user', 'transfers.assign_to', '=', 'assign_to_user.id')
            ->where('transfers.is_deleted', 0)
            ->where('transfers.reject_reason_id', $rejectReason->id)
            ->whereRaw('COALESCE(transfers.reject_quantity, 0) > 0')
            ->orderByDesc('transfers.date')
            ->orderByDesc('transfers.time')
            ->orderByDesc('transfers.created_at');

        $sf1UsageCount = (int) $sf1UsageQuery->count();
        $sf2UsageCount = (int) $sf2UsageQuery->count();
        $totalUsageCount = $sf1UsageCount + $sf2UsageCount;

        $sf1Usages = $sf1UsageQuery->paginate(20, ['*'], 'sf1_page')->withQueryString();
        $sf2Usages = $sf2UsageQuery->paginate(20, ['*'], 'sf2_page')->withQueryString();

        return view('backend.reject-reasons.show', compact(
            'rejectReason',
            'sf1Usages',
            'sf2Usages',
            'sf1UsageCount',
            'sf2UsageCount',
            'totalUsageCount',
            'chartLabels',
            'chartSf1',
            'chartSf2'
        ));
    }

    public function create(): View
    {
        return view('backend.reject-reasons.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('reject_reasons', 'name')],
            'category' => ['required', Rule::in(['SF1', 'SF2', 'Both'])],
            'status' => ['required', 'boolean'],
        ]);

        $validated['category'] = $validated['category'] ?? 'SF1';
        $validated['is_deleted'] = false;

        RejectReason::create($validated);

        return redirect()->route('admin.reject-reasons.index')
            ->with('success', 'Reject reason created successfully.');
    }

    public function edit(string $encryptedId): View
    {
        try {
            $rejectReasonId = (int) Crypt::decryptString($encryptedId);
            $rejectReason = RejectReason::findOrFail($rejectReasonId);
        } catch (\Exception $e) {
            abort(404, 'Reject reason not found.');
        }

        return view('backend.reject-reasons.edit', compact('rejectReason'));
    }

    public function update(Request $request, string $encryptedId): RedirectResponse
    {
        try {
            $rejectReasonId = (int) Crypt::decryptString($encryptedId);
            $rejectReason = RejectReason::findOrFail($rejectReasonId);
        } catch (\Exception $e) {
            abort(404, 'Reject reason not found.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('reject_reasons', 'name')->ignore($rejectReason->id)],
            'category' => ['required', Rule::in(['SF1', 'SF2', 'Both'])],
            'status' => ['required', 'boolean'],
        ]);

        $validated['category'] = $validated['category'] ?? 'SF1';

        $rejectReason->update($validated);

        return redirect()->route('admin.reject-reasons.index')
            ->with('success', 'Reject reason updated successfully.');
    }

    public function destroy(string $encryptedId): RedirectResponse
    {
        try {
            $rejectReasonId = (int) Crypt::decryptString($encryptedId);
            $rejectReason = RejectReason::findOrFail($rejectReasonId);
        } catch (\Exception $e) {
            abort(404, 'Reject reason not found.');
        }

        $rejectReason->update(['is_deleted' => true]);

        return redirect()->route('admin.reject-reasons.index')
            ->with('success', 'Reject reason deleted successfully.');
    }
}
