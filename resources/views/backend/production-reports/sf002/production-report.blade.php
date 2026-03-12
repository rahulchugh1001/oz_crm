@extends('backend.layout.app')

@section('title', 'Production Report - Hourly')

@section('page-title', 'Production Report Entry')

@section('breadcrumb')
    <span class="text-slate-600">Production Reports</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">CED & Zinc (SF2)</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">Production</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Report</span>
@endsection

@section('content')
<div class="p-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Production Report - Hourly</h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Item: <span class="font-medium text-slate-700">{{ $transfer->item_code }}</span> - 
                        <span class="font-medium text-slate-700">{{ $transfer->item_name }}</span> 
                        (<span class="font-medium text-slate-700">{{ $transfer->item_size }}</span>)
                    </p>
                </div>
                <a href="{{ route('admin.production-reports.sf002.process') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors font-medium">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900">
                <div class="flex items-start gap-3">
                    <i data-lucide="construction" class="w-5 h-5 mt-0.5"></i>
                    <div>
                        <p class="font-semibold">Work Under Process</p>
                        <p class="text-sm">You can view and fill the form fields. Layout and calculations may still be updated.</p>
                    </div>
                </div>
            </div>

            <form id="productionReportForm" method="POST" action="{{ route('admin.production-reports.sf002.production-report.store', $transfer->id) }}">
                @csrf

                @php
                    $slots = [
                        '6AM to 8AM',
                        '8AM to 9AM',
                        '9AM to 10AM',
                        '10AM to 11AM',
                        '11AM to 12Noon',
                        '12Noon to 1PM',
                        '1PM to 2PM',
                        '2PM to 3PM',
                        '3PM to 4PM',
                        '4PM to 5PM',
                        '5PM to 6PM',
                    ];
                @endphp

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-[1600px] w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-100">
                                <th class="border border-slate-300 px-3 py-2 text-left font-semibold text-slate-900">Type</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">Production Plan Set / SLP Set Hour</th>
                                @foreach ($slots as $slot)
                                    <th colspan="2" class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">{{ $slot }}</th>
                                @endforeach
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">Actual / Set / Shift</th>
                                <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-900">Manpower / Workman</th>
                            </tr>
                            <tr class="bg-slate-50">
                                <th class="border border-slate-300 px-3 py-2"></th>
                                <th class="border border-slate-300 px-3 py-2"></th>
                                @for ($i = 1; $i <= count($slots); $i++)
                                    <th class="border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700">Set</th>
                                    <th class="border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700">Actual</th>
                                @endfor
                                <th class="border border-slate-300 px-3 py-2"></th>
                                <th class="border border-slate-300 px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (['ced' => 'CED', 'zinc' => 'ZINC'] as $prefix => $label)
                                <tr class="hover:bg-slate-50">
                                    <td class="border border-slate-300 px-3 py-2 font-medium text-slate-900">{{ $label }}</td>
                                    <td class="border border-slate-300 px-3 py-2">
                                        <input type="number" name="{{ $prefix }}_plan" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="0.01">
                                    </td>
                                    @for ($slot = 1; $slot <= 11; $slot++)
                                        <td class="border border-slate-300 px-3 py-2">
                                            <input type="number" name="{{ $prefix }}_slot{{ $slot }}_set" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="0.01">
                                        </td>
                                        <td class="border border-slate-300 px-3 py-2">
                                            <input type="number" name="{{ $prefix }}_slot{{ $slot }}_actual" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="0.01">
                                        </td>
                                    @endfor
                                    <td class="border border-slate-300 px-3 py-2">
                                        <input type="text" name="{{ $prefix }}_shift" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="">
                                    </td>
                                    <td class="border border-slate-300 px-3 py-2">
                                        <input type="number" name="{{ $prefix }}_manpower" class="w-full px-2 py-1 border border-slate-200 rounded text-sm" placeholder="-" step="0.01">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.production-reports.sf002.process') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors font-medium">
                        Cancel
                    </a>
                    <button type="submit" disabled aria-disabled="true" title="Work Under Process" class="px-4 py-2 rounded-lg bg-slate-400 text-white cursor-not-allowed opacity-80 font-medium" >
                        Save Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('productionReportForm');
    if (!form) return;

    form.addEventListener('submit', function () {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (!submitBtn) return;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="flex items-center gap-2"><i data-lucide="loader" class="w-4 h-4 animate-spin"></i>Saving...</span>';
    });
});
</script>
@endpush
