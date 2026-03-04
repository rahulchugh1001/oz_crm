@extends('backend.layout.app')

@section('title', 'SF002 Process - Production Reports List')

@section('page-title', 'SF002 Process Management')

@section('breadcrumb')
    <span class="text-slate-600">Production Reports</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="text-slate-600">SF002 Process</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">List</span>
@endsection

@section('content')
<div class="p-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="p-12 flex flex-col items-center justify-center text-center">
            <div class="w-20 h-20 rounded-full bg-amber-100 flex items-center justify-center mb-4">
                <i data-lucide="clock" class="w-10 h-10 text-amber-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">SF002 Process - Coming Soon</h2>
            <p class="text-slate-500 mb-4">We're working hard to bring SF002 Production Reports feature to you.</p>
            <p class="text-sm text-slate-400">This feature will be available in the next update.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
