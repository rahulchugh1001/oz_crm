@extends('backend.layout.app')

@section('title', 'PPC - Stock Management')

@section('page-title', 'PPC Stock')

@section('breadcrumb')
    <span class="text-slate-600">Production Reports</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">PPC</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Stock Management</span>
@endsection

@section('content')
<div class="p-4">
    @if(session('success'))
    <div class="mb-3 p-3 rounded-xl bg-green-50 border border-green-200 flex items-center gap-2.5">
        <i data-lucide="check-circle" class="w-4 h-4 text-green-600 flex-shrink-0"></i>
        <p class="text-xs font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-3 p-3 rounded-xl bg-rose-50 border border-rose-200 flex items-center gap-2.5">
        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0"></i>
        <p class="text-xs font-medium text-rose-800">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Header Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle mb-4">
        <div class="p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                        <i data-lucide="package" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">PPC Stock - Ready for Assembly</h2>
                        <p class="text-xs text-slate-500">Verified and available stock to transfer to SF3</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-t border-slate-200 flex">
            <button
                id="tab-btn-ced"
                onclick="switchTab('ced')"
                class="flex items-center gap-2 px-4 py-2.5 text-xs font-medium border-b-2 transition-colors tab-btn-active"
            >
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">CED</span>
                <span>CED Stock</span>
                <span class="ml-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">{{ collect($cedStocks)->count() }}</span>
            </button>
            <button
                id="tab-btn-zinc"
                onclick="switchTab('zinc')"
                class="flex items-center gap-2 px-4 py-2.5 text-xs font-medium border-b-2 transition-colors tab-btn-inactive"
            >
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-cyan-100 text-cyan-700">ZINC</span>
                <span>ZINC Stock</span>
                <span class="ml-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">{{ collect($zincStocks)->count() }}</span>
            </button>
        </div>
    </div>

    <!-- CED Tab Panel -->
    <div id="panel-ced">
        @include('backend.production-reports.ppc.partials.ppc-stock-table', [
            'stocks'   => $cedStocks,
            'tabType'  => 'ced',
            'tabLabel' => 'CED',
        ])
    </div>

    <!-- ZINC Tab Panel -->
    <div id="panel-zinc" class="hidden">
        @include('backend.production-reports.ppc.partials.ppc-stock-table', [
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
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                        <i data-lucide="arrow-right-left" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Transfer PPC Stock to Assembly Line</h3>
                        <p class="text-xs text-slate-500">Route stock from PPC to SF3 production lines</p>
                    </div>
                </div>
                <button onclick="closeTransferModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <form id="transferForm" action="{{ route('admin.production-reports.ppc.stock.transfer') }}" method="POST" class="space-y-4">
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
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Item Type</label>
                        <input type="text" id="transfer_type_display" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-medium">
                    </div>

                    <div>
                        <label for="transfer_sf3_process" class="block text-sm font-semibold text-slate-700 mb-2">Transfer to</label>
                        <select id="transfer_sf3_process" name="sf3_process" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('sf3_process') border-rose-500 @enderror">
                            <option value="" selected>SF3</option>
                        </select>
                        @error('sf3_process')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Verified Available Stock</label>
                        <input type="text" id="transfer_available_quantity" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-emerald-50 text-emerald-700 font-semibold">
                    </div>

                    <div>
                        <label for="transfer_quantity" class="block text-sm font-semibold text-slate-700 mb-2">Quantity to Transfer <span class="text-rose-500">*</span></label>
                        <input type="number" id="transfer_quantity" name="quantity" required min="1" step="1"
                            value="{{ old('quantity') }}"
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter quantity">
                        <p id="transfer_quantity_help" class="mt-1 text-xs text-slate-500"></p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Item Name</label>
                        <input type="text" id="transfer_item_name" readonly class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700">
                    </div>

                    <div class="md:col-span-2">
                        <label for="transfer_remark" class="block text-sm font-semibold text-slate-700 mb-2">Remark (Optional)</label>
                        <textarea id="transfer_remark" name="remark" rows="2"
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Add optional remark for SF3 context...">{{ old('remark') }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button id="transfer_submit_btn" type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Assign to Assembly Line
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
    let transferSubmitting = false;

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

    function openTransferModal(button) {
        const itemId        = button.getAttribute('data-item-id');
        const itemName      = button.getAttribute('data-item-name');
        const type          = button.getAttribute('data-type');
        const availableStock = parseFloat(button.getAttribute('data-available-stock') || '0');

        transferState.available = availableStock;

        document.getElementById('transfer_item_id').value = itemId;
        document.getElementById('transfer_type').value = type;
        document.getElementById('transfer_type_display').value = type.toUpperCase();
        document.getElementById('transfer_item_name').value = itemName;
        document.getElementById('transfer_available_quantity').value = Math.round(availableStock);
        document.getElementById('transfer_sf3_process').value = '';

        const quantityInput = document.getElementById('transfer_quantity');
        quantityInput.max = Math.round(availableStock);
        quantityInput.value = '';
        document.getElementById('transfer_quantity_help').innerText = `Max allowed: ${Math.round(availableStock)}`;

        const now = new Date();
        document.getElementById('transfer_date').value = formatServerDate(now);
        document.getElementById('transfer_time').value = formatServerTime(now);

        document.getElementById('transfer_remark').value = '';
        document.getElementById('transferModal').classList.remove('hidden');

        if (typeof lucide !== 'undefined') { lucide.createIcons(); }
    }

    function closeTransferModal() {
        document.getElementById('transferModal').classList.add('hidden');
    }

    document.getElementById('transferForm').addEventListener('submit', function(event) {
        const submitBtn = document.getElementById('transfer_submit_btn');

        if (transferSubmitting) {
            event.preventDefault();
            return;
        }

        const quantity = parseFloat(document.getElementById('transfer_quantity').value || '0');
        if (quantity > transferState.available || quantity <= 0) {
            event.preventDefault();
            alert(`Invalid quantity! Must be between 1 and ${Math.round(transferState.available)}.`);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
            }
            transferSubmitting = false;
            return;
        }

        transferSubmitting = true;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
            submitBtn.textContent = 'Transferring...';
        }
    });

    if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
@endsection
