@extends('backend.layout.app')

@section('title', 'View Item')

@section('page-title', 'Item Details')

@section('breadcrumb')
    <span class="text-slate-600">Items</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">View</span>
@endsection

@section('content')
<div class="p-6">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
            <!-- Header -->
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center">
                            <i data-lucide="eye" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Item Details</h2>
                            <p class="text-sm text-slate-500">View item information</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.items.edit', $item) }}" class="px-4 py-2 gradient-warning text-white font-semibold rounded-lg hover:shadow-lg transition-all hover:scale-105 flex items-center gap-2">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                            <span>Edit</span>
                        </a>
                        <a href="{{ route('admin.items.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition-all">
                            Back
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-6">
                <!-- ID & Status -->
                <div class="flex items-center justify-between pb-6 border-b border-slate-200">
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Item ID</p>
                        <p class="text-2xl font-bold text-slate-900">#{{ $item->id }}</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $item->status ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                            {{ $item->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-500 mb-2">Item Name</label>
                        <p class="text-lg font-semibold text-slate-900">{{ $item->name }}</p>
                    </div>

                    <!-- Code -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-500 mb-2">Item Code</label>
                        <p class="text-slate-700 leading-relaxed">{{ $item->code }}</p>
                    </div>

                    <!-- SF2 Name -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-500 mb-2">Item Name SF2</label>
                        <p class="text-slate-700 leading-relaxed">{{ $item->name_sf2 ?: '-' }}</p>
                    </div>

                    <!-- SF2 Code -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-500 mb-2">Item Code SF2</label>
                        <p class="text-slate-700 leading-relaxed">{{ $item->code_sf2 ?: '-' }}</p>
                    </div>
                </div>

                <!-- Size & Weight Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Size -->
                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                                <i data-lucide="package" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-blue-600">Size</p>
                                <p class="text-2xl font-bold text-blue-900">{{ $item->size ?: '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Weight -->
                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center">
                                <i data-lucide="scale" class="w-6 h-6 text-emerald-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-emerald-600">Weight</p>
                                <p class="text-2xl font-bold text-emerald-900">{{ number_format((float) $item->weight, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-200">
                    <div>
                        <label class="block text-sm font-semibold text-slate-500 mb-2">Created At</label>
                        <p class="text-slate-700">{{ $item->created_at->format('F d, Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-500 mb-2">Last Updated</label>
                        <p class="text-slate-700">{{ $item->updated_at->format('F d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="p-6 border-t border-slate-200 bg-slate-50">
                <div class="flex items-center justify-between">
                    <form action="{{ route('admin.items.destroy', $item) }}" method="POST" class="js-swal-delete-form" data-item-name="{{ $item->name }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-rose-600 text-white font-semibold rounded-lg hover:bg-rose-700 transition-all flex items-center gap-2">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            <span>Delete Item</span>
                        </button>
                    </form>
                    <a href="{{ route('admin.items.index') }}" class="px-6 py-2 gradient-primary text-white font-semibold rounded-lg hover:shadow-lg transition-all hover:scale-105 flex items-center gap-2">
                        <i data-lucide="list" class="w-4 h-4"></i>
                        <span>View All Items</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    (() => {
        const deleteForms = document.querySelectorAll('.js-swal-delete-form');
        if (!deleteForms.length || typeof Swal === 'undefined') return;

        deleteForms.forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const itemName = form.getAttribute('data-item-name') || 'this item';

                const result = await Swal.fire({
                    title: 'Delete item?',
                    text: `Are you sure you want to delete ${itemName}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel',
                });

                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    })();
</script>
@endpush
