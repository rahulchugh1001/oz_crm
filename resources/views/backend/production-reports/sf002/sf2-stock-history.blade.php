@extends('backend.layout.app')

@section('title', 'CED & Zinc (SF2) - SF2 Stock History')

@section('page-title', 'CED & Zinc (SF2) - SF2 Stock History')

@section('breadcrumb')
    <span class="text-slate-600">CED & Zinc (SF2)</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <a href="{{ route('admin.production-reports.sf002.sf2-stock') }}" class="text-slate-600 hover:text-slate-900">SF2 Stock</a>
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
                        <h2 class="text-lg font-bold text-slate-900">SF2 Stock History - {{ $item->name }}</h2>
                        <p class="text-sm text-slate-500">Item Code: <span class="font-medium">{{ $item->code }}</span> | Size: <span class="font-medium">{{ $item->size }}</span></p>
                    </div>
                </div>
                <a href="{{ route('admin.production-reports.sf002.sf2-stock') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to SF2 Stock
                </a>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-t border-slate-200 flex">
            <button id="htab-btn-ced" onclick="switchHistoryTab('ced')"
                class="flex items-center gap-2 px-6 py-3 text-sm font-medium border-b-2 transition-colors htab-active">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">CED</span>
                <span>CED History</span>
            </button>
            <button id="htab-btn-zinc" onclick="switchHistoryTab('zinc')"
                class="flex items-center gap-2 px-6 py-3 text-sm font-medium border-b-2 transition-colors htab-inactive">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-cyan-100 text-cyan-700">ZINC</span>
                <span>ZINC History</span>
            </button>
        </div>
    </div>

    {{-- ===================== CED Panel ===================== --}}
    <div id="hpanel-ced">
        <!-- CED Production History -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden mb-6">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">CED Production Report History</h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-500">Total Records:</span>
                    <span class="text-sm font-semibold text-slate-900">{{ $cedHistory->count() }}</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">#</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Report Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Shift</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Created By</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Total Set/Shift</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Actual Set/Shift</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($cedHistory as $index => $record)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                    <span class="text-sm font-medium text-slate-900">{{ \Carbon\Carbon::parse($record->report_date)->format('M d, Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $record->shift === 'morning' ? 'bg-amber-50 text-amber-700' : 'bg-indigo-50 text-indigo-700' }}">
                                    <i data-lucide="{{ $record->shift === 'morning' ? 'sun' : 'moon' }}" class="w-3 h-3 mr-1"></i>
                                    {{ ucfirst($record->shift) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $record->created_by_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 text-slate-700">
                                    <span class="text-sm font-semibold">{{ number_format($record->total_set_shift, 0) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-green-50 text-green-700">
                                    <i data-lucide="package-check" class="w-4 h-4"></i>
                                    <span class="text-sm font-semibold">{{ number_format($record->actual_set_shift, 0) }}</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                        <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                    </div>
                                    <p class="text-sm font-medium text-slate-900">No CED production history found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($cedHistory->count() > 0)
            <div class="p-6 border-t border-slate-200 bg-slate-50 flex justify-end">
                <div class="text-sm">
                    <span class="text-slate-600">Total CED Production:</span>
                    <span class="ml-2 font-semibold text-slate-900">{{ number_format($cedHistory->sum('actual_set_shift'), 0) }}</span>
                </div>
            </div>
            @endif
        </div>

        <!-- CED Transfer History -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden mb-6">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">CED Stock Transfer History</h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-500">Total Records:</span>
                    <span class="text-sm font-semibold text-slate-900">{{ $cedTransferHistory->count() }}</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">#</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Transfer By</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">SF3 Process</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Assigned To</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Rejected Qty</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">SF2 Remark</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">SF3 Remark</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($cedTransferHistory as $index => $transfer)
                        @php
                            $sf2Remark  = trim((string) ($transfer->remark ?? ''));
                            $sf3Remark  = trim((string) ($transfer->sf003_remark ?? ''));
                            $sf2Short   = mb_strimwidth($sf2Remark, 0, 60, '...');
                            $sf3Short   = mb_strimwidth($sf3Remark, 0, 60, '...');
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                {{ \Carbon\Carbon::parse($transfer->date . ' ' . $transfer->time)->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $transfer->transfer_by_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($transfer->sf3_process === 'line_1')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-sky-50 text-sky-700">Assemble Line 1</span>
                                @elseif($transfer->sf3_process === 'line_2')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-violet-50 text-violet-700">Assemble Line 2</span>
                                @elseif($transfer->sf3_process === 'line_3')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">Assemble Line 3</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                @if($transfer->assign_to_name)
                                    {{ $transfer->assign_to_name }}
                                @else
                                    <span class="text-slate-400">Assembly (SF3) — Unclaimed</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700">
                                    <i data-lucide="arrow-right-left" class="w-4 h-4"></i>
                                    <span class="text-sm font-semibold">{{ number_format($transfer->quantity, 0) }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700">
                                    <i data-lucide="ban" class="w-4 h-4"></i>
                                    <span class="text-sm font-semibold">{{ number_format((float) ($transfer->rejected_quantity ?? 0), 0) }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($transfer->is_accept == 1)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700">Accepted</span>
                                @elseif($transfer->is_accept == 2)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700">Rejected</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 align-top">
                                @if($sf2Remark === '')
                                    -
                                @elseif($sf2Remark === $sf2Short)
                                    {{ $sf2Remark }}
                                @else
                                    <div class="max-w-[220px]">
                                        <span class="js-remark-short">{{ $sf2Short }}</span>
                                        <span class="js-remark-full hidden">{{ $sf2Remark }}</span>
                                        <button type="button" class="js-remark-toggle ml-1 text-[11px] font-medium text-blue-600 hover:text-blue-700">Read more</button>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 align-top">
                                @if($sf3Remark === '')
                                    -
                                @elseif($sf3Remark === $sf3Short)
                                    {{ $sf3Remark }}
                                @else
                                    <div class="max-w-[220px]">
                                        <span class="js-remark-short">{{ $sf3Short }}</span>
                                        <span class="js-remark-full hidden">{{ $sf3Remark }}</span>
                                        <button type="button" class="js-remark-toggle ml-1 text-[11px] font-medium text-blue-600 hover:text-blue-700">Read more</button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                        <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                    </div>
                                    <p class="text-sm font-medium text-slate-900">No CED transfer history found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ===================== ZINC Panel ===================== --}}
    <div id="hpanel-zinc" class="hidden">
        <!-- ZINC Production History -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden mb-6">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">ZINC Production Report History</h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-500">Total Records:</span>
                    <span class="text-sm font-semibold text-slate-900">{{ $zincHistory->count() }}</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">#</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Report Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Shift</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Created By</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Total Set/Shift</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Actual Set/Shift</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($zincHistory as $index => $record)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                    <span class="text-sm font-medium text-slate-900">{{ \Carbon\Carbon::parse($record->report_date)->format('M d, Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $record->shift === 'morning' ? 'bg-amber-50 text-amber-700' : 'bg-indigo-50 text-indigo-700' }}">
                                    <i data-lucide="{{ $record->shift === 'morning' ? 'sun' : 'moon' }}" class="w-3 h-3 mr-1"></i>
                                    {{ ucfirst($record->shift) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $record->created_by_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 text-slate-700">
                                    <span class="text-sm font-semibold">{{ number_format($record->total_set_shift, 0) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-green-50 text-green-700">
                                    <i data-lucide="package-check" class="w-4 h-4"></i>
                                    <span class="text-sm font-semibold">{{ number_format($record->actual_set_shift, 0) }}</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                        <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                    </div>
                                    <p class="text-sm font-medium text-slate-900">No ZINC production history found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($zincHistory->count() > 0)
            <div class="p-6 border-t border-slate-200 bg-slate-50 flex justify-end">
                <div class="text-sm">
                    <span class="text-slate-600">Total ZINC Production:</span>
                    <span class="ml-2 font-semibold text-slate-900">{{ number_format($zincHistory->sum('actual_set_shift'), 0) }}</span>
                </div>
            </div>
            @endif
        </div>

        <!-- ZINC Transfer History -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden mb-6">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">ZINC Stock Transfer History</h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-500">Total Records:</span>
                    <span class="text-sm font-semibold text-slate-900">{{ $zincTransferHistory->count() }}</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">#</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Transfer By</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">SF3 Process</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Assigned To</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Rejected Qty</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">SF2 Remark</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">SF3 Remark</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($zincTransferHistory as $index => $transfer)
                        @php
                            $sf2Remark  = trim((string) ($transfer->remark ?? ''));
                            $sf3Remark  = trim((string) ($transfer->sf003_remark ?? ''));
                            $sf2Short   = mb_strimwidth($sf2Remark, 0, 60, '...');
                            $sf3Short   = mb_strimwidth($sf3Remark, 0, 60, '...');
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                {{ \Carbon\Carbon::parse($transfer->date . ' ' . $transfer->time)->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $transfer->transfer_by_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($transfer->sf3_process === 'line_1')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-sky-50 text-sky-700">Assemble Line 1</span>
                                @elseif($transfer->sf3_process === 'line_2')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-violet-50 text-violet-700">Assemble Line 2</span>
                                @elseif($transfer->sf3_process === 'line_3')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">Assemble Line 3</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                @if($transfer->assign_to_name)
                                    {{ $transfer->assign_to_name }}
                                @else
                                    <span class="text-slate-400">Assembly (SF3) — Unclaimed</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700">
                                    <i data-lucide="arrow-right-left" class="w-4 h-4"></i>
                                    <span class="text-sm font-semibold">{{ number_format($transfer->quantity, 0) }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700">
                                    <i data-lucide="ban" class="w-4 h-4"></i>
                                    <span class="text-sm font-semibold">{{ number_format((float) ($transfer->rejected_quantity ?? 0), 0) }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($transfer->is_accept == 1)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700">Accepted</span>
                                @elseif($transfer->is_accept == 2)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700">Rejected</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 align-top">
                                @if($sf2Remark === '')
                                    -
                                @elseif($sf2Remark === $sf2Short)
                                    {{ $sf2Remark }}
                                @else
                                    <div class="max-w-[220px]">
                                        <span class="js-remark-short">{{ $sf2Short }}</span>
                                        <span class="js-remark-full hidden">{{ $sf2Remark }}</span>
                                        <button type="button" class="js-remark-toggle ml-1 text-[11px] font-medium text-blue-600 hover:text-blue-700">Read more</button>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 align-top">
                                @if($sf3Remark === '')
                                    -
                                @elseif($sf3Remark === $sf3Short)
                                    {{ $sf3Remark }}
                                @else
                                    <div class="max-w-[220px]">
                                        <span class="js-remark-short">{{ $sf3Short }}</span>
                                        <span class="js-remark-full hidden">{{ $sf3Remark }}</span>
                                        <button type="button" class="js-remark-toggle ml-1 text-[11px] font-medium text-blue-600 hover:text-blue-700">Read more</button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                        <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                    </div>
                                    <p class="text-sm font-medium text-slate-900">No ZINC transfer history found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .htab-active   { color: #4f46e5; border-bottom-color: #4f46e5; }
    .htab-inactive { color: #64748b; border-bottom-color: transparent; }
    .htab-inactive:hover { color: #334155; background-color: #f8fafc; }
</style>

<script>
    function switchHistoryTab(tab) {
        const panels  = { ced: 'hpanel-ced',  zinc: 'hpanel-zinc'  };
        const buttons = { ced: 'htab-btn-ced', zinc: 'htab-btn-zinc' };

        Object.keys(panels).forEach(key => {
            const panel = document.getElementById(panels[key]);
            const btn   = document.getElementById(buttons[key]);
            if (key === tab) {
                panel.classList.remove('hidden');
                btn.classList.remove('htab-inactive');
                btn.classList.add('htab-active');
            } else {
                panel.classList.add('hidden');
                btn.classList.remove('htab-active');
                btn.classList.add('htab-inactive');
            }
        });
    }

    // Read more / Read less toggle for remarks
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('js-remark-toggle')) {
            const container = e.target.closest('div');
            const shortEl = container.querySelector('.js-remark-short');
            const fullEl  = container.querySelector('.js-remark-full');
            if (fullEl.classList.contains('hidden')) {
                shortEl.classList.add('hidden');
                fullEl.classList.remove('hidden');
                e.target.textContent = 'Read less';
            } else {
                fullEl.classList.add('hidden');
                shortEl.classList.remove('hidden');
                e.target.textContent = 'Read more';
            }
        }
    });
</script>
@endsection
