@extends('backend.layout.app')

@section('title', 'Production Report - Hourly')

@section('page-title', 'Production Report Entry')

@section('breadcrumb')
    <span class="text-slate-600">Production Reports</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">SF002</span>
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

        <div class="p-12">
            <div class="flex flex-col items-center justify-center gap-6 py-12">
                <div class="w-20 h-20 rounded-full bg-amber-50 flex items-center justify-center">
                    <i data-lucide="construction" class="w-10 h-10 text-amber-600"></i>
                </div>
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-slate-900 mb-2">Work In Progress</h3>
                    <p class="text-slate-600">This form is currently under development and will be available soon.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
