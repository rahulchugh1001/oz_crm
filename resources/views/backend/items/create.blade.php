@extends('backend.layout.app')

@section('title', 'Create Item')

@section('page-title', 'Create New Item')

@section('breadcrumb')
    <span class="text-slate-600">Items</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Create</span>
@endsection

@section('content')
<div class="p-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
            <!-- Header -->
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center">
                        <i data-lucide="plus-circle" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Add New Item</h2>
                        <p class="text-sm text-slate-500">Fill in the details below</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form id="item-create-form" action="{{ route('admin.items.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">
                        Item Name <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
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
                        value="{{ old('code') }}"
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

                <!-- Size & Weight -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Size -->
                    <div>
                        <label for="size" class="block text-sm font-semibold text-slate-700 mb-2">
                            Size
                        </label>
                        <input 
                            type="text" 
                            id="size" 
                            name="size" 
                            value="{{ old('size') }}"
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
                            value="{{ old('weight', 0) }}"
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
                        <option value="1" {{ old('status', '1') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200">
                    <a href="{{ route('admin.items.index') }}" class="px-6 py-3 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 gradient-primary text-white font-semibold rounded-lg hover:shadow-lg transition-all hover:scale-105 flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Create Item</span>
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
        const form = document.getElementById('item-create-form');
        if (!form) return;

        const submitButton = form.querySelector('button[type="submit"]');
        const submitLabel = submitButton?.querySelector('span');
        const defaultLabel = submitLabel ? submitLabel.textContent : 'Create Item';

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
                    toastr.success((data.message || 'Item created successfully.') + ' Redirecting to list page...');
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
