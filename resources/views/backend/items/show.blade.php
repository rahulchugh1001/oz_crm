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

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-500 mb-2">Category</label>
                        <p class="text-slate-700 leading-relaxed">{{ $item->category ?: '-' }}</p>
                    </div>

                    @if ($item->category !== 'SF3')
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
                    @endif
                </div>

                <!-- Size & Weight Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 {{ $item->category === 'Store' ? 'lg:grid-cols-3' : '' }} gap-6">
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
                                <p class="text-2xl font-bold text-emerald-900">{{ $item->weight !== null ? (int) $item->weight : '-' }}</p>
                            </div>
                        </div>
                    </div>

                    @if ($item->category === 'Store')
                        <div class="p-4 bg-amber-50 rounded-xl border border-amber-100">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center">
                                    <i data-lucide="hash" class="w-6 h-6 text-amber-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-amber-600">Quantity</p>
                                    <p class="text-2xl font-bold text-amber-900">{{ $item->quantity !== null ? (int) $item->quantity : '-' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                @if ($item->category === 'SF3')
                    <div class="pt-6 border-t border-slate-200">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                <i data-lucide="boxes" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">SF3 Products</p>
                                <p class="text-sm text-slate-500">Product and quantity rows linked to this item</p>
                            </div>
                        </div>

                        @if ($item->sf3Products->count() === 0)
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                                <p class="text-sm text-slate-600">No SF3 products added.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto rounded-xl border border-slate-200">
                                <table class="min-w-full divide-y divide-slate-200 bg-white">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Product</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        @foreach ($item->sf3Products as $sf3Product)
                                            @php
                                                $productItem = $sf3Product->productItem;
                                                $productLabel = $productItem
                                                    ? ($productItem->category === 'SF1-SF2' ? ($productItem->name_sf2 ?: $productItem->name) : $productItem->name)
                                                    : $sf3Product->product;
                                            @endphp
                                            <tr>
                                                <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $productLabel }}</td>
                                                <td class="px-4 py-3 text-sm text-slate-700">{{ (float) $sf3Product->quantity }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Machines -->
                <div class="pt-6 border-t border-slate-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                            <i data-lucide="cpu" class="w-5 h-5 text-slate-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Machines</p>
                            <p class="text-sm text-slate-500">Machines linked to this item</p>
                        </div>
                    </div>

                    @if ($item->machines->count() === 0)
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                            <p class="text-sm text-slate-600">No machines linked.</p>
                        </div>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach ($item->machines as $machine)
                                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-r-4 {{ $machine->status ? 'border-r-emerald-500' : 'border-r-rose-500' }} border-slate-200 bg-white text-sm text-slate-700" title="{{ $machine->status ? 'Active' : 'Inactive' }}">
                                    <span class="font-semibold">{{ $machine->name }}</span>
                                    <span class="text-slate-500">({{ $machine->machine_code }})</span>
                                    @if ($machine->pivot && $machine->pivot->created_at)
                                        <span class="text-xs text-slate-400" title="Linked at">
                                            <i data-lucide="clock" class="inline w-3 h-3 mr-1"></i>
                                            {{ $machine->pivot->created_at->format('d M Y H:i') }}
                                        </span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    @endif
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
