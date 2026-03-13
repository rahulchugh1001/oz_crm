@extends('backend.layout.app')

@section('title', 'CED & Zinc (SF2) - SF2 Stock')

@section('page-title', 'CED & Zinc (SF2) - SF2 Stock Management')

@section('breadcrumb')
    <span class="text-slate-600">CED & Zinc (SF2)</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">SF2 Stock</span>
@endsection

@section('content')
<div class="p-6">
    @if(session('success'))
    <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 flex-shrink-0"></i>
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
        <p class="text-sm font-medium text-rose-800">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Header Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center">
                        <i data-lucide="package" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">CED & Zinc (SF2) - Item Wise Stock</h2>
                        <p class="text-sm text-slate-500">View aggregated SF2 stock quantities by item (CED and ZINC)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-t border-slate-200 flex">
            <button
                id="tab-btn-ced"
                onclick="switchTab('ced')"
                class="flex items-center gap-2 px-6 py-3 text-sm font-medium border-b-2 transition-colors tab-btn-active"
            >
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">CED</span>
                <span>CED Stock</span>
                <span class="ml-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">{{ $cedStocks->count() }}</span>
            </button>
            <button
                id="tab-btn-zinc"
                onclick="switchTab('zinc')"
                class="flex items-center gap-2 px-6 py-3 text-sm font-medium border-b-2 transition-colors tab-btn-inactive"
            >
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-cyan-100 text-cyan-700">ZINC</span>
                <span>ZINC Stock</span>
                <span class="ml-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">{{ $zincStocks->count() }}</span>
            </button>
        </div>
    </div>

    <!-- CED Tab Panel -->
    <div id="panel-ced">
        @include('backend.production-reports.sf002.partials.sf2-stock-table', [
            'stocks'   => $cedStocks,
            'tabType'  => 'ced',
            'tabLabel' => 'CED',
        ])
    </div>

    <!-- ZINC Tab Panel -->
    <div id="panel-zinc" class="hidden">
        @include('backend.production-reports.sf002.partials.sf2-stock-table', [
            'stocks'   => $zincStocks,
            'tabType'  => 'zinc',
            'tabLabel' => 'ZINC',
        ])
    </div>
</div>

<!-- Transfer Modal -->
<div id="transferModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-2xl bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                        <i data-lucide="arrow-right-left" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Transfer SF2 Stock</h3>
                        <p class="text-xs text-slate-500">Transfer to Assembly (SF3)</p>
                    </div>
                </div>
                <button onclick="closeTransferModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <form id="transferForm" action="{{ route('admin.production-reports.sf002.sf2-stock.transfer') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="item_id" id="transfer_item_id">
                <input type="hidden" name="type"    id="transfer_type">
                <input type="hidden" name="date"    id="transfer_date">
                <input type="hidden" name="time"    id="transfer_time">

                @error('quantity')
                <div class="p-3 rounded-lg bg-rose-50 border border-rose-200">
                    <p class="text-xs text-rose-700">{{ $message }}</p>
                </div>
                @enderror

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">SF2 Type</label>
                        <input type="text" id="transfer_type_display" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-medium">
                    </div>

                    <div>
                        <label for="transfer_sf3_process" class="block text-sm font-semibold text-slate-700 mb-2">SF3 Process <span class="text-rose-500">*</span></label>
                        <select id="transfer_sf3_process" name="sf3_process" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('sf3_process') border-rose-500 @enderror">
                            <option value="">Select SF3 Process</option>
                            <option value="line_1" {{ old('sf3_process') === 'line_1' ? 'selected' : '' }}>Assemble Line 1</option>
                            <option value="line_2" {{ old('sf3_process') === 'line_2' ? 'selected' : '' }}>Assemble Line 2</option>
                            <option value="line_3" {{ old('sf3_process') === 'line_3' ? 'selected' : '' }}>Assemble Line 3</option>
                        </select>
                        @error('sf3_process')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Available Stock</label>
                        <input type="text" id="transfer_available_quantity" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700">
                    </div>

                    <div>
                        <label for="transfer_quantity" class="block text-sm font-semibold text-slate-700 mb-2">Quantity to Transfer <span class="text-rose-500">*</span></label>
                        <input type="number" id="transfer_quantity" name="quantity" required min="1" step="1"
                            value="{{ old('quantity') }}"
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter quantity">
                        <p id="transfer_quantity_help" class="mt-1 text-xs text-slate-500"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Date & Time</label>
                        <input type="text" id="transfer_display_datetime" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Item Code</label>
                        <input type="text" id="transfer_item_code" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Item Name</label>
                        <input type="text" id="transfer_item_name" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Item Size</label>
                        <input type="text" id="transfer_item_size" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700">
                    </div>

                    <div class="md:col-span-2">
                        <label for="transfer_remark" class="block text-sm font-semibold text-slate-700 mb-2">Remark (Optional)</label>
                        <textarea id="transfer_remark" name="remark" rows="2"
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Add optional remark...">{{ old('remark') }}</textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Save Transfer
                    </button>
                    <button type="button" onclick="closeTransferModal()" class="flex-1 px-4 py-2.5 bg-slate-600 text-white text-sm font-medium rounded-lg hover:bg-slate-700 transition-colors">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .tab-btn-active  { color: #4f46e5; border-bottom-color: #4f46e5; }
    .tab-btn-inactive { color: #64748b; border-bottom-color: transparent; }
    .tab-btn-inactive:hover { color: #334155; background-color: #f8fafc; }
</style>

<script>
    const transferState = { available: 0 };

    function switchTab(tab) {
        const panels  = { ced: 'panel-ced',  zinc: 'panel-zinc'  };
        const buttons = { ced: 'tab-btn-ced', zinc: 'tab-btn-zinc' };

        Object.keys(panels).forEach(key => {
            const panel = document.getElementById(panels[key]);
            const btn   = document.getElementById(buttons[key]);
            if (key === tab) {
                panel.classList.remove('hidden');
                btn.classList.remove('tab-btn-inactive');
                btn.classList.add('tab-btn-active');
            } else {
                panel.classList.add('hidden');
                btn.classList.remove('tab-btn-active');
                btn.classList.add('tab-btn-inactive');
            }
        });
    }

    function formatServerDate(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function formatServerTime(date) {
        const h = String(date.getHours()).padStart(2, '0');
        const mi = String(date.getMinutes()).padStart(2, '0');
        const s  = String(date.getSeconds()).padStart(2, '0');
        return `${h}:${mi}:${s}`;
    }

    function formatDisplayDateTime(date) {
        return date.toLocaleString('en-IN', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true,
        });
    }

    function openTransferModal(button) {
        const itemId        = button.getAttribute('data-item-id');
        const itemCode      = button.getAttribute('data-item-code');
        const itemName      = button.getAttribute('data-item-name');
        const itemSize      = button.getAttribute('data-item-size');
        const type          = button.getAttribute('data-type');
        const availableStock = parseFloat(button.getAttribute('data-available-stock') || '0');

        transferState.available = availableStock;

        document.getElementById('transfer_item_id').value = itemId;
        document.getElementById('transfer_type').value = type;
        document.getElementById('transfer_type_display').value = type.toUpperCase();
        document.getElementById('transfer_item_code').value = itemCode;
        document.getElementById('transfer_item_name').value = itemName;
        document.getElementById('transfer_item_size').value = itemSize;
        document.getElementById('transfer_available_quantity').value = Math.round(availableStock);
        document.getElementById('transfer_sf3_process').value = '';

        const quantityInput = document.getElementById('transfer_quantity');
        quantityInput.max = Math.round(availableStock);
        quantityInput.value = '';
        document.getElementById('transfer_quantity_help').innerText = `Max allowed: ${Math.round(availableStock)}`;

        const now = new Date();
        document.getElementById('transfer_date').value = formatServerDate(now);
        document.getElementById('transfer_time').value = formatServerTime(now);
        document.getElementById('transfer_display_datetime').value = formatDisplayDateTime(now);

        document.getElementById('transfer_remark').value = '';
        document.getElementById('transferModal').classList.remove('hidden');

        if (typeof lucide !== 'undefined') { lucide.createIcons(); }
    }

    function closeTransferModal() {
        document.getElementById('transferModal').classList.add('hidden');
    }

    document.getElementById('transferForm').addEventListener('submit', function(event) {
        const quantity = parseFloat(document.getElementById('transfer_quantity').value || '0');
        if (quantity > transferState.available) {
            event.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Quantity',
                text: `Quantity cannot exceed available stock (${Math.round(transferState.available)}).`,
                confirmButtonColor: '#4f46e5',
            });
        }
    });

    // Auto-open modal if there were validation errors on old input
    @if($errors->any() && old('item_id'))
    document.addEventListener('DOMContentLoaded', function() {
        const tabType = '{{ old('type', 'ced') }}';
        switchTab(tabType);

        const fakeBtn = {
            getAttribute: function(attr) {
                const map = {
                    'data-item-id': '{{ old('item_id') }}',
                    'data-item-code': '',
                    'data-item-name': '',
                    'data-item-size': '',
                    'data-type': tabType,
                    'data-available-stock': '0',
                };
                return map[attr] || '';
            }
        };
        openTransferModal(fakeBtn);
        document.getElementById('transfer_quantity').value = '{{ old('quantity') }}';
        document.getElementById('transfer_sf3_process').value = '{{ old('sf3_process') }}';
        document.getElementById('transfer_remark').value = @json(old('remark', ''));
    });
    @endif
</script>
@endsection
