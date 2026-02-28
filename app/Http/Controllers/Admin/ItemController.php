<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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

        $items = $query->latest()->paginate(10)->withQueryString();

        return view('backend.items.index', compact('items', 'mode', 'search'));
    }

    /**
     * Show the form for creating a new item.
     */
    public function create(): View
    {
        return view('backend.items.create');
    }

    /**
     * Store a newly created item in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:items,code',
            'size' => 'nullable|string|max:255',
            'weight' => 'required|numeric|min:0',
            'status' => 'required|boolean',
        ]);

        $validated['is_deleted'] = false;

        Item::create($validated);

        return redirect()->route('admin.items.index')
            ->with('success', 'Item created successfully.');
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item): View
    {
        return view('backend.items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item): View
    {
        return view('backend.items.edit', compact('item'));
    }

    /**
     * Update the specified item in storage.
     */
    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:items,code,' . $item->id,
            'size' => 'nullable|string|max:255',
            'weight' => 'required|numeric|min:0',
            'status' => 'required|boolean',
        ]);

        $item->update($validated);

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
}
