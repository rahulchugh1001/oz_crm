@extends('backend.layout.app')

@section('title', 'Reject Reasons')

@section('page-title', 'Reject Reasons')

@section('breadcrumb')
    <span class="text-slate-600">Master Data</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Reject Reasons</span>
@endsection

@section('content')
<div class="p-4">
    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
            <p class="text-xs text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="p-4 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl gradient-primary flex items-center justify-center">
                        <i data-lucide="ban" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Reject Reasons</h2>
                        <p class="text-xs text-slate-500">Manage reject reason master data</p>
                    </div>
                </div>
                <a href="{{ route('admin.reject-reasons.create') }}" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs gradient-primary text-white font-semibold rounded-lg hover:shadow-lg transition-all">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Add Reject Reason</span>
                </a>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mt-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.reject-reasons.index', ['mode' => 'active']) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $mode === 'active' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                        Active
                    </a>
                    <a href="{{ route('admin.reject-reasons.index', ['mode' => 'deleted']) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $mode === 'deleted' ? 'bg-rose-600 text-white border-rose-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                        Deleted
                    </a>
                    <a href="{{ route('admin.reject-reasons.index', ['mode' => 'all']) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $mode === 'all' ? 'bg-slate-700 text-white border-slate-700' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                        All
                    </a>
                </div>

                <form action="{{ route('admin.reject-reasons.index') }}" method="GET" class="w-full lg:w-auto">
                    <input type="hidden" name="mode" value="{{ $mode }}">
                    <div class="flex items-center gap-2">
                        <div class="relative w-full lg:w-80">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input
                                type="text"
                                name="search"
                                value="{{ $search ?? '' }}"
                                placeholder="Search name..."
                                class="w-full pl-10 pr-3 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all">Search</button>
                        @if(!empty($search))
                            <a href="{{ route('admin.reject-reasons.index', ['mode' => $mode]) }}" class="px-3 py-1.5 text-xs font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">Reset</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Category</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Used Count</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($rejectReasons as $reason)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-slate-900 font-medium">#{{ $reason->id }}</td>
                            <td class="px-4 py-3 text-slate-900 font-semibold">{{ $reason->name }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ ($reason->category ?? 'SF1') === 'SF2' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $reason->category ?? 'SF1' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $reason->status ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $reason->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                                    {{ (int) ($reason->usage_count ?? 0) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.reject-reasons.show', $reason) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="View Usage">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <a href="{{ route('admin.reject-reasons.edit', $reason) }}" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit">
                                        <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                    </a>
                                    @if(!$reason->is_deleted)
                                        <form action="{{ route('admin.reject-reasons.destroy', $reason) }}" method="POST" class="inline js-swal-delete-form" data-item-name="{{ $reason->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Delete">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <i data-lucide="inbox" class="w-10 h-10 text-slate-300"></i>
                                    <p class="text-xs text-slate-500">No reject reasons found</p>
                                    <a href="{{ route('admin.reject-reasons.create') }}" class="text-blue-600 hover:text-blue-700 font-medium">Create your first reject reason</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rejectReasons->hasPages())
            <div class="px-4 py-3 border-t border-slate-200">
                {{ $rejectReasons->links() }}
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
                const itemName = form.getAttribute('data-item-name') || 'this item';

                const result = await Swal.fire({
                    title: 'Delete reject reason?',
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
