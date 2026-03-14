@extends('backend.layout.app')

@section('title', 'Roll Forming (SF1) Coil Stock')

@section('page-title', 'Roll Forming (SF1) Coil Stock')

@section('breadcrumb')
    <span class="text-slate-600">Roll Forming (SF1)</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Coil Stock</span>
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

    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden" style="transform: scale(0.86); transform-origin: top left; width: 116.3%;">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-4 font-bold text-slate-900">Available Coils</h2>
                <p class="text-slate-500 mt-1">Raw material inventory for Roll Forming (SF1) production line</p>
            </div>
            <button type="button" onclick="openAddCoilModal()" title="Add New Coil" class="inline-flex items-center justify-center p-3 rounded-xl bg-blue-700 hover:bg-blue-800 text-white transition-all">
                <i data-lucide="plus" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-slate-700 font-semibold">COIL NO</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">COIL SIZE</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">SUPPLIER NAME</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">THICKNESS</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">NET WEIGHT (KG)</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">LOADED MACHINE</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">CREATED DATE</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">STATUS</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coils as $coil)
                    <tr class="border-b border-slate-200">
                        <td class="px-6 py-5 text-slate-900 font-semibold">{{ $coil->coil_no }}</td>
                        <td class="px-6 py-5 text-slate-700">{{ $coil->coil_size }}</td>
                        <td class="px-6 py-5 text-slate-700">{{ $coil->manufacture->name ?? '-' }}</td>
                        <td class="px-6 py-5 text-slate-700">{{ number_format((float) $coil->thickness, 0) }}</td>
                        <td class="px-6 py-5 text-slate-700">{{ number_format((float) $coil->net_weight_kg, 0) }}</td>
                        <td class="px-6 py-5 text-slate-700">
                            @if(!empty($loadedMachineNames[$coil->id]))
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold">
                                    {{ $loadedMachineNames[$coil->id] }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-rose-100 text-rose-700 text-xs font-semibold">
                                    Not Loaded
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-slate-700">{{ $coil->created_at ? \Carbon\Carbon::parse($coil->created_at)->format('d-m-Y h:i A') : '-' }}</td>
                        <td class="px-6 py-5">
                            @if($coil->process === 'available')
                                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-800 font-medium">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    Available
                                </span>
                            @elseif($coil->process === 'in_use')
                                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-100 text-blue-700 font-medium">
                                    <i data-lucide="loader" class="w-4 h-4"></i>
                                    In Use
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-100 text-slate-800 font-medium">
                                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                    Completed
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-slate-500">
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    onclick="openViewCoilModal(this)"
                                    title="View"
                                    class="inline-flex items-center justify-center p-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200"
                                    data-coil-no="{{ $coil->coil_no }}"
                                    data-coil-size="{{ $coil->coil_size }}"
                                    data-supplier-name="{{ $coil->manufacture->name ?? '-' }}"
                                    data-thickness="{{ number_format((float) $coil->thickness, 0) }}"
                                    data-net-weight="{{ number_format((float) $coil->net_weight_kg, 0) }}"
                                    data-machine-name="{{ $loadedMachineNames[$coil->id] ?? 'Not Loaded' }}"
                                    data-machine-loaded="{{ !empty($loadedMachineNames[$coil->id]) ? '1' : '0' }}"
                                    data-process="{{ $coil->process }}"
                                    data-status="{{ (int) $coil->status }}"
                                >
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>

                                <button
                                    type="button"
                                    onclick="openEditCoilModal(this)"
                                    title="Edit"
                                    class="inline-flex items-center justify-center p-2 rounded-lg bg-amber-100 text-amber-700 hover:bg-amber-200"
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
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>

                                <button
                                    type="button"
                                    onclick="openLoadToMachineModal(this)"
                                    title="Load to Machine"
                                    class="inline-flex items-center justify-center p-2 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 {{ (float) $coil->net_weight_kg <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    data-coil-id="{{ $coil->id }}"
                                    data-coil-no="{{ $coil->coil_no }}"
                                    data-net-weight="{{ (float) $coil->net_weight_kg }}"
                                    {{ (float) $coil->net_weight_kg <= 0 ? 'disabled' : '' }}
                                >
                                    <i data-lucide="truck" class="w-4 h-4"></i>
                                </button>

                                <button
                                    type="button"
                                    onclick="openUnloadFromMachineModal(this)"
                                    title="Unload from Machine"
                                    class="inline-flex items-center justify-center p-2 rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-200 {{ empty($loadedMachinesByCoil[$coil->id]) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    data-coil-id="{{ $coil->id }}"
                                    data-coil-no="{{ $coil->coil_no }}"
                                    data-loaded-machines='@json($loadedMachinesByCoil[$coil->id] ?? [])'
                                    {{ empty($loadedMachinesByCoil[$coil->id]) ? 'disabled' : '' }}
                                >
                                    <i data-lucide="package-minus" class="w-4 h-4"></i>
                                </button>

                                <form action="{{ route('admin.production-reports.sf001.coil-stock.destroy', $coil->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this coil stock?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete" class="inline-flex items-center justify-center p-2 rounded-lg bg-rose-100 text-rose-700 hover:bg-rose-200">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-10 text-center text-slate-500">No coil stock found.</td>
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

<div id="loadToMachineModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 p-4">
    <div class="mx-auto mt-10 w-full max-w-xl rounded-2xl bg-white shadow-xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Load Coil to Machine</h3>
            <button type="button" onclick="closeLoadToMachineModal()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="loadToMachineForm" action="{{ route('admin.production-reports.sf001.coil-stock.load-machine') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="form_type" value="load">
            <input type="hidden" id="load_coil_id" name="coil_id" value="{{ old('coil_id') }}">

            <div>
                <label for="machine_id" class="block text-sm font-semibold text-slate-700 mb-2">Machine <span class="text-rose-500">*</span></label>
                <select id="machine_id" name="machine_id" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('machine_id') border-rose-500 @enderror">
                    <option value="">Select Active Machine</option>
                    @foreach($machines as $machine)
                        <option value="{{ $machine->id }}" {{ (string) old('machine_id') === (string) $machine->id ? 'selected' : '' }}>
                            {{ $machine->name }}{{ $machine->machine_code ? ' (' . $machine->machine_code . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('machine_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="load_coil_no" class="block text-sm font-semibold text-slate-700 mb-2">Selected Coil Number</label>
                <input type="text" id="load_coil_no" value="{{ old('coil_no') }}" readonly class="w-full px-3 py-2.5 border border-slate-300 bg-slate-100 rounded-lg text-slate-700 cursor-not-allowed">
                @error('coil_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="load_weight" class="block text-sm font-semibold text-slate-700 mb-2">Load Weight (KG)</label>
                <input type="number" id="load_weight" name="load_weight" value="{{ old('load_weight') }}" required min="1" step="1" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('load_weight') border-rose-500 @enderror" placeholder="Enter load weight">
                <p id="load_weight_hint" class="mt-1 text-xs text-slate-500">Max you can load 0 KG net weight.</p>
                @error('load_weight')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="load_remark" class="block text-sm font-semibold text-slate-700 mb-2">Remark</label>
                <input type="text" id="load_remark" name="remark" value="{{ old('remark') }}" maxlength="255" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Optional note for load action">
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeLoadToMachineModal()" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">Cancel</button>
                <button type="submit" id="loadToMachineButton" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-medium">Load</button>
            </div>
        </form>
    </div>
</div>

<div id="unloadFromMachineModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 p-4">
    <div class="mx-auto mt-10 w-full max-w-xl rounded-2xl bg-white shadow-xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Unload Coil from Machine</h3>
            <button type="button" onclick="closeUnloadFromMachineModal()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="unloadFromMachineForm" action="{{ route('admin.production-reports.sf001.coil-stock.unload-machine') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="form_type" value="unload">
            <input type="hidden" id="unload_coil_id" name="coil_id" value="{{ old('coil_id') }}">

            <div>
                <label for="unload_machine_id" class="block text-sm font-semibold text-slate-700 mb-2">Machine <span class="text-rose-500">*</span></label>
                <select id="unload_machine_id" name="machine_id" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('machine_id') border-rose-500 @enderror">
                    <option value="">Select Loaded Machine</option>
                </select>
                @error('machine_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="unload_coil_no" class="block text-sm font-semibold text-slate-700 mb-2">Selected Coil Number</label>
                <input type="text" id="unload_coil_no" value="{{ old('coil_no') }}" readonly class="w-full px-3 py-2.5 border border-slate-300 bg-slate-100 rounded-lg text-slate-700 cursor-not-allowed">
                @error('coil_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="unload_weight" class="block text-sm font-semibold text-slate-700 mb-2">Pending Weight After Unload (KG) <span class="text-rose-500">*</span></label>
                <input type="number" id="unload_weight" name="unload_weight" value="{{ old('unload_weight') }}" required min="0" step="1" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('unload_weight') border-rose-500 @enderror" placeholder="0">
                @error('unload_weight')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="unload_remark" class="block text-sm font-semibold text-slate-700 mb-2">Remark</label>
                <input type="text" id="unload_remark" name="remark" value="{{ old('remark') }}" maxlength="255" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Optional note for unload action">
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeUnloadFromMachineModal()" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">Cancel</button>
                <button type="submit" id="unloadFromMachineButton" class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 font-medium">Unload</button>
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
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('manufacture_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    @error('manufacture_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="process" class="block text-sm font-semibold text-slate-700 mb-2">Process <span class="text-rose-500">*</span></label>
                    <select id="process" name="process" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('process') border-rose-500 @enderror">
                        <option value="available" {{ old('process', 'available') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="in_use" {{ old('process') === 'in_use' ? 'selected' : '' }}>In Use</option>
                        <option value="completed" {{ old('process') === 'completed' ? 'selected' : '' }}>Completed</option>
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

<div class="p-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-4 font-bold text-slate-900">Coil Load/Unload History</h2>
            <p class="text-slate-500 mt-1">Latest 50 events from coil-machine tracking</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-slate-700 font-semibold">DATE & TIME</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">TYPE</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">MACHINE</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">COIL NO</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">LOAD WEIGHT (KG)</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">UNLOAD/PENDING (KG)</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">BY</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">REMARK</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coilTrackLogs as $track)
                    <tr class="border-b border-slate-200">
                        <td class="px-6 py-4 text-slate-700">{{ $track->event_at ? $track->event_at->format('d-m-Y h:i A') : ($track->created_at ? $track->created_at->format('d-m-Y h:i A') : '-') }}</td>
                        <td class="px-6 py-4">
                            @if($track->type === 'load')
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">LOAD</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">UNLOAD</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-700">{{ $track->machine?->name ?? '-' }}{{ $track->machine?->machine_code ? ' (' . $track->machine?->machine_code . ')' : '' }}</td>
                        <td class="px-6 py-4 text-slate-700">{{ $track->coil?->coil_no ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-700">{{ number_format((float) $track->load_weight, 0) }}</td>
                        <td class="px-6 py-4 text-slate-700">{{ $track->unload_weight !== null ? number_format((float) $track->unload_weight, 0) : '-' }}</td>
                        <td class="px-6 py-4 text-slate-700">{{ $track->creator?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-700">{{ $track->remark ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-slate-500">No load/unload history found.</td>
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
    function normalizeProcess(process) {
        if (process === 'in_use') return 'In Use';
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

    function openLoadToMachineModal(button) {
        document.getElementById('load_coil_id').value = button.getAttribute('data-coil-id') || '';
        document.getElementById('load_coil_no').value = button.getAttribute('data-coil-no') || '';
        const netWeight = parseFloat(button.getAttribute('data-net-weight') || '0');
        const loadWeightInput = document.getElementById('load_weight');
        const loadToMachineButton = document.getElementById('loadToMachineButton');
        const loadWeightHint = document.getElementById('load_weight_hint');

        if (loadWeightHint) {
            loadWeightHint.textContent = 'Max you can load ' + String(Math.floor(netWeight)) + ' KG net weight.';
        }

        if (loadWeightInput) {
            loadWeightInput.max = String(Math.floor(netWeight));
            loadWeightInput.value = netWeight > 0 ? String(Math.floor(netWeight)) : '';
            loadWeightInput.disabled = netWeight <= 0;
            if (netWeight <= 0) {
                loadWeightInput.classList.add('bg-slate-100', 'cursor-not-allowed');
            } else {
                loadWeightInput.classList.remove('bg-slate-100', 'cursor-not-allowed');
            }
        }

        if (loadToMachineButton) {
            loadToMachineButton.disabled = netWeight <= 0;
            if (netWeight <= 0) {
                loadToMachineButton.classList.add('opacity-60', 'cursor-not-allowed');
                loadToMachineButton.textContent = 'Load Disabled';
            } else {
                loadToMachineButton.classList.remove('opacity-60', 'cursor-not-allowed');
                loadToMachineButton.textContent = 'Load';
            }
        }
        document.getElementById('loadToMachineModal').classList.remove('hidden');
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function closeLoadToMachineModal() {
        document.getElementById('loadToMachineModal').classList.add('hidden');
    }

    function openUnloadFromMachineModal(button) {
        document.getElementById('unload_coil_id').value = button.getAttribute('data-coil-id') || '';
        document.getElementById('unload_coil_no').value = button.getAttribute('data-coil-no') || '';

        let loadedMachines = [];
        try {
            loadedMachines = JSON.parse(button.getAttribute('data-loaded-machines') || '[]');
        } catch (e) {
            loadedMachines = [];
        }

        const unloadMachineSelect = document.getElementById('unload_machine_id');
        unloadMachineSelect.innerHTML = '<option value="">Select Loaded Machine</option>';

        loadedMachines.forEach(function (machine) {
            const option = document.createElement('option');
            option.value = machine.id;
            option.textContent = machine.name + (machine.machine_code ? ' (' + machine.machine_code + ')' : '');
            unloadMachineSelect.appendChild(option);
        });

        if (loadedMachines.length === 1) {
            unloadMachineSelect.value = String(loadedMachines[0].id);
        }

        document.getElementById('unloadFromMachineModal').classList.remove('hidden');
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function closeUnloadFromMachineModal() {
        document.getElementById('unloadFromMachineModal').classList.add('hidden');
    }

    function openAddCoilModal() {
        document.getElementById('addCoilModal').classList.remove('hidden');
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function closeAddCoilModal() {
        document.getElementById('addCoilModal').classList.add('hidden');
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
        @elseif(old('form_type') === 'load')
            const oldCoilId = @json(old('coil_id'));
            const loadButton = document.querySelector('[data-coil-id="' + String(oldCoilId || '') + '"][data-net-weight]');
            if (loadButton) {
                openLoadToMachineModal(loadButton);
            } else {
                document.getElementById('loadToMachineModal').classList.remove('hidden');
            }
        @elseif(old('form_type') === 'unload')
            const oldCoilId = @json(old('coil_id'));
            const oldMachineId = @json(old('machine_id'));
            const unloadButton = document.querySelector('[data-coil-id="' + String(oldCoilId || '') + '"][data-loaded-machines]');
            if (unloadButton) {
                openUnloadFromMachineModal(unloadButton);
                if (oldMachineId) {
                    document.getElementById('unload_machine_id').value = String(oldMachineId);
                }
            } else {
                document.getElementById('unloadFromMachineModal').classList.remove('hidden');
            }
        @else
            openAddCoilModal();
        @endif
    });
    @endif

    document.addEventListener('DOMContentLoaded', function () {
        const addCoilForm = document.getElementById('addCoilForm');
        const saveCoilButton = document.getElementById('saveCoilButton');
        const editCoilForm = document.getElementById('editCoilForm');
        const updateCoilButton = document.getElementById('updateCoilButton');
        const loadToMachineForm = document.getElementById('loadToMachineForm');
        const loadToMachineButton = document.getElementById('loadToMachineButton');
        const unloadFromMachineForm = document.getElementById('unloadFromMachineForm');
        const unloadFromMachineButton = document.getElementById('unloadFromMachineButton');

        if (addCoilForm && saveCoilButton) {
            addCoilForm.addEventListener('submit', function () {
                saveCoilButton.disabled = true;
                saveCoilButton.classList.add('opacity-60', 'cursor-not-allowed');
                saveCoilButton.textContent = 'Saving...';
            });
        }

        if (editCoilForm && updateCoilButton) {
            editCoilForm.addEventListener('submit', function () {
                updateCoilButton.disabled = true;
                updateCoilButton.classList.add('opacity-60', 'cursor-not-allowed');
                updateCoilButton.textContent = 'Updating...';
            });
        }

        if (loadToMachineForm && loadToMachineButton) {
            loadToMachineForm.addEventListener('submit', function () {
                if (loadToMachineButton.disabled) {
                    return;
                }

                const loadWeightInput = document.getElementById('load_weight');
                const maxWeight = loadWeightInput ? Number(loadWeightInput.max || '0') : 0;
                const enteredWeight = loadWeightInput ? Number(loadWeightInput.value || '0') : 0;

                if (loadWeightInput && (enteredWeight <= 0 || (maxWeight > 0 && enteredWeight > maxWeight))) {
                    alert('Load weight must be between 1 and ' + maxWeight + ' KG.');
                    loadWeightInput.focus();
                    return;
                }

                loadToMachineButton.disabled = true;
                loadToMachineButton.classList.add('opacity-60', 'cursor-not-allowed');
                loadToMachineButton.textContent = 'Loading...';
            });
        }

        if (unloadFromMachineForm && unloadFromMachineButton) {
            unloadFromMachineForm.addEventListener('submit', function () {
                unloadFromMachineButton.disabled = true;
                unloadFromMachineButton.classList.add('opacity-60', 'cursor-not-allowed');
                unloadFromMachineButton.textContent = 'Unloading...';
            });
        }

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
