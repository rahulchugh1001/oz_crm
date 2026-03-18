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
@php
    $reasonCategory = $rejectReason->category ?? 'SF1';
    $showSf1 = in_array($reasonCategory, ['SF1', 'Both'], true);
    $showSf2 = in_array($reasonCategory, ['SF2', 'Both'], true);
@endphp
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
            <div class="grid grid-cols-1 {{ $showSf1 && $showSf2 ? 'md:grid-cols-5' : 'md:grid-cols-4' }} gap-3">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500">Category</p>
                    <p class="text-sm font-semibold text-slate-900 mt-1">{{ $rejectReason->category ?? 'SF1' }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500">Status</p>
                    <p class="text-sm font-semibold text-slate-900 mt-1">{{ $rejectReason->status ? 'Active' : 'Inactive' }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500">Total Used</p>
                    <p class="text-sm font-semibold text-slate-900 mt-1">{{ (int) $totalUsageCount }}</p>
                </div>
                @if($showSf1)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <p class="text-[11px] uppercase tracking-wider text-slate-500">SF001 Transfers</p>
                        <p class="text-sm font-semibold text-slate-900 mt-1">{{ (int) $sf1UsageCount }}</p>
                    </div>
                @endif
                @if($showSf2)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <p class="text-[11px] uppercase tracking-wider text-slate-500">SF002 Transfers</p>
                        <p class="text-sm font-semibold text-slate-900 mt-1">{{ (int) $sf2UsageCount }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Usage Graph (Last 30 Days)</h3>
                <p class="text-xs text-slate-500">Daily reject counts using this reason</p>
            </div>
            <div class="flex items-center gap-3 text-xs">
                @if($showSf1)
                    <span class="inline-flex items-center gap-1.5 text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-sm bg-emerald-600"></span>
                        SF001
                    </span>
                @endif
                @if($showSf2)
                    <span class="inline-flex items-center gap-1.5 text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-sm bg-amber-600"></span>
                        SF002
                    </span>
                @endif
            </div>
        </div>
        <div class="p-4">
            <div id="rejectReasonUsageChart"
                 class="w-full"
                 data-labels='@json($chartLabels ?? [])'
                 data-sf1='@json($chartSf1 ?? [])'
                 data-sf2='@json($chartSf2 ?? [])'
                 data-show-sf1='@json($showSf1)'
                 data-show-sf2='@json($showSf2)'>
                <div class="h-44 w-full rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center">
                    <span class="text-xs text-slate-500">Loading chart…</span>
                </div>
            </div>
        </div>
    </div>

    @if($showSf1)
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
    @endif

    @if($showSf2)
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
    @endif
</div>
@endsection

@push('scripts')
<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    (function () {
        const holder = document.getElementById('rejectReasonUsageChart');
        if (!holder) return;

        let labels = [];
        let sf1 = [];
        let sf2 = [];
        let showSf1 = true;
        let showSf2 = true;

        try {
            labels = JSON.parse(holder.getAttribute('data-labels') || '[]');
            sf1 = JSON.parse(holder.getAttribute('data-sf1') || '[]');
            sf2 = JSON.parse(holder.getAttribute('data-sf2') || '[]');
            showSf1 = JSON.parse(holder.getAttribute('data-show-sf1') || 'true');
            showSf2 = JSON.parse(holder.getAttribute('data-show-sf2') || 'true');
        } catch (e) {
            labels = [];
            sf1 = [];
            sf2 = [];
            showSf1 = true;
            showSf2 = true;
        }

        const n = Math.min(labels.length, sf1.length, sf2.length);
        if (!n) {
            holder.innerHTML = '<div class="h-44 w-full rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center"><span class="text-xs text-slate-500">No chart data.</span></div>';
            return;
        }

        const totals = Array.from({ length: n }, (_, i) => (Number(sf1[i] || 0) + Number(sf2[i] || 0)));
        const maxTotal = Math.max(1, ...totals);

        const width = 920;
        const height = 176;
        const pad = { l: 32, r: 12, t: 10, b: 28 };
        const plotW = width - pad.l - pad.r;
        const plotH = height - pad.t - pad.b;
        const gap = 2;
        const barW = Math.max(3, Math.floor((plotW - gap * (n - 1)) / n));

        const xAt = (i) => pad.l + i * (barW + gap);
        const yAt = (v) => pad.t + (1 - (v / maxTotal)) * plotH;

        let bars = '';
        let xLabels = '';
        const labelEvery = n > 12 ? 5 : 1;

        for (let i = 0; i < n; i++) {
            const v1 = showSf1 ? Number(sf1[i] || 0) : 0;
            const v2 = showSf2 ? Number(sf2[i] || 0) : 0;

            const total = v1 + v2;
            const hTotal = (total / maxTotal) * plotH;
            const h2 = (v2 / maxTotal) * plotH;
            const h1 = (v1 / maxTotal) * plotH;

            const x = xAt(i);
            const yBase = pad.t + plotH;
            const y2 = yBase - h2;
            const y1 = y2 - h1;

            const title = `${labels[i]}: SF001 ${v1}, SF002 ${v2}`;

            if (h2 > 0) {
                bars += `<g class="text-amber-600"><title>${title}</title><rect x="${x}" y="${y2}" width="${barW}" height="${h2}" rx="2" fill="currentColor" /></g>`;
            }
            if (h1 > 0) {
                bars += `<g class="text-emerald-600"><title>${title}</title><rect x="${x}" y="${y1}" width="${barW}" height="${h1}" rx="2" fill="currentColor" /></g>`;
            }

            if (i % labelEvery === 0 || i === n - 1) {
                const lx = x + barW / 2;
                xLabels += `<text x="${lx}" y="${height - 10}" text-anchor="middle" class="text-slate-500" fill="currentColor" font-size="10">${labels[i]}</text>`;
            }
        }

        const yAxis = `<g class="text-slate-300"><line x1="${pad.l}" y1="${pad.t}" x2="${pad.l}" y2="${pad.t + plotH}" stroke="currentColor" stroke-width="1" /></g>`;
        const xAxis = `<g class="text-slate-300"><line x1="${pad.l}" y1="${pad.t + plotH}" x2="${pad.l + plotW}" y2="${pad.t + plotH}" stroke="currentColor" stroke-width="1" /></g>`;

        const ticks = 4;
        let yTicks = '';
        for (let t = 0; t <= ticks; t++) {
            const v = Math.round((maxTotal * t) / ticks);
            const y = yAt(v);
            yTicks += `<g class="text-slate-200"><line x1="${pad.l}" y1="${y}" x2="${pad.l + plotW}" y2="${y}" stroke="currentColor" stroke-width="1" /></g>`;
            yTicks += `<text x="${pad.l - 8}" y="${y + 3}" text-anchor="end" class="text-slate-500" fill="currentColor" font-size="10">${v}</text>`;
        }

        holder.innerHTML = `
            <div class="h-44 w-full rounded-xl border border-slate-200 bg-white overflow-hidden">
                <svg viewBox="0 0 ${width} ${height}" class="w-full h-full">
                    ${yTicks}
                    ${yAxis}
                    ${xAxis}
                    ${bars}
                    ${xLabels}
                </svg>
            </div>
        `;
    })();
</script>
@endpush
