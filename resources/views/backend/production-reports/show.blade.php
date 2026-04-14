@extends('backend.layout.app')

@section('title', 'Roll Forming (SF1) Process - View Production Report')

@section('page-title', 'Roll Forming (SF1) Process Management')

@section('breadcrumb')
    <a href="{{ route('admin.production-reports.index') }}" class="text-slate-600 hover:text-slate-900">Production Reports</a>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">Roll Forming (SF1) Process</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">View Report #{{ $productionReport->id }}</span>
@endsection

@section('content')
<div class="p-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                        <i data-lucide="eye" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Roll Forming (SF1) Process - Production Report Details</h2>
                        <p class="text-sm text-slate-500">Roll Forming (SF1) Report #{{ $productionReport->id }} - {{ $productionReport->report_date }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.production-reports.edit', $productionReport) }}" class="px-4 py-2 text-sm font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all flex items-center gap-2">
                        <i data-lucide="edit" class="w-4 h-4"></i>
                        Edit
                    </a>
                    <a href="{{ route('admin.production-reports.index') }}" class="px-4 py-2 text-sm font-medium text-white rounded-lg hover:shadow-lg transition-all flex items-center gap-2" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Basic Information -->
            <div class="mb-6 pb-6 border-b border-slate-200">
                <h3 class="text-md font-semibold text-slate-900 mb-3 flex items-center gap-2">
                    <i data-lucide="info" class="w-5 h-5 text-blue-500"></i>
                    Basic Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-slate-50 rounded-lg p-4">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Machine</label>
                        <p class="text-base font-semibold text-slate-900">{{ $productionReport->machine->name }}</p>
                        @if($productionReport->coil)
                            <p class="text-xs text-slate-500 mt-1">Coil: {{ $productionReport->coil->coil_no }}</p>
                        @endif
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Slide Size</label>
                        <p class="text-base font-semibold text-slate-900">{{ $productionReport->slideSize->name }} ({{ $productionReport->slideSize->size }})</p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Is Ballcage</label>
                        <p class="text-base font-semibold">
                            @if($productionReport->is_ballcage)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    <i data-lucide="check" class="w-3.5 h-3.5 mr-1"></i>
                                    Yes
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                    <i data-lucide="minus" class="w-3.5 h-3.5 mr-1"></i>
                                    No
                                </span>
                            @endif
                        </p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Report Date</label>
                        <p class="text-base font-semibold text-slate-900">{{ \Carbon\Carbon::parse($productionReport->report_date)->format('d M, Y') }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Shift</label>
                        <p class="text-base font-semibold text-slate-900">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $productionReport->shift == 'Morning' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                @if($productionReport->shift == 'Morning')
                                    <i data-lucide="sun" class="w-4 h-4 mr-1"></i>
                                @else
                                    <i data-lucide="moon" class="w-4 h-4 mr-1"></i>
                                @endif
                                {{ $productionReport->shift }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Production Plan -->
            <div class="mb-8 pb-8 border-b border-slate-200">
                <h3 class="text-md font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <i data-lucide="target" class="w-5 h-5 text-green-500"></i>
                    Production Plan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <label class="block text-xs font-medium text-green-600 mb-1">Total Set/Shift (Target)</label>
                        <p class="text-3xl font-bold text-green-700">{{ $productionReport->total_set_shift !== null ? number_format($productionReport->total_set_shift, 2) : '-' }}</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <label class="block text-xs font-medium text-blue-600 mb-1">Set/Hour (Target)</label>
                        <p class="text-3xl font-bold text-blue-700">{{ $productionReport->set_per_hour !== null ? number_format($productionReport->set_per_hour, 2) : '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Target Achievement Status -->
            @php
                $targetAchievement = ($productionReport->total_set_shift ?? 0) > 0 
                    ? (($productionReport->actual_set_shift ?? 0) / $productionReport->total_set_shift) * 100 
                    : 0;
                $efficiency = $targetAchievement; // Same as achievement percentage
                $isAchieved = ($productionReport->actual_set_shift ?? 0) >= ($productionReport->total_set_shift ?? 0);
                $pendingPercentage = $isAchieved ? 0 : max(0, 100 - $targetAchievement);
                $pendingAmount = $isAchieved ? 0 : max(0, ($productionReport->total_set_shift ?? 0) - ($productionReport->actual_set_shift ?? 0));
            @endphp
            <div class="mb-8 pb-8 border-b border-slate-200">
                <h3 class="text-md font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <i data-lucide="bar-chart-2" class="w-5 h-5 text-indigo-500"></i>
                    Target Achievement Status
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Achievement Status Box -->
                    <div class="relative overflow-hidden rounded-xl border-2 {{ $isAchieved ? 'border-green-300 bg-gradient-to-br from-green-50 via-green-100 to-emerald-50' : 'border-red-300 bg-gradient-to-br from-red-50 via-red-100 to-rose-50' }} p-4 shadow-lg">
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold {{ $isAchieved ? 'text-green-800' : 'text-red-800' }}">
                                    Target Status
                                </label>
                                @if($isAchieved)
                                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                                @else
                                    <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                                @endif
                            </div>
                            <div class="flex items-baseline gap-2 mb-1">
                                <p class="text-3xl font-extrabold {{ $isAchieved ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $isAchieved ? 'ACHIEVED' : 'FAILED' }}
                                </p>
                            </div>
                            <p class="text-xs {{ $isAchieved ? 'text-green-600' : 'text-red-600' }} font-medium">
                                @if($isAchieved)
                                    Target successfully completed!
                                @else
                                    Target not achieved
                                @endif
                            </p>
                            <div class="mt-3 pt-3 border-t {{ $isAchieved ? 'border-green-200' : 'border-red-200' }}">
                                <div class="flex justify-between items-center text-sm mb-2">
                                    <span class="{{ $isAchieved ? 'text-green-700' : 'text-red-700' }} font-medium">Achievement:</span>
                                    <span class="text-xl font-bold {{ $isAchieved ? 'text-green-800' : 'text-red-800' }}">{{ number_format($targetAchievement, 1) }}%</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="{{ $isAchieved ? 'text-green-700' : 'text-red-700' }} font-medium flex items-center gap-1">
                                        <i data-lucide="zap" class="w-4 h-4"></i>
                                        Efficiency:
                                    </span>
                                    <span class="text-lg font-bold {{ $efficiency >= 100 ? 'text-green-700' : ($efficiency >= 80 ? 'text-yellow-600' : 'text-red-700') }}">
                                        {{ number_format($efficiency, 1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Background decoration -->
                        <div class="absolute -right-5 -bottom-5 opacity-10">
                            @if($isAchieved)
                                <i data-lucide="trophy" class="w-24 h-24 {{ $isAchieved ? 'text-green-600' : 'text-red-600' }}"></i>
                            @else
                                <i data-lucide="alert-triangle" class="w-24 h-24 {{ $isAchieved ? 'text-green-600' : 'text-red-600' }}"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Target Progress Box -->
                    <div class="relative overflow-hidden rounded-xl border-2 border-indigo-300 bg-gradient-to-br from-indigo-50 via-indigo-100 to-blue-50 p-4 shadow-lg">
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold text-indigo-800">
                                    {{ $isAchieved ? 'Exceeded Target' : 'Pending Target' }}
                                </label>
                                <i data-lucide="trending-up" class="w-6 h-6 text-indigo-600"></i>
                            </div>
                            <div class="flex items-baseline gap-2 mb-1">
                                @if($isAchieved)
                                    @php
                                        $exceededAmount = ($productionReport->actual_set_shift ?? 0) - ($productionReport->total_set_shift ?? 0);
                                        $exceededPercentage = $targetAchievement - 100;
                                    @endphp
                                    <p class="text-3xl font-extrabold text-indigo-700">
                                        +{{ number_format($exceededAmount, 2) }}
                                    </p>
                                @else
                                    <p class="text-3xl font-extrabold text-indigo-700">
                                        {{ number_format($pendingAmount, 2) }}
                                    </p>
                                @endif
                                <span class="text-xs text-indigo-600 font-medium">sets</span>
                            </div>
                            <p class="text-xs text-indigo-600 font-medium mb-3">
                                @if($isAchieved)
                                    Exceeded by {{ number_format($exceededPercentage, 1) }}%
                                @else
                                    Remaining to achieve target
                                @endif
                            </p>
                            
                            <!-- Progress Bar -->
                            <div class="mt-3 pt-3 border-t border-indigo-200">
                                <div class="flex justify-between items-center text-sm mb-2">
                                    <span class="text-indigo-700 font-medium">Progress</span>
                                    <span class="text-indigo-900 font-bold">{{ number_format(min($targetAchievement, 100), 1) }}%</span>
                                </div>
                                <div class="w-full bg-indigo-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full transition-all duration-500 {{ $isAchieved ? 'bg-gradient-to-r from-green-500 to-emerald-600' : 'bg-gradient-to-r from-indigo-500 to-blue-600' }}" 
                                         style="width: {{ min($targetAchievement, 100) }}%">
                                    </div>
                                </div>
                                @if(!$isAchieved)
                                    <p class="text-xs text-indigo-600 mt-2">
                                        {{ number_format($pendingPercentage, 1) }}% pending to complete target
                                    </p>
                                @endif
                            </div>
                        </div>
                        <!-- Background decoration -->
                        <div class="absolute -right-5 -bottom-5 opacity-10">
                            <i data-lucide="percent" class="w-24 h-24 text-indigo-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hourly Production Table -->
            <div class="mb-8 pb-8 border-b border-slate-200">
                <h3 class="text-md font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <i data-lucide="clock" class="w-5 h-5 text-purple-500"></i>
                    Hourly Production (Set Count)
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead class="text-white" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                            <tr>
                                @php
                                    $timeLabels = $productionReport->shift == 'Night' 
                                        ? ['8PM-9PM', '9PM-10PM', '10PM-11PM', '11PM-12AM', '12AM-1AM', '1AM-2AM', '2AM-3AM', '3AM-4AM', '4AM-5AM', '5AM-6AM', '6AM-7AM', '7AM-8AM']
                                        : ['8AM-9AM', '9AM-10AM', '10AM-11AM', '11AM-12PM', '12PM-1PM', '1PM-2PM', '2PM-3PM', '3PM-4PM', '4PM-5PM', '5PM-6PM', '6PM-7PM', '7PM-8PM'];
                                    
                                    $hourFields = ['hour_8_9', 'hour_9_10', 'hour_10_11', 'hour_11_12', 'hour_12_1', 'hour_1_2', 'hour_2_3', 'hour_3_4', 'hour_4_5', 'hour_5_6', 'hour_6_7', 'hour_7_8'];
                                @endphp
                                @foreach($timeLabels as $label)
                                    <th class="border border-slate-300 px-4 py-2 text-center text-[10px] font-semibold text-white min-w-24 whitespace-nowrap">{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white">
                                @foreach($hourFields as $field)
                                    <td class="border border-slate-300 px-4 py-3 text-center">
                                        <span class="text-lg font-semibold text-slate-900">{{ $productionReport->$field !== null ? number_format($productionReport->$field, 0) : '-' }}</span>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Production Actual & Summary -->
            <div class="mb-8 pb-8 border-b border-slate-200">
                <h3 class="text-md font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
                    Production Actual & Summary
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg p-5 border border-emerald-200 shadow-sm">
                        <label class="block text-xs font-medium text-emerald-600 mb-2 flex items-center gap-1">
                            <i data-lucide="package" class="w-4 h-4"></i>
                            Actual Set/Shift
                        </label>
                        <p class="text-3xl font-bold text-emerald-700">{{ number_format($productionReport->actual_set_shift, 2) }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-5 border border-orange-200 shadow-sm">
                        <label class="block text-xs font-medium text-orange-600 mb-2 flex items-center gap-1">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            Workman Count
                        </label>
                        <p class="text-3xl font-bold text-orange-700">{{ $productionReport->workman_count }}</p>
                        <p class="text-xs text-orange-600 mt-2">Production workers</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-5 border border-purple-200 shadow-sm">
                        <label class="block text-xs font-medium text-purple-600 mb-2 flex items-center gap-1">
                            <i data-lucide="briefcase" class="w-4 h-4"></i>
                            Staff Count
                        </label>
                        <p class="text-3xl font-bold text-purple-700">{{ $productionReport->staff_count }}</p>
                        <p class="text-xs text-purple-600 mt-2">Support staff</p>
                    </div>
                </div>
            </div>

            <!-- Status & Meta Information -->
            <div>
                <h3 class="text-md font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <i data-lucide="settings" class="w-5 h-5 text-slate-500"></i>
                    Status & Meta Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-slate-50 rounded-lg p-4">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                        <p class="text-base font-semibold">
                            @if($productionReport->status)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i>
                                    Inactive
                                </span>
                            @endif
                        </p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Deleted</label>
                        <p class="text-base font-semibold">
                            @if($productionReport->is_deleted)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>
                                    Yes
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    <i data-lucide="check" class="w-4 h-4 mr-1"></i>
                                    No
                                </span>
                            @endif
                        </p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Created At</label>
                        <p class="text-sm font-semibold text-slate-900">{{ $productionReport->created_at->format('d M, Y') }}</p>
                        <p class="text-xs text-slate-500">{{ $productionReport->created_at->format('h:i A') }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Updated At</label>
                        <p class="text-sm font-semibold text-slate-900">{{ $productionReport->updated_at->format('d M, Y') }}</p>
                        <p class="text-xs text-slate-500">{{ $productionReport->updated_at->format('h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    table {
        border-collapse: collapse;
    }
</style>
@endsection
