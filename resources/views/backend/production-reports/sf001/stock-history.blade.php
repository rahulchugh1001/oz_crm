@extends('backend.layout.app')

@section('title', 'SF001 Stock - Production History')

@section('page-title', 'SF001 Process - Production History')

@section('breadcrumb')
    <span class="text-slate-600">SF001</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <a href="{{ route('admin.production-reports.sf001.stock') }}" class="text-slate-600 hover:text-slate-900">Stock</a>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">History</span>
@endsection

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center">
                        <i data-lucide="history" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Production History - {{ $item->name }}</h2>
                        <p class="text-sm text-slate-500">Item Code: <span class="font-medium">{{ $item->code }}</span> | Size: <span class="font-medium">{{ $item->size }}</span></p>
                    </div>
                </div>
                <a href="{{ route('admin.production-reports.sf001.stock') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to Stock
                </a>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">Production Report History</h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-500">Total Records:</span>
                    <span class="text-sm font-semibold text-slate-900">{{ $history->count() }}</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            #
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Report Date
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Shift
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Machine
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Actual Set/Shift
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($history as $index => $record)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-slate-700">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                <span class="text-sm font-medium text-slate-900">{{ \Carbon\Carbon::parse($record->report_date)->format('M d, Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $record->shift === 'Morning' ? 'bg-amber-50 text-amber-700' : 'bg-indigo-50 text-indigo-700' }}">
                                <i data-lucide="{{ $record->shift === 'Morning' ? 'sun' : 'moon' }}" class="w-3 h-3 mr-1"></i>
                                {{ $record->shift }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i data-lucide="cog" class="w-4 h-4 text-slate-400"></i>
                                <span class="text-sm text-slate-700">{{ $record->machine_name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-green-50 text-green-700">
                                <i data-lucide="package-check" class="w-4 h-4"></i>
                                <span class="text-sm font-semibold">{{ number_format($record->actual_set_shift, 2) }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">No production history found</p>
                                    <p class="text-sm text-slate-500 mt-1">This item has no production records yet</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($history->count() > 0)
        <div class="p-6 border-t border-slate-200 bg-slate-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-slate-600">
                    <i data-lucide="info" class="w-4 h-4 inline-block mr-1"></i>
                    Showing all production records for this item
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-sm">
                        <span class="text-slate-600">Total Production Quantity:</span>
                        <span class="ml-2 font-semibold text-slate-900">{{ number_format($history->sum('actual_set_shift'), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
