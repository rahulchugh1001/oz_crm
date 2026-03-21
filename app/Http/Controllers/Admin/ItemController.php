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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ItemController extends Controller
{
    /**
     * Display a listing of items.
     */
    public function index(Request $request): View
    {
        $mode = $request->query('mode', 'active');
        $search = trim((string) $request->query('search', ''));
        $userRole = (string) optional(Auth::user())->role;
        $isStoreRoleUser = in_array($userRole, ['Stock', 'Store'], true);

        $query = Item::query();

        if ($isStoreRoleUser) {
            $query->where('category', 'Store');
        }

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
                    ->orWhere('category', 'like', "%{$search}%")
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

        $productItems = Item::query()
            ->where('is_deleted', false)
            ->where('category', '!=', 'SF3')
            ->orderBy('category')
            ->orderBy('name')
            ->get(['id', 'name', 'name_sf2', 'category']);

        return view('backend.items.create', compact('machines', 'productItems'));
    }

    /**
     * Store a newly created item in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:items,code',
            'category' => ['required', Rule::in(['SF1-SF2', 'SF3', 'Store'])],
            'name_sf2' => 'nullable|string|max:255',
            'code_sf2' => ['nullable', 'string', 'max:255', Rule::unique('items', 'code_sf2')],
            'size' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
            'machine_ids' => ['nullable', 'array'],
            'machine_ids.*' => ['integer', Rule::exists('machines', 'id')->where('is_deleted', false)],
            'sf3_products' => ['nullable', 'array'],
            'sf3_products.*.product' => ['nullable', 'integer', Rule::exists('items', 'id')],
            'sf3_products.*.quantity' => ['nullable', 'numeric', 'min:0'],
        ];

        if ($request->input('category') === 'SF3') {
            $rules['sf3_products'] = ['required', 'array', 'min:1'];
            $rules['sf3_products.*.product'] = ['required', 'integer', Rule::exists('items', 'id')];
            $rules['sf3_products.*.quantity'] = ['required', 'numeric', 'min:0'];
        }

        $validated = $request->validate($rules);

        if (($validated['category'] ?? null) !== 'Store') {
            $validated['quantity'] = null;
        }

        $machineIds = $validated['machine_ids'] ?? [];
        $sf3Products = $validated['sf3_products'] ?? [];
        unset($validated['machine_ids'], $validated['sf3_products']);

        $validated['is_deleted'] = false;

        $item = DB::transaction(function () use ($validated, $machineIds, $sf3Products) {
            $item = Item::create($validated);

            $item->machines()->sync($machineIds);

            if ($item->category === 'SF3' && count($sf3Products) > 0) {
                $item->sf3Products()->createMany($this->normalizeSf3Products($sf3Products));
            }

            return $item;
        });

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
            'sf3Products' => function ($query) {
                $query->select('id', 'item_id', 'product', 'quantity')->orderBy('id');
            },
            'sf3Products.productItem' => function ($query) {
                $query->select('id', 'name', 'name_sf2', 'category');
            },
        ]);

        $stockUsageHistory = collect();
        if (in_array((string) $item->category, ['Store', 'Stock'], true) && Schema::hasTable('sf3_stock_usages')) {
            $stockUsageHistory = DB::table('sf3_stock_usages as usage')
                ->select(
                    'usage.id',
                    'usage.report_id',
                    'usage.item_id as sf3_item_id',
                    'usage.stock_id',
                    'usage.in_stock',
                    'usage.used_stock',
                    'usage.created_at',
                    'sf3_items.name as sf3_item_name',
                    'sf3_items.code as sf3_item_code',
                    'sf3_reports.report_date',
                    'sf3_reports.sf3_process',
                    'sf3_reports.shift'
                )
                ->leftJoin('items as sf3_items', 'usage.item_id', '=', 'sf3_items.id')
                ->leftJoin('sf3_production_reports as sf3_reports', 'usage.report_id', '=', 'sf3_reports.id')
                ->where('usage.stock_id', $item->id)
                ->where('usage.is_deleted', 0)
                ->where('usage.status', 1)
                ->orderByDesc('usage.created_at')
                ->get();
        }

        return view('backend.items.show', compact('item', 'stockUsageHistory'));
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

        $item->load([
            'machines:id',
            'sf3Products:id,item_id,product,quantity',
        ]);

        $productItems = Item::query()
            ->where('is_deleted', false)
            ->where('category', '!=', 'SF3')
            ->orderBy('category')
            ->orderBy('name')
            ->get(['id', 'name', 'name_sf2', 'category']);

        return view('backend.items.edit', compact('item', 'machines', 'productItems'));
    }

    /**
     * Update the specified item in storage.
     */
    public function update(Request $request, Item $item): RedirectResponse|JsonResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:items,code,' . $item->id,
            'category' => ['required', Rule::in(['SF1-SF2', 'SF3', 'Store'])],
            'name_sf2' => 'nullable|string|max:255',
            'code_sf2' => ['nullable', 'string', 'max:255', Rule::unique('items', 'code_sf2')->ignore($item->id)],
            'size' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
            'machine_ids' => ['nullable', 'array'],
            'machine_ids.*' => ['integer', Rule::exists('machines', 'id')->where('is_deleted', false)],
            'sf3_products' => ['nullable', 'array'],
            'sf3_products.*.product' => ['nullable', 'integer', Rule::exists('items', 'id')],
            'sf3_products.*.quantity' => ['nullable', 'numeric', 'min:0'],
        ];

        if ($request->input('category') === 'SF3') {
            $rules['sf3_products'] = ['required', 'array', 'min:1'];
            $rules['sf3_products.*.product'] = ['required', 'integer', Rule::exists('items', 'id')];
            $rules['sf3_products.*.quantity'] = ['required', 'numeric', 'min:0'];
        }

        $validated = $request->validate($rules);

        if (($validated['category'] ?? null) !== 'Store') {
            $validated['quantity'] = null;
        }

        $machineIds = $validated['machine_ids'] ?? [];
        $sf3Products = $validated['sf3_products'] ?? [];
        unset($validated['machine_ids'], $validated['sf3_products']);

        DB::transaction(function () use ($item, $validated, $machineIds, $sf3Products) {
            $item->update($validated);

            $item->machines()->sync($machineIds);

            $item->sf3Products()->delete();
            if ($item->category === 'SF3' && count($sf3Products) > 0) {
                $item->sf3Products()->createMany($this->normalizeSf3Products($sf3Products));
            }
        });

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

    /**
     * @param  array<int, array{product?: mixed, quantity?: mixed}>  $rows
     * @return array<int, array{product: string, quantity: float}>
     */
    protected function normalizeSf3Products(array $rows): array
    {
        return collect($rows)
            ->map(function (array $row): array {
                return [
                    'product'  => (int) ($row['product'] ?? 0),
                    'quantity' => (float) ($row['quantity'] ?? 0),
                ];
            })
            ->values()
            ->all();
    }
}
