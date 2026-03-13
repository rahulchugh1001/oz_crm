@extends('backend.layout.app')

@php
    $sf2Type = strtolower((string) request()->query('type', 'ced'));
    $sf2TypeLabel = $sf2Type === 'zinc' ? 'ZINC' : 'CED';
    $activeTab = strtolower((string) request()->query('tab', 'production'));
    $activeTab = in_array($activeTab, ['stock', 'production'], true) ? $activeTab : 'production';
    $firstAcceptedTransfer = $acceptedTransfers->first();
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
                        <h2 class="text-lg font-bold text-slate-900">{{ $sf2TypeLabel }} SF2 Process</h2>
                        <p class="text-sm text-slate-500">
                            @if($activeTab === 'stock')
                                Stock list assigned to this {{ $sf2TypeLabel }} SF2 process (view only)
                            @else
                                Production list for this {{ $sf2TypeLabel }} SF2 process (currently empty)
                            @endif
                        </p>
                    </div>
                    @if($activeTab === 'stock')
                    <div class="text-sm">
                        <span class="text-slate-500">Total Records:</span>
                        <span class="ml-1 font-semibold text-slate-900">{{ $acceptedTransfers->count() }}</span>
                    </div>
                    @else
                    @if($firstAcceptedTransfer)
                    <a href="{{ route('admin.production-reports.sf002.production-report', ['transferId' => $firstAcceptedTransfer->id, 'type' => $sf2Type]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition-colors" title="Add Production">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Add Production
                    </a>
                    @else
                    <button type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-300 text-slate-600 text-sm font-medium cursor-not-allowed" title="No accepted stock available" disabled>
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Add Production
                    </button>
                    @endif
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <a href="{{ route('admin.production-reports.sf002.process', ['type' => $sf2Type, 'tab' => 'production']) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold uppercase tracking-wider transition-all {{ $activeTab === 'production' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">Production</a>
                    <a href="{{ route('admin.production-reports.sf002.process', ['type' => $sf2Type, 'tab' => 'stock']) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold uppercase tracking-wider transition-all {{ $activeTab === 'stock' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">Stock</a>
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
                        $transferDateTime = \Carbon\Carbon::parse($transfer->date . ' ' . $transfer->time)->format('M d, Y h:i A');
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-700">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $transferDateTime }}</td>
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
                            <button
                                type="button"
                                class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-slate-100 transition-colors"
                                onclick="openStockDetailsModal(this)"
                                title="View"
                                data-id="{{ $transfer->id }}"
                                data-datetime="{{ $transferDateTime }}"
                                data-item-code="{{ $transfer->item_code }}"
                                data-item-name="{{ $transfer->item_name }}"
                                data-item-size="{{ $transfer->item_size }}"
                                data-assign-sf2="{{ $transfer->assign_sf2 ?? '-' }}"
                                data-accepted-quantity="{{ number_format((float) ($transfer->accepted_quantity ?? $transfer->quantity), 0, '.', '') }}"
                                data-transfer-by="{{ $transfer->transfer_by_name ?? 'N/A' }}"
                                data-accepted-by="{{ $transfer->accepted_by_name ?? '' }}"
                                data-is-you="{{ $transfer->assign_to === auth()->id() ? '1' : '0' }}"
                                data-sf001-remark="{{ $transfer->remark ?? '' }}"
                                data-sf002-remark="{{ $transfer->sf002_remark ?? '' }}"
                            >
                                <i data-lucide="eye" class="w-4 h-4 text-slate-600"></i>
                            </button>
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
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-[13px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">ID</th>
                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Item</th>
                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Report Date</th>
                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Shift</th>
                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Total/Actual</th>
                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Workman</th>
                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Staff</th>
                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Created By</th>
                        <th class="px-3 py-2.5 text-right text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse(($sf2ProductionReports ?? collect()) as $report)
                    @php
                        $actualSetShift = number_format((float) ($report->actual_set_shift ?? 0), 0);
                        $totalSetShift = number_format((float) ($report->total_set_shift ?? 0), 0);
                        $manpowerWorkman = number_format((float) ($report->manpower_workman ?? 0), 0);
                        $staffCount = number_format((float) ($report->staff_count ?? 0), 0);
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-3 py-2.5 text-xs text-slate-900 font-medium">#{{ $report->id }}</td>
                        <td class="px-3 py-2.5 text-xs text-slate-900">
                            {{ $report->item_code ?? '-' }} - {{ $report->item_name ?? '-' }} ({{ $report->item_size ?? '-' }})
                        </td>
                        <td class="px-3 py-2.5 text-xs text-slate-700">{{ $report->report_date ? \Carbon\Carbon::parse($report->report_date)->format('d M Y') : '-' }}</td>
                        <td class="px-3 py-2.5 text-xs text-slate-700">{{ ucfirst($report->shift ?? '-') }}</td>
                        <td class="px-3 py-2.5 text-xs text-slate-900 font-medium">{{ $actualSetShift }}/{{ $totalSetShift }}</td>
                        <td class="px-3 py-2.5 text-xs text-slate-700">{{ $manpowerWorkman }}</td>
                        <td class="px-3 py-2.5 text-xs text-slate-700">{{ $staffCount }}</td>
                        <td class="px-3 py-2.5 text-xs text-slate-700">{{ $report->created_by_name ?? 'N/A' }}</td>
                        <td class="px-3 py-2.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a
                                    href="{{ route('admin.production-reports.sf002.production.show', ['encryptedId' => \Illuminate\Support\Facades\Crypt::encryptString((string) $report->id), 'type' => $sf2Type]) }}"
                                    class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                    title="View"
                                >
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                </a>
                                <a href="{{ route('admin.production-reports.sf002.production-report', ['transferId' => $report->transfered_id, 'type' => $sf2Type, 'report_id' => \Illuminate\Support\Facades\Crypt::encryptString((string) $report->id)]) }}" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit">
                                    <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                </a>
                                <form action="{{ route('admin.production-reports.sf002.production.destroy', $report->id) }}" method="POST" class="inline js-sf2-delete-form" data-item-name="Report #{{ $report->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Delete">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-10 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">No production reports found</p>
                                    <p class="text-sm text-slate-500 mt-1">Create a production report to see records here.</p>
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

<div id="productionDetailsModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 p-4">
    <div class="mx-auto mt-10 w-full max-w-2xl rounded-2xl bg-white shadow-xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Production Details</h3>
            <button type="button" onclick="closeProductionDetailsModal()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] text-slate-500 uppercase">Report ID</p><p id="prodDetailId" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] text-slate-500 uppercase">Item</p><p id="prodDetailItem" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] text-slate-500 uppercase">Report Date</p><p id="prodDetailDate" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] text-slate-500 uppercase">Shift</p><p id="prodDetailShift" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] text-slate-500 uppercase">Total Set/Shift</p><p id="prodDetailTotal" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] text-slate-500 uppercase">Set/Hour</p><p id="prodDetailSetHour" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] text-slate-500 uppercase">Actual Set/Shift</p><p id="prodDetailActual" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] text-slate-500 uppercase">Workman</p><p id="prodDetailWorkman" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 md:col-span-2"><p class="text-[11px] text-slate-500 uppercase">Staff</p><p id="prodDetailStaff" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
        </div>
    </div>
