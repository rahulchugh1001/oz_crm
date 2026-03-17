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
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">
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
                        <label for="code" class="block text-sm font-semibold text-slate-700 mb-2">
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

                    <!-- Weight -->
                    <div>
                        <label for="weight" class="block text-sm font-semibold text-slate-700 mb-2">
                            Weight <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="weight" 
                            name="weight" 
                            value="{{ old('weight', $item->weight) }}"
                            required
                            min="0"
                            step="0.01"
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('weight') border-rose-500 @enderror"
                            placeholder="0.00"
                        >
                        @error('weight')
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
