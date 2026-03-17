@extends('backend.layout.app')

@section('title', 'Add Weight Capacity')

@section('page-title', 'Add Weight Capacity')

@section('breadcrumb')
    <a href="{{ route('admin.weight-capacities.index') }}" class="text-slate-600 hover:text-slate-900">Weight Capacities</a>
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
                    <h2 class="text-base font-semibold text-slate-900">Create Weight Capacity</h2>
                    <p class="text-xs text-slate-500">Add a new weight capacity option</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.weight-capacities.store') }}" method="POST" class="p-4">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Name <span class="text-rose-600">*</span></label>
                    <input
                        type="text"
                        name="name"
                        maxlength="50"
                        value="{{ old('name') }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g. 45kg-STD"
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Status</label>
                    <select
                        name="status"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 mt-6">
                <a href="{{ route('admin.weight-capacities.index') }}" class="px-4 py-2 text-xs font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">Cancel</a>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush

