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
            <table class="w-full text-[13px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Date & Time</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Item Code</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Item Name</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Item Size</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Quantity</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Transfer By</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Roll Forming (SF1) Remark</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">CED & Zinc (SF2) Remark</th>
                        @if($canUpdateStatus)
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Action</th>
                        @endif
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
                        <td class="px-4 py-3 text-slate-700">{{ \Carbon\Carbon::parse($transfer->date . ' ' . $transfer->time)->format('M d, Y h:i A') }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $transfer->item_code }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $transfer->item_name }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $transfer->item_size }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold">
                                {{ number_format($transfer->quantity, 0) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ $transfer->transfer_by_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($transfer->is_accept == 1)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-green-50 text-green-700">Accepted</span>
                            @elseif($transfer->is_accept == 2)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700">Rejected</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700">Pending</span>
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
                        @if($canUpdateStatus)
                        <td class="px-4 py-3">
                            @if((int) $transfer->is_accept === 0)
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('admin.production-reports.sf002.stock.status', $transfer->id) }}" method="POST" class="js-swal-status-form" data-status-label="accept" data-item-name="{{ $transfer->item_name }}" data-current-remark="{{ $transfer->sf002_remark ?? '' }}">
                                    @csrf
                                    <input type="hidden" name="status" value="1">
                                    <input type="hidden" name="sf002_remark" value="{{ $transfer->sf002_remark ?? '' }}">
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium rounded-lg transition-all bg-green-50 text-green-700 hover:bg-green-100">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                        Accept
                                    </button>
                                </form>

                                <form action="{{ route('admin.production-reports.sf002.stock.status', $transfer->id) }}" method="POST" class="js-swal-status-form" data-status-label="reject" data-item-name="{{ $transfer->item_name }}" data-current-remark="{{ $transfer->sf002_remark ?? '' }}">
                                    @csrf
                                    <input type="hidden" name="status" value="2">
                                    <input type="hidden" name="sf002_remark" value="{{ $transfer->sf002_remark ?? '' }}">
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium rounded-lg transition-all bg-rose-50 text-rose-700 hover:bg-rose-100">
                                        <i data-lucide="x" class="w-3 h-3"></i>
                                        Reject
                                    </button>
                                </form>
                            </div>
                            @else
                            <div class="text-center text-[11px] font-medium text-slate-500">
                                Status already updated
                            </div>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $canUpdateStatus ? 11 : 10 }}" class="px-4 py-10 text-center">
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusForms = document.querySelectorAll('.js-swal-status-form');
    const remarkToggleButtons = document.querySelectorAll('.js-remark-toggle');
    const successMessage = document.getElementById('swal-success-message');
    const errorMessage = document.getElementById('swal-error-message');

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

    if (!statusForms.length || typeof Swal === 'undefined') {
        return;
    }

    statusForms.forEach(function(form) {
        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            const statusLabel = form.dataset.statusLabel || 'update';
            const itemName = form.dataset.itemName || 'this item';
            const isAccept = statusLabel === 'accept';
            const remarkInput = form.querySelector('input[name="sf002_remark"]');
            const currentRemark = form.dataset.currentRemark || '';

            const result = await Swal.fire({
                title: isAccept ? 'Accept this transfer?' : 'Reject this transfer?',
                text: `You are about to ${statusLabel} the transfer for ${itemName}.`,
                icon: 'warning',
                input: 'textarea',
                inputLabel: 'CED & Zinc (SF2) Remark',
                inputPlaceholder: 'Add remark here...',
                inputValue: currentRemark,
                inputAttributes: {
                    maxlength: 500,
                },
                showCancelButton: true,
                confirmButtonText: isAccept ? 'Yes, accept it' : 'Yes, reject it',
                cancelButtonText: 'Cancel',
                confirmButtonColor: isAccept ? '#16a34a' : '#dc2626',
                footer: '<div style="color: #dc2626; font-size: 12px; margin-top: 10px;"><strong>Note:</strong> This decision cannot be changed once submitted. Please verify before confirming.</div>',
            });

            if (result.isConfirmed) {
                if (remarkInput) {
                    remarkInput.value = result.value || '';
                }
                form.submit();
            }
        });
    });
});
</script>
@endpush
