@extends('backend.layout.app')

@section('title', 'Manufacturing Traceability System')

@section('page-title', 'Production Command Dashboard')

@section('breadcrumb')
    <span class="font-medium text-slate-900">Dashboard</span>
@endsection

@push('styles')
<style>
    .dashboard-zoom-out {
        zoom: 0.9;
    }

    @supports not (zoom: 1) {
        .dashboard-zoom-out {
            transform: scale(0.9);
            transform-origin: top left;
            width: 111.12%;
        }
    }

    @media (max-width: 1024px) {
        .dashboard-zoom-out {
            zoom: 1;
            transform: none;
            width: 100%;
        }
    }

    .kpi-card {
        --kpi-glow: rgba(59, 130, 246, 0.18);
        --kpi-glow-soft: rgba(59, 130, 246, 0.1);
        --kpi-accent: rgba(59, 130, 246, 0.75);
        position: relative;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 0.9rem;
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        padding: 1rem;
        box-shadow: 0 1px 2px rgba(148, 163, 184, 0.18), 0 8px 20px var(--kpi-glow-soft);
        opacity: 0;
        transform: translateY(12px) scale(0.985);
        transition: transform 260ms ease, box-shadow 260ms ease, border-color 260ms ease;
    }

    .kpi-card.is-visible {
        animation: kpiFadeUp 520ms cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .kpi-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, transparent, var(--kpi-accent), transparent);
    }

    .kpi-card::after {
        content: "";
        position: absolute;
        top: 0;
        left: -150%;
        width: 70%;
        height: 100%;
        background: linear-gradient(110deg, transparent 0%, rgba(255, 255, 255, 0.55) 50%, transparent 100%);
        transition: left 650ms ease;
    }

    .kpi-card:hover::after {
        left: 140%;
    }

    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 28px var(--kpi-glow), 0 0 0 1px var(--kpi-glow-soft);
        border-color: #dbeafe;
    }

    .kpi-icon-wrap {
        width: 2.1rem;
        height: 2.1rem;
        border-radius: 0.65rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .kpi-icon-wrap::after {
        content: "";
        position: absolute;
        inset: -3px;
        border-radius: 0.75rem;
        background: var(--kpi-accent);
        opacity: 0.18;
        filter: blur(7px);
        animation: kpiPulse 2.4s ease-in-out infinite;
        pointer-events: none;
    }

    .kpi-icon-wrap i {
        animation: kpiIconFloat 2.8s ease-in-out infinite;
    }

    .kpi-badge {
        font-size: 10px;
        line-height: 1;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        border-radius: 9999px;
        padding: 0.35rem 0.55rem;
    }

    .kpi-value {
        font-size: 1.65rem;
        line-height: 1.1;
        font-weight: 800;
        color: #0f172a;
    }

    .kpi-detail {
        margin-top: 0.45rem;
        font-size: 11px;
        line-height: 1.35;
        color: #64748b;
    }

    .kpi-card-blue { --kpi-glow: rgba(59, 130, 246, 0.2); --kpi-glow-soft: rgba(59, 130, 246, 0.12); --kpi-accent: rgba(59, 130, 246, 0.75); }
    .kpi-card-emerald { --kpi-glow: rgba(16, 185, 129, 0.2); --kpi-glow-soft: rgba(16, 185, 129, 0.11); --kpi-accent: rgba(16, 185, 129, 0.78); }
    .kpi-card-rose { --kpi-glow: rgba(244, 63, 94, 0.2); --kpi-glow-soft: rgba(244, 63, 94, 0.11); --kpi-accent: rgba(244, 63, 94, 0.78); }
    .kpi-card-indigo { --kpi-glow: rgba(99, 102, 241, 0.2); --kpi-glow-soft: rgba(99, 102, 241, 0.11); --kpi-accent: rgba(99, 102, 241, 0.78); }
    .kpi-card-amber { --kpi-glow: rgba(245, 158, 11, 0.2); --kpi-glow-soft: rgba(245, 158, 11, 0.12); --kpi-accent: rgba(245, 158, 11, 0.8); }
    .kpi-card-teal { --kpi-glow: rgba(20, 184, 166, 0.2); --kpi-glow-soft: rgba(20, 184, 166, 0.12); --kpi-accent: rgba(20, 184, 166, 0.78); }

    .kpi-card-blue .kpi-badge { background: #dbeafe; color: #1d4ed8; }
    .kpi-card-emerald .kpi-badge { background: #d1fae5; color: #047857; }
    .kpi-card-rose .kpi-badge { background: #ffe4e6; color: #be123c; }
    .kpi-card-indigo .kpi-badge { background: #e0e7ff; color: #4338ca; }
    .kpi-card-amber .kpi-badge { background: #fef3c7; color: #b45309; }
    .kpi-card-teal .kpi-badge { background: #ccfbf1; color: #0f766e; }

    @keyframes kpiFadeUp {
        from {
            opacity: 0;
            transform: translateY(12px) scale(0.985);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes kpiIconFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-2px); }
    }

    @keyframes kpiPulse {
        0%, 100% { opacity: 0.1; transform: scale(1); }
        50% { opacity: 0.2; transform: scale(1.06); }
    }

    .kpi-card:nth-child(2) .kpi-icon-wrap i { animation-delay: 120ms; }
    .kpi-card:nth-child(3) .kpi-icon-wrap i { animation-delay: 220ms; }
    .kpi-card:nth-child(4) .kpi-icon-wrap i { animation-delay: 320ms; }
    .kpi-card:nth-child(5) .kpi-icon-wrap i { animation-delay: 420ms; }
    .kpi-card:nth-child(6) .kpi-icon-wrap i { animation-delay: 520ms; }

    @media (prefers-reduced-motion: reduce) {
        .kpi-card {
            animation: none;
            transition: none;
            opacity: 1;
            transform: none;
        }

        .kpi-card:hover {
            transform: none;
        }

        .kpi-card::after {
            display: none;
        }

        .kpi-icon-wrap::after,
        .kpi-icon-wrap i {
            animation: none;
        }
    }
</style>
@endpush

@section('content')
<div class="p-6 space-y-6 dashboard-zoom-out">
    <!-- 1. Plant Health Overview -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle p-6 hover-lift transition-all">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center">
                    <i class="fas fa-tachometer-alt text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Plant Health Overview</h2>
                    <p class="text-sm text-slate-500">Real-time operational metrics</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                <span>Updated just now</span>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            @php
                $stats = [
                    ['label' => 'Total Production Today', 'value' => $todayProductionTotal, 'detail' => 'SF001 Actual Set Shift Today', 'badge' => 'Today', 'color' => 'blue', 'icon' => 'factory'],
                    ['label' => 'Active Machines', 'value' => $activeMachinesCount, 'detail' => 'Inactive Machines: ' . $notActiveMachinesCount . ' | Machines In Use: ' . $machineInUseCount, 'badge' => 'Live', 'color' => 'emerald', 'icon' => 'cog'],
                    ['label' => 'Total Suppliers', 'value' => $totalSuppliersCount, 'detail' => 'Inactive Suppliers: ' . $inactiveSuppliersCount, 'badge' => 'Master', 'color' => 'rose', 'icon' => 'building-2'],
                    ['label' => 'Manpower Working', 'value' => $totalManpowerWorking, 'detail' => 'Staff Working: ' . $totalStaffWorkingSf001 . ' | Worker Working Count: ' . $totalWorkerWorkingSf001, 'badge' => 'Shift', 'color' => 'indigo', 'icon' => 'users-round'],
                    ['label' => 'Total Coil', 'value' => $totalCoilsCount, 'detail' => 'Total Weight: ' . $totalCoilWeightKg . ' KG', 'badge' => 'Stock', 'color' => 'amber', 'icon' => 'disc-3'],
                    ['label' => 'In Use Coil', 'value' => $inUseCoilsCount, 'detail' => 'Loaded Coil Weight: ' . $loadedCoilWeightKg . ' KG', 'badge' => 'Running', 'color' => 'teal', 'icon' => 'truck'],
                ];
            @endphp
            @foreach($stats as $stat)
            <div class="kpi-card kpi-card-{{ $stat['color'] }} js-kpi-card" style="animation-delay: {{ $loop->index * 90 }}ms;">
                <div class="flex items-start justify-between gap-3">
                    <div class="kpi-icon-wrap bg-{{ $stat['color'] }}-50">
                        <i data-lucide="{{ $stat['icon'] }}" class="w-4 h-4 text-{{ $stat['color'] }}-600"></i>
                    </div>
                    <span class="kpi-badge">{{ $stat['badge'] }}</span>
                </div>
                <p class="text-[13px] font-semibold text-slate-600 mt-3">{{ $stat['label'] }}</p>
                <div class="mt-1">
                    <p class="kpi-value js-countup" data-value="{{ preg_replace('/[^0-9]/', '', (string) $stat['value']) }}">{{ $stat['value'] }}</p>
                </div>
                <p class="kpi-detail">{{ $stat['detail'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- 2. Production Stage Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @php
            $stages = [
                [
                    'title' => 'SF1 - Primary Processing',
                    'status' => 'Healthy',
                    'statusColor' => 'emerald',
                    'icon' => 'cog',
                    'bottleneck' => false,
                    'metrics' => [
                        ['label' => 'Production Weight', 'value' => '6,850 Kg', 'progress' => 92, 'color' => 'blue', 'trend' => '+2.1%'],
                        ['label' => 'Efficiency', 'value' => '92.1%', 'progress' => 92, 'color' => 'emerald', 'trend' => '+1.3%'],
                        ['label' => 'Machines Running', 'value' => '5 / 5', 'progress' => 100, 'color' => 'emerald', 'trend' => '100%'],
                        ['label' => 'Scrap Rate', 'value' => '4.7%', 'progress' => 47, 'color' => 'amber', 'trend' => '-0.5%']
                    ]
                ],
                [
                    'title' => 'SF2 - Secondary Processing',
                    'status' => 'Warning',
                    'statusColor' => 'amber',
                    'icon' => 'hammer',
                    'bottleneck' => true,
                    'metrics' => [
                        ['label' => 'Production Weight', 'value' => '5,230 Kg', 'progress' => 70, 'color' => 'amber', 'trend' => '-12.3%'],
                        ['label' => 'Efficiency', 'value' => '76.3%', 'progress' => 76, 'color' => 'rose', 'trend' => '-8.7%'],
                        ['label' => 'Machines Running', 'value' => '3 / 5', 'progress' => 60, 'color' => 'rose', 'trend' => '-40%'],
                        ['label' => 'Scrap Rate', 'value' => '13.0%', 'progress' => 100, 'color' => 'rose', 'trend' => '+6.2%']
                    ]
                ],
                [
                    'title' => 'SF3 - Finishing',
                    'status' => 'Critical',
                    'statusColor' => 'rose',
                    'icon' => 'check-circle',
                    'bottleneck' => false,
                    'metrics' => [
                        ['label' => 'Production Weight', 'value' => '6,670 Kg', 'progress' => 89, 'color' => 'blue', 'trend' => '+3.8%'],
                        ['label' => 'Efficiency', 'value' => '88.9%', 'progress' => 89, 'color' => 'amber', 'trend' => '-1.1%'],
                        ['label' => 'Machines Running', 'value' => '4 / 5', 'progress' => 80, 'color' => 'amber', 'trend' => '-20%'],
                        ['label' => 'Scrap Rate', 'value' => '3.6%', 'progress' => 36, 'color' => 'emerald', 'trend' => '-0.4%']
                    ]
                ]
            ];
        @endphp
        @foreach($stages as $stage)
        <div class="bg-white rounded-2xl border {{ $stage['bottleneck'] ? 'border-2 border-rose-200' : 'border-slate-200' }} shadow-subtle p-6 hover-lift transition-all relative">
            @if($stage['bottleneck'])
            <div class="absolute top-4 right-4 gradient-danger text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1">
                <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                <span>Bottleneck</span>
            </div>
            @endif
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl {{ $stage['statusColor'] === 'emerald' ? 'gradient-success' : ($stage['statusColor'] === 'amber' ? 'gradient-warning' : 'gradient-danger') }} flex items-center justify-center">
                        <i data-lucide="{{ $stage['icon'] }}" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900">{{ $stage['title'] }}</h3>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="w-2 h-2 rounded-full bg-{{ $stage['statusColor'] }}-500"></div>
                            <span class="text-sm text-slate-600">{{ $stage['status'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                @foreach($stage['metrics'] as $metric)
                <div class="group">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-slate-700">{{ $metric['label'] }}</span>
                        <div class="flex items-center gap-1">
                            <span class="text-sm font-bold text-slate-900">{{ $metric['value'] }}</span>
                            <span class="text-xs font-semibold {{ str_starts_with($metric['trend'], '+') ? 'text-emerald-600' : (str_starts_with($metric['trend'], '-') ? 'text-rose-600' : 'text-slate-600') }}">
                                {{ $metric['trend'] }}
                            </span>
                        </div>
                    </div>
                    <div class="relative w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="absolute inset-0 bg-{{ $metric['color'] }}-500/20 rounded-full"></div>
                        <div class="absolute h-full rounded-full bg-gradient-to-r {{ $metric['color'] === 'blue' ? 'from-blue-500 to-blue-600' : ($metric['color'] === 'emerald' ? 'from-emerald-500 to-teal-500' : ($metric['color'] === 'amber' ? 'from-amber-500 to-orange-500' : 'from-rose-500 to-pink-500')) }}" style="width: {{ $metric['progress'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- 3. Machine Status Grid -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle p-6 hover-lift transition-all">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center">
                    <i data-lucide="cog" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Machine Status Grid</h2>
                    <p class="text-sm text-slate-500">{{ $allMachines->count() }} Machines | Real-time monitoring</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 bg-emerald-50 px-3 py-1.5 rounded-full">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                    <span class="text-sm font-medium text-emerald-700">Running: {{ $runningMachinesCount }}</span>
                </div>
                <div class="flex items-center gap-2 bg-rose-50 px-3 py-1.5 rounded-full">
                    <div class="w-2 h-2 bg-rose-500 rounded-full"></div>
                    <span class="text-sm font-medium text-rose-700">Stopped: {{ $stoppedMachinesCount }}</span>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @forelse($allMachines as $machine)
            <div class="bg-gradient-to-b from-white to-slate-50 rounded-xl border {{ $machine->coil_id ? 'border-l-4 border-emerald-500' : 'border-l-4 border-rose-500' }} p-4 hover:shadow-subtle transition-all hover-lift">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="text-base font-bold text-slate-900">{{ $machine->machine_code ?: 'No Code' }}</div>
                        <div class="text-xs text-slate-500 mt-1">ID: #{{ $machine->id }}</div>
                    </div>
                    <div class="flex items-center gap-1 px-2 py-1 rounded-full {{ $machine->coil_id ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                        <div class="w-1.5 h-1.5 rounded-full {{ $machine->coil_id ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                        <span class="text-xs font-medium">{{ $machine->coil_id ? 'Running' : 'Stopped' }}</span>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100">
                    <div class="text-sm font-semibold text-slate-800 truncate" title="{{ $machine->name }}">{{ $machine->name }}</div>
                    <div class="text-xs {{ $machine->coil_id ? 'text-emerald-600' : 'text-rose-600' }} mt-1">
                        @if($machine->coil_id)
                            <div>Loaded Coil: {{ $machine->coil->coil_no ?? 'N/A' }}</div>
                            <div class="mt-1">Load Weight: {{ number_format((float) ($machine->loaded_weight_kg ?? 0), 0) }} KG</div>
                            @if($machine->load_time)
                                <div class="mt-1">Loaded At: {{ $machine->load_time->format('M d, Y h:i A') }}</div>
                            @endif
                        @else
                            No Coil Loaded
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full rounded-xl border border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-500">
                No machine records found.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Lucide icons after content is loaded
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const countElements = document.querySelectorAll('.js-countup');
        const cards = document.querySelectorAll('.js-kpi-card');

        if (!prefersReducedMotion && 'IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries, obs) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });

            cards.forEach((card) => observer.observe(card));
        } else {
            cards.forEach((card) => card.classList.add('is-visible'));
        }

        if (prefersReducedMotion) {
            return;
        }

        countElements.forEach((element) => {
            const target = parseInt(element.dataset.value || '0', 10);
            if (!Number.isFinite(target) || target < 1) {
                return;
            }

            const duration = 900;
            const start = performance.now();

            const tick = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const currentValue = Math.floor(target * eased);
                element.textContent = currentValue.toLocaleString('en-US');

                if (progress < 1) {
                    requestAnimationFrame(tick);
                } else {
                    element.textContent = target.toLocaleString('en-US');
                }
            };

            requestAnimationFrame(tick);
        });
    });
</script>
@endpush
