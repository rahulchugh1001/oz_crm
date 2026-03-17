@extends('backend.layout.app')

@section('title', 'Edit Machine')

@section('page-title', 'Edit Machine')

@section('breadcrumb')
    <span class="text-slate-600">Machines</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Edit</span>
@endsection

@section('content')
<div class="p-6">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl gradient-warning flex items-center justify-center">
                        <i data-lucide="edit" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Edit Machine</h2>
                        <p class="text-sm text-slate-500">Update machine details</p>
                    </div>
                </div>
            </div>

            <form id="machine-edit-form" action="{{ route('admin.machines.update', $machine) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">
                        Machine Name <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $machine->name) }}"
                        required
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('name') border-rose-500 @enderror"
                        placeholder="Enter machine name"
                    >
                    @error('name')
                        <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="machine_code" class="block text-sm font-semibold text-slate-700 mb-2">
                        Machine Code <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="machine_code"
                        name="machine_code"
                        value="{{ old('machine_code', $machine->machine_code) }}"
                        required
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('machine_code') border-rose-500 @enderror"
                        placeholder="Enter unique machine code"
                    >
                    @error('machine_code')
                        <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="rf_set" class="block text-sm font-semibold text-slate-700 mb-2">
                        RF Set
                    </label>
                    <select
                        id="rf_set"
                        name="rf_set"
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('rf_set') border-rose-500 @enderror"
                    >
                        @php($selectedRfSet = old('rf_set', $machine->rf_set))
                        <option value="">Select RF set</option>
                        @foreach (\App\Models\Machine::RF_SET_OPTIONS as $option)
                            <option value="{{ $option }}" {{ $selectedRfSet === $option ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                    @error('rf_set')
                        <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                <div class="md:col-span-2">
                    <label for="weight_capacity" class="block text-sm font-semibold text-slate-700 mb-2">
                        Weight Capacity
                    </label>
                    <p class="text-sm text-slate-500 mb-3">Search and select multiple weight capacities.</p>

                    <div id="weight_capacity_group" class="border border-slate-300 rounded-lg p-3 @error('weight_capacity') border-rose-500 @enderror">
                        <div id="weight_capacity_selected" class="flex flex-wrap gap-2"></div>

                        <div class="mt-3 relative">
                            <input
                                type="text"
                                id="weight_capacity_search"
                                autocomplete="off"
                                placeholder="Type to search weight capacities..."
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            >

                            <div id="weight_capacity_dropdown" class="hidden absolute z-20 mt-2 w-full bg-white border border-slate-200 rounded-lg max-h-60 overflow-y-auto shadow-subtle"></div>
                        </div>

                        <div id="weight_capacity_hidden_inputs"></div>
                    </div>
                    @error('weight_capacity')
                        <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    @error('weight_capacity.*')
                        <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

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
                        <option value="1" {{ (string) old('status', (int) $machine->status) === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ (string) old('status', (int) $machine->status) === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200">
                    <a href="{{ route('admin.machines.index') }}" class="px-6 py-3 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 gradient-warning text-white font-semibold rounded-lg hover:shadow-lg transition-all hover:scale-105 flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Update Machine</span>
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
        const form = document.getElementById('machine-edit-form');
        if (!form) return;

        const submitButton = form.querySelector('button[type="submit"]');
        const submitLabel = submitButton?.querySelector('span');
        const defaultLabel = submitLabel ? submitLabel.textContent : 'Update Machine';

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
                const input = form.querySelector(`[name="${field}"]`);
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
                    toastr.success((data.message || 'Machine updated successfully.') + ' Redirecting to list page...');
                    setTimeout(() => {
                        window.location.href = '{{ route('admin.machines.index') }}';
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

    (() => {
        const rawCapacities = @json($weightCapacities);
        const capacities = (rawCapacities || []).map((c) => {
            return {
                id: String(c.id),
                name: String(c.name),
                label: String(c.name),
            };
        });
        const initialSelected = @json(collect(old('weight_capacity', $machine->weight_capacities ? $machine->weight_capacities->pluck('name')->toArray() : []))->toArray());

        const selectedWrap = document.getElementById('weight_capacity_selected');
        const searchInput = document.getElementById('weight_capacity_search');
        const dropdown = document.getElementById('weight_capacity_dropdown');
        const hiddenInputs = document.getElementById('weight_capacity_hidden_inputs');
        if (!selectedWrap || !searchInput || !dropdown || !hiddenInputs) return;

        const selected = new Map();

        const normalize = (value) => String(value ?? '').toLowerCase().trim();

        const createHiddenInput = (name) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'weight_capacity[]';
            input.value = name;
            input.dataset.capacityName = name;
            return input;
        };

        const renderChip = (capacity) => {
            const chip = document.createElement('span');
            chip.className = 'inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-blue-200 bg-blue-50 text-xs text-blue-800 font-semibold';
            chip.dataset.capacityName = capacity.name;

            const label = document.createElement('span');
            label.textContent = capacity.label;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'text-blue-400 hover:text-blue-700 font-semibold leading-none';
            removeBtn.setAttribute('aria-label', 'Remove weight capacity');
            removeBtn.textContent = '×';
            removeBtn.addEventListener('click', () => {
                removeSelected(capacity.name);
            });

            chip.appendChild(label);
            chip.appendChild(removeBtn);
            return chip;
        };

        const addSelected = (capacity) => {
            const name = String(capacity.name);
            if (selected.has(name)) return;
            selected.set(name, capacity);
            selectedWrap.appendChild(renderChip(capacity));
            hiddenInputs.appendChild(createHiddenInput(capacity.name));
        };

        const removeSelected = (name) => {
            if (!selected.has(name)) return;
            selected.delete(name);
            selectedWrap.querySelectorAll(`[data-capacity-name="${CSS.escape(name)}"]`).forEach((el) => el.remove());
            hiddenInputs.querySelectorAll(`input[data-capacity-name="${CSS.escape(name)}"]`).forEach((el) => el.remove());
        };

        const showDropdown = () => dropdown.classList.remove('hidden');
        const hideDropdown = () => dropdown.classList.add('hidden');

        const renderDropdown = (query) => {
            const q = normalize(query);
            dropdown.innerHTML = '';
            const filtered = capacities.filter((c) => {
                if (selected.has(String(c.name))) return false;
                if (!q) return true;
                return normalize(c.label).includes(q);
            });
            if (filtered.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'px-4 py-3 text-sm text-slate-500';
                empty.textContent = 'No weight capacities found.';
                dropdown.appendChild(empty);
                return;
            }
            filtered.forEach((c) => {
                const option = document.createElement('button');
                option.type = 'button';
                option.className = 'w-full text-left px-4 py-3 text-sm text-blue-800 hover:bg-blue-50 transition-all';
                option.textContent = c.label;
                option.addEventListener('click', () => {
                    addSelected(c);
                    searchInput.value = '';
                    renderDropdown('');
                    searchInput.focus();
                });
                dropdown.appendChild(option);
            });
        };

        // Init with old() selection
        (initialSelected || []).forEach((name) => {
            const match = capacities.find((c) => String(c.name) === String(name));
            if (match) addSelected(match);
        });

        let dropdownShouldStay = false;
        const openDropdown = () => {
            renderDropdown(searchInput.value);
            showDropdown();
        };
        searchInput.addEventListener('focus', openDropdown);
        searchInput.addEventListener('input', openDropdown);

        // Keep dropdown open while typing or clicking
        dropdown.addEventListener('mousedown', () => {
            dropdownShouldStay = true;
        });
        searchInput.addEventListener('blur', () => {
            setTimeout(() => {
                if (!dropdownShouldStay) hideDropdown();
                dropdownShouldStay = false;
            }, 150);
        });

        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target) && e.target !== searchInput) {
                hideDropdown();
            }
        });
    })();
</script>
@endpush
