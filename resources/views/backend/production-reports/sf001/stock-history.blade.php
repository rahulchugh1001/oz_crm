@extends('backend.layout.app')

@section('title', 'Roll Forming (SF1) Stock - Production History')

@section('page-title', 'Roll Forming (SF1) Process - Production History')

@section('breadcrumb')
    <span class="text-slate-600">Roll Forming (SF1)</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <a href="{{ route('admin.production-reports.sf001.stock') }}" class="text-slate-600 hover:text-slate-900">Stock</a>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">History</span>
@endsection

@section('content')
<div class="p-4">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle mb-4">
        <div class="p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                        <i data-lucide="history" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Production History - {{ $item->name }}</h2>
                        <p class="text-xs text-slate-500">Item Code: <span class="font-medium">{{ $item->code }}</span> | Size: <span class="font-medium">{{ $item->size }}</span></p>
                    </div>
                </div>
                <a href="{{ route('admin.production-reports.sf001.stock') }}" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    Back to Stock
                </a>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-4 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900">Production Report History</h3>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500">Total Records:</span>
                    <span class="text-xs font-semibold text-slate-900">{{ $history->count() }}</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="border-b border-slate-200" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    <tr>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                            #
                        </th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                            Report Date
                        </th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                            Shift
                        </th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                            Machine
                        </th>
                        <th class="px-4 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                            Actual Set/Shift
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($history as $index => $record)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-700">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span class="font-medium text-slate-900">{{ \Carbon\Carbon::parse($record->report_date)->format('M d, Y') }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium {{ $record->shift === 'Morning' ? 'bg-amber-50 text-amber-700' : 'bg-indigo-50 text-indigo-700' }}">
                                <i data-lucide="{{ $record->shift === 'Morning' ? 'sun' : 'moon' }}" class="w-3 h-3 mr-1"></i>
                                {{ $record->shift }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i data-lucide="cog" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span class="text-slate-700">{{ $record->machine_name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-green-50 text-green-700">
                                <i data-lucide="package-check" class="w-3.5 h-3.5"></i>
                                <span class="font-semibold">{{ number_format($record->actual_set_shift, 0) }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-slate-900">No production history found</p>
                                    <p class="text-xs text-slate-500 mt-1">This item has no production records yet</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($history->count() > 0)
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            <div class="flex items-center justify-between">
                <div class="text-xs text-slate-600">
                    <i data-lucide="info" class="w-3.5 h-3.5 inline-block mr-1"></i>
                    Showing all production records for this item
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-xs">
                        <span class="text-slate-600">Total Production Quantity:</span>
                        <span class="ml-2 font-semibold text-slate-900">{{ number_format($history->sum('actual_set_shift'), 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Stock Manage History Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden mt-4">
        <div class="p-4 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900">Stock Manage History</h3>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500">Total Records:</span>
                    <span class="text-xs font-semibold text-slate-900">{{ $stockManageHistory->count() }}</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="border-b border-slate-200" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    <tr>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">#</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Date & Time</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Transfer By</th>
                        <th class="px-4 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">SF2 Process</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Accepted By</th>
                        <th class="px-4 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Quantity</th>
                        <th class="px-4 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Rejected Quantity</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Reject Reason</th>
                        <th class="px-4 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Status</th>
                        <th class="px-4 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Self Transferred</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Roll Forming (SF1) Remark</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">SF002 Remark</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($stockManageHistory as $index => $transfer)
                    @php
                        $sf001Remark = trim((string) ($transfer->remark ?? ''));
                        $sf002Remark = trim((string) ($transfer->sf002_remark ?? ''));
                        $sf001ShortRemark = mb_strimwidth($sf001Remark, 0, 60, '...');
                        $sf002ShortRemark = mb_strimwidth($sf002Remark, 0, 60, '...');
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-700">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-slate-700">
                            {{ \Carbon\Carbon::parse($transfer->date . ' ' . $transfer->time)->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ $transfer->transfer_by_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($transfer->assign_sf2 === 'CED')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-indigo-50 text-indigo-700">CED</span>
                            @elseif($transfer->assign_sf2 === 'ZINC')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-cyan-50 text-cyan-700">ZINC</span>
                            @else
                                <span class="text-sm text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-700">
                            @if($transfer->assign_to_name)
                                {{ $transfer->assign_to_name }}
                            @elseif($transfer->assign_role === 'SF002')
                                Unclaimed
                            @elseif($transfer->assign_role === 'SF003')
                                Assembly (SF3) - Unclaimed
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700">
                                <i data-lucide="arrow-right-left" class="w-3.5 h-3.5"></i>
                                <span class="font-semibold">{{ number_format($transfer->quantity, 0) }}</span>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700">
                                <i data-lucide="ban" class="w-3.5 h-3.5"></i>
                                <span class="font-semibold">{{ number_format((float) ($transfer->rejected_quantity ?? 0), 0) }}</span>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-700">
                            @php
                                $rejectReasonName = trim((string) ($transfer->reject_reason_name ?? ''));
                                $rejectedQty = (float) ($transfer->rejected_quantity ?? 0);
                            @endphp
                            @if($rejectedQty > 0 && $rejectReasonName !== '')
                                <span class="block truncate max-w-[220px]" title="{{ $rejectReasonName }}">{{ $rejectReasonName }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($transfer->is_accept == 1)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-green-50 text-green-700">Accepted</span>
                            @elseif($transfer->is_accept == 2)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700">Rejected</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($transfer->is_self_transferred)
                                <div class="flex flex-col items-center gap-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-purple-50 text-purple-700">
                                        <i data-lucide="repeat" class="w-3 h-3 mr-1"></i> Yes
                                    </span>
                                    @if($transfer->parent_assign_sf2)
                                        <span class="text-[10px] text-slate-500">From {{ $transfer->parent_assign_sf2 }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-700 align-top">
                            @if($sf001Remark === '')
                                -
                            @elseif($sf001Remark === $sf001ShortRemark)
                                {{ $sf001Remark }}
                            @else
                                <div class="max-w-[220px]">
                                    <span class="js-remark-short">{{ $sf001ShortRemark }}</span>
                                    <span class="js-remark-full hidden">{{ $sf001Remark }}</span>
                                    <button type="button" class="js-remark-toggle ml-1 text-[11px] font-medium text-blue-600 hover:text-blue-700">Read more</button>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-700 align-top">
                            @if($sf002Remark === '')
                                -
                            @elseif($sf002Remark === $sf002ShortRemark)
                                {{ $sf002Remark }}
                            @else
                                <div class="max-w-[220px]">
                                    <span class="js-remark-short">{{ $sf002ShortRemark }}</span>
                                    <span class="js-remark-full hidden">{{ $sf002Remark }}</span>
                                    <button type="button" class="js-remark-toggle ml-1 text-[11px] font-medium text-blue-600 hover:text-blue-700">Read more</button>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="px-4 py-10 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-slate-900">No stock manage history found</p>
                                    <p class="text-xs text-slate-500 mt-1">This item has no stock transfer records yet</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stockManageHistory->count() > 0)
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            <div class="flex items-center justify-between">
                <div class="text-xs text-slate-600">
                    <i data-lucide="info" class="w-3.5 h-3.5 inline-block mr-1"></i>
                    Showing all stock transfer records for this item
                </div>
                <div class="text-xs">
                    <span class="text-slate-600">Total Transferred Quantity:</span>
                    <span class="ml-2 font-semibold text-slate-900">{{ number_format($stockManageHistory->sum('quantity'), 0) }}</span>
                </div>
                <div class="text-xs">
                    <span class="text-slate-600">Total Rejected Quantity:</span>
                    <span class="ml-2 font-semibold text-rose-700">{{ number_format($stockManageHistory->sum('rejected_quantity'), 0) }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const remarkToggleButtons = document.querySelectorAll('.js-remark-toggle');

        remarkToggleButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const wrapper = button.parentElement;
                const shortText = wrapper.querySelector('.js-remark-short');
                const fullText = wrapper.querySelector('.js-remark-full');
                const isExpanded = !fullText.classList.contains('hidden');

                if (isExpanded) {
                    fullText.classList.add('hidden');
                    shortText.classList.remove('hidden');
                    button.textContent = 'Read more';
                } else {
                    shortText.classList.add('hidden');
                    fullText.classList.remove('hidden');
                    button.textContent = 'Read less';
                }
            });
        });

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
