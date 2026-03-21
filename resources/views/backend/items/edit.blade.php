@extends('backend.layout.app')

@section('title', 'Edit Item')

@section('page-title', 'Edit Item')

@section('breadcrumb')
    <span class="text-slate-600">Items</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Edit</span>
@endsection

@section('content')
<div class="p-6">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
            <!-- Header -->
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl gradient-warning flex items-center justify-center">
                        <i data-lucide="edit" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Edit Item</h2>
                        <p class="text-sm text-slate-500">Update item details</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form id="item-edit-form" action="{{ route('admin.items.update', $item) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                @php
                    $selectedMachineIds = collect(old('machine_ids', $item->machines?->pluck('id')->all() ?? []))
                        ->map(fn ($id) => (string) $id)
                        ->all();

                    $sf3ProductsFromItem = $item->sf3Products
                        ->map(fn ($row) => [
                            'product' => (string) $row->product,
                            'quantity' => (string) ((float) $row->quantity),
                        ])
                        ->all();

                    $oldSf3Products = old('sf3_products', $sf3ProductsFromItem);

                    if (!is_array($oldSf3Products) || count($oldSf3Products) === 0) {
                        $oldSf3Products = [
                            ['product' => '', 'quantity' => ''],
                        ];
                    }
                @endphp


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-semibold text-slate-700 mb-2">
                            Category <span class="text-rose-500">*</span>
                        </label>
                        @if(in_array(auth()->user()->role, ['Stock', 'Store']))
                            <input type="hidden" name="category" value="Store">
                            <div class="w-full px-4 py-3 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 text-sm cursor-not-allowed">
                                Store
                            </div>
                        @else
                        <select
                            id="category"
                            name="category"
                            required
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('category') border-rose-500 @enderror"
                        >
                            <option value="">Select category</option>
                            <option value="SF1-SF2" {{ old('category', $item->category) === 'SF1-SF2' ? 'selected' : '' }}>SF1-SF2</option>
                            <option value="SF3" {{ old('category', $item->category) === 'SF3' ? 'selected' : '' }}>SF3</option>
                            <option value="Store" {{ old('category', $item->category) === 'Store' ? 'selected' : '' }}>Store</option>
                        </select>
                        @error('category')
                            <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        @endif
                    </div>

                    <!-- Weight -->
                    <div>
                        <label for="weight" class="block text-sm font-semibold text-slate-700 mb-2">
                            Weight
                        </label>
                        <input
                            type="number"
                            id="weight"
                            name="weight"
                            value="{{ old('weight', $item->weight) }}"
                            step="0.01"
                            min="0"
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('weight') border-rose-500 @enderror"
                            placeholder="Enter weight"
                        >
                        @error('weight')
                            <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Store Quantity -->
                    <div id="store-quantity-field" style="display: none;">
                        <label for="quantity" class="block text-sm font-semibold text-slate-700 mb-2">
                            Quantity
                        </label>
                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            value="{{ old('quantity', $item->quantity) }}"
                            step="1"
                            min="0"
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('quantity') border-rose-500 @enderror"
                            placeholder="Enter quantity"
                        >
                        @error('quantity')
                            <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <!-- Name -->
                    <div>
                        <label id="name-label" for="name" class="block text-sm font-semibold text-slate-700 mb-2">
                            Item Name <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $item->name) }}"
                            required
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('name') border-rose-500 @enderror"
                            placeholder="Enter item name"
                            data-default-placeholder="Enter item name"
                            data-sf12-placeholder="Enter item name SF1"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Code -->
                    <div>
                        <label id="code-label" for="code" class="block text-sm font-semibold text-slate-700 mb-2">
                            Item Code <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text"
                            id="code" 
                            name="code" 
                            value="{{ old('code', $item->code) }}"
                            required
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('code') border-rose-500 @enderror"
                            placeholder="Enter unique item code"
                            data-default-placeholder="Enter unique item code"
                            data-sf12-placeholder="Enter unique item code SF1"
                        >
                        @error('code')
                            <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- SF2 Name -->
                    <div>
                        <label for="name_sf2" class="block text-sm font-semibold text-slate-700 mb-2">
                            Item Name SF2
                        </label>
                        <input 
                            type="text" 
                            id="name_sf2" 
                            name="name_sf2" 
                            value="{{ old('name_sf2', $item->name_sf2) }}"
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('name_sf2') border-rose-500 @enderror"
                            placeholder="Enter item name (SF2)"
                        >
                        @error('name_sf2')
                            <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- SF2 Code -->
                    <div>
                        <label for="code_sf2" class="block text-sm font-semibold text-slate-700 mb-2">
                            Item Code SF2
                        </label>
                        <input 
                            type="text"
                            id="code_sf2" 
                            name="code_sf2" 
                            value="{{ old('code_sf2', $item->code_sf2) }}"
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('code_sf2') border-rose-500 @enderror"
                            placeholder="Enter item code (SF2)"
                        >
                        @error('code_sf2')
                            <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- SF3 Products -->
                    <div id="sf3-products-section" class="md:col-span-2 border border-slate-200 rounded-xl bg-slate-50/70 p-4" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div>
                                <h3 class="text-sm font-semibold text-slate-800">SF3 Products & Quantity</h3>
                                <p class="text-xs text-slate-500">Add one or more product and quantity rows for SF3.</p>
                            </div>
                            <button
                                type="button"
                                id="add-sf3-row"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-blue-200 text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg font-medium text-sm transition-all"
                            >
                                <span>+ Add Product</span>
                            </button>
                        </div>

                        <div id="sf3-product-rows" class="space-y-3">
                            @php
                                $groupedProductItems = $productItems->groupBy('category');
                            @endphp
                            @foreach ($oldSf3Products as $index => $row)
                                <div class="sf3-row grid grid-cols-1 md:grid-cols-12 gap-3 p-3 rounded-lg border border-slate-200 bg-white" data-row-index="{{ $index }}">
                                    <div class="md:col-span-7">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Product</label>
                                        <select
                                            name="sf3_products[{{ $index }}][product]"
                                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                        >
                                            <option value="">Select product</option>
                                            @foreach ($groupedProductItems as $productCategory => $items)
                                                <optgroup label="{{ $productCategory }}">
                                                    @foreach ($items as $pi)
                                                        @php
                                                            $piLabel = $productCategory === 'SF1-SF2' ? ($pi->name_sf2 ?: $pi->name) : $pi->name;
                                                        @endphp
                                                        <option value="{{ $pi->id }}" {{ (string) data_get($row, 'product') === (string) $pi->id ? 'selected' : '' }}>{{ $piLabel }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="md:col-span-4">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Quantity</label>
                                        <input
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            name="sf3_products[{{ $index }}][quantity]"
                                            value="{{ data_get($row, 'quantity') !== '' && data_get($row, 'quantity') !== null ? (float) data_get($row, 'quantity') : '' }}"
                                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                            placeholder="0"
                                        >
                                    </div>

                                    <div class="md:col-span-1 flex items-end">
                                        <button
                                            type="button"
                                            class="remove-sf3-row w-full px-3 py-2.5 border border-rose-200 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg font-medium text-sm transition-all"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @error('sf3_products')
                            <p class="mt-3 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        @error('sf3_products.*.product')
                            <p class="mt-3 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        @error('sf3_products.*.quantity')
                            <p class="mt-3 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Size -->
                    <div>
                        <label for="size" class="block text-sm font-semibold text-slate-700 mb-2">
                            Size
                        </label>
                        <input 
                            type="text" 
                            id="size" 
                            name="size" 
                            value="{{ old('size', $item->size) }}"
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('size') border-rose-500 @enderror"
                            placeholder="e.g. Large"
                        >
                        @error('size')
                            <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    <!-- Machines -->
                    <div class="md:col-span-2">
                        <label for="machine_ids" class="block text-sm font-semibold text-slate-700 mb-2">
                            Machines
                        </label>
                        <p class="text-sm text-slate-500 mb-3">Search and select multiple machines.</p>

                        <div id="machine_ids_group" class="border border-slate-300 rounded-lg p-3 @error('machine_ids') border-rose-500 @enderror">
                            <div id="machine_selected" class="flex flex-wrap gap-2"></div>

                            <div class="mt-3 relative">
                                <input
                                    type="text"
                                    id="machine_search"
                                    autocomplete="off"
                                    placeholder="Type to search machines..."
                                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                >

                                <div id="machine_dropdown" class="hidden absolute z-20 mt-2 w-full bg-white border border-slate-200 rounded-lg max-h-60 overflow-y-auto shadow-subtle"></div>
                            </div>

                            <div id="machine_hidden_inputs"></div>
                        </div>
                        @error('machine_ids')
                            <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        @error('machine_ids.*')
                            <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-slate-700 mb-2">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            id="status" 
                            name="status" 
                            required
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('status') border-rose-500 @enderror"
                        >
                            <option value="1" {{ (string) old('status', (int) $item->status) === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ (string) old('status', (int) $item->status) === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200">
                    <a href="{{ route('admin.items.index') }}" class="px-6 py-3 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 gradient-warning text-white font-semibold rounded-lg hover:shadow-lg transition-all hover:scale-105 flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Update Item</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    (() => {
        const category = document.getElementById('category') || document.querySelector('[name="category"]');
        const nameLabel = document.getElementById('name-label');
        const codeLabel = document.getElementById('code-label');
        const nameInput = document.getElementById('name');
        const codeInput = document.getElementById('code');
        const sf2Name = document.getElementById('name_sf2')?.closest('div');
        const sf2Code = document.getElementById('code_sf2')?.closest('div');
        const storeQuantityField = document.getElementById('store-quantity-field');
        const quantityInput = document.getElementById('quantity');
        const sf3Section = document.getElementById('sf3-products-section');
        const sf3Rows = document.getElementById('sf3-product-rows');
        const addSf3RowBtn = document.getElementById('add-sf3-row');

        if (!category || !nameLabel || !codeLabel || !nameInput || !codeInput || !sf2Name || !sf2Code) return;

        const rawProductItems = @json($productItems);

        const buildProductOptions = (selectedValue = '') => {
            const grouped = rawProductItems.reduce((acc, item) => {
                const categoryKey = item.category || 'Other';
                if (!acc[categoryKey]) acc[categoryKey] = [];
                acc[categoryKey].push(item);
                return acc;
            }, {});

            let html = '<option value="">Select product</option>';
            Object.keys(grouped).forEach((categoryKey) => {
                html += `<optgroup label="${categoryKey}">`;
                grouped[categoryKey].forEach((i) => {
                    const label = categoryKey === 'SF1-SF2' ? (i.name_sf2 || i.name) : i.name;
                    const sel = String(selectedValue) === String(i.id) ? ' selected' : '';
                    html += `<option value="${i.id}"${sel}>${label}</option>`;
                });
                html += '</optgroup>';
            });

            return html;
        };

        const applyPrimaryFieldLabels = (selectedCategory) => {
            const isSf1Sf2 = selectedCategory === 'SF1-SF2';

            nameLabel.innerHTML = `${isSf1Sf2 ? 'Item Name SF1' : 'Item Name'} <span class="text-rose-500">*</span>`;
            codeLabel.innerHTML = `${isSf1Sf2 ? 'Item Code SF1' : 'Item Code'} <span class="text-rose-500">*</span>`;
            nameInput.placeholder = isSf1Sf2
                ? (nameInput.dataset.sf12Placeholder || nameInput.placeholder)
                : (nameInput.dataset.defaultPlaceholder || nameInput.placeholder);
            codeInput.placeholder = isSf1Sf2
                ? (codeInput.dataset.sf12Placeholder || codeInput.placeholder)
                : (codeInput.dataset.defaultPlaceholder || codeInput.placeholder);
        };

        const buildSf3Row = (index) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'sf3-row grid grid-cols-1 md:grid-cols-12 gap-3 p-3 rounded-lg border border-slate-200 bg-white';
            wrapper.dataset.rowIndex = String(index);
            wrapper.innerHTML = `
                <div class="md:col-span-7">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Product</label>
                    <select
                        name="sf3_products[${index}][product]"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    >
                        ${buildProductOptions()}
                    </select>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Quantity</label>
                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        name="sf3_products[${index}][quantity]"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        placeholder="0"
                    >
                </div>
                <div class="md:col-span-1 flex items-end">
                    <button
                        type="button"
                        class="remove-sf3-row w-full px-3 py-2.5 border border-rose-200 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg font-medium text-sm transition-all"
                    >
                        Remove
                    </button>
                </div>
            `;
            return wrapper;
        };

        const updateSf3InputNames = () => {
            if (!sf3Rows) return;
            const rows = Array.from(sf3Rows.querySelectorAll('.sf3-row'));

            rows.forEach((row, index) => {
                row.dataset.rowIndex = String(index);
                const productInput = row.querySelector('[name*="[product]"]');
                const quantityInput = row.querySelector('input[name*="[quantity]"]');

                if (productInput) productInput.name = `sf3_products[${index}][product]`;
                if (quantityInput) quantityInput.name = `sf3_products[${index}][quantity]`;
            });
        };

        const addSf3Row = () => {
            if (!sf3Rows) return;
            const nextIndex = sf3Rows.querySelectorAll('.sf3-row').length;
            sf3Rows.appendChild(buildSf3Row(nextIndex));
        };

        const ensureAtLeastOneSf3Row = () => {
            if (!sf3Rows) return;
            if (sf3Rows.querySelectorAll('.sf3-row').length === 0) {
                sf3Rows.appendChild(buildSf3Row(0));
            }
            updateSf3InputNames();
        };

        const setSf3RequiredState = (isRequired) => {
            if (!sf3Rows) return;
            sf3Rows.querySelectorAll('[name*="[product]"]').forEach((el) => {
                el.required = isRequired;
            });
            sf3Rows.querySelectorAll('input[name*="[quantity]"]').forEach((input) => {
                input.required = isRequired;
            });
        };

        const toggleCategoryFields = () => {
            const selectedCategory = category.value;

            applyPrimaryFieldLabels(selectedCategory);

            if (selectedCategory === 'SF1-SF2') {
                sf2Name.style.display = '';
                sf2Code.style.display = '';
            } else {
                sf2Name.style.display = 'none';
                sf2Code.style.display = 'none';
            }

            if (sf3Section) {
                if (selectedCategory === 'SF3') {
                    sf3Section.style.display = '';
                    ensureAtLeastOneSf3Row();
                    setSf3RequiredState(true);
                } else {
                    sf3Section.style.display = 'none';
                    setSf3RequiredState(false);
                }
            }

            if (storeQuantityField && quantityInput) {
                const isStoreCategory = selectedCategory === 'Store';
                storeQuantityField.style.display = isStoreCategory ? '' : 'none';
            }
        };

        if (addSf3RowBtn) {
            addSf3RowBtn.addEventListener('click', addSf3Row);
        }

        if (sf3Rows) {
            sf3Rows.addEventListener('click', (event) => {
                const target = event.target;
                if (!(target instanceof HTMLElement)) return;

                const removeBtn = target.closest('.remove-sf3-row');
                if (!removeBtn) return;

                const row = removeBtn.closest('.sf3-row');
                if (!row) return;

                row.remove();
                ensureAtLeastOneSf3Row();
                setSf3RequiredState(category.value === 'SF3');
            });
        }

        if (category.tagName === 'SELECT') {
            category.addEventListener('change', toggleCategoryFields);
        }
        toggleCategoryFields();
    })();

    (() => {
        const rawMachines = @json($machines);
        const machines = (rawMachines || []).map((m) => {
            const name = String(m?.name ?? '');
            const code = String(m?.machine_code ?? '');
            const label = code ? `${name} (${code})` : name;

            return {
                id: Number(m?.id),
                name,
                code,
                label,
            };
        });

        const initialSelectedIds = @json($selectedMachineIds);

        const selectedWrap = document.getElementById('machine_selected');
        const searchInput = document.getElementById('machine_search');
        const dropdown = document.getElementById('machine_dropdown');
        const hiddenInputs = document.getElementById('machine_hidden_inputs');

        if (!selectedWrap || !searchInput || !dropdown || !hiddenInputs) return;

        const selected = new Map();

        const normalize = (value) => String(value ?? '').toLowerCase().trim();

        const createHiddenInput = (id) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'machine_ids[]';
            input.value = String(id);
            input.dataset.machineId = String(id);
            return input;
        };

        const renderChip = (machine) => {
            const chip = document.createElement('span');
            chip.className = 'inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700';
            chip.dataset.machineId = String(machine.id);

            const label = document.createElement('span');
            label.textContent = machine.label;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'text-slate-500 hover:text-slate-700 font-semibold leading-none';
            removeBtn.setAttribute('aria-label', 'Remove machine');
            removeBtn.textContent = '×';

            removeBtn.addEventListener('click', () => {
                removeSelected(machine.id);
            });

            chip.appendChild(label);
            chip.appendChild(removeBtn);
            return chip;
        };

        const addSelected = (machine) => {
            const id = String(machine.id);
            if (selected.has(id)) return;

            selected.set(id, machine);
            selectedWrap.appendChild(renderChip(machine));
            hiddenInputs.appendChild(createHiddenInput(machine.id));
        };

        const removeSelected = (id) => {
            const key = String(id);
            if (!selected.has(key)) return;
            selected.delete(key);

            selectedWrap.querySelectorAll(`[data-machine-id="${CSS.escape(key)}"]`).forEach((el) => el.remove());
            hiddenInputs.querySelectorAll(`input[data-machine-id="${CSS.escape(key)}"]`).forEach((el) => el.remove());
        };

        const showDropdown = () => dropdown.classList.remove('hidden');
        const hideDropdown = () => dropdown.classList.add('hidden');

        const renderDropdown = (query) => {
            const q = normalize(query);
            dropdown.innerHTML = '';

            const filtered = machines.filter((m) => {
                if (selected.has(String(m.id))) return false;
                if (!q) return true;
                return normalize(m.label).includes(q) || normalize(m.name).includes(q) || normalize(m.code).includes(q);
            });

            if (filtered.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'px-4 py-3 text-sm text-slate-500';
                empty.textContent = 'No machines found.';
                dropdown.appendChild(empty);
                return;
            }

            filtered.forEach((m) => {
                const option = document.createElement('button');
                option.type = 'button';
                option.className = 'w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 transition-all';
                option.textContent = m.label;
                option.addEventListener('click', () => {
                    addSelected(m);
                    searchInput.value = '';
                    renderDropdown('');
                    searchInput.focus();
                });
                dropdown.appendChild(option);
            });
        };

        // Init with current selection
        (initialSelectedIds || []).forEach((idStr) => {
            const match = machines.find((m) => String(m.id) === String(idStr));
            if (match) addSelected(match);
        });

        searchInput.addEventListener('focus', () => {
            renderDropdown(searchInput.value);
            showDropdown();
        });

        searchInput.addEventListener('input', () => {
            renderDropdown(searchInput.value);
            showDropdown();
        });

        document.addEventListener('click', (e) => {
            const target = e.target;
            if (!(target instanceof Node)) return;
            if (dropdown.contains(target) || searchInput.contains(target) || selectedWrap.contains(target)) return;
            hideDropdown();
        });
    })();

    (() => {
        const form = document.getElementById('item-edit-form');
        if (!form) return;

        const submitButton = form.querySelector('button[type="submit"]');
        const submitLabel = submitButton?.querySelector('span');
        const defaultLabel = submitLabel ? submitLabel.textContent : 'Update Item';

        const clearErrors = () => {
            form.querySelectorAll('.ajax-error-text').forEach((el) => el.remove());
            form.querySelectorAll('.ajax-error-input').forEach((el) => el.classList.remove('ajax-error-input', 'border-rose-500'));
        };

        const setLoading = (loading) => {
            if (!submitButton) return;
            submitButton.disabled = loading;
            submitButton.classList.toggle('opacity-70', loading);
            submitButton.classList.toggle('cursor-not-allowed', loading);
            if (submitLabel) {
                submitLabel.textContent = loading ? 'Saving...' : defaultLabel;
            }
        };

        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 2500,
        };

        const applyFieldErrors = (errors) => {
            Object.entries(errors).forEach(([field, messages]) => {
                const input = (() => {
                    const exact = form.querySelector(`[name="${field}"]`);
                    if (exact) return exact;

                    const base = String(field).split('.')[0];
                    if (base === 'machine_ids') {
                        return document.getElementById('machine_ids_group');
                    }

                    if (base === 'sf3_products') {
                        return document.getElementById('sf3-products-section');
                    }

                    return form.querySelector(`[name="${base}"]`) || form.querySelector(`[name="${base}[]"]`);
                })();
                if (!input) return;

                input.classList.add('ajax-error-input', 'border-rose-500');

                const errorElement = document.createElement('p');
                errorElement.className = 'ajax-error-text mt-2 text-sm text-rose-600';
                errorElement.textContent = Array.isArray(messages) ? messages[0] : String(messages);

                input.insertAdjacentElement('afterend', errorElement);
            });
        };

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearErrors();
            setLoading(true);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: new FormData(form),
                });

                const data = await response.json().catch(() => ({}));

                if (response.ok) {
                    toastr.success((data.message || 'Item updated successfully.') + ' Redirecting to list page...');
                    setTimeout(() => {
                        window.location.href = '{{ route('admin.items.index') }}';
                    }, 2500);
                    return;
                }

                if (response.status === 422 && data.errors) {
                    applyFieldErrors(data.errors);
                    toastr.error(data.message || 'Please fix the highlighted fields.');
                    return;
                }

                toastr.error(data.message || 'Something went wrong. Please try again.');
            } catch (error) {
                toastr.error('Network error. Please try again.');
            } finally {
                setLoading(false);
            }
        });
    })();
</script>
@endpush
