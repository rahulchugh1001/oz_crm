@extends('backend.layout.app')

@section('title', 'SF001 Coil Stock - View')

@section('page-title', 'Roll Forming (SF1) Coil Stock')

@section('breadcrumb')
    <a href="{{ route('admin.production-reports.index') }}" class="text-slate-600 hover:text-slate-900">Production Reports</a>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <a href="{{ route('admin.production-reports.sf001') }}" class="text-slate-600 hover:text-slate-900">Roll Forming (SF1)</a>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <a href="{{ route('admin.production-reports.sf001.coil-stock') }}" class="text-slate-600 hover:text-slate-900">Coil Stock</a>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">View Coil {{ $coil->coil_no }}</span>
@endsection

@section('content')
<div class="p-4 md:p-6 space-y-5">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Coil Details: {{ $coil->coil_no }}</h2>
                <p class="text-sm text-slate-500">Complete details, reporting and load/unload history.</p>
            </div>
            <a href="{{ route('admin.production-reports.sf001.coil-stock') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back to Coil Stock
            </a>
        </div>

        <div class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-[11px] uppercase tracking-wide text-slate-500">Coil No</p>
                <p class="mt-1 text-base font-semibold text-slate-900">{{ $coil->coil_no }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-[11px] uppercase tracking-wide text-slate-500">Supplier</p>
                <p class="mt-1 text-base font-semibold text-slate-900">{{ $coil->manufacture->name ?? '-' }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-[11px] uppercase tracking-wide text-slate-500">Coil Size</p>
                <p class="mt-1 text-base font-semibold text-slate-900">{{ $coil->coil_size }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-[11px] uppercase tracking-wide text-slate-500">Thickness</p>
                <p class="mt-1 text-base font-semibold text-slate-900">{{ number_format((float) $coil->thickness, 3) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-[11px] uppercase tracking-wide text-slate-500">Current Net Weight (KG)</p>
                <p class="mt-1 text-base font-semibold text-slate-900">{{ number_format((float) $coil->net_weight_kg, 0) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-[11px] uppercase tracking-wide text-slate-500">Process</p>
                <p class="mt-1 text-base font-semibold text-slate-900">{{ ucwords(str_replace('_', ' ', (string) $coil->process)) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-[11px] uppercase tracking-wide text-slate-500">Last Process Type</p>
                <p class="mt-1 text-base font-semibold text-slate-900">{{ $coil->process_type ? ucfirst($coil->process_type) : '-' }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-[11px] uppercase tracking-wide text-slate-500">Status</p>
                <p class="mt-1 text-base font-semibold {{ (int) $coil->status === 1 ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ (int) $coil->status === 1 ? 'Active' : 'Inactive' }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Loaded Machine(s)</h3>
        </div>
        <div class="p-5">
            @if($loadedMachines->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                    @foreach($loadedMachines as $machine)
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                            <p class="text-sm font-semibold text-emerald-900">{{ $machine->name }}</p>
                            <p class="text-xs text-emerald-700 mt-0.5">Code: {{ $machine->machine_code ?: '-' }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-500">This coil is not currently loaded on any machine.</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Assigned Machine(s)</h3>
            <p class="text-xs text-slate-500 mt-1">Machines linked to this coil (with assigned time).</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-white" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    <tr>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">Machine</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">Code</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">Assigned At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($assignedMachines as $machine)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-900 font-medium">{{ $machine->name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $machine->machine_code ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $machine->pivot?->created_at ? \Carbon\Carbon::parse($machine->pivot->created_at)->format('d-m-Y h:i A') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-slate-500">No machines assigned to this coil.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Production Reporting (Using This Coil)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-white" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    <tr>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">Date</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">Machine</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">Shift</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">Slide Size</th>
                        <th class="px-4 py-2 text-right text-[10px] font-semibold whitespace-nowrap">Actual Set</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">Report</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($productionReports as $report)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-700">{{ $report->report_date ? \Carbon\Carbon::parse($report->report_date)->format('d-m-Y') : '-' }}</td>
                            <td class="px-4 py-3 text-slate-900 font-medium">
                                {{ $report->machine->name ?? '-' }}
                                @if(!empty($report->machine->machine_code))
                                    <span class="text-xs text-slate-500">({{ $report->machine->machine_code }})</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $report->shift }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $report->slideSize->name ?? '-' }}
                                @if(!empty($report->slideSize->size))
                                    <span class="text-xs text-slate-500">({{ $report->slideSize->size }})</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-slate-900 font-semibold">{{ number_format((float) $report->actual_set_shift, 0) }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.production-reports.show', $report) }}" class="inline-flex items-center gap-1 rounded-md bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700 hover:bg-blue-200">
                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                    Open
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-500">No production report found for this coil.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Load/Unload Track History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-white" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    <tr>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">Event Time</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">Action</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">Machine</th>
                        <th class="px-4 py-2 text-right text-[10px] font-semibold whitespace-nowrap">Load Wt</th>
                        <th class="px-4 py-2 text-right text-[10px] font-semibold whitespace-nowrap">Pending/Unload Wt</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">Remark</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($trackHistory as $track)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-700">{{ $track->event_at ? \Carbon\Carbon::parse($track->event_at)->format('d-m-Y h:i A') : '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $track->type === 'load' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst($track->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-900 font-medium">{{ $track->machine->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-right text-slate-800">{{ number_format((float) $track->load_weight, 0) }}</td>
                            <td class="px-4 py-3 text-right text-slate-800">{{ number_format((float) $track->unload_weight, 0) }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $track->remark ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $track->creator->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-500">No load/unload track history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Track Log History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-white" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                    <tr>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">Created At</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">Action</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">Machine</th>
                        <th class="px-4 py-2 text-right text-[10px] font-semibold whitespace-nowrap">Load Wt</th>
                        <th class="px-4 py-2 text-right text-[10px] font-semibold whitespace-nowrap">Unload Wt</th>
                        <th class="px-4 py-2 text-right text-[10px] font-semibold whitespace-nowrap">Total Wt</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">Message</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold whitespace-nowrap">By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($logHistory as $log)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-700">{{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d-m-Y h:i A') : '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $log->action_type === 'load' ? 'bg-blue-100 text-blue-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ ucfirst((string) $log->action_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-900 font-medium">{{ $log->machine->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-right text-slate-800">{{ number_format((float) $log->load_weight, 0) }}</td>
                            <td class="px-4 py-3 text-right text-slate-800">{{ number_format((float) $log->unload_weight, 0) }}</td>
                            <td class="px-4 py-3 text-right text-slate-800">{{ number_format((float) $log->total_weight, 0) }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $log->message ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $log->creator->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-slate-500">No track logs found for this coil.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
