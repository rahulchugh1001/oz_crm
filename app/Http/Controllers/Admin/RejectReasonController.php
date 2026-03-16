<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RejectReason;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RejectReasonController extends Controller
{
    public function index(Request $request): View
    {
        $mode = $request->query('mode', 'active');
        $search = trim((string) $request->query('search', ''));

        $query = RejectReason::query();

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
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('backend.reject-reasons.index', compact('rejectReasons', 'mode', 'search'));
    }

    public function create(): View
    {
        return view('backend.reject-reasons.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('reject_reasons', 'name')],
            'status' => ['required', 'boolean'],
        ]);

        $validated['is_deleted'] = false;

        RejectReason::create($validated);

        return redirect()->route('admin.reject-reasons.index')
            ->with('success', 'Reject reason created successfully.');
    }

    public function edit(RejectReason $rejectReason): View
    {
        return view('backend.reject-reasons.edit', compact('rejectReason'));
    }

    public function update(Request $request, RejectReason $rejectReason): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('reject_reasons', 'name')->ignore($rejectReason->id)],
            'status' => ['required', 'boolean'],
        ]);

        $rejectReason->update($validated);

        return redirect()->route('admin.reject-reasons.index')
            ->with('success', 'Reject reason updated successfully.');
    }

    public function destroy(RejectReason $rejectReason): RedirectResponse
    {
        $rejectReason->update(['is_deleted' => true]);

        return redirect()->route('admin.reject-reasons.index')
            ->with('success', 'Reject reason deleted successfully.');
    }
}
