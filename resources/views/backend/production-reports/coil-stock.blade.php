@extends('backend.layout.app')

@section('title', 'Roll Forming (SF1) Coil Stock')

@section('page-title', 'Roll Forming (SF1) Coil Stock')

@section('breadcrumb')
    <span class="text-slate-600">Roll Forming (SF1)</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Coil Stock</span>
@endsection

@section('content')
@php
    $machinesForJs = $machines->map(function ($machine) {
        return [
            'id' => $machine->id,
            'name' => $machine->name,
            'machine_code' => $machine->machine_code,
            'coil_id' => $machine->coil_id,
        ];
    })->values();

    $manageActionTabs = collect($trackActionTabs ?? [])
        ->map(function ($label, $value) {
            return [
                'value' => (string) $value,
                'label' => (string) $label,
            ];
        })
        ->values();

    if ($manageActionTabs->isEmpty()) {
        $manageActionTabs = collect([
            ['value' => 'load', 'label' => 'Load'],
            ['value' => 'unload', 'label' => 'Unload'],
        ]);
    }
@endphp
<div class="p-4">
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-4 py-3">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Coil Stock List</h2>
                <p class="mt-1 text-xs text-slate-500">Raw material inventory for Roll Forming (SF1).</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openManageSuppliersModal()" title="Manage Suppliers" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    <i data-lucide="building-2" class="w-3.5 h-3.5"></i>
                    Suppliers
                </button>
                <button type="button" onclick="openAddCoilModal()" title="Add New Coil" class="inline-flex items-center gap-2 rounded-xl bg-blue-700 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-800 transition-colors">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Add Coil
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-600">Coil No</th>
                        <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-600">Supplier</th>
                        <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-600">Thickness</th>
                        <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-600">Net Weight (KG)</th>
                        <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-600">Loaded Machine</th>
                        <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-600">Created At</th>
                        <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-600">Status</th>
                        <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($coils as $coil)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-900 font-medium">{{ $coil->coil_no }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $coil->manufacture->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ number_format((float) $coil->thickness, 0) }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ number_format((float) $coil->net_weight_kg, 0) }}</td>
                        <td class="px-4 py-3 text-slate-700">
                            @if(!empty($loadedMachinesByCoil[$coil->id]))
                                <div class="space-y-1.5 min-w-[180px]">
                                    @foreach($loadedMachinesByCoil[$coil->id] as $loadedMachine)
                                        <div class="flex items-center justify-between gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1.5">
                                            <div>
                                                <p class="text-xs font-semibold text-emerald-900 leading-4">{{ $loadedMachine['name'] }}</p>
                                                <p class="text-[10px] uppercase tracking-[0.14em] text-emerald-700">Machine</p>
                                            </div>
                                            <span class="inline-flex rounded-full bg-white px-2 py-0.5 text-[10px] font-semibold text-emerald-700 border border-emerald-200">
                                                {{ $loadedMachine['machine_code'] ?: 'No Code' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="inline-flex min-w-[150px] items-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-white text-rose-600 border border-rose-200">
                                        <i data-lucide="circle-off" class="w-3.5 h-3.5"></i>
                                    </span>
                                    <div>
                                        <p class="text-xs font-semibold leading-4 text-rose-700">Not Loaded</p>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ $coil->created_at ? \Carbon\Carbon::parse($coil->created_at)->format('d-m-Y h:i A') : '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="min-w-[150px]">
                                @if($coil->process === 'available')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-800">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                        Available
                                    </span>
                                @elseif($coil->process === 'in_use')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">
                                        <i data-lucide="loader" class="w-3.5 h-3.5 in-use-spin"></i>
                                        In Use
                                    </span>
                                @elseif($coil->process === 'out_of_stock')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-800">
                                        <i data-lucide="ban" class="w-3.5 h-3.5"></i>
                                        Out Of Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-800">
                                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                                        Completed
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-500">
                            <div class="flex flex-nowrap items-center gap-2 whitespace-nowrap">
                                <a
                                    href="{{ route('admin.production-reports.sf001.coil-stock.view', $coil->id) }}"
                                    title="View"
                                    class="inline-flex items-center justify-center rounded-lg bg-sky-100 p-1.5 text-sky-700 hover:bg-sky-200"
                                >
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                </a>

                                <button
                                    type="button"
                                    onclick="openEditCoilModal(this)"
                                    title="Edit"
                                    class="inline-flex items-center justify-center rounded-lg bg-amber-100 p-1.5 text-amber-700 hover:bg-amber-200"
                                    data-edit-id="{{ $coil->id }}"
                                    data-update-url="{{ route('admin.production-reports.sf001.coil-stock.update', $coil->id) }}"
                                    data-manufacture-id="{{ $coil->manufacture_id }}"
                                    data-coil-no="{{ $coil->coil_no }}"
                                    data-coil-size="{{ $coil->coil_size }}"
                                    data-thickness="{{ number_format((float) $coil->thickness, 3, '.', '') }}"
                                    data-net-weight="{{ number_format((float) $coil->net_weight_kg, 3, '.', '') }}"
                                    data-process="{{ $coil->process }}"
                                    data-status="{{ (int) $coil->status }}"
                                >
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>

                                <button
                                    type="button"
                                    onclick="openManageCoilModal(this)"
                                    title="Manage Load/Unload"
                                    class="inline-flex items-center justify-center rounded-lg bg-indigo-100 p-1.5 text-indigo-700 hover:bg-indigo-200 {{ !empty($loadedMachinesByCoil[$coil->id]) ? 'loaded-truck-bg' : '' }}"
                                    data-coil-id="{{ $coil->id }}"
                                    data-coil-no="{{ $coil->coil_no }}"
                                    data-net-weight="{{ (float) $coil->net_weight_kg }}"
                                    data-loaded-machines='@json($loadedMachinesByCoil[$coil->id] ?? [])'
                                >
                                    <i data-lucide="truck" class="w-3.5 h-3.5 {{ !empty($loadedMachinesByCoil[$coil->id]) ? 'loaded-truck' : '' }}"></i>
                                </button>

                                <form action="{{ route('admin.production-reports.sf001.coil-stock.destroy', $coil->id) }}" method="POST" class="inline js-swal-delete-form" data-delete-title="Delete coil stock?" data-delete-text="Are you sure you want to delete coil {{ $coil->coil_no }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete" class="inline-flex items-center justify-center rounded-lg bg-rose-100 p-1.5 text-rose-700 hover:bg-rose-200">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-slate-500 text-xs">No coil stock found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="viewCoilModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 p-4">
    <div class="mx-auto mt-10 w-full max-w-2xl rounded-2xl bg-white shadow-xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Coil Stock Details</h3>
            <button type="button" onclick="closeViewCoilModal()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Coil No</p><p id="viewCoilNo" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Coil Size</p><p id="viewCoilSize" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Supplier</p><p id="viewSupplier" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Thickness</p><p id="viewThickness" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Net Weight (KG)</p><p id="viewNetWeight" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Loaded Machine</p><div class="mt-1"><span id="viewMachineName" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold">-</span></div></div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-[11px] uppercase tracking-wider text-slate-500">Process</p><p id="viewProcess" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 md:col-span-2"><p class="text-[11px] uppercase tracking-wider text-slate-500">Status</p><p id="viewStatus" class="text-sm font-semibold text-slate-900 mt-1">-</p></div>
        </div>
    </div>
</div>

<div id="manageCoilModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 p-4">
    <div class="mx-auto mt-10 w-full max-w-xl rounded-2xl bg-white shadow-xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Manage Coil Load/Unload</h3>
            <button type="button" onclick="closeManageCoilModal()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="manageCoilForm" action="{{ route('admin.production-reports.sf001.coil-stock.load-machine') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" id="manage_form_type" name="form_type" value="{{ old('form_type', 'load') }}">
            <input type="hidden" id="manage_coil_id" name="coil_id" value="{{ old('coil_id') }}">

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Action <span class="text-rose-500">*</span></label>
                <div id="manage_action_tabs" class="inline-flex w-full rounded-lg bg-slate-100 p-1">
                    @foreach($manageActionTabs as $actionTab)
                        <button
                            type="button"
                            class="manage-action-tab flex-1 rounded-md px-3 py-2 text-sm font-semibold text-slate-600 transition-colors hover:text-slate-900"
                            data-action="{{ $actionTab['value'] }}"
                        >
                            <span>{{ $actionTab['label'] }}</span>
                        </button>
                    @endforeach
                </div>
                <div id="manage_loaded_status_chip" class="hidden mt-2 inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                    <i data-lucide="loader-circle" class="h-3.5 w-3.5 manage-load-indicator-icon"></i>
                    Coil Loaded - unload first
                </div>
            </div>

            <div>
                <label for="manage_machine_id" class="block text-sm font-semibold text-slate-700 mb-2">Machine <span class="text-rose-500">*</span></label>
                <select id="manage_machine_id" name="machine_id" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('machine_id') border-rose-500 @enderror">
                    <option value="">Select Machine</option>
                </select>
                @error('machine_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="manage_coil_no" class="block text-sm font-semibold text-slate-700 mb-2">Selected Coil Number</label>
                <input type="text" id="manage_coil_no" value="{{ old('coil_no') }}" readonly class="w-full px-3 py-2.5 border border-slate-300 bg-slate-100 rounded-lg text-slate-700 cursor-not-allowed">
                @error('coil_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div id="manage_load_section">
                <div id="manage_load_rule_notice" class="hidden mb-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2.5 text-amber-800">
                    <div class="flex items-start gap-2">
                        <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                            <i data-lucide="alert-triangle" class="h-3.5 w-3.5"></i>
                        </span>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide">Coil already loaded</p>
                            <p class="mt-0.5 text-xs">
                                This coil is currently loaded on
                                <span id="manage_loaded_machine_list" class="font-semibold">machine(s)</span>.
                                Please unload first.
                            </p>
                        </div>
                    </div>
                </div>
                <label for="manage_load_weight" class="block text-sm font-semibold text-slate-700 mb-2">Load Weight (KG)</label>
                <input type="number" id="manage_load_weight" name="load_weight" value="{{ old('load_weight') }}" min="1" step="1" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('load_weight') border-rose-500 @enderror" placeholder="Enter load weight">
                <p id="manage_load_weight_hint" class="mt-1 text-xs text-slate-500">Max you can load 0 KG net weight.</p>
                @error('load_weight')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div id="manage_unload_section">
                <label for="manage_unload_weight" class="block text-sm font-semibold text-slate-700 mb-2">Pending Weight After Unload (KG)</label>
                <input type="number" id="manage_unload_weight" name="unload_weight" value="{{ old('unload_weight') }}" min="0" step="1" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('unload_weight') border-rose-500 @enderror" placeholder="0">
                <p id="manage_unload_weight_hint" class="mt-1 text-xs text-slate-500">Select machine to see max pending weight.</p>
                @error('unload_weight')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="manage_remark" class="block text-sm font-semibold text-slate-700 mb-2">Remark</label>
                <input type="text" id="manage_remark" name="remark" value="{{ old('remark') }}" maxlength="255" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Optional note">
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeManageCoilModal()" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">Cancel</button>
                <button type="submit" id="manageCoilSubmitButton" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 font-medium">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="addCoilModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 p-4">
    <div class="mx-auto mt-10 w-full max-w-3xl rounded-2xl bg-white shadow-xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Add New Coil Stock</h3>
            <button type="button" onclick="closeAddCoilModal()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="addCoilForm" action="{{ route('admin.production-reports.sf001.coil-stock.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="manufacture_id" class="block text-sm font-semibold text-slate-700 mb-2">Supplier Name <span class="text-rose-500">*</span></label>
                    <select id="manufacture_id" name="manufacture_id" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('manufacture_id') border-rose-500 @enderror">
                        <option value="">Select Supplier</option>
                        <option value="__new__" {{ old('manufacture_id') === '__new__' ? 'selected' : '' }}>+ Add New Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('manufacture_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    @error('manufacture_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror

                    <div id="new_supplier_wrap" class="mt-3 {{ old('manufacture_id') === '__new__' ? '' : 'hidden' }}">
                        <label for="new_manufacture_name" class="block text-xs font-semibold text-slate-600 mb-1.5">New Supplier Name <span class="text-rose-500">*</span></label>
                        <input
                            type="text"
                            id="new_manufacture_name"
                            name="new_manufacture_name"
                            value="{{ old('new_manufacture_name') }}"
                            maxlength="100"
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('new_manufacture_name') border-rose-500 @enderror"
                            placeholder="e.g. OZ Steel"
                        >
                        @error('new_manufacture_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="process" class="block text-sm font-semibold text-slate-700 mb-2">Process <span class="text-rose-500">*</span></label>
                    <select id="process" name="process" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('process') border-rose-500 @enderror">
                        <option value="available" {{ old('process', 'available') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="in_use" {{ old('process') === 'in_use' ? 'selected' : '' }}>In Use</option>
                        <option value="completed" {{ old('process') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="out_of_stock" {{ old('process') === 'out_of_stock' ? 'selected' : '' }}>Out Of Stock</option>
                    </select>
                    @error('process')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="coil_no" class="block text-sm font-semibold text-slate-700 mb-2">Coil No <span class="text-rose-500">*</span></label>
                    <input type="text" id="coil_no" name="coil_no" value="{{ old('coil_no') }}" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('coil_no') border-rose-500 @enderror" placeholder="OZ-BBDS-CRC Coil - 53.10 X 1 mm">
                    @error('coil_no')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="coil_size" class="block text-sm font-semibold text-slate-700 mb-2">Coil Size <span class="text-rose-500">*</span></label>
                    <input type="text" id="coil_size" name="coil_size" value="{{ old('coil_size') }}" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('coil_size') border-rose-500 @enderror" placeholder="53.10 X 1 mm">
                    @error('coil_size')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="thickness" class="block text-sm font-semibold text-slate-700 mb-2">Thickness <span class="text-rose-500">*</span></label>
                    <input type="number" id="thickness" name="thickness" value="{{ old('thickness') }}" required min="0" step="0.001" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('thickness') border-rose-500 @enderror" placeholder="0.950">
                    @error('thickness')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="net_weight_kg" class="block text-sm font-semibold text-slate-700 mb-2">Net Weight (KG) <span class="text-rose-500">*</span></label>
                    <input type="number" id="net_weight_kg" name="net_weight_kg" value="{{ old('net_weight_kg') }}" required min="0" step="0.001" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('net_weight_kg') border-rose-500 @enderror" placeholder="161">
                    @error('net_weight_kg')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-700 mb-2">Status <span class="text-rose-500">*</span></label>
                    <select id="status" name="status" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-rose-500 @enderror">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeAddCoilModal()" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">Cancel</button>
                <button type="submit" id="saveCoilButton" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-medium">Save Coil</button>
            </div>
        </form>
    </div>
</div>

<div id="editCoilModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 p-4">
    <div class="mx-auto mt-10 w-full max-w-3xl rounded-2xl bg-white shadow-xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Edit Coil Stock</h3>
            <button type="button" onclick="closeEditCoilModal()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="editCoilForm" action="{{ old('edit_id') ? route('admin.production-reports.sf001.coil-stock.update', old('edit_id')) : '#' }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="form_type" value="edit">
            <input type="hidden" id="edit_id" name="edit_id" value="{{ old('edit_id') }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_manufacture_id" class="block text-sm font-semibold text-slate-700 mb-2">Supplier Name <span class="text-rose-500">*</span></label>
                    <select id="edit_manufacture_id" name="manufacture_id" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('manufacture_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="edit_process" class="block text-sm font-semibold text-slate-700 mb-2">Process <span class="text-rose-500">*</span></label>
                    <select id="edit_process" name="process" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="available" {{ old('process', 'available') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="in_use" {{ old('process') === 'in_use' ? 'selected' : '' }}>In Use</option>
                        <option value="completed" {{ old('process') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="out_of_stock" {{ old('process') === 'out_of_stock' ? 'selected' : '' }}>Out Of Stock</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="edit_coil_no" class="block text-sm font-semibold text-slate-700 mb-2">Coil No <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit_coil_no" name="coil_no" value="{{ old('coil_no') }}" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="OZ-BBDS-CRC Coil - 53.10 X 1 mm">
                </div>

                <div>
                    <label for="edit_coil_size" class="block text-sm font-semibold text-slate-700 mb-2">Coil Size <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit_coil_size" name="coil_size" value="{{ old('coil_size') }}" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="53.10 X 1 mm">
                </div>

                <div>
                    <label for="edit_thickness" class="block text-sm font-semibold text-slate-700 mb-2">Thickness <span class="text-rose-500">*</span></label>
                    <input type="number" id="edit_thickness" name="thickness" value="{{ old('thickness') }}" required min="0" step="0.001" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0.950">
                </div>

                <div>
                    <label for="edit_net_weight_kg" class="block text-sm font-semibold text-slate-700 mb-2">Net Weight (KG) <span class="text-rose-500">*</span></label>
                    <input type="number" id="edit_net_weight_kg" name="net_weight_kg" value="{{ old('net_weight_kg') }}" required min="0" step="0.001" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="161">
                </div>

                <div>
                    <label for="edit_status" class="block text-sm font-semibold text-slate-700 mb-2">Status <span class="text-rose-500">*</span></label>
                    <select id="edit_status" name="status" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditCoilModal()" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">Cancel</button>
                <button type="submit" id="updateCoilButton" class="px-4 py-2 rounded-lg bg-amber-600 text-white hover:bg-amber-700 font-medium">Update Coil</button>
            </div>
        </form>
    </div>
</div>

{{-- Manage Suppliers Modal --}}
<div id="manageSuppliersModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 p-4 overflow-y-auto">
    <div class="mx-auto mt-10 w-full max-w-2xl rounded-2xl bg-white shadow-xl border border-slate-200 mb-10">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Manage Suppliers</h3>
                <p class="text-xs text-slate-500 mt-0.5">Add, edit or remove coil suppliers.</p>
            </div>
            <button type="button" onclick="closeManageSuppliersModal()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="px-6 pt-5 pb-4">
            <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">Existing Suppliers</h4>
            @if($manufacturers->isEmpty())
                <p class="text-sm text-slate-400 py-4 text-center">No suppliers added yet.</p>
            @else
            <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 overflow-hidden">
                @foreach($manufacturers as $manufacturer)
                <div class="flex items-center justify-between gap-3 px-4 py-3 bg-white hover:bg-slate-50">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="text-sm font-medium text-slate-800 truncate">{{ $manufacturer->name }}</span>
                        @if($manufacturer->status)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">Active</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">Inactive</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button
                            type="button"
                            onclick="openEditSupplierForm(this)"
                            class="supplier-edit-btn inline-flex items-center justify-center rounded-lg bg-amber-100 p-1.5 text-amber-700 hover:bg-amber-200"
                            data-id="{{ $manufacturer->id }}"
                            data-name="{{ $manufacturer->name }}"
                            data-status="{{ (int) $manufacturer->status }}"
                            data-update-url="{{ route('admin.production-reports.sf001.coil-manufacturers.update', $manufacturer->id) }}"
                            title="Edit"
                        >
                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                        </button>
                        <form action="{{ route('admin.production-reports.sf001.coil-manufacturers.destroy', $manufacturer->id) }}" method="POST" class="inline js-swal-delete-form" data-delete-title="Delete supplier?" data-delete-text="Are you sure you want to delete supplier {{ $manufacturer->name }}? This cannot be undone.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-rose-100 p-1.5 text-rose-700 hover:bg-rose-200" title="Delete">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="px-6 pb-6 pt-4 border-t border-slate-100">
            <div id="addSupplierSection">
                <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">Add New Supplier</h4>
                <form id="addSupplierForm" action="{{ route('admin.production-reports.sf001.coil-manufacturers.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_type" value="add_supplier">
                    <div class="flex items-start gap-3">
                        <div class="flex-1">
                            <input
                                type="text"
                                id="supplier_name"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                maxlength="100"
                                class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-rose-500 @enderror"
                                placeholder="e.g. OZ Steel"
                            >
                            @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" id="addSupplierButton" class="shrink-0 px-4 py-2.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-medium text-sm">Add</button>
                    </div>
                </form>
            </div>

            <div id="editSupplierSection" class="hidden">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Edit Supplier</h4>
                    <button type="button" onclick="cancelEditSupplier()" class="text-xs text-slate-500 hover:text-slate-700 underline">Cancel</button>
                </div>
                <form id="editSupplierForm" action="#" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_type" value="edit_supplier">
                    <input type="hidden" id="edit_supplier_id" name="edit_supplier_id" value="">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="md:col-span-2">
                            <label for="edit_supplier_name" class="block text-xs font-semibold text-slate-600 mb-1.5">Supplier Name <span class="text-rose-500">*</span></label>
                            <input
                                type="text"
                                id="edit_supplier_name"
                                name="name"
                                required
                                maxlength="100"
                                class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="Supplier name"
                            >
                        </div>
                        <div>
                            <label for="edit_supplier_status" class="block text-xs font-semibold text-slate-600 mb-1.5">Status <span class="text-rose-500">*</span></label>
                            <select id="edit_supplier_status" name="status" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-3">
                        <button type="button" onclick="cancelEditSupplier()" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm">Cancel</button>
                        <button type="submit" id="editSupplierButton" class="px-4 py-2 rounded-lg bg-amber-600 text-white hover:bg-amber-700 font-medium text-sm">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
    .in-use-spin {
        animation: coilSpin 1.4s linear infinite;
    }

    .loaded-truck {
        animation: truckPulseMove 1.8s ease-in-out infinite;
        transform-origin: center;
    }

    .manage-load-indicator-icon {
        animation: loadSpinner 1s linear infinite;
    }

    .loaded-truck-bg {
        animation: truckBgFlash 1.1s ease-in-out infinite;
    }

    #manage_machine_id option:disabled {
        color: #94a3b8;
    }

    @keyframes coilSpin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes truckPulseMove {
        0%, 100% {
            transform: translateX(0) scale(1);
        }
        30% {
            transform: translateX(1.5px) scale(1.03);
        }
        60% {
            transform: translateX(-1.5px) scale(0.98);
        }
    }

    @keyframes truckBgFlash {
        0%, 100% {
            background-color: rgb(224 231 255);
            box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
        }
        50% {
            background-color: rgb(199 210 254);
            box-shadow: 0 0 0 5px rgba(79, 70, 229, 0.18);
        }
    }

    @keyframes loadSpinner {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .in-use-spin,
        .loaded-truck,
        .loaded-truck-bg,
        .manage-load-indicator-icon {
            animation: none;
        }
    }
</style>
<script>
    const allActiveMachines = @json($machinesForJs);
    const manageActionTabs = @json($manageActionTabs);
    let currentManageContext = {
        loadedMachines: [],
        netWeight: 0,
    };

    function normalizeProcess(process) {
        if (process === 'in_use') return 'In Use';
        if (process === 'out_of_stock') return 'Out Of Stock';
        if (process === 'completed') return 'Completed';
        return 'Available';
    }

    function openViewCoilModal(button) {
        document.getElementById('viewCoilNo').textContent = button.getAttribute('data-coil-no') || '-';
        document.getElementById('viewCoilSize').textContent = button.getAttribute('data-coil-size') || '-';
        document.getElementById('viewSupplier').textContent = button.getAttribute('data-supplier-name') || '-';
        document.getElementById('viewThickness').textContent = button.getAttribute('data-thickness') || '-';
        document.getElementById('viewNetWeight').textContent = button.getAttribute('data-net-weight') || '-';
        const machineName = button.getAttribute('data-machine-name') || 'Not Loaded';
        const machineLoaded = button.getAttribute('data-machine-loaded') === '1';
        const viewMachineName = document.getElementById('viewMachineName');
        viewMachineName.textContent = machineName;
        viewMachineName.classList.remove('bg-emerald-100', 'text-emerald-800', 'bg-rose-100', 'text-rose-700');
        if (machineLoaded) {
            viewMachineName.classList.add('bg-emerald-100', 'text-emerald-800');
        } else {
            viewMachineName.classList.add('bg-rose-100', 'text-rose-700');
        }
        document.getElementById('viewProcess').textContent = normalizeProcess(button.getAttribute('data-process'));
        document.getElementById('viewStatus').textContent = button.getAttribute('data-status') === '1' ? 'Active' : 'Inactive';
        document.getElementById('viewCoilModal').classList.remove('hidden');
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function closeViewCoilModal() {
        document.getElementById('viewCoilModal').classList.add('hidden');
    }

    function setManageActionTabState(action) {
        const isAlreadyLoaded = Array.isArray(currentManageContext.loadedMachines)
            ? currentManageContext.loadedMachines.length > 0
            : false;
        const ruleNotice = document.getElementById('manage_load_rule_notice');
        const loadedStatusChip = document.getElementById('manage_loaded_status_chip');
        const loadedMachineList = document.getElementById('manage_loaded_machine_list');

        if (loadedStatusChip) {
            loadedStatusChip.classList.toggle('hidden', !isAlreadyLoaded);
        }

        if (ruleNotice) {
            ruleNotice.classList.toggle('hidden', !(isAlreadyLoaded && action === 'load'));
        }

        if (loadedMachineList && isAlreadyLoaded) {
            const machineNames = currentManageContext.loadedMachines
                .map(function (machine) { return machine.name || ''; })
                .filter(function (name) { return name.length > 0; });

            loadedMachineList.textContent = machineNames.length > 0 ? machineNames.join(', ') : 'selected machine(s)';
        }

        document.querySelectorAll('.manage-action-tab').forEach(function (button) {
            const buttonAction = button.getAttribute('data-action') || '';
            const isActive = buttonAction === action;
            const hideLoadTab = isAlreadyLoaded && buttonAction === 'load';

            button.classList.toggle('bg-white', isActive);
            button.classList.toggle('text-indigo-700', isActive);
            button.classList.toggle('shadow-sm', isActive);
            button.classList.toggle('text-slate-600', !isActive);
            button.classList.toggle('hover:text-slate-900', !isActive);
            button.classList.toggle('hidden', hideLoadTab);

            if (hideLoadTab) {
                button.setAttribute('title', 'Load hidden because this coil is already loaded.');
            } else {
                button.removeAttribute('title');
            }
        });
    }

    function setManageAction(action, loadedMachines, netWeight) {
        const allowedActions = Array.isArray(manageActionTabs)
            ? manageActionTabs.map(function (tab) { return tab.value; })
            : [];
        const isAlreadyLoaded = Array.isArray(loadedMachines) ? loadedMachines.length > 0 : false;

        if (isAlreadyLoaded && action === 'load') {
            action = 'unload';
        }

        if (allowedActions.length > 0 && !allowedActions.includes(action)) {
            action = allowedActions[0];
        }

        const formTypeInput = document.getElementById('manage_form_type');
        const machineSelect = document.getElementById('manage_machine_id');
        const loadSection = document.getElementById('manage_load_section');
        const unloadSection = document.getElementById('manage_unload_section');
        const submitButton = document.getElementById('manageCoilSubmitButton');
        const loadWeightInput = document.getElementById('manage_load_weight');
        const unloadWeightInput = document.getElementById('manage_unload_weight');
        const loadWeightHint = document.getElementById('manage_load_weight_hint');
        const unloadWeightHint = document.getElementById('manage_unload_weight_hint');

        function updateUnloadConstraints() {
            const selectedMachineId = Number(machineSelect.value || 0);
            const selectedMachine = loadedMachines.find(function (machine) {
                return Number(machine.id) === selectedMachineId;
            });

            const maxPendingWeight = selectedMachine && selectedMachine.active_load_weight !== null && selectedMachine.active_load_weight !== undefined
                ? Number(selectedMachine.active_load_weight)
                : 0;

            unloadWeightInput.max = String(maxPendingWeight);

            if (unloadWeightHint) {
                if (selectedMachineId === 0) {
                    unloadWeightHint.textContent = 'Select machine to see max pending weight.';
                } else {
                    unloadWeightHint.textContent = 'Max pending weight for selected machine is ' + maxPendingWeight + ' KG.';
                }
            }

            if (Number(unloadWeightInput.value || 0) > maxPendingWeight) {
                unloadWeightInput.value = String(maxPendingWeight);
            }
        }

        function enforceUnloadInputRange() {
            const enteredValue = Number(unloadWeightInput.value || '0');
            const maxPendingWeight = Number(unloadWeightInput.max || '0');

            if (enteredValue < 0) {
                unloadWeightInput.value = '0';
                return;
            }

            if (maxPendingWeight > 0 && enteredValue > maxPendingWeight) {
                unloadWeightInput.value = String(maxPendingWeight);
            }
        }

        formTypeInput.value = action;
        setManageActionTabState(action);
        machineSelect.innerHTML = '<option value="">Select Machine</option>';

        if (action === 'load') {
            loadSection.classList.remove('hidden');
            unloadSection.classList.add('hidden');
            loadWeightInput.required = true;
            unloadWeightInput.required = false;
            unloadWeightInput.value = '';
            unloadWeightInput.disabled = true;
            machineSelect.onchange = null;

            const freeMachineCount = allActiveMachines.filter(function (machine) {
                return !machine.coil_id;
            }).length;

            allActiveMachines.forEach(function (machine) {
                const option = document.createElement('option');
                option.value = machine.id;
                const label = machine.name + (machine.machine_code ? ' (' + machine.machine_code + ')' : '');
                const isLoaded = !!machine.coil_id;

                option.textContent = isLoaded ? (label + ' - Loaded') : label;
                option.disabled = isLoaded;
                machineSelect.appendChild(option);
            });

            const maxWeight = Math.floor(netWeight);
            loadWeightInput.max = String(maxWeight);
            loadWeightInput.value = maxWeight > 0 ? String(maxWeight) : '';
            loadWeightInput.disabled = maxWeight <= 0;
            if (maxWeight > 0) {
                loadWeightInput.disabled = false;
            }

            if (loadWeightHint) {
                if (freeMachineCount === 0) {
                    loadWeightHint.textContent = 'No free machine available. Unload a machine first.';
                } else {
                    loadWeightHint.textContent = 'Loaded machines are shown but cannot be selected. Max you can load ' + String(maxWeight) + ' KG net weight.';
                }
            }

            if (unloadWeightHint) {
                unloadWeightHint.textContent = 'Select machine to see max pending weight.';
            }

            const isLoadDisabled = maxWeight <= 0 || freeMachineCount === 0;
            submitButton.disabled = isLoadDisabled;
            submitButton.textContent = isLoadDisabled ? 'Load Disabled' : 'Load';
            submitButton.classList.toggle('opacity-60', isLoadDisabled);
            submitButton.classList.toggle('cursor-not-allowed', isLoadDisabled);
        } else {
            loadSection.classList.add('hidden');
            unloadSection.classList.remove('hidden');
            loadWeightInput.required = false;
            loadWeightInput.disabled = true;
            unloadWeightInput.required = true;
            unloadWeightInput.disabled = false;

            loadedMachines.forEach(function (machine) {
                const option = document.createElement('option');
                option.value = machine.id;
                option.textContent = machine.name + (machine.machine_code ? ' (' + machine.machine_code + ')' : '');
                machineSelect.appendChild(option);
            });

            if (loadedMachines.length === 1) {
                machineSelect.value = String(loadedMachines[0].id);
            }

            updateUnloadConstraints();
            machineSelect.onchange = updateUnloadConstraints;
            unloadWeightInput.oninput = enforceUnloadInputRange;
            unloadWeightInput.onblur = enforceUnloadInputRange;

            submitButton.disabled = loadedMachines.length === 0;
            submitButton.textContent = loadedMachines.length === 0 ? 'Unload Disabled' : 'Unload';
            submitButton.classList.toggle('opacity-60', loadedMachines.length === 0);
            submitButton.classList.toggle('cursor-not-allowed', loadedMachines.length === 0);

            if (loadedMachines.length === 0) {
                unloadWeightInput.disabled = true;
            }
        }
    }

    function openManageCoilModal(button, forcedAction = null) {
        const coilId = button.getAttribute('data-coil-id') || '';
        const coilNo = button.getAttribute('data-coil-no') || '';
        const netWeight = parseFloat(button.getAttribute('data-net-weight') || '0');

        let loadedMachines = [];
        try {
            loadedMachines = JSON.parse(button.getAttribute('data-loaded-machines') || '[]');
        } catch (e) {
            loadedMachines = [];
        }

        document.getElementById('manage_coil_id').value = coilId;
        document.getElementById('manage_coil_no').value = coilNo;

        currentManageContext = {
            loadedMachines: loadedMachines,
            netWeight: netWeight,
        };

        const allowedActions = Array.isArray(manageActionTabs)
            ? manageActionTabs.map(function (tab) { return tab.value; })
            : ['load', 'unload'];
        const fallbackAction = allowedActions.includes('load') ? 'load' : (allowedActions[0] || 'load');
        const preferredAction = loadedMachines.length > 0 && allowedActions.includes('unload') ? 'unload' : fallbackAction;
        const defaultAction = forcedAction && allowedActions.includes(forcedAction) ? forcedAction : preferredAction;

        setManageAction(defaultAction, loadedMachines, netWeight);

        document.getElementById('manageCoilModal').classList.remove('hidden');
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function closeManageCoilModal() {
        document.getElementById('manageCoilModal').classList.add('hidden');
    }

    function openAddCoilModal() {
        document.getElementById('addCoilModal').classList.remove('hidden');
        toggleNewSupplierInput();
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function closeAddCoilModal() {
        document.getElementById('addCoilModal').classList.add('hidden');
    }

    function toggleNewSupplierInput() {
        const supplierSelect = document.getElementById('manufacture_id');
        const newSupplierWrap = document.getElementById('new_supplier_wrap');
        const newSupplierInput = document.getElementById('new_manufacture_name');

        if (!supplierSelect || !newSupplierWrap || !newSupplierInput) {
            return;
        }

        const isNewSupplier = supplierSelect.value === '__new__';
        newSupplierWrap.classList.toggle('hidden', !isNewSupplier);
        newSupplierInput.required = isNewSupplier;
    }

    function openEditCoilModal(button) {
        const form = document.getElementById('editCoilForm');
        if (form) {
            form.action = button.getAttribute('data-update-url') || '#';
        }

        document.getElementById('edit_id').value = button.getAttribute('data-edit-id') || '';
        document.getElementById('edit_manufacture_id').value = button.getAttribute('data-manufacture-id') || '';
        document.getElementById('edit_coil_no').value = button.getAttribute('data-coil-no') || '';
        document.getElementById('edit_coil_size').value = button.getAttribute('data-coil-size') || '';
        document.getElementById('edit_thickness').value = button.getAttribute('data-thickness') || '';
        document.getElementById('edit_net_weight_kg').value = button.getAttribute('data-net-weight') || '';
        document.getElementById('edit_process').value = button.getAttribute('data-process') || 'available';
        document.getElementById('edit_status').value = button.getAttribute('data-status') || '1';

        document.getElementById('editCoilModal').classList.remove('hidden');
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function closeEditCoilModal() {
        document.getElementById('editCoilModal').classList.add('hidden');
    }

    @if($errors->any())
    document.addEventListener('DOMContentLoaded', function () {
        @if(old('form_type') === 'edit')
            document.getElementById('editCoilModal').classList.remove('hidden');
        @elseif(old('form_type') === 'load' || old('form_type') === 'unload')
            const oldCoilId = @json(old('coil_id'));
            const oldFormType = @json(old('form_type'));
            const manageButton = document.querySelector('[data-coil-id="' + String(oldCoilId || '') + '"][data-loaded-machines]');
            if (manageButton) {
                openManageCoilModal(manageButton, oldFormType === 'unload' ? 'unload' : 'load');
                const oldMachineId = @json(old('machine_id'));
                if (oldMachineId) {
                    document.getElementById('manage_machine_id').value = String(oldMachineId);
                }
            } else {
                document.getElementById('manageCoilModal').classList.remove('hidden');
            }
        @elseif(old('form_type') === 'add_supplier' || old('form_type') === 'edit_supplier')
            openManageSuppliersModal();
            @if(old('form_type') === 'edit_supplier')
            (function() {
                var oldEditId = @json(old('edit_supplier_id'));
                if (oldEditId) {
                    var editBtn = document.querySelector('.supplier-edit-btn[data-id="' + String(oldEditId) + '"]');
                    if (editBtn) {
                        editBtn.setAttribute('data-name', @json(old('name', '')));
                        editBtn.setAttribute('data-status', @json(old('status', '1')));
                        openEditSupplierForm(editBtn);
                    }
                }
            })();
            @endif
        @else
            openAddCoilModal();
        @endif
    });
    @endif

    document.addEventListener('DOMContentLoaded', function () {
        const addCoilForm = document.getElementById('addCoilForm');
        const saveCoilButton = document.getElementById('saveCoilButton');
        const supplierSelect = document.getElementById('manufacture_id');
        const editCoilForm = document.getElementById('editCoilForm');
        const updateCoilButton = document.getElementById('updateCoilButton');
        const manageCoilForm = document.getElementById('manageCoilForm');
        const manageCoilSubmitButton = document.getElementById('manageCoilSubmitButton');

        if (typeof toastr !== 'undefined') {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 2500,
            };

            @if(session('success'))
                toastr.success(@json(session('success')));
            @endif

            @if(session('error'))
                toastr.error(@json(session('error')));
            @endif

            @if(session('info'))
                toastr.info(@json(session('info')));
            @endif

            @if($errors->any())
                toastr.error('Please fix the highlighted fields and try again.');
            @endif
        }

        const deleteForms = document.querySelectorAll('.js-swal-delete-form');
        if (deleteForms.length && typeof Swal !== 'undefined') {
            deleteForms.forEach(function (form) {
                form.addEventListener('submit', async function (event) {
                    event.preventDefault();

                    const title = form.getAttribute('data-delete-title') || 'Confirm delete?';
                    const text = form.getAttribute('data-delete-text') || 'This action cannot be undone.';

                    const result = await Swal.fire({
                        title: title,
                        text: text,
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
        }

        if (supplierSelect) {
            supplierSelect.addEventListener('change', toggleNewSupplierInput);
            toggleNewSupplierInput();
        }

        document.querySelectorAll('.manage-action-tab').forEach(function (button) {
            button.addEventListener('click', function () {
                const action = button.getAttribute('data-action') || 'load';
                setManageAction(action, currentManageContext.loadedMachines, currentManageContext.netWeight);
            });
        });

        if (addCoilForm && saveCoilButton) {
            addCoilForm.addEventListener('submit', function () {
                saveCoilButton.disabled = true;
                saveCoilButton.classList.add('opacity-60', 'cursor-not-allowed');
                saveCoilButton.textContent = 'Saving...';
            });
        }

        const addSupplierForm = document.getElementById('addSupplierForm');
        const addSupplierButton = document.getElementById('addSupplierButton');
        const editSupplierForm = document.getElementById('editSupplierForm');
        const editSupplierButton = document.getElementById('editSupplierButton');

        if (addSupplierForm && addSupplierButton) {
            addSupplierForm.addEventListener('submit', function () {
                addSupplierButton.disabled = true;
                addSupplierButton.classList.add('opacity-60', 'cursor-not-allowed');
                addSupplierButton.textContent = 'Adding...';
            });
        }

        if (editSupplierForm && editSupplierButton) {
            editSupplierForm.addEventListener('submit', function () {
                editSupplierButton.disabled = true;
                editSupplierButton.classList.add('opacity-60', 'cursor-not-allowed');
                editSupplierButton.textContent = 'Updating...';
            });
        }

        if (editCoilForm && updateCoilButton) {
            editCoilForm.addEventListener('submit', function () {
                updateCoilButton.disabled = true;
                updateCoilButton.classList.add('opacity-60', 'cursor-not-allowed');
                updateCoilButton.textContent = 'Updating...';
            });
        }

        if (manageCoilForm && manageCoilSubmitButton) {
            manageCoilForm.addEventListener('submit', function () {
                if (manageCoilSubmitButton.disabled) {
                    return;
                }

                const formType = document.getElementById('manage_form_type').value;
                const loadWeightInput = document.getElementById('manage_load_weight');
                const maxWeight = loadWeightInput ? Number(loadWeightInput.max || '0') : 0;
                const enteredWeight = loadWeightInput ? Number(loadWeightInput.value || '0') : 0;

                if (formType === 'load' && loadWeightInput && (enteredWeight <= 0 || (maxWeight > 0 && enteredWeight > maxWeight))) {
                    alert('Load weight must be between 1 and ' + maxWeight + ' KG.');
                    loadWeightInput.focus();
                    return;
                }

                manageCoilSubmitButton.disabled = true;
                manageCoilSubmitButton.classList.add('opacity-60', 'cursor-not-allowed');
                manageCoilSubmitButton.textContent = formType === 'load' ? 'Loading...' : 'Unloading...';
            });
        }

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    function openManageSuppliersModal() {
        document.getElementById('manageSuppliersModal').classList.remove('hidden');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeManageSuppliersModal() {
        document.getElementById('manageSuppliersModal').classList.add('hidden');
        cancelEditSupplier();
    }

    function openEditSupplierForm(button) {
        document.getElementById('edit_supplier_id').value = button.getAttribute('data-id') || '';
        document.getElementById('edit_supplier_name').value = button.getAttribute('data-name') || '';
        document.getElementById('edit_supplier_status').value = button.getAttribute('data-status') || '1';
        document.getElementById('editSupplierForm').action = button.getAttribute('data-update-url') || '#';
        document.getElementById('addSupplierSection').classList.add('hidden');
        document.getElementById('editSupplierSection').classList.remove('hidden');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function cancelEditSupplier() {
        document.getElementById('addSupplierSection').classList.remove('hidden');
        document.getElementById('editSupplierSection').classList.add('hidden');
        document.getElementById('editSupplierForm').action = '#';
    }
</script>
@endpush
