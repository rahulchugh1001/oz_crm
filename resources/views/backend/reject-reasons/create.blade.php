@extends('backend.layout.app')

@section('title', 'Add Reject Reason')

@section('page-title', 'Add Reject Reason')

@section('breadcrumb')
    <a href="{{ route('admin.reject-reasons.index') }}" class="text-slate-600 hover:text-slate-900">Reject Reasons</a>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Add</span>
@endsection

@section('content')
<div class="p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="p-4 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl gradient-primary flex items-center justify-center">
                    <i data-lucide="plus" class="w-4 h-4 text-white"></i>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Create Reject Reason</h2>
                    <p class="text-xs text-slate-500">Add a new reject reason</p>
                </div>
            </div>
        </div>

        <form id="reject-reason-create-form" action="{{ route('admin.reject-reasons.store') }}" method="POST" class="p-4">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Name <span class="text-rose-600">*</span></label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter reject reason name"
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Category <span class="text-rose-600">*</span></label>
                    <select
                        name="category"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="SF1" {{ old('category', 'SF1') === 'SF1' ? 'selected' : '' }}>SF1</option>
                        <option value="SF2" {{ old('category') === 'SF2' ? 'selected' : '' }}>SF2</option>
                        <option value="Both" {{ old('category') === 'Both' ? 'selected' : '' }}>Both</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status-toggle-create" class="block text-xs font-medium text-slate-700 mb-1">Status</label>
                    <div class="px-3 py-2 rounded-lg">
                        <input
                            type="hidden"
                            name="status"
                            value="0"
                        >
                        <label class="status-toggle" for="status-toggle-create">
                            <input
                                type="checkbox"
                                id="status-toggle-create"
                                name="status"
                                value="1"
                                data-status-toggle
                                data-status-text-id="status-toggle-text-create"
                                {{ old('status', '1') == '1' ? 'checked' : '' }}
                            >
                            <span class="status-toggle-track">
                                <span class="status-toggle-thumb"></span>
                            </span>
                            <span id="status-toggle-text-create" class="status-toggle-text">Active</span>
                        </label>
                    </div>
                    @error('status')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 mt-6">
                <a href="{{ route('admin.reject-reasons.index') }}" class="px-4 py-2 text-xs font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">Cancel</a>
                <button id="reject-reason-create-submit" type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    (() => {
        const form = document.getElementById('reject-reason-create-form');
        const submitButton = document.getElementById('reject-reason-create-submit');
        if (!form || !submitButton) return;

        form.addEventListener('submit', () => {
            submitButton.disabled = true;
            submitButton.classList.add('opacity-70', 'cursor-not-allowed');
            submitButton.textContent = 'Saving...';
        });
    })();
</script>
@endpush