</div>

<div id="stockDetailsModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 p-4">
    <div class="mx-auto mt-10 w-full max-w-3xl rounded-2xl bg-white shadow-xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Stock Details</h3>
                <p class="text-sm text-slate-500 mt-1">View selected stock transfer information</p>
            </div>
            <button type="button" onclick="closeStockDetailsModal()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="px-6 py-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Transfer ID</p><p id="stockDetailId" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Date & Time</p><p id="stockDetailDatetime" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Item Code</p><p id="stockDetailItemCode" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Item Name</p><p id="stockDetailItemName" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Item Size</p><p id="stockDetailItemSize" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Assign SF2</p><p id="stockDetailAssignSf2" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Accepted Quantity</p><p id="stockDetailAcceptedQty" class="text-sm font-semibold text-slate-900 mt-1">0</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Transfer By</p><p id="stockDetailTransferBy" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Accepted By</p><p id="stockDetailAcceptedBy" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 md:col-span-2"><p class="text-[11px] uppercase tracking-wider text-slate-500">SF001 Remark</p><p id="stockDetailSf001Remark" class="text-sm text-slate-800 mt-1 break-words">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 md:col-span-2"><p class="text-[11px] uppercase tracking-wider text-slate-500">{{ $sf2TypeLabel }} SF2 Remark</p><p id="stockDetailSf002Remark" class="text-sm text-slate-800 mt-1 break-words">-</p></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const remarkToggleButtons = document.querySelectorAll('.js-remark-toggle');
    const sf2DeleteForms = document.querySelectorAll('.js-sf2-delete-form');
    const stockDetailsModal = document.getElementById('stockDetailsModal');
    const productionDetailsModal = document.getElementById('productionDetailsModal');

    const stockDetailId = document.getElementById('stockDetailId');
    const stockDetailDatetime = document.getElementById('stockDetailDatetime');
    const stockDetailItemCode = document.getElementById('stockDetailItemCode');
    const stockDetailItemName = document.getElementById('stockDetailItemName');
    const stockDetailItemSize = document.getElementById('stockDetailItemSize');
    const stockDetailAssignSf2 = document.getElementById('stockDetailAssignSf2');
    const stockDetailAcceptedQty = document.getElementById('stockDetailAcceptedQty');
    const stockDetailTransferBy = document.getElementById('stockDetailTransferBy');
    const stockDetailAcceptedBy = document.getElementById('stockDetailAcceptedBy');
    const stockDetailSf001Remark = document.getElementById('stockDetailSf001Remark');
    const stockDetailSf002Remark = document.getElementById('stockDetailSf002Remark');

    const prodDetailId = document.getElementById('prodDetailId');
    const prodDetailItem = document.getElementById('prodDetailItem');
    const prodDetailDate = document.getElementById('prodDetailDate');
    const prodDetailShift = document.getElementById('prodDetailShift');
    const prodDetailTotal = document.getElementById('prodDetailTotal');
    const prodDetailSetHour = document.getElementById('prodDetailSetHour');
    const prodDetailActual = document.getElementById('prodDetailActual');
    const prodDetailWorkman = document.getElementById('prodDetailWorkman');
    const prodDetailStaff = document.getElementById('prodDetailStaff');

    window.openStockDetailsModal = function(button) {
        if (!stockDetailsModal) return;

        const isYou = button.getAttribute('data-is-you') === '1';
        const acceptedBy = button.getAttribute('data-accepted-by') || '';
        const acceptedByLabel = isYou ? 'You' : (acceptedBy || '-');

        stockDetailId.textContent = button.getAttribute('data-id') || '-';
        stockDetailDatetime.textContent = button.getAttribute('data-datetime') || '-';
        stockDetailItemCode.textContent = button.getAttribute('data-item-code') || '-';
        stockDetailItemName.textContent = button.getAttribute('data-item-name') || '-';
        stockDetailItemSize.textContent = button.getAttribute('data-item-size') || '-';
        stockDetailAssignSf2.textContent = button.getAttribute('data-assign-sf2') || '-';
        stockDetailAcceptedQty.textContent = button.getAttribute('data-accepted-quantity') || '0';
        stockDetailTransferBy.textContent = button.getAttribute('data-transfer-by') || '-';
        stockDetailAcceptedBy.textContent = acceptedByLabel;
        stockDetailSf001Remark.textContent = (button.getAttribute('data-sf001-remark') || '').trim() || '-';
        stockDetailSf002Remark.textContent = (button.getAttribute('data-sf002-remark') || '').trim() || '-';

        stockDetailsModal.classList.remove('hidden');
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    };

    window.closeStockDetailsModal = function() {
        if (!stockDetailsModal) return;
        stockDetailsModal.classList.add('hidden');
    };

    window.openProductionDetailsModal = function(button) {
        if (!productionDetailsModal) return;

        prodDetailId.textContent = button.getAttribute('data-id') || '-';
        prodDetailItem.textContent = button.getAttribute('data-item') || '-';
        prodDetailDate.textContent = button.getAttribute('data-report-date') || '-';
        prodDetailShift.textContent = button.getAttribute('data-shift') || '-';
        prodDetailTotal.textContent = button.getAttribute('data-total') || '-';
        prodDetailSetHour.textContent = button.getAttribute('data-set-hour') || '-';
        prodDetailActual.textContent = button.getAttribute('data-actual') || '-';
        prodDetailWorkman.textContent = button.getAttribute('data-workman') || '-';
        prodDetailStaff.textContent = button.getAttribute('data-staff') || '-';

        productionDetailsModal.classList.remove('hidden');
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    };

    window.closeProductionDetailsModal = function() {
        if (!productionDetailsModal) return;
        productionDetailsModal.classList.add('hidden');
    };

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

    document.addEventListener('click', function(event) {
        if (event.target === productionDetailsModal) {
            closeProductionDetailsModal();
        }

        if (event.target === stockDetailsModal) {
            closeStockDetailsModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && productionDetailsModal && !productionDetailsModal.classList.contains('hidden')) {
            closeProductionDetailsModal();
        }

        if (event.key === 'Escape' && stockDetailsModal && !stockDetailsModal.classList.contains('hidden')) {
            closeStockDetailsModal();
        }
    });

    sf2DeleteForms.forEach(function(form) {
        form.addEventListener('submit', async function(event) {
            if (typeof Swal === 'undefined') {
                return;
            }

            event.preventDefault();
            const itemName = form.getAttribute('data-item-name') || 'this report';
            const result = await Swal.fire({
                title: 'Delete report?',
                text: 'Are you sure you want to delete ' + itemName + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
            });

            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
