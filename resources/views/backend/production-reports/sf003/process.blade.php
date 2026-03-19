@extends('backend.layout.app')

@php
    $activeTab = strtolower((string) request()->query('tab', 'production'));
    $activeTab = in_array($activeTab, ['stock', 'production'], true) ? $activeTab : 'production';
    $lineBadgeLabel = $lineLabel . ' Production';
    $stockCount = $acceptedTransfers->count();
    $addProductionUrl = route('admin.production-reports.sf003.production-report', ['line' => $requestedLine]);
@endphp

@section('title', 'Assemble SF3 ' . $lineLabel . ' Process')

@section('page-title', 'Assemble SF3 ' . $lineLabel . ' Process Management')

@section('breadcrumb')
    <span class="text-slate-600">Production Reports</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">Assemble SF3</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">{{ $lineLabel }} Process</span>
@endsection

@section('content')
<div class="p-6">
    @if(session('success'))
    <div class="mb-4 p-3 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-3 rounded-lg border border-rose-200 bg-rose-50 text-rose-800 text-sm">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Assemble SF3 {{ $lineLabel }} Process</h2>
                        <p class="text-sm text-slate-500">
                            @if($activeTab === 'stock')
                                Accepted stock list for {{ $lineBadgeLabel }}
                            @else
                                Production list for {{ $lineBadgeLabel }}
                            @endif
                        </p>
                    </div>
                    @if($activeTab === 'stock')
                    <div class="text-sm">
                        <span class="text-slate-500">Total Records:</span>
                        <span class="ml-1 font-semibold text-slate-900">{{ $stockCount }}</span>
                    </div>
                    @else
                    <a href="{{ $addProductionUrl }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition-colors" title="Add Production">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Add Production
                    </a>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <a href="{{ route('admin.production-reports.sf003.process', ['line' => $requestedLine, 'tab' => 'production']) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold uppercase tracking-wider transition-all {{ $activeTab === 'production' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">Production</a>
                    <a href="{{ route('admin.production-reports.sf003.process', ['line' => $requestedLine, 'tab' => 'stock']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold uppercase tracking-wider transition-all {{ $activeTab === 'stock' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <span>Stock</span>
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[10px] font-bold {{ $activeTab === 'stock' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $stockCount }}</span>
                    </a>
                </div>
            </div>
        </div>

        @if($activeTab === 'stock')
        <div class="overflow-x-auto">
            <table class="w-full text-[13px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Date & Time</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Item Code</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Item Name</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Item Size</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">SF3 Line</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Accepted Quantity</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Used Quantity</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Transfer By</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Accepted By</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">SF2 Remark</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">SF3 Remark</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($acceptedTransfers as $index => $transfer)
                    @php
                        $transferDateTime = \Carbon\Carbon::parse($transfer->date . ' ' . $transfer->time)->format('M d, Y h:i A');
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-700">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $transferDateTime }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $transfer->item_code }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $transfer->item_name }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $transfer->item_size }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($transfer->sf3_process === 'line_1')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-indigo-50 text-indigo-700">Assemble Line 1</span>
                            @elseif($transfer->sf3_process === 'line_2')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-cyan-50 text-cyan-700">Assemble Line 2</span>
                            @elseif($transfer->sf3_process === 'line_3')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">Assemble Line 3</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-green-50 text-green-700 text-xs font-semibold">
                                {{ number_format((float) ($transfer->accepted_quantity ?? $transfer->quantity), 0) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-amber-50 text-amber-700 text-xs font-semibold">
                                {{ number_format((float) ($transfer->used_quantity ?? 0), 0) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ $transfer->transfer_by_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-slate-700">
                            @if($transfer->assign_to === auth()->id())
                                <span class="inline-flex items-center px-2 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold">You</span>
                            @elseif($transfer->accepted_by_name)
                                {{ $transfer->accepted_by_name }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ $transfer->remark ?: '-' }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $transfer->sf003_remark ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="px-4 py-10 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">No accepted stock found</p>
                                    <p class="text-sm text-slate-500 mt-1">There are no accepted transfers in {{ $lineBadgeLabel }} yet.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-[13px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Item</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Report Date</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Shift</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Actual/Total</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Created By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse(($sf3ProductionReports ?? collect()) as $report)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-900 font-medium">#{{ $report->id }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ ($report->item_code ?? '-') . ' - ' . ($report->item_name ?? '-') }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $report->report_date ? \Carbon\Carbon::parse($report->report_date)->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ ucfirst($report->shift ?? '-') }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ number_format((float) ($report->actual_set_shift ?? 0), 0) }}/{{ number_format((float) ($report->total_set_shift ?? 0), 0) }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $report->created_by_name ?? 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="clipboard-list" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">No production list found</p>
                                    <p class="text-sm text-slate-500 mt-1">Production records for {{ $lineBadgeLabel }} will appear here.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
