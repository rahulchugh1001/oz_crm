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
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden" style="transform: scale(0.92); transform-origin: top left; width: 108.7%;">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-4 font-bold text-slate-900">Available Coils</h2>
                <p class="text-slate-500 mt-1">Raw material inventory for Roll Forming (SF1) production line</p>
            </div>
            <button type="button" title="Add New Coil" class="inline-flex items-center justify-center p-3 rounded-xl bg-blue-700 hover:bg-blue-800 text-white transition-all">
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
                        <th class="px-6 py-4 text-slate-700 font-semibold">STATUS</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-slate-200">
                        <td class="px-6 py-5 text-slate-900 font-semibold">OZ-BBDS-CRC Coil - 53.10 X 1 mm</td>
                        <td class="px-6 py-5 text-slate-700">53.10 X 1 mm</td>
                        <td class="px-6 py-5 text-slate-700">Uttam / Tata</td>
                        <td class="px-6 py-5 text-slate-700">0.950</td>
                        <td class="px-6 py-5 text-slate-700">161</td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-800 font-medium">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                Available
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <button type="button" title="Load Coil" class="inline-flex items-center justify-center p-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white transition-all">
                                <i data-lucide="truck" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>

                    <tr class="border-b border-slate-200">
                        <td class="px-6 py-5 text-slate-900 font-semibold">OZ-BBDS-CRC Coil - 65.5 X 1 mm</td>
                        <td class="px-6 py-5 text-slate-700">65.5 X 1 mm</td>
                        <td class="px-6 py-5 text-slate-700">Uttam</td>
                        <td class="px-6 py-5 text-slate-700">0.950</td>
                        <td class="px-6 py-5 text-slate-700">175</td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-800 font-medium">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                Available
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <button type="button" title="Load Coil" class="inline-flex items-center justify-center p-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white transition-all">
                                <i data-lucide="truck" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>

                    <tr class="border-b border-slate-200">
                        <td class="px-6 py-5 text-slate-900 font-semibold">OZ-BBDS-CRC Coil - 34.70 X 1.20 mm</td>
                        <td class="px-6 py-5 text-slate-700">34.70 X 1.20 mm</td>
                        <td class="px-6 py-5 text-slate-700">Tata</td>
                        <td class="px-6 py-5 text-slate-700">1.150</td>
                        <td class="px-6 py-5 text-slate-700">185</td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-100 text-blue-700 font-medium">
                                <i data-lucide="loader" class="w-4 h-4"></i>
                                In Use
                            </span>
                        </td>
                        <td class="px-6 py-5 text-slate-500">
                            <span title="In Production" class="inline-flex items-center justify-center p-2 rounded-lg bg-slate-100 text-slate-600">
                                <i data-lucide="factory" class="w-4 h-4"></i>
                            </span>
                        </td>
                    </tr>

                    <tr class="border-b border-slate-200">
                        <td class="px-6 py-5 text-slate-900 font-semibold">OZ-BBDS-GP Coil- Width- 89.8 mm to 90 mm X Thick- 0.6 mm</td>
                        <td class="px-6 py-5 text-slate-700">89.8 mm to 90 mm</td>
                        <td class="px-6 py-5 text-slate-700">JSW</td>
                        <td class="px-6 py-5 text-slate-700">1.050</td>
                        <td class="px-6 py-5 text-slate-700">170</td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-100 text-slate-800 font-medium">
                                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                Completed
                            </span>
                        </td>
                        <td class="px-6 py-5 text-slate-500">
                            <span title="Completed" class="inline-flex items-center justify-center p-2 rounded-lg bg-emerald-100 text-emerald-700">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td class="px-6 py-5 text-slate-900 font-semibold">CRCA Coil 0.8 MM Thk SPCC 4D</td>
                        <td class="px-6 py-5 text-slate-700">0.8 MM</td>
                        <td class="px-6 py-5 text-slate-700">-</td>
                        <td class="px-6 py-5 text-slate-700">-</td>
                        <td class="px-6 py-5 text-slate-700">-</td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-800 font-medium">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                Available
                            </span>
                        </td>
                        <td class="px-6 py-5 text-slate-500">
                            <span title="No Action" class="inline-flex items-center justify-center p-2 rounded-lg bg-slate-100 text-slate-500">
                                <i data-lucide="minus" class="w-4 h-4"></i>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
