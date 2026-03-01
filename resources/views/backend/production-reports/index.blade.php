@extends('backend.layout.app')

@section('title', 'Production Reports List')

@section('page-title', 'Production Reports Management')

@section('breadcrumb')
    <span class="text-slate-600">Production Reports</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">List</span>
@endsection

@section('content')
<div class="p-6">
    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
        <p class="text-sm text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Header with Add Button -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center">
                        <i data-lucide="file-text" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">All Production Reports</h2>
                        <p class="text-sm text-slate-500">Manage production reports and data</p>
                    </div>
                </div>
                <a href="{{ route('admin.production-reports.create') }}" class="inline-flex items-center gap-2 px-4 py-2 gradient-primary text-white font-semibold rounded-lg hover:shadow-lg transition-all hover:scale-105">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Add New Report</span>
                </a>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mt-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.production-reports.index', ['mode' => 'active']) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium border {{ $mode === 'active' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                        Active
                    </a>
                    <a href="{{ route('admin.production-reports.index', ['mode' => 'deleted']) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium border {{ $mode === 'deleted' ? 'bg-rose-600 text-white border-rose-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                        Deleted
                    </a>
                    <a href="{{ route('admin.production-reports.index', ['mode' => 'all']) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium border {{ $mode === 'all' ? 'bg-slate-700 text-white border-slate-700' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                        All
                    </a>
                </div>

                <form action="{{ route('admin.production-reports.index') }}" method="GET" class="w-full lg:w-auto">
                    <input type="hidden" name="mode" value="{{ $mode }}">
                    <div class="flex items-center gap-2">
                        <div class="relative w-full lg:w-80">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input
                                type="text"
                                name="search"
                                value="{{ $search ?? '' }}"
                                placeholder="Search machine, date, shift..."
                                class="w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all">
                            Search
                        </button>
                        @if(!empty($search))
                        <a href="{{ route('admin.production-reports.index', ['mode' => $mode]) }}" class="px-4 py-2 text-sm font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">
                            Reset
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Production Reports Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Machine</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Slide Size</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Report Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Shift</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Total Set</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Actual Set</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Workman</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Staff</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($productionReports as $report)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-slate-900 font-medium">#{{ $report->id }}</td>
                        <td class="px-6 py-4 text-sm text-slate-900 font-semibold">{{ $report->machine->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $report->slideSize->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-900">{{ $report->report_date ? \Carbon\Carbon::parse($report->report_date)->format('d M Y') : '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-900">{{ $report->shift }}</td>
                        <td class="px-6 py-4 text-sm text-slate-900 font-medium">{{ $report->total_set_shift ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-900 font-medium">{{ $report->actual_set_shift ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-900">{{ $report->workman_count ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-900">{{ $report->staff_count ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $report->status ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                {{ $report->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.production-reports.show', $report) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="View">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.production-reports.edit', $report) }}" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                @if(!$report->is_deleted)
                                    <form action="{{ route('admin.production-reports.destroy', $report) }}" method="POST" class="inline js-swal-delete-form" data-item-name="Report #{{ $report->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <i data-lucide="inbox" class="w-12 h-12 text-slate-300"></i>
                                <p class="text-slate-500">No production reports found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($productionReports->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $productionReports->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
