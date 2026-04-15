@extends('backend.layout.app')

@section('title', 'PPC - Pending Transfers')

@section('page-title', 'PPC Management')

@section('breadcrumb')
    <span class="text-slate-600">Production Reports</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">PPC</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Pending Transfers</span>
@endsection

@section('content')
<div class="p-6">
    @php
        $canUpdateStatus = auth()->user()->role === 'PPC' || auth()->user()->role === 'Admin';
    @endphp

    @if(session('success'))
        <div id="swal-success-message" class="hidden" data-message="{{ session('success') }}"></div>
    @endif

    @if(session('error'))
        <div id="swal-error-message" class="hidden" data-message="{{ session('error') }}"></div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-3 rounded-lg border border-rose-200 bg-rose-50 text-rose-800 text-sm">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Pending Transfers</h2>
                    <p class="text-sm text-slate-500">Incoming transfers awaiting PPC verification and acceptance</p>
                </div>
                <div class="text-sm">
                    <span class="text-slate-500">Total Pending:</span>
                    <span class="ml-1 font-semibold text-slate-900">{{ $pendingTransfers->count() }}</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[1300px] w-full table-fixed text-[13px]">
                <thead class="border-b border-slate-200" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    <tr>
                        <th class="w-[56px] px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">#</th>
                        <th class="w-[120px] px-4 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Action</th>
                        <th class="w-[150px] px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Date & Time</th>
                        <th class="w-[100px] px-4 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Type</th>
                        <th class="w-[120px] px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Item Code</th>
                        <th class="w-[180px] px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Item Name</th>
                        <th class="w-[130px] px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Item Size</th>
                        <th class="w-[120px] px-4 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Quantity</th>
                        <th class="w-[150px] px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Transfer By</th>
                        <th class="w-[200px] px-4 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Remark</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($pendingTransfers as $index => $transfer)
                    <tr class="hover:bg-slate-50 transition-colors">
                        @php
                            $transferDateTime = \Carbon\Carbon::parse($transfer->date . ' ' . $transfer->time)->format('M d, Y h:i A');
                            $sf002Remark = trim((string) ($transfer->remark ?? ''));
                        @endphp
                        <td class="px-4 py-3 text-slate-700">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($canUpdateStatus)
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center p-2 text-[11px] font-medium rounded-lg transition-all bg-green-50 text-green-700 hover:bg-green-100"
                                        onclick="openStatusModal(this)"
                                        data-action="{{ route('admin.production-reports.ppc.process.status', $transfer->id) }}"
                                        data-status="1"
                                        data-item-name="{{ $transfer->item_name }}"
                                        data-quantity="{{ (float) $transfer->quantity }}"
                                        data-type="{{ strtoupper($transfer->type) }}"
                                        data-date="{{ $transferDateTime }}"
                                        title="Accept"
                                    >
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            @else
                                <span class="text-slate-400 text-xs">No Access</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ $transferDateTime }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-indigo-50 text-indigo-700">{{ strtoupper($transfer->type) }}</span>
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $transfer->item_code }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $transfer->item_name }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $transfer->item_size }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold">
                                {{ number_format($transfer->quantity, 0) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ $transfer->transfer_by_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-slate-700 align-top break-words">
                            {{ $sf002Remark ?: '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-10 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">No pending transfers found</p>
                                    <p class="text-sm text-slate-500 mt-1">There are no pending SF2 transfers to PPC at this time.</p>
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
                <h3 id="statusModalTitle" class="text-base font-bold text-slate-900">Verify Transfer</h3>
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
                    <p class="text-[11px] uppercase tracking-wider text-slate-500">Source Type</p>
                    <p id="statusModalType" class="text-sm font-semibold text-slate-900 mt-1">-</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500">Date</p>
                    <p id="statusModalDate" class="text-sm font-semibold text-slate-900 mt-1">-</p>
                </div>
            </div>

            <!-- Full Accept / Reject Toggle Container -->
            <div class="border border-slate-200 rounded-xl p-4 space-y-3">
                <p class="text-sm font-semibold text-slate-800">Action Type</p>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="action_type" value="accept_all" class="text-green-600 focus:ring-green-500" checked onchange="handleActionTypeChange()">
                        <span class="text-sm font-medium text-green-700">Accept All Quantity</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="action_type" value="partial_reject" class="text-amber-600 focus:ring-amber-500" onchange="handleActionTypeChange()">
                        <span class="text-sm font-medium text-amber-700">Partially Reject</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="action_type" value="reject_all" class="text-rose-600 focus:ring-rose-500" onchange="handleActionTypeChange()">
                        <span class="text-sm font-medium text-rose-700">Reject Entire Record</span>
                    </label>
                </div>
            </div>

            <div id="reject_quantity_group" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="reject_quantity_field" class="block text-sm font-semibold text-slate-700 mb-2">Reject Quantity</label>
                        <input type="number" id="reject_quantity_field" name="reject_quantity" min="0" step="1" value="0" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter reject quantity">
                    </div>

                    <div>
                        <label for="reject_reason_id_field" class="block text-sm font-semibold text-slate-700 mb-2">Reject Reason</label>
                        <select id="reject_reason_id_field" name="reject_reason_id" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select reject reason</option>
                            @foreach(($rejectReasons ?? collect()) as $reason)
                                <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label for="ppc_remark" class="block text-sm font-semibold text-slate-700 mb-2">PPC Remark (Optional)</label>
                <textarea id="ppc_remark" name="ppc_remark" rows="2" maxlength="500" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Add remark here..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeStatusModal()" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">Cancel</button>
                <button type="submit" id="status_confirm_button" class="px-4 py-2 rounded-lg text-white font-medium transition-colors bg-green-600 hover:bg-green-700">Confirm Process</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentQuantity = 0;

    function handleActionTypeChange() {
        const type = document.querySelector('input[name="action_type"]:checked').value;
        const rejectGroup = document.getElementById('reject_quantity_group');
        const rejectQtyInput = document.getElementById('reject_quantity_field');
        const rejectReasonSelect = document.getElementById('reject_reason_id_field');
        const acceptAllInput = document.getElementById('accept_all_quantity_field');
        const statusField = document.getElementById('status_field');
        const confirmBtn = document.getElementById('status_confirm_button');
        
        if (type === 'accept_all') {
            rejectGroup.classList.add('hidden');
            rejectQtyInput.value = 0;
            rejectQtyInput.readOnly = true;
            acceptAllInput.value = '1';
            statusField.value = '1'; 
            confirmBtn.className = 'px-4 py-2 rounded-lg text-white font-medium transition-colors bg-green-600 hover:bg-green-700';
            confirmBtn.textContent = 'Accept All';
        } else if (type === 'partial_reject') {
            rejectGroup.classList.remove('hidden');
            rejectQtyInput.value = '';
            rejectQtyInput.max = currentQuantity - 1;
            rejectQtyInput.readOnly = false;
            acceptAllInput.value = '0';
            statusField.value = '1'; // Partial means record accepted, some rejected
            confirmBtn.className = 'px-4 py-2 rounded-lg text-white font-medium transition-colors bg-amber-600 hover:bg-amber-700';
            confirmBtn.textContent = 'Process with Rejection';
        } else if (type === 'reject_all') {
            rejectGroup.classList.remove('hidden');
            rejectQtyInput.value = currentQuantity;
            rejectQtyInput.readOnly = true;
            acceptAllInput.value = '0';
            statusField.value = '2'; // Status = 2 for fully rejected record
            confirmBtn.className = 'px-4 py-2 rounded-lg text-white font-medium transition-colors bg-rose-600 hover:bg-rose-700';
            confirmBtn.textContent = 'Reject Record completely';
        }
    }

    window.openStatusModal = function(button) {
        document.getElementById('statusUpdateForm').action = button.getAttribute('data-action');
        currentQuantity = parseFloat(button.getAttribute('data-quantity') || '0');
        
        document.getElementById('statusModalTitle').textContent = 'Verify Transfer';
        document.getElementById('statusModalSubtitle').textContent = 'Process ' + button.getAttribute('data-item-name');
        
        document.getElementById('statusModalQuantity').textContent = Math.round(currentQuantity);
        document.getElementById('statusModalType').textContent = button.getAttribute('data-type');
        document.getElementById('statusModalDate').textContent = button.getAttribute('data-date');
        
        // Reset form
        document.querySelector('input[name="action_type"][value="accept_all"]').checked = true;
        document.getElementById('reject_reason_id_field').value = '';
        document.getElementById('ppc_remark').value = '';
        handleActionTypeChange();

        document.getElementById('statusUpdateModal').classList.remove('hidden');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    };

    window.closeStatusModal = function() {
        document.getElementById('statusUpdateModal').classList.add('hidden');
    };

    document.getElementById('statusUpdateForm').addEventListener('submit', function(e) {
        const type = document.querySelector('input[name="action_type"]:checked').value;
        const reason = document.getElementById('reject_reason_id_field').value;
        const qty = parseFloat(document.getElementById('reject_quantity_field').value || 0);

        if (type === 'partial_reject') {
            if (qty <= 0 || qty >= currentQuantity) {
               e.preventDefault();
               alert('For Partial Reject, reject quantity must be between 1 and ' + (currentQuantity - 1));
               return;
            }
            if (!reason) {
                e.preventDefault();
                alert('Please select a Reject Reason.');
                return;
            }
        } else if (type === 'reject_all') {
            if (!reason) {
                e.preventDefault();
                alert('Please select a Reject Reason.');
                return;
            }
        }
    });

    if (typeof Swal !== 'undefined') {
        const successMessage = document.getElementById('swal-success-message');
        const errorMessage = document.getElementById('swal-error-message');
        if (successMessage) {
            Swal.fire({
                icon: 'success',
                title: 'Data Verified',
                text: successMessage.dataset.message,
                confirmButtonColor: '#16a34a',
            });
        }
        if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Error processing data',
                text: errorMessage.dataset.message,
                confirmButtonColor: '#dc2626',
            });
        }
    }
</script>
@endpush
