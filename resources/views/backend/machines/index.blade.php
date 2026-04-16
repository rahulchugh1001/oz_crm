@extends('backend.layout.app')

@section('title', 'Machines List')

@section('page-title', 'Machines Management')

@section('breadcrumb')
    <span class="text-slate-600">Machines</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">List</span>
@endsection

@section('content')
<div class="p-4">
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
        <p class="text-sm text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="p-4 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                        <i data-lucide="cog" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">All Machines</h2>
                        <p class="text-sm text-slate-500">Manage your machine master data</p>
                    </div>
                </div>
                <a href="{{ route('admin.machines.create') }}" class="inline-flex items-center gap-2 px-3 py-1.5 text-white text-sm font-semibold rounded-lg hover:shadow-lg transition-all hover:scale-105" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Add New Machine</span>
                </a>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mt-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.machines.index', ['mode' => 'active']) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium border {{ $mode === 'active' ? 'text-white border-transparent' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}" @if($mode === 'active') style="background: linear-gradient(to right, #141d30, #2d3a52);" @endif>
                        Active
                    </a>
                    <a href="{{ route('admin.machines.index', ['mode' => 'deleted']) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium border {{ $mode === 'deleted' ? 'bg-rose-600 text-white border-rose-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                        Deleted
                    </a>
                    <a href="{{ route('admin.machines.index', ['mode' => 'all']) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium border {{ $mode === 'all' ? 'bg-slate-700 text-white border-slate-700' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                        All
                    </a>
                </div>

                <form action="{{ route('admin.machines.index') }}" method="GET" class="w-full lg:w-auto">
                    <input type="hidden" name="mode" value="{{ $mode }}">
                    <div class="flex items-center gap-2">
                        <div class="relative w-full lg:w-80">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input
                                type="text"
                                name="search"
                                value="{{ $search ?? '' }}"
                                placeholder="Search name, machine code, RF set..."
                                class="w-full pl-10 pr-3 py-1.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <button type="submit" class="px-3 py-1.5 text-sm font-medium text-white rounded-lg transition-all" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                            Search
                        </button>
                        @if(!empty($search))
                        <a href="{{ route('admin.machines.index', ['mode' => $mode]) }}" class="px-3 py-1.5 text-sm font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">
                            Reset
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-slate-200" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    <tr>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">ID</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Name</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Machine Code</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">RF Set</th>
                        <th class="px-4 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Is Ballcage</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Status</th>
                        <th class="px-4 py-2 text-right text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($machines as $machine)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-xs text-slate-900 font-medium">#{{ $machine->id }}</td>
                        <td class="px-4 py-3 text-xs text-slate-900 font-semibold">{{ $machine->name }}</td>
                        <td class="px-4 py-3 text-xs text-slate-600">{{ $machine->machine_code }}</td>
                        <td class="px-4 py-3 text-xs text-slate-900">{{ $machine->rf_set ?: '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($machine->is_ballcage)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-700">
                                    <i data-lucide="check" class="w-3 h-3 mr-1"></i> Yes
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500">No</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $machine->status ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                {{ $machine->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.machines.show', $machine) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="View">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.machines.edit', $machine) }}" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                @if(!$machine->is_deleted)
                                    <form action="{{ route('admin.machines.destroy', $machine) }}" method="POST" class="inline js-swal-delete-form" data-machine-name="{{ $machine->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <i data-lucide="inbox" class="w-12 h-12 text-slate-300"></i>
                                <p class="text-slate-500">No machines found</p>
                                <a href="{{ route('admin.machines.create') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                    Create your first machine
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($machines->hasPages())
        <div class="px-4 py-3 border-t border-slate-200">
            {{ $machines->links() }}
        </div>
        @endif
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
