<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Manufacturing Traceability System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('admin/css/custom.css') }}">
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    @include('backend.layout.sidebar')

    <!-- Main Content -->
    <div class="ml-64">
        <!-- Header -->
        <header class="sticky top-0 z-30 bg-white border-b border-gray-100 shadow-subtle">
            <div class="px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">@yield('page-title', 'Dashboard')</h1>
                        <div class="flex items-center gap-1 mt-1 text-sm text-slate-600">
                            @yield('breadcrumb')
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <button class="p-2 hover:bg-slate-100 rounded-lg relative transition-all">
                                <i data-lucide="bell" class="w-5 h-5 text-slate-600"></i>
                                <span class="absolute -top-1 -right-1 w-5 h-5 gradient-danger text-white text-xs rounded-full flex items-center justify-center hidden" id="notification-badge">
                                    0
                                </span>
                            </button>
                        </div>
                        <div class="flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-lg border border-slate-200">
                            <i data-lucide="calendar" class="w-4 h-4 text-slate-600"></i>
                            <span class="text-slate-700">{{ date('F d, Y') }}</span>
                        </div>
                        <div class="flex items-center gap-3 px-4 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                            <div class="w-8 h-8 rounded-full gradient-primary flex items-center justify-center">
                                <i data-lucide="zap" class="w-4 h-4 text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-900">Shift Day</p>
                                <p class="text-xs text-slate-500">08:00 - 20:00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>