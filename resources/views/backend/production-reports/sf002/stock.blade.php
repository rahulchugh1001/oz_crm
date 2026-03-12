@extends('backend.layout.app')

@section('title', 'CED & Zinc (SF2) Stock - Assigned Transfers')

@section('page-title', 'CED & Zinc (SF2) Stock Management')

@section('breadcrumb')
    <span class="text-slate-600">Production Reports</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">CED & Zinc (SF2)</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Stock</span>
@endsection

@section('content')
<div class="p-6">
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
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Assigned Stock Transfers</h2>
                    <p class="text-sm text-slate-500">Stock transfers assigned from SF001 to this CED & Zinc (SF2) user</p>
                </div>
                <div class="text-sm">
                    <span class="text-slate-500">Total Records:</span>
                    <span class="ml-1 font-semibold text-slate-900">{{ $assignedTransfers->count() }}</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[1700px] w-full table-fixed text-[13px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="w-[56px] px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">#</th>
                        @if($canUpdateStatus)
                        <th class="w-[96px] px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Action</th>
                        @endif
                        <th class="w-[150px] px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">When Assigned</th>
                        <th class="w-[120px] px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Item Code</th>
                        <th class="w-[180px] px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Item Name</th>
                        <th class="w-[130px] px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Item Size</th>
                        <th class="w-[110px] px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Assign SF2</th>
                        <th class="w-[130px] px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Received Quantity</th>
                        <th class="w-[130px] px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Rejected Quantity</th>
                        <th class="w-[160px] px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Transfer By</th>
                        <th class="w-[110px] px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="w-[220px] px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Roll Forming (SF1) Remark</th>
                        <th class="w-[220px] px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">CED & Zinc (SF2) Remark</th>
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
                        @endphp
                        <td class="px-4 py-3 text-slate-700">{{ $index + 1 }}</td>
                        @if($canUpdateStatus)
                        <td class="px-4 py-3">
                            @if((int) $transfer->is_accept === 0)
                            <div class="flex items-center justify-center gap-2 flex-nowrap whitespace-nowrap">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center p-2 text-[11px] font-medium rounded-lg transition-all bg-green-50 text-green-700 hover:bg-green-100"
                                    onclick="openStatusModal(this)"
                                    data-action="{{ route('admin.production-reports.sf002.stock.status', $transfer->id) }}"
                                    data-status="1"
                                    data-item-name="{{ $transfer->item_name }}"
                                    data-quantity="{{ (float) $transfer->quantity }}"
                                    data-assign-sf2="{{ $transfer->assign_sf2 ?? '' }}"
                                    data-assigned-at="{{ $transfer->created_at ? \Carbon\Carbon::parse($transfer->created_at)->format('M d, Y h:i A') : '-' }}"
                                    data-current-remark="{{ $transfer->sf002_remark ?? '' }}"
                                    title="Accept"
                                    aria-label="Accept"
                                >
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                </button>

                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center p-2 text-[11px] font-medium rounded-lg transition-all bg-rose-50 text-rose-700 hover:bg-rose-100"
                                    onclick="openStatusModal(this)"
                                    data-action="{{ route('admin.production-reports.sf002.stock.status', $transfer->id) }}"
                                    data-status="2"
                                    data-item-name="{{ $transfer->item_name }}"
                                    data-quantity="{{ (float) $transfer->quantity }}"
                                    data-assign-sf2="{{ $transfer->assign_sf2 ?? '' }}"
                                    data-assigned-at="{{ $transfer->created_at ? \Carbon\Carbon::parse($transfer->created_at)->format('M d, Y h:i A') : '-' }}"
                                    data-current-remark="{{ $transfer->sf002_remark ?? '' }}"
                                    title="Reject"
                                    aria-label="Reject"
                                >
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                            @else
                            <div class="text-center text-[11px] font-medium text-slate-500">
                                Done
                            </div>
                            @endif
                        </td>
                        @endif
                        <td class="px-4 py-3 text-slate-700">{{ $transfer->created_at ? \Carbon\Carbon::parse($transfer->created_at)->format('M d, Y h:i A') : '-' }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            <span class="block truncate" title="{{ $transfer->item_code }}">{{ $transfer->item_code }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-700">
                            <span class="block truncate" title="{{ $transfer->item_name }}">{{ $transfer->item_name }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-700">
                            <span class="block truncate" title="{{ $transfer->item_size }}">{{ $transfer->item_size }}</span>
                        </td>
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
                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold">
                                {{ number_format($transfer->quantity, 0) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-rose-50 text-rose-700 text-xs font-semibold">
                                {{ number_format((float) ($transfer->reject_quantity ?? 0), 0) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-700">
                            <span class="block truncate" title="{{ $transfer->transfer_by_name ?? 'N/A' }}">{{ $transfer->transfer_by_name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($transfer->is_accept == 1)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-green-50 text-green-700">Accepted</span>
                            @elseif($transfer->is_accept == 2)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700">Rejected</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-700 align-top break-words">
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
                        <td class="px-4 py-3 text-slate-700 align-top break-words">
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
                        <td colspan="{{ $canUpdateStatus ? 13 : 12 }}" class="px-4 py-10 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">No assigned stock found</p>
                                    <p class="text-sm text-slate-500 mt-1">There are no stock transfers assigned to you yet.</p>
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
                <label for="reject_quantity_field" class="block text-sm font-semibold text-slate-700 mb-2">Reject Quantity</label>
                <input type="number" id="reject_quantity_field" name="reject_quantity" min="0" step="0.01" value="0" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter reject quantity">
                <p class="mt-1 text-xs text-slate-500">If no value entered, it will be saved as 0.</p>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const remarkToggleButtons = document.querySelectorAll('.js-remark-toggle');
    const successMessage = document.getElementById('swal-success-message');
    const errorMessage = document.getElementById('swal-error-message');

    const statusModal = document.getElementById('statusUpdateModal');
    const statusForm = document.getElementById('statusUpdateForm');
    const statusField = document.getElementById('status_field');
    const acceptAllField = document.getElementById('accept_all_quantity_field');
    const rejectQuantityField = document.getElementById('reject_quantity_field');
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

        acceptAllToggle.checked = true;
        rejectQuantityField.max = activeQuantity.toString();
        rejectQuantityField.value = '0';
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
        if (event.target === statusModal) {
            closeStatusModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !statusModal.classList.contains('hidden')) {
            closeStatusModal();
        }
    });
});
</script>
@endpush
