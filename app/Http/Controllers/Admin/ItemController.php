<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Machine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    /**
     * Display a listing of items.
     */
    public function index(Request $request): View
    {
        $mode = $request->query('mode', 'active');
        $search = trim((string) $request->query('search', ''));

        $query = Item::query();

        if ($mode === 'deleted') {
            $query->where('is_deleted', true);
        } elseif ($mode === 'all') {
            // no filter
        } else {
            $mode = 'active';
            $query->where('is_deleted', false);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('size', 'like', "%{$search}%")
                    ->orWhere('weight', 'like', "%{$search}%");
            });
        }

        $items = $query
            ->withSum([
                'productionReports as total_production_count' => function ($builder) {
                    $builder->where('is_deleted', false);
                }
            ], 'actual_set_shift')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('backend.items.index', compact('items', 'mode', 'search'));
    }

    /**
     * Show the form for creating a new item.
     */
    public function create(): View
    {
        $machines = Machine::query()
            ->where('is_deleted', false)
            ->where('status', true)
            ->orderBy('name')
            ->get(['id', 'name', 'machine_code']);

        return view('backend.items.create', compact('machines'));
    }

    /**
     * Store a newly created item in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:items,code',
            'name_sf2' => 'nullable|string|max:255',
            'code_sf2' => ['nullable', 'string', 'max:255', Rule::unique('items', 'code_sf2')],
            'size' => 'nullable|string|max:255',
            'weight' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'machine_ids' => ['nullable', 'array'],
            'machine_ids.*' => ['integer', Rule::exists('machines', 'id')->where('is_deleted', false)],
        ]);

        $machineIds = $validated['machine_ids'] ?? [];
        unset($validated['machine_ids']);

        $validated['is_deleted'] = false;

        $item = Item::create($validated);

        $item->machines()->sync($machineIds);

        if ($this->isAjaxRequest($request)) {
            return response()->json([
                'message' => 'Item created successfully.',
            ]);
        }

        return redirect()->route('admin.items.index')
            ->with('success', 'Item created successfully.');
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item): View
    {
        $item->load([
            'machines' => function ($query) {
                $query->select('machines.id', 'machines.name', 'machines.machine_code', 'machines.status', 'machines.is_deleted');
            },
        ]);

        return view('backend.items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item): View
    {
        $machines = Machine::query()
            ->where('is_deleted', false)
            ->where('status', true)
            ->orderBy('name')
            ->get(['id', 'name', 'machine_code']);

        $item->load(['machines:id']);

        return view('backend.items.edit', compact('item', 'machines'));
    }

    /**
     * Update the specified item in storage.
     */
    public function update(Request $request, Item $item): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:items,code,' . $item->id,
            'name_sf2' => 'nullable|string|max:255',
            'code_sf2' => ['nullable', 'string', 'max:255', Rule::unique('items', 'code_sf2')->ignore($item->id)],
            'size' => 'nullable|string|max:255',
            'weight' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'machine_ids' => ['nullable', 'array'],
            'machine_ids.*' => ['integer', Rule::exists('machines', 'id')->where('is_deleted', false)],
        ]);

        $machineIds = $validated['machine_ids'] ?? [];
        unset($validated['machine_ids']);

        $item->update($validated);

        $item->machines()->sync($machineIds);

        if ($this->isAjaxRequest($request)) {
            return response()->json([
                'message' => 'Item updated successfully.',
            ]);
        }

        return redirect()->route('admin.items.index')
            ->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item): RedirectResponse
    {
        $item->update(['is_deleted' => true]);

        return redirect()->route('admin.items.index')
            ->with('success', 'Item deleted successfully.');
    }

    protected function isAjaxRequest(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson() || $request->expectsJson();
    }
}
