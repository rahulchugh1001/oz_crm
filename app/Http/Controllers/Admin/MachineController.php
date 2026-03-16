<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MachineController extends Controller
{
    /**
     * Display a listing of machines.
     */
    public function index(Request $request): View
    {
        $mode = $request->query('mode', 'active');
        $search = trim((string) $request->query('search', ''));

        $query = Machine::query();

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
                    ->orWhere('machine_code', 'like', "%{$search}%")
                    ->orWhere('rf_set', 'like', "%{$search}%");
            });
        }

        $machines = $query->latest()->paginate(10)->withQueryString();

        return view('backend.machines.index', compact('machines', 'mode', 'search'));
    }

    /**
     * Show the form for creating a new machine.
     */
    public function create(): View
    {
        return view('backend.machines.create');
    }

    /**
     * Store a newly created machine in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'machine_code' => 'required|string|max:255|unique:machines,machine_code',
            'rf_set' => ['nullable', Rule::in(Machine::RF_SET_OPTIONS)],
            'status' => 'required|boolean',
        ]);

        $validated['is_deleted'] = false;

        Machine::create($validated);

        if ($this->isAjaxRequest($request)) {
            return response()->json([
                'message' => 'Machine created successfully.',
            ]);
        }

        return redirect()->route('admin.machines.index')
            ->with('success', 'Machine created successfully.');
    }

    /**
     * Display the specified machine.
     */
    public function show(Machine $machine): View
    {
        return view('backend.machines.show', compact('machine'));
    }

    /**
     * Show the form for editing the specified machine.
     */
    public function edit(Machine $machine): View
    {
        return view('backend.machines.edit', compact('machine'));
    }

    /**
     * Update the specified machine in storage.
     */
    public function update(Request $request, Machine $machine): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'machine_code' => 'required|string|max:255|unique:machines,machine_code,' . $machine->id,
            'rf_set' => ['nullable', Rule::in(Machine::RF_SET_OPTIONS)],
            'status' => 'required|boolean',
        ]);

        $machine->update($validated);

        if ($this->isAjaxRequest($request)) {
            return response()->json([
                'message' => 'Machine updated successfully.',
            ]);
        }

        return redirect()->route('admin.machines.index')
            ->with('success', 'Machine updated successfully.');
    }

    /**
     * Remove the specified machine from storage.
     */
    public function destroy(Machine $machine): RedirectResponse
    {
        $machine->update(['is_deleted' => true]);

        return redirect()->route('admin.machines.index')
            ->with('success', 'Machine deleted successfully.');
    }

    protected function isAjaxRequest(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson() || $request->expectsJson();
    }
}
