@extends('backend.layout.app')

@section('title', 'SF3 Final Stock Details')

@section('page-title', 'SF3 Final Stock Details')

@section('breadcrumb')
    <span class="text-slate-600">Production Reports</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">Assemble SF3</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <a href="{{ route('admin.production-reports.sf003.final-stock') }}" class="text-slate-600 hover:text-slate-800">Final Stock</a>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Details</span>
@endsection

@section('content')
<div class="p-6 space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Final Stock Master Details</h2>
                {{-- <p class="text-sm text-slate-500 mt-1">Source table: sf3_production_reports</p> --}}
            </div>
            <a href="{{ route('admin.production-reports.sf003.final-stock') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back
            </a>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Report ID</div>
                    <div class="mt-1 text-sm font-semibold text-slate-900">#{{ $report->id }}</div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Line</div>
                    <div class="mt-1 text-sm font-semibold text-slate-900">{{ ucfirst(str_replace('_', ' ', (string) ($report->sf3_process ?? '-'))) }}</div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Report Date</div>
                    <div class="mt-1 text-sm font-semibold text-slate-900">{{ $report->report_date ? \Carbon\Carbon::parse($report->report_date)->format('d M Y') : '-' }}</div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Shift</div>
                    <div class="mt-1 text-sm font-semibold text-slate-900">{{ ucfirst((string) ($report->shift ?? '-')) }}</div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Item</div>
                    <div class="mt-1 text-sm font-semibold text-slate-900">{{ ($report->item_code ?? '-') . ' - ' . ($report->item_name ?? '-') }}</div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Actual / Set / Shift</div>
                    <div class="mt-1 text-sm font-semibold text-slate-900">{{ number_format((float) ($report->actual_set_shift ?? 0), 0) }}</div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total / Set / Shift</div>
                    <div class="mt-1 text-sm font-semibold text-slate-900">{{ number_format((float) ($report->total_set_shift ?? 0), 0) }}</div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Created By</div>
                    <div class="mt-1 text-sm font-semibold text-slate-900">{{ $report->created_by_name ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Product Breakdown</h3>
            {{-- <p class="text-sm text-slate-500 mt-1">Source table: sf3_production_report_products</p> --}}
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-[13px]">
                <thead class="border-b border-slate-200" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    <tr>
                        <th class="w-[50px] px-4 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-white whitespace-nowrap">#</th>
                        <th class="w-[100px] px-4 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-white whitespace-nowrap">Transfer ID</th>
                        <th class="w-[250px] px-4 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-white whitespace-nowrap">Product</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-white whitespace-nowrap">Category</th>
                        <th class="px-4 py-2 text-right text-[10px] font-semibold uppercase tracking-wider text-white whitespace-nowrap">Qty Required</th>
                        <th class="px-4 py-2 text-right text-[10px] font-semibold uppercase tracking-wider text-white whitespace-nowrap">Qty Used</th>
                        <th class="px-4 py-2 text-right text-[10px] font-semibold uppercase tracking-wider text-white whitespace-nowrap">Transfer Qty</th>
                        <th class="px-4 py-2 text-right text-[10px] font-semibold uppercase tracking-wider text-white whitespace-nowrap">Transfer Used</th>
                        <th class="px-4 py-2 text-right text-[10px] font-semibold uppercase tracking-wider text-white whitespace-nowrap">Transfer Available</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($productRows as $index => $detail)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-700">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $detail->transfered_id ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-700 break-words">
                            {{ $detail->product_name ?? '-' }}
                            <span class="text-[11px] text-slate-500">({{ $detail->product_code ?? '-' }})</span>
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ $detail->product_category ?? '-' }}</td>
                        <td class="px-4 py-3 text-right text-slate-700">{{ number_format((float) ($detail->quantity_required ?? 0), 2) }}</td>
                        <td class="px-4 py-3 text-right text-slate-700">{{ number_format((float) ($detail->quantity_used ?? 0), 2) }}</td>
                        <td class="px-4 py-3 text-right text-slate-700">{{ number_format((float) ($detail->transfer_quantity ?? 0), 2) }}</td>
                        <td class="px-4 py-3 text-right text-slate-700">{{ number_format((float) ($detail->transfer_used_quantity ?? 0), 2) }}</td>
                        <td class="px-4 py-3 text-right text-slate-700">{{ number_format((float) ($detail->transfer_available_quantity ?? 0), 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-10 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="table-properties" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">No product breakdown rows found</p>
                                    <p class="text-sm text-slate-500 mt-1">This record does not have entries in sf3_production_report_products.</p>
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