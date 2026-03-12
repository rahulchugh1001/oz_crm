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
                                <span class="text-sm font-semibold">{{ number_format($record->actual_set_shift, 0) }}</span>
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
                        <span class="ml-2 font-semibold text-slate-900">{{ number_format($history->sum('actual_set_shift'), 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Stock Manage History Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden mt-6">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">Stock Manage History</h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-500">Total Records:</span>
                    <span class="text-sm font-semibold text-slate-900">{{ $stockManageHistory->count() }}</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">#</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Transfer By</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">SF2 Process</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Accepted By</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Roll Forming (SF1) Remark</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">SF002 Remark</th>
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
                        <td class="px-6 py-4 text-sm text-slate-700">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm text-slate-700">
                            {{ \Carbon\Carbon::parse($transfer->date . ' ' . $transfer->time)->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">{{ $transfer->transfer_by_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($transfer->assign_sf2 === 'CED')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">CED</span>
                            @elseif($transfer->assign_sf2 === 'ZINC')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-50 text-cyan-700">ZINC</span>
                            @else
                                <span class="text-sm text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">
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
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700">
                                <i data-lucide="arrow-right-left" class="w-4 h-4"></i>
                                <span class="text-sm font-semibold">{{ number_format($transfer->quantity, 0) }}</span>
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
                        <td class="px-6 py-4 text-sm text-slate-700 align-top">
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
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">No stock manage history found</p>
                                    <p class="text-sm text-slate-500 mt-1">This item has no stock transfer records yet</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stockManageHistory->count() > 0)
        <div class="p-6 border-t border-slate-200 bg-slate-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-slate-600">
                    <i data-lucide="info" class="w-4 h-4 inline-block mr-1"></i>
                    Showing all stock transfer records for this item
                </div>
                <div class="text-sm">
                    <span class="text-slate-600">Total Transferred Quantity:</span>
                    <span class="ml-2 font-semibold text-slate-900">{{ number_format($stockManageHistory->sum('quantity'), 0) }}</span>
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
