@extends('backend.layout.app')

@section('title', 'CED & Zinc (SF2) SF1 Stock - Assigned Transfers')

@section('page-title', 'CED & Zinc (SF2) SF1 Stock Management')

@section('breadcrumb')
    <span class="text-slate-600">Production Reports</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">CED & Zinc (SF2)</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">SF1 Stock</span>
@endsection

@section('content')
<div class="p-4">
    @php
        $canUpdateStatus = auth()->user()->role === 'SF002';
    @endphp

    @if(session('success'))
        <div id="swal-success-message" class="hidden" data-message="{{ session('success') }}"></div>
    @endif

    @if(session('error'))
        <div id="swal-error-message" class="hidden" data-message="{{ session('error') }}"></div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-4 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Assigned SF1 Stock Transfers</h2>
                    <p class="text-xs text-slate-500">SF1 stock transfers assigned to this CED & Zinc (SF2) user</p>
                </div>
                <div class="text-xs">
                    <span class="text-slate-500">Total Records:</span>
                    <span class="ml-1 font-semibold text-slate-900">{{ $assignedTransfers->count() }}</span>
                </div>
                @if($canUpdateStatus && $selfTransferItems->count() > 0)
                <button type="button" onclick="openSelfTransferModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white rounded-lg hover:opacity-90 transition-all" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    <i data-lucide="repeat" class="w-3.5 h-3.5"></i>
                    Self Transfer
                </button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[1700px] w-full table-fixed text-xs">
                <thead class="border-b border-slate-200" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    <tr>
                        <th class="w-[56px] px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">#</th>
                        <th class="w-[130px] px-3 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Action</th>
                        <th class="w-[110px] px-3 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Status</th>
                        <th class="w-[120px] px-3 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Self Transferred</th>
                        <th class="w-[150px] px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">When Assigned</th>
                        <th class="w-[120px] px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Item Code</th>
                        <th class="w-[180px] px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Item Name</th>
                        <th class="w-[130px] px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Item Size</th>
                        <th class="w-[110px] px-3 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Assign SF2</th>
                        <th class="w-[130px] px-3 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Received Quantity</th>
                        <th class="w-[130px] px-3 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Rejected Quantity</th>
                        <th class="w-[160px] px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Transfer By</th>
                        <th class="w-[220px] px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Roll Forming (SF1) Remark</th>
                        <th class="w-[220px] px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">CED & Zinc (SF2) Remark</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($assignedTransfers as $index => $transfer)
                    <tr class="hover:bg-slate-50 transition-colors">
                        @php
                            $sf001Remark = trim((string) ($transfer->remark ?? ''));
                            $sf002Remark = trim((string) ($transfer->sf002_remark ?? ''));
                            $sf001ShortRemark = mb_strimwidth($sf001Remark, 0, 60, '...');
                            $sf002ShortRemark = mb_strimwidth($sf002Remark, 0, 60, '...');
                            $acceptedQuantity = max((float) $transfer->quantity - (float) ($transfer->reject_quantity ?? 0), 0);
                        @endphp
                        <td class="px-3 py-2.5 text-slate-700">{{ $index + 1 }}</td>
                        <td class="px-3 py-2.5">
                            <div class="flex items-center justify-center gap-2 flex-nowrap whitespace-nowrap">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center p-1.5 text-[11px] font-medium rounded-lg transition-all bg-blue-50 text-blue-700 hover:bg-blue-100"
                                    onclick="openDetailsModal(this)"
                                    data-id="{{ $transfer->id }}"
                                    data-item-code="{{ $transfer->item_code }}"
                                    data-item-name="{{ $transfer->item_name }}"
                                    data-item-size="{{ $transfer->item_size }}"
                                    data-assign-sf2="{{ $transfer->assign_sf2 ?? '-' }}"
                                    data-assign-role="{{ $transfer->assign_role ?? '-' }}"
                                    data-assigned-to-name="{{ $transfer->assigned_to_name ?? '-' }}"
                                    data-quantity="{{ (float) $transfer->quantity }}"
                                    data-reject-quantity="{{ (float) ($transfer->reject_quantity ?? 0) }}"
                                    data-accepted-quantity="{{ $acceptedQuantity }}"
                                    data-transfer-by="{{ $transfer->transfer_by_name ?? 'N/A' }}"
                                    data-assigned-at="{{ $transfer->created_at ? \Carbon\Carbon::parse($transfer->created_at)->format('M d, Y h:i A') : '-' }}"
                                    data-transfer-date="{{ $transfer->date ? \Carbon\Carbon::parse($transfer->date)->format('d-m-Y') : '-' }}"
                                    data-transfer-time="{{ $transfer->time ?? '-' }}"
                                    data-updated-at="{{ $transfer->updated_at ? \Carbon\Carbon::parse($transfer->updated_at)->format('M d, Y h:i A') : '-' }}"
                                    data-status="{{ (int) $transfer->is_accept }}"
                                    data-sf001-remark="{{ $transfer->remark ?? '' }}"
                                    data-sf002-remark="{{ $transfer->sf002_remark ?? '' }}"
                                    title="View Details"
                                    aria-label="View Details"
                                >
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                </button>
                                @if($canUpdateStatus)
                                @php
                                    $isUsedInSf2 = (int) ($transfer->is_used_in_sf2 ?? 0) === 1;
                                @endphp
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center p-1.5 text-[11px] font-medium rounded-lg transition-all {{ $isUsedInSf2 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-green-50 text-green-700 hover:bg-green-100' }}"
                                    {{ $isUsedInSf2 ? '' : 'onclick=openStatusModal(this)' }}
                                    data-action="{{ route('admin.production-reports.sf002.stock.status', $transfer->id) }}"
                                    data-status="1"
                                    data-item-name="{{ $transfer->item_name }}"
                                    data-quantity="{{ (float) $transfer->quantity }}"
                                    data-assign-sf2="{{ $transfer->assign_sf2 ?? '' }}"
                                    data-assigned-at="{{ $transfer->created_at ? \Carbon\Carbon::parse($transfer->created_at)->format('M d, Y h:i A') : '-' }}"
                                    data-current-remark="{{ $transfer->sf002_remark ?? '' }}"
                                    data-reject-quantity="{{ (float) ($transfer->reject_quantity ?? 0) }}"
                                    data-reject-reason-id="{{ $transfer->reject_reason_id ?? '' }}"
                                    title="{{ $isUsedInSf2 ? 'Stock used in SF2 production' : 'Accept' }}"
                                    aria-label="{{ $isUsedInSf2 ? 'Stock used in SF2 production' : 'Accept' }}"
                                    {{ $isUsedInSf2 ? 'disabled' : '' }}
                                >
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                        <td class="px-3 py-2.5 text-center">
                            @if($transfer->is_accept == 1)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-green-50 text-green-700">Accepted</span>
                            @elseif($transfer->is_accept == 2)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700">Rejected</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700">Pending</span>
                            @endif
                        </td>
                        <td class="px-3 py-2.5 text-center">
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
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600">
                                    <i data-lucide="x" class="w-3 h-3 mr-1"></i> No
                                </span>
                            @endif
                        </td>
                        <td class="px-3 py-2.5 text-slate-700">{{ $transfer->created_at ? \Carbon\Carbon::parse($transfer->created_at)->format('M d, Y h:i A') : '-' }}</td>
                        <td class="px-3 py-2.5 font-medium text-slate-900">
                            <span class="block truncate" title="{{ $transfer->item_code }}">{{ $transfer->item_code }}</span>
                        </td>
                        <td class="px-3 py-2.5 text-slate-700">
                            <span class="block truncate" title="{{ $transfer->item_name }}">{{ $transfer->item_name }}</span>
                        </td>
                        <td class="px-3 py-2.5 text-slate-700">
                            <span class="block truncate" title="{{ $transfer->item_size }}">{{ $transfer->item_size }}</span>
                        </td>
                        <td class="px-3 py-2.5 text-center">
                            @if($transfer->assign_sf2 === 'CED')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-indigo-50 text-indigo-700">CED</span>
                            @elseif($transfer->assign_sf2 === 'ZINC')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-cyan-50 text-cyan-700">ZINC</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-3 py-2.5 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold">
                                {{ number_format($transfer->quantity, 0) }}
                            </span>
                        </td>
                        <td class="px-3 py-2.5 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-rose-50 text-rose-700 text-xs font-semibold">
                                {{ number_format((float) ($transfer->reject_quantity ?? 0), 0) }}
                            </span>
                        </td>
                        <td class="px-3 py-2.5 text-slate-700">
                            <span class="block truncate" title="{{ $transfer->transfer_by_name ?? 'N/A' }}">{{ $transfer->transfer_by_name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-3 py-2.5 text-slate-700 align-top break-words">
                            @if($sf001Remark === '')
                                -
                            @elseif($sf001Remark === $sf001ShortRemark)
                                <span class="block max-w-[220px]">{{ $sf001Remark }}</span>
                            @else
                                <div class="max-w-[220px]">
                                    <span class="js-remark-short">{{ $sf001ShortRemark }}</span>
                                    <span class="js-remark-full hidden">{{ $sf001Remark }}</span>
                                    <button type="button" class="js-remark-toggle ml-1 text-[11px] font-medium text-blue-600 hover:text-blue-700">Read more</button>
                                </div>
                            @endif
                        </td>
                        <td class="px-3 py-2.5 text-slate-700 align-top break-words">
                            @if($sf002Remark === '')
                                -
                            @elseif($sf002Remark === $sf002ShortRemark)
                                <span class="block max-w-[220px]">{{ $sf002Remark }}</span>
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
                        <td colspan="14" class="px-4 py-10 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">No assigned SF1 stock found</p>
                                    <p class="text-sm text-slate-500 mt-1">There are no SF1 stock transfers assigned to you yet.</p>
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

<div id="detailsModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 p-4">
    <div class="mx-auto mt-8 w-full max-w-4xl rounded-2xl bg-white shadow-xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">SF1 Stock Transfer Details</h3>
                <p class="text-sm text-slate-500 mt-1">Complete information of the selected SF1 stock record</p>
            </div>
            <button type="button" onclick="closeDetailsModal()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="px-6 py-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Transfer ID</p><p id="detailTransferId" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Item Code</p><p id="detailItemCode" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Item Name</p><p id="detailItemName" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Item Size</p><p id="detailItemSize" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Assign SF2 Type</p><p id="detailAssignSf2" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Assign Role</p><p id="detailAssignRole" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Assigned To</p><p id="detailAssignedTo" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Transfer By</p><p id="detailTransferBy" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Status</p><p id="detailStatus" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Received Quantity</p><p id="detailQuantity" class="text-sm font-semibold text-slate-900 mt-1">0</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Rejected Quantity</p><p id="detailRejectQuantity" class="text-sm font-semibold text-slate-900 mt-1">0</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Accepted Quantity</p><p id="detailAcceptedQuantity" class="text-sm font-semibold text-slate-900 mt-1">0</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Assigned At</p><p id="detailAssignedAt" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Transfer Date</p><p id="detailTransferDate" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Transfer Time</p><p id="detailTransferTime" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 md:col-span-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Last Updated</p><p id="detailUpdatedAt" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 md:col-span-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Roll Forming (SF1) Remark</p><p id="detailSf001Remark" class="text-sm text-slate-800 mt-1 break-words">-</p></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 md:col-span-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">CED & Zinc (SF2) Remark</p><p id="detailSf002Remark" class="text-sm text-slate-800 mt-1 break-words">-</p></div>
            </div>
        </div>
    </div>
</div>

<div id="statusUpdateModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 p-4">
    <div class="mx-auto mt-10 w-full max-w-2xl rounded-2xl bg-white shadow-xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 id="statusModalTitle" class="text-base font-bold text-slate-900">Update Transfer Status</h3>
                <p id="statusModalSubtitle" class="text-sm text-slate-500 mt-1"></p>
            </div>
            <button type="button" onclick="closeStatusModal()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="statusUpdateForm" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="status" id="status_field" value="1">
            <input type="hidden" name="accept_all_quantity" id="accept_all_quantity_field" value="1">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500">Quantity</p>
                    <p id="statusModalQuantity" class="text-sm font-semibold text-slate-900 mt-1">-</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500">Assign SF2</p>
                    <p id="statusModalAssignSf2" class="text-sm font-semibold text-slate-900 mt-1">-</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500">When Assigned</p>
                    <p id="statusModalAssignedAt" class="text-sm font-semibold text-slate-900 mt-1">-</p>
                </div>
            </div>

            <label class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 cursor-pointer">
                <span class="text-sm font-medium text-slate-700">Accept all quantity</span>
                <span class="relative inline-flex items-center">
                    <input type="checkbox" id="accept_all_quantity_toggle" class="sr-only" checked>
                    <span id="accept_all_quantity_track" class="w-11 h-6 rounded-full bg-green-500 transition-colors"></span>
                    <span id="accept_all_quantity_thumb" class="absolute left-6 w-4 h-4 rounded-full bg-white transition-all"></span>
                </span>
            </label>

            <div id="reject_quantity_group" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="reject_quantity_field" class="block text-sm font-semibold text-slate-700 mb-2">Reject Quantity</label>
                        <input type="number" id="reject_quantity_field" name="reject_quantity" min="0" step="0.01" value="0" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter reject quantity">
                        <p class="mt-1 text-xs text-slate-500">If no value entered, it will be saved as 0.</p>
                    </div>

                    <div>
                        <label for="reject_reason_id_field" class="block text-sm font-semibold text-slate-700 mb-2">Reject Reason</label>
                        <select id="reject_reason_id_field" name="reject_reason_id" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select reject reason</option>
                            @foreach(($rejectReasons ?? collect()) as $reason)
                                <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-500">Required if you enter a reject quantity.</p>
                    </div>
                </div>
            </div>

            <div>
                <label for="status_sf002_remark" class="block text-sm font-semibold text-slate-700 mb-2">CED & Zinc (SF2) Remark</label>
                <textarea id="status_sf002_remark" name="sf002_remark" rows="3" maxlength="500" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Add remark here..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeStatusModal()" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">Cancel</button>
                <button type="submit" id="status_confirm_button" class="px-4 py-2 rounded-lg text-white font-medium transition-colors bg-green-600 hover:bg-green-700">Confirm</button>
            </div>
        </form>
    </div>
</div>
@endsection

@if($canUpdateStatus && $selfTransferItems->count() > 0)
<!-- Self Transfer Modal -->
<div id="selfTransferModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 p-4">
    <div class="mx-auto mt-10 w-full max-w-2xl rounded-2xl bg-white shadow-xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center">
                    <i data-lucide="repeat" class="w-5 h-5 text-violet-600"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Self Transfer</h3>
                    <p class="text-xs text-slate-500">Transfer stock between CED and ZINC</p>
                </div>
            </div>
            <button type="button" onclick="closeSelfTransferModal()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="selfTransferForm" action="{{ route('admin.production-reports.sf002.sf2-stock.self-transfer') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="item_id" id="self_transfer_item_id">
            <input type="hidden" name="date" id="self_transfer_date">
            <input type="hidden" name="time" id="self_transfer_time">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="self_transfer_item_select" class="block text-sm font-semibold text-slate-700 mb-2">Item <span class="text-rose-500">*</span></label>
                    <select id="self_transfer_item_select" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="onSelfTransferItemChange()">
                        <option value="">Select Item</option>
                        @foreach($selfTransferItems as $item)
                            <option value="{{ $item->id }}" data-code="{{ $item->code }}" data-name="{{ $item->name }}" data-size="{{ $item->size }}" data-type="{{ $item->assign_sf2 }}" data-accepted-qty="{{ (int) $item->accepted_quantity }}">{{ $item->code }} - {{ $item->name }} ({{ strtoupper($item->assign_sf2) }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">From Type</label>
                    <input type="text" id="self_transfer_from_display" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-medium">
                    <input type="hidden" name="from_type" id="self_transfer_from_type">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">To Type</label>
                    <input type="text" id="self_transfer_to_display" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-indigo-50 text-indigo-700 font-semibold">
                    <input type="hidden" name="to_type" id="self_transfer_to_type">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Accepted Quantity</label>
                    <input type="text" id="self_transfer_accepted_qty" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700">
                </div>

                <div>
                    <label for="self_transfer_quantity" class="block text-sm font-semibold text-slate-700 mb-2">Quantity to Transfer <span class="text-rose-500">*</span></label>
                    <input type="number" id="self_transfer_quantity" name="quantity" required min="1" step="1"
                        class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter quantity">
                    <p id="self_transfer_quantity_help" class="mt-1 text-xs text-slate-500"></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Date & Time</label>
                    <input type="text" id="self_transfer_display_datetime" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Item Size</label>
                    <input type="text" id="self_transfer_item_size" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700">
                </div>

                <div class="md:col-span-2">
                    <label for="self_transfer_remark" class="block text-sm font-semibold text-slate-700 mb-2">Remark (Optional)</label>
                    <textarea id="self_transfer_remark" name="remark" rows="2" maxlength="500"
                        class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Add optional remark..."></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeSelfTransferModal()" class="px-4 py-2.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors font-medium">Cancel</button>
                <button id="self_transfer_submit_btn" type="submit" class="px-4 py-2.5 text-white text-sm font-medium rounded-lg hover:opacity-90 transition-colors" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    Save Self Transfer
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const remarkToggleButtons = document.querySelectorAll('.js-remark-toggle');
    const successMessage = document.getElementById('swal-success-message');
    const errorMessage = document.getElementById('swal-error-message');

    const detailsModal = document.getElementById('detailsModal');
    const detailTransferId = document.getElementById('detailTransferId');
    const detailItemCode = document.getElementById('detailItemCode');
    const detailItemName = document.getElementById('detailItemName');
    const detailItemSize = document.getElementById('detailItemSize');
    const detailAssignSf2 = document.getElementById('detailAssignSf2');
    const detailAssignRole = document.getElementById('detailAssignRole');
    const detailAssignedTo = document.getElementById('detailAssignedTo');
    const detailTransferBy = document.getElementById('detailTransferBy');
    const detailStatus = document.getElementById('detailStatus');
    const detailQuantity = document.getElementById('detailQuantity');
    const detailRejectQuantity = document.getElementById('detailRejectQuantity');
    const detailAcceptedQuantity = document.getElementById('detailAcceptedQuantity');
    const detailAssignedAt = document.getElementById('detailAssignedAt');
    const detailTransferDate = document.getElementById('detailTransferDate');
    const detailTransferTime = document.getElementById('detailTransferTime');
    const detailUpdatedAt = document.getElementById('detailUpdatedAt');
    const detailSf001Remark = document.getElementById('detailSf001Remark');
    const detailSf002Remark = document.getElementById('detailSf002Remark');

    const statusModal = document.getElementById('statusUpdateModal');
    const statusForm = document.getElementById('statusUpdateForm');
    const statusField = document.getElementById('status_field');
    const acceptAllField = document.getElementById('accept_all_quantity_field');
    const rejectQuantityField = document.getElementById('reject_quantity_field');
    const rejectReasonField = document.getElementById('reject_reason_id_field');
    const rejectQuantityGroup = document.getElementById('reject_quantity_group');
    const acceptAllToggle = document.getElementById('accept_all_quantity_toggle');
    const acceptAllTrack = document.getElementById('accept_all_quantity_track');
    const acceptAllThumb = document.getElementById('accept_all_quantity_thumb');
    const confirmButton = document.getElementById('status_confirm_button');
    const modalTitle = document.getElementById('statusModalTitle');
    const modalSubtitle = document.getElementById('statusModalSubtitle');
    const modalQuantity = document.getElementById('statusModalQuantity');
    const modalAssignSf2 = document.getElementById('statusModalAssignSf2');
    const modalAssignedAt = document.getElementById('statusModalAssignedAt');
    const modalRemark = document.getElementById('status_sf002_remark');

    let activeQuantity = 0;

    function updateToggleUI() {
        const isChecked = !!acceptAllToggle.checked;
        acceptAllField.value = isChecked ? '1' : '0';

        if (isChecked) {
            rejectQuantityGroup.classList.add('hidden');
            rejectQuantityField.value = '0';
            if (rejectReasonField) {
                rejectReasonField.value = '';
            }
            acceptAllTrack.classList.remove('bg-slate-300');
            acceptAllTrack.classList.add('bg-green-500');
            acceptAllThumb.classList.remove('left-1');
            acceptAllThumb.classList.add('left-6');
        } else {
            rejectQuantityGroup.classList.remove('hidden');
            acceptAllTrack.classList.remove('bg-green-500');
            acceptAllTrack.classList.add('bg-slate-300');
            acceptAllThumb.classList.remove('left-6');
            acceptAllThumb.classList.add('left-1');
        }
    }

    function getStatusLabel(statusCode) {
        const normalized = parseInt(statusCode || '0', 10);
        if (normalized === 1) return 'Accepted';
        if (normalized === 2) return 'Rejected';
        return 'Pending';
    }

    window.openDetailsModal = function(button) {
        detailTransferId.textContent = button.getAttribute('data-id') || '-';
        detailItemCode.textContent = button.getAttribute('data-item-code') || '-';
        detailItemName.textContent = button.getAttribute('data-item-name') || '-';
        detailItemSize.textContent = button.getAttribute('data-item-size') || '-';
        detailAssignSf2.textContent = button.getAttribute('data-assign-sf2') || '-';
        detailAssignRole.textContent = button.getAttribute('data-assign-role') || '-';
        detailAssignedTo.textContent = button.getAttribute('data-assigned-to-name') || '-';
        detailTransferBy.textContent = button.getAttribute('data-transfer-by') || '-';
        detailStatus.textContent = getStatusLabel(button.getAttribute('data-status'));
        detailQuantity.textContent = Math.round(parseFloat(button.getAttribute('data-quantity') || '0')).toString();
        detailRejectQuantity.textContent = Math.round(parseFloat(button.getAttribute('data-reject-quantity') || '0')).toString();
        detailAcceptedQuantity.textContent = Math.round(parseFloat(button.getAttribute('data-accepted-quantity') || '0')).toString();
        detailAssignedAt.textContent = button.getAttribute('data-assigned-at') || '-';
        detailTransferDate.textContent = button.getAttribute('data-transfer-date') || '-';
        detailTransferTime.textContent = button.getAttribute('data-transfer-time') || '-';
        detailUpdatedAt.textContent = button.getAttribute('data-updated-at') || '-';

        const sf001Remark = (button.getAttribute('data-sf001-remark') || '').trim();
        const sf002Remark = (button.getAttribute('data-sf002-remark') || '').trim();
        detailSf001Remark.textContent = sf001Remark || '-';
        detailSf002Remark.textContent = sf002Remark || '-';

        detailsModal.classList.remove('hidden');
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    };

    window.closeDetailsModal = function() {
        detailsModal.classList.add('hidden');
    };

    window.openStatusModal = function(button) {
        const status = button.getAttribute('data-status') || '1';
        const isAccept = status === '1';

        statusForm.action = button.getAttribute('data-action') || '';
        statusField.value = status;
        activeQuantity = parseFloat(button.getAttribute('data-quantity') || '0');

        modalTitle.textContent = isAccept ? 'Accept Transfer' : 'Reject Transfer';
        modalSubtitle.textContent = (button.getAttribute('data-item-name') || 'Item') + (isAccept ? ' will be accepted.' : ' will be rejected.');
        modalQuantity.textContent = Math.round(activeQuantity).toString();
        modalAssignSf2.textContent = button.getAttribute('data-assign-sf2') || '-';
        modalAssignedAt.textContent = button.getAttribute('data-assigned-at') || '-';
        modalRemark.value = button.getAttribute('data-current-remark') || '';

        confirmButton.textContent = isAccept ? 'Confirm Accept' : 'Confirm Reject';
        confirmButton.classList.toggle('bg-green-600', isAccept);
        confirmButton.classList.toggle('hover:bg-green-700', isAccept);
        confirmButton.classList.toggle('bg-rose-600', !isAccept);
        confirmButton.classList.toggle('hover:bg-rose-700', !isAccept);
        confirmButton.classList.remove('opacity-60', 'cursor-not-allowed');
        confirmButton.disabled = false;

        const prevRejectQty = parseFloat(button.getAttribute('data-reject-quantity') || '0');
        const prevRejectReasonId = button.getAttribute('data-reject-reason-id') || '';

        if (prevRejectQty > 0) {
            acceptAllToggle.checked = false;
            rejectQuantityField.max = activeQuantity.toString();
            rejectQuantityField.value = prevRejectQty.toString();
            if (rejectReasonField) {
                rejectReasonField.value = prevRejectReasonId;
            }
        } else {
            acceptAllToggle.checked = true;
            rejectQuantityField.max = activeQuantity.toString();
            rejectQuantityField.value = '0';
            if (rejectReasonField) {
                rejectReasonField.value = '';
            }
        }
        updateToggleUI();

        statusModal.classList.remove('hidden');
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    };

    window.closeStatusModal = function() {
        statusModal.classList.add('hidden');
    };

    acceptAllToggle.addEventListener('change', updateToggleUI);

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

    if (typeof Swal !== 'undefined') {
        if (successMessage) {
            Swal.fire({
                icon: 'success',
                title: 'Status Updated',
                text: successMessage.dataset.message,
                confirmButtonColor: '#16a34a',
            });
        }

        if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Update Not Allowed',
                text: errorMessage.dataset.message,
                confirmButtonColor: '#dc2626',
            });
        }
    }

    statusForm.addEventListener('submit', function(event) {
        const isAcceptAll = acceptAllToggle.checked;
        const rejectQuantity = parseFloat(rejectQuantityField.value || '0');
        const status = parseInt(statusField.value || '1', 10);

        if (!isAcceptAll) {
            if (Number.isNaN(rejectQuantity) || rejectQuantity < 0) {
                event.preventDefault();
                alert('Please enter a valid reject quantity.');
                return;
            }

            if (rejectQuantity > 0 && rejectReasonField && (rejectReasonField.value || '').trim() === '') {
                event.preventDefault();
                alert('Please select a reject reason.');
                return;
            }

            if (rejectQuantity > activeQuantity) {
                event.preventDefault();
                alert('Reject quantity cannot be greater than transfer quantity.');
                return;
            }

            if (status === 1 && rejectQuantity >= activeQuantity) {
                event.preventDefault();
                alert('Accepted quantity must be greater than zero.');
                return;
            }
        }

        confirmButton.disabled = true;
        confirmButton.classList.add('opacity-60', 'cursor-not-allowed');
        confirmButton.textContent = 'Saving...';
    });

    document.addEventListener('click', function(event) {
        if (event.target === detailsModal) {
            closeDetailsModal();
        }

        if (event.target === statusModal) {
            closeStatusModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !detailsModal.classList.contains('hidden')) {
            closeDetailsModal();
        }

        if (event.key === 'Escape' && !statusModal.classList.contains('hidden')) {
            closeStatusModal();
        }

        const selfModal = document.getElementById('selfTransferModal');
        if (event.key === 'Escape' && selfModal && !selfModal.classList.contains('hidden')) {
            closeSelfTransferModal();
        }
    });
});

// ── Self Transfer Modal ──
let selfTransferSubmitting = false;

function formatSelfDate(d) {
    return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
}
function formatSelfTime(d) {
    return String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0') + ':' + String(d.getSeconds()).padStart(2,'0');
}
function formatSelfDisplay(d) {
    return d.toLocaleString('en-IN', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit', second:'2-digit', hour12:true });
}

window.openSelfTransferModal = function() {
    const modal = document.getElementById('selfTransferModal');
    if (!modal) return;

    document.getElementById('self_transfer_item_select').value = '';
    document.getElementById('self_transfer_from_type').value = '';
    document.getElementById('self_transfer_from_display').value = '';
    document.getElementById('self_transfer_to_type').value = '';
    document.getElementById('self_transfer_to_display').value = '';
    document.getElementById('self_transfer_item_id').value = '';
    document.getElementById('self_transfer_quantity').value = '';
    document.getElementById('self_transfer_item_size').value = '';
    document.getElementById('self_transfer_accepted_qty').value = '';
    document.getElementById('self_transfer_quantity_help').innerText = '';
    document.getElementById('self_transfer_remark').value = '';

    const now = new Date();
    document.getElementById('self_transfer_date').value = formatSelfDate(now);
    document.getElementById('self_transfer_time').value = formatSelfTime(now);
    document.getElementById('self_transfer_display_datetime').value = formatSelfDisplay(now);

    selfTransferSubmitting = false;
    const btn = document.getElementById('self_transfer_submit_btn');
    if (btn) {
        btn.disabled = false;
        btn.classList.remove('opacity-60', 'cursor-not-allowed');
        btn.textContent = 'Save Self Transfer';
    }

    modal.classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
};

window.closeSelfTransferModal = function() {
    const modal = document.getElementById('selfTransferModal');
    if (modal) modal.classList.add('hidden');
};

window.onSelfTransferItemChange = function() {
    const sel = document.getElementById('self_transfer_item_select');
    const opt = sel.options[sel.selectedIndex];
    document.getElementById('self_transfer_item_id').value = sel.value;
    document.getElementById('self_transfer_item_size').value = opt && sel.value ? opt.getAttribute('data-size') || '' : '';

    if (opt && sel.value) {
        const fromType = opt.getAttribute('data-type') || '';
        const toType = fromType === 'ced' ? 'zinc' : 'ced';
        const acceptedQty = parseInt(opt.getAttribute('data-accepted-qty') || '0', 10);

        document.getElementById('self_transfer_from_type').value = fromType;
        document.getElementById('self_transfer_from_display').value = fromType.toUpperCase();
        document.getElementById('self_transfer_to_type').value = toType;
        document.getElementById('self_transfer_to_display').value = toType.toUpperCase();
        document.getElementById('self_transfer_accepted_qty').value = acceptedQty;
        document.getElementById('self_transfer_quantity').max = acceptedQty;
        document.getElementById('self_transfer_quantity_help').innerText = 'Max allowed: ' + acceptedQty;
    } else {
        document.getElementById('self_transfer_from_type').value = '';
        document.getElementById('self_transfer_from_display').value = '';
        document.getElementById('self_transfer_to_type').value = '';
        document.getElementById('self_transfer_to_display').value = '';
        document.getElementById('self_transfer_accepted_qty').value = '';
        document.getElementById('self_transfer_quantity_help').innerText = '';
    }
};

(function() {
    const form = document.getElementById('selfTransferForm');
    if (!form) return;

    form.addEventListener('submit', function(event) {
        const submitBtn = document.getElementById('self_transfer_submit_btn');

        if (selfTransferSubmitting) {
            event.preventDefault();
            return;
        }

        const itemId = document.getElementById('self_transfer_item_id').value;
        const fromType = document.getElementById('self_transfer_from_type').value;
        const quantity = parseFloat(document.getElementById('self_transfer_quantity').value || '0');
        const acceptedQty = parseInt(document.getElementById('self_transfer_accepted_qty').value || '0', 10);

        if (!itemId || !fromType || quantity <= 0) {
            event.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Missing Fields', text: 'Please select item and enter quantity.', confirmButtonColor: '#4f46e5' });
            return;
        }

        if (quantity > acceptedQty) {
            event.preventDefault();
            Swal.fire({ icon: 'error', title: 'Invalid Quantity', text: 'Quantity cannot exceed accepted quantity (' + acceptedQty + ').', confirmButtonColor: '#4f46e5' });
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                submitBtn.textContent = 'Save Self Transfer';
            }
            selfTransferSubmitting = false;
            return;
        }

        selfTransferSubmitting = true;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
            submitBtn.textContent = 'Transferring...';
        }
    });
})();
</script>
@endpush
