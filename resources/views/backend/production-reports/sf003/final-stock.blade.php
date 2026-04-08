@extends('backend.layout.app')

@php
    $lineFilterOptions = [
        'all' => 'All Lines',
        'l1' => 'Line 1',
        'l2' => 'Line 2',
        'l3' => 'Line 3',
        'l4' => 'Line 4',
        'l5' => 'Line 5',
        'l6' => 'Line 6',
    ];
@endphp

@section('title', 'Assemble SF3 Final Stock')

@section('page-title', 'Assemble SF3 Final Stock')

@section('breadcrumb')
    <span class="text-slate-600">Production Reports</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">Assemble SF3</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Final Stock</span>
@endsection

@section('content')
<div class="p-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">SF3 Final Stock List</h2>
                <p class="text-sm text-slate-500 mt-1">Data source: sf3_production_reports table</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @foreach($lineFilterOptions as $lineKey => $lineLabel)
                    <a href="{{ route('admin.production-reports.sf003.final-stock', ['line' => $lineKey]) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold uppercase tracking-wider transition-all {{ $selectedLine === $lineKey ? 'text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}" @if($selectedLine === $lineKey) style="background: linear-gradient(to right, #141d30, #2d3a52);" @endif>
                        {{ $lineLabel }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-[13px]">
                <thead class="border-b border-slate-200" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    <tr>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">ID</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Line</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Item</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Report Date</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Shift</th>
                        <th class="px-4 py-2 text-right text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Actual / Set / Shift</th>
                        <th class="px-4 py-2 text-right text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Total / Set / Shift</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Created By</th>
                        <th class="px-4 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($finalStockReports as $report)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-900 font-medium">#{{ $report->id }}</td>
                        <td class="px-4 py-3 text-slate-700">
                            @if($report->sf3_process === 'line_1')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-indigo-50 text-indigo-700">Line 1</span>
                            @elseif($report->sf3_process === 'line_2')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-cyan-50 text-cyan-700">Line 2</span>
                            @elseif($report->sf3_process === 'line_3')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">Line 3</span>
                            @elseif($report->sf3_process === 'line_4')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700">Line 4</span>
                            @elseif($report->sf3_process === 'line_5')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700">Line 5</span>
                            @elseif($report->sf3_process === 'line_6')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-teal-50 text-teal-700">Line 6</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ ($report->item_code ?? '-') . ' - ' . ($report->item_name ?? '-') }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $report->report_date ? \Carbon\Carbon::parse($report->report_date)->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ ucfirst($report->shift ?? '-') }}</td>
                        <td class="px-4 py-3 text-right text-slate-700">{{ number_format((float) ($report->actual_set_shift ?? 0), 0) }}</td>
                        <td class="px-4 py-3 text-right text-slate-700">{{ number_format((float) ($report->total_set_shift ?? 0), 0) }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $report->created_by_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.production-reports.sf003.final-stock.show', ['encryptedId' => \Illuminate\Support\Facades\Crypt::encryptString((string) $report->id)]) }}" class="inline-flex items-center justify-center rounded-lg border border-blue-200 bg-blue-50 p-2 text-blue-700 transition-all hover:bg-blue-100" title="View Details">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-10 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="package-search" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">No final stock records found</p>
                                    <p class="text-sm text-slate-500 mt-1">SF3 production rows will appear here once reports are submitted.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection