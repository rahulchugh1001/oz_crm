@extends('backend.layout.app')

@php
    $sf2Type = strtolower((string) request()->query('type', 'ced'));
    $sf2TypeLabel = $sf2Type === 'zinc' ? 'ZINC' : 'CED';
@endphp

@section('title', $sf2TypeLabel . ' SF2 Process - Accepted Transfers')

@section('page-title', $sf2TypeLabel . ' SF2 Process Management')

@section('breadcrumb')
    <span class="text-slate-600">Production Reports</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">{{ $sf2TypeLabel }} SF2</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Process</span>
@endsection

@section('content')
<div class="p-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $sf2TypeLabel }} SF2 Process - Accepted Transfers</h2>
                    <p class="text-sm text-slate-500">Accepted quantities assigned to this {{ $sf2TypeLabel }} SF2 process</p>
                </div>
                <div class="text-sm">
                    <span class="text-slate-500">Total Records:</span>
                    <span class="ml-1 font-semibold text-slate-900">{{ $acceptedTransfers->count() }}</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-[13px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Date & Time</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Item Code</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Item Name</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Item Size</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Assign SF2</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Accepted Quantity</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Transfer By</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Accepted By</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">SF001 Remark</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">{{ $sf2TypeLabel }} SF2 Remark</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($acceptedTransfers as $index => $transfer)
                    @php
                        $sf001Remark = trim((string) ($transfer->remark ?? ''));
                        $sf002Remark = trim((string) ($transfer->sf002_remark ?? ''));
                        $sf001ShortRemark = mb_strimwidth($sf001Remark, 0, 60, '...');
                        $sf002ShortRemark = mb_strimwidth($sf002Remark, 0, 60, '...');
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-700">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ \Carbon\Carbon::parse($transfer->date . ' ' . $transfer->time)->format('M d, Y h:i A') }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $transfer->item_code }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $transfer->item_name }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $transfer->item_size }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($transfer->assign_sf2 === 'CED')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-indigo-50 text-indigo-700">CED</span>
                            @elseif($transfer->assign_sf2 === 'ZINC')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-cyan-50 text-cyan-700">ZINC</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-green-50 text-green-700 text-xs font-semibold">
                                {{ number_format((float) ($transfer->accepted_quantity ?? $transfer->quantity), 0) }}
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
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.production-reports.sf002.production-report', ['transferId' => $transfer->id, 'type' => request()->query('type', 'ced')]) }}" class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-slate-100 transition-colors" title="Manage">
                                <i data-lucide="settings" class="w-4 h-4 text-slate-600"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-4 py-10 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">No accepted stock found</p>
                                    <p class="text-sm text-slate-500 mt-1">There are no accepted transfers assigned to you yet.</p>
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

@push('scripts')
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
});
</script>
@endpush
