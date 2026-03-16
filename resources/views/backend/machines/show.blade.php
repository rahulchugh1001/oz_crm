@extends('backend.layout.app')

@section('title', 'View Machine')

@section('page-title', 'Machine Details')

@section('breadcrumb')
    <span class="text-slate-600">Machines</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">View</span>
@endsection

@section('content')
<div class="p-6">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center">
                            <i data-lucide="eye" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Machine Details</h2>
                            <p class="text-sm text-slate-500">View machine information</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.machines.edit', $machine) }}" class="px-4 py-2 gradient-warning text-white font-semibold rounded-lg hover:shadow-lg transition-all hover:scale-105 flex items-center gap-2">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                            <span>Edit</span>
                        </a>
                        <a href="{{ route('admin.machines.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition-all">
                            Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="flex items-center justify-between pb-6 border-b border-slate-200">
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Machine ID</p>
                        <p class="text-2xl font-bold text-slate-900">#{{ $machine->id }}</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $machine->status ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                            {{ $machine->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-500 mb-2">Machine Name</label>
                        <p class="text-lg font-semibold text-slate-900">{{ $machine->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-500 mb-2">Machine Code</label>
                        <p class="text-slate-700 leading-relaxed">{{ $machine->machine_code }}</p>
                    </div>

                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                                <i data-lucide="radio" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-blue-600">RF Set</p>
                                <p class="text-2xl font-bold text-blue-900">{{ $machine->rf_set ?: '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-500 mb-2">Weight Capacity</label>
                        <p class="text-slate-700 leading-relaxed">{{ $machine->weight_capacity ?: '-' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-200">
                    <div>
                        <label class="block text-sm font-semibold text-slate-500 mb-2">Created At</label>
                        <p class="text-slate-700">{{ $machine->created_at->format('F d, Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-500 mb-2">Last Updated</label>
                        <p class="text-slate-700">{{ $machine->updated_at->format('F d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-slate-200 bg-slate-50">
                <div class="flex items-center justify-between">
                    <form action="{{ route('admin.machines.destroy', $machine) }}" method="POST" class="js-swal-delete-form" data-machine-name="{{ $machine->name }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-rose-600 text-white font-semibold rounded-lg hover:bg-rose-700 transition-all flex items-center gap-2">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            <span>Delete Machine</span>
                        </button>
                    </form>
                    <a href="{{ route('admin.machines.index') }}" class="px-6 py-2 gradient-primary text-white font-semibold rounded-lg hover:shadow-lg transition-all hover:scale-105 flex items-center gap-2">
                        <i data-lucide="list" class="w-4 h-4"></i>
                        <span>View All Machines</span>
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
                const machineName = form.getAttribute('data-machine-name') || 'this machine';

                const result = await Swal.fire({
                    title: 'Delete machine?',
                    text: `Are you sure you want to delete ${machineName}?`,
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
