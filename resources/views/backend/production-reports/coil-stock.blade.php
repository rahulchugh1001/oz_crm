@extends('backend.layout.app')

@section('title', 'SF001 Coil Stock')

@section('page-title', 'SF001 Coil Stock')

@section('breadcrumb')
    <span class="text-slate-600">SF001</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Coil Stock</span>
@endsection

@section('content')
<div class="p-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden" style="transform: scale(0.92); transform-origin: top left; width: 108.7%;">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-4 font-bold text-slate-900">Available Coils</h2>
                <p class="text-slate-500 mt-1">Raw material inventory for SF1 production line</p>
            </div>
            <button type="button" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-semibold transition-all">
                <i data-lucide="plus" class="w-5 h-5"></i>
                <span>Add New Coil</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-slate-700 font-semibold">COIL NO</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">COIL SIZE</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">COIL MAKE</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">THICKNESS</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">NET WEIGHT (KG)</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">STATUS</th>
                        <th class="px-6 py-4 text-slate-700 font-semibold">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-slate-200">
                        <td class="px-6 py-5 text-slate-900 font-semibold">CRIUS26A0043(6/28)</td>
                        <td class="px-6 py-5 text-slate-700">65.5×1.00</td>
                        <td class="px-6 py-5 text-slate-700">Uttam / Tata</td>
                        <td class="px-6 py-5 text-slate-700">0.950</td>
                        <td class="px-6 py-5 text-slate-700">161 / 345 / 200</td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-800 font-medium">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                Available
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <button type="button" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-semibold transition-all">
                                <i data-lucide="truck" class="w-4 h-4"></i>
                                Load Coil
                            </button>
                        </td>
                    </tr>

                    <tr class="border-b border-slate-200">
                        <td class="px-6 py-5 text-slate-900 font-semibold">CRIUS26A0044(6/28)</td>
                        <td class="px-6 py-5 text-slate-700">65.5×1.00</td>
                        <td class="px-6 py-5 text-slate-700">Uttam</td>
                        <td class="px-6 py-5 text-slate-700">0.950</td>
                        <td class="px-6 py-5 text-slate-700">175 / 350 / 210</td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-800 font-medium">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                Available
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <button type="button" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-semibold transition-all">
                                <i data-lucide="truck" class="w-4 h-4"></i>
                                Load Coil
                            </button>
                        </td>
                    </tr>

                    <tr class="border-b border-slate-200">
                        <td class="px-6 py-5 text-slate-900 font-semibold">CRIUS26A0045(6/28)</td>
                        <td class="px-6 py-5 text-slate-700">70.0x1.20</td>
                        <td class="px-6 py-5 text-slate-700">Tata</td>
                        <td class="px-6 py-5 text-slate-700">1.150</td>
                        <td class="px-6 py-5 text-slate-700">185 / 380 / 225</td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-100 text-blue-700 font-medium">
                                <i data-lucide="loader" class="w-4 h-4"></i>
                                In Use
                            </span>
                        </td>
                        <td class="px-6 py-5 text-slate-500 text-2">In Production</td>
                    </tr>

                    <tr>
                        <td class="px-6 py-5 text-slate-900 font-semibold">CRIUS26A0046(6/28)</td>
                        <td class="px-6 py-5 text-slate-700">68.0x1.10</td>
                        <td class="px-6 py-5 text-slate-700">JSW</td>
                        <td class="px-6 py-5 text-slate-700">1.050</td>
                        <td class="px-6 py-5 text-slate-700">170 / 360 / 215</td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-100 text-slate-800 font-medium">
                                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                Completed
                            </span>
                        </td>
                        <td class="px-6 py-5 text-slate-500 text-2">Completed</td>
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
