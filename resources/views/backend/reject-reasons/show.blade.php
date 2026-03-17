@extends('backend.layout.app')

@section('title', 'Reject Reason Usage')

@section('page-title', 'Reject Reason Usage')

@section('breadcrumb')
    <span class="text-slate-600">Master Data</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <a href="{{ route('admin.reject-reasons.index') }}" class="text-slate-600 hover:text-slate-900">Reject Reasons</a>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Usage</span>
@endsection

@section('content')
<div class="p-4 space-y-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl gradient-primary flex items-center justify-center">
                    <i data-lucide="eye" class="w-4 h-4 text-white"></i>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-slate-900">{{ $rejectReason->name }}</h2>
                    <p class="text-xs text-slate-500">Usage report for this reject reason</p>
                </div>
            </div>
            <a href="{{ route('admin.reject-reasons.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                Back
            </a>
        </div>

        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500">Status</p>
                    <p class="text-sm font-semibold text-slate-900 mt-1">{{ $rejectReason->status ? 'Active' : 'Inactive' }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500">Total Used</p>
                    <p class="text-sm font-semibold text-slate-900 mt-1">{{ (int) $totalUsageCount }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500">SF001 Transfers</p>
                    <p class="text-sm font-semibold text-slate-900 mt-1">{{ (int) $sf1UsageCount }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500">SF002 Transfers</p>
                    <p class="text-sm font-semibold text-slate-900 mt-1">{{ (int) $sf2UsageCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-900">SF001 Stock Transfers</h3>
            <span class="text-xs text-slate-500">Records: <span class="font-semibold text-slate-900">{{ $sf1Usages->total() }}</span></span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[1200px] w-full text-xs">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Date & Time</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Item</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Qty</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Reject Qty</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Transfer By</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Accepted By</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">SF2</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">SF002 Remark</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($sf1Usages as $index => $row)
                        @php
                            $dt = ($row->date ?? '') . ' ' . ($row->time ?? '');
                            $sf002Remark = trim((string) ($row->sf002_remark ?? ''));
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-slate-700">#{{ $row->id }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $dt ? \Carbon\Carbon::parse($dt)->format('M d, Y h:i A') : '-' }}</td>
                            <td class="px-4 py-3 text-slate-900 font-medium">
                                <div class="max-w-[320px]">
                                    <div class="truncate" title="{{ ($row->item_code ?? '-') . ' - ' . ($row->item_name ?? '-') }}">
                                        {{ $row->item_code ?? '-' }} - {{ $row->item_name ?? '-' }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 truncate" title="{{ $row->item_size ?? '-' }}">{{ $row->item_size ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-slate-700 font-semibold">{{ number_format((float) ($row->quantity ?? 0), 0) }}</td>
                            <td class="px-4 py-3 text-center text-rose-700 font-semibold">{{ number_format((float) ($row->reject_quantity ?? 0), 0) }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row->transfer_by_name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row->accepted_by_name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row->assign_sf2 ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                @if($sf002Remark === '')
                                    -
                                @else
                                    <span class="block truncate max-w-[260px]" title="{{ $sf002Remark }}">{{ $sf002Remark }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-xs text-slate-500">No SF001 usage found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sf1Usages->hasPages())
            <div class="px-4 py-3 border-t border-slate-200">
                {{ $sf1Usages->links() }}
            </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-900">SF002 Stock Transfers</h3>
            <span class="text-xs text-slate-500">Records: <span class="font-semibold text-slate-900">{{ $sf2Usages->total() }}</span></span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[1200px] w-full text-xs">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Date & Time</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Item</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">SF3 Process</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Qty</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Reject Qty</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Transfer By</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Accepted By</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">SF3 Remark</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($sf2Usages as $index => $row)
                        @php
                            $dt = ($row->date ?? '') . ' ' . ($row->time ?? '');
                            $sf3Remark = trim((string) ($row->sf003_remark ?? ''));
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-slate-700">#{{ $row->id }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $dt ? \Carbon\Carbon::parse($dt)->format('M d, Y h:i A') : '-' }}</td>
                            <td class="px-4 py-3 text-slate-900 font-medium">
                                <div class="max-w-[320px]">
                                    <div class="truncate" title="{{ ($row->item_code ?? '-') . ' - ' . ($row->item_name ?? '-') }}">
                                        {{ $row->item_code ?? '-' }} - {{ $row->item_name ?? '-' }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 truncate" title="{{ $row->item_size ?? '-' }}">{{ $row->item_size ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-slate-700">{{ strtoupper((string) ($row->type ?? '-')) }}</td>
                            <td class="px-4 py-3 text-center text-slate-700">{{ $row->sf3_process ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-slate-700 font-semibold">{{ number_format((float) ($row->quantity ?? 0), 0) }}</td>
                            <td class="px-4 py-3 text-center text-rose-700 font-semibold">{{ number_format((float) ($row->reject_quantity ?? 0), 0) }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row->transfer_by_name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row->accepted_by_name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                @if($sf3Remark === '')
                                    -
                                @else
                                    <span class="block truncate max-w-[260px]" title="{{ $sf3Remark }}">{{ $sf3Remark }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-10 text-center text-xs text-slate-500">No SF002 usage found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sf2Usages->hasPages())
            <div class="px-4 py-3 border-t border-slate-200">
                {{ $sf2Usages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
@endpush
