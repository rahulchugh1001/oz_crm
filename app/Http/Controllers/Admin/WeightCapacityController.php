<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WeightCapacity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WeightCapacityController extends Controller
{
    public function index(Request $request): View
    {
        $mode = $request->query('mode', 'active');
        $search = trim((string) $request->query('search', ''));

        $query = WeightCapacity::query();

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

        $weightCapacities = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('backend.weight-capacities.index', compact('weightCapacities', 'mode', 'search'));
    }

    public function create(): View
    {
        return view('backend.weight-capacities.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('weight_capacities', 'name')],
            'status' => ['required', 'boolean'],
        ]);

        $validated['is_deleted'] = false;

        WeightCapacity::create($validated);

        return redirect()->route('admin.weight-capacities.index')
            ->with('success', 'Weight capacity created successfully.');
    }

    public function edit(WeightCapacity $weightCapacity): View
    {
        return view('backend.weight-capacities.edit', compact('weightCapacity'));
    }

    public function update(Request $request, WeightCapacity $weightCapacity): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('weight_capacities', 'name')->ignore($weightCapacity->id)],
            'status' => ['required', 'boolean'],
        ]);

        $weightCapacity->update($validated);

        return redirect()->route('admin.weight-capacities.index')
            ->with('success', 'Weight capacity updated successfully.');
    }

    public function destroy(WeightCapacity $weightCapacity): RedirectResponse
    {
        $weightCapacity->update(['is_deleted' => true]);

        return redirect()->route('admin.weight-capacities.index')
            ->with('success', 'Weight capacity deleted successfully.');
    }
}

