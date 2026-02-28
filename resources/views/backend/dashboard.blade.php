@extends('backend.layout.app')

@section('title', 'Manufacturing Traceability System')

@section('page-title', 'Production Command Dashboard')

@section('breadcrumb')
    <span class="font-medium text-slate-900">Dashboard</span>
@endsection

@section('content')
<div class="p-6 space-y-6">
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
                    ['label' => 'Total Production Today', 'value' => '18,750', 'detail' => '3,125 Units | 6,850 Kg in SF1', 'trend' => '+4.2%', 'color' => 'blue', 'icon' => 'package'],
                    ['label' => 'Active Machines', 'value' => '12', 'detail' => '80% Operational | 3 Down', 'trend' => '+1', 'color' => 'emerald', 'icon' => 'cog'],
                    ['label' => 'Stopped Machines', 'value' => '3', 'detail' => 'M-07, M-09, M-12', 'trend' => '-2', 'color' => 'rose', 'icon' => 'alert-circle'],
                    ['label' => 'Manpower Working', 'value' => '84', 'detail' => '91% Attendance | 8 Absent', 'trend' => '-8', 'color' => 'indigo', 'icon' => 'users'],
                    ['label' => 'Scrap + Rejection', 'value' => '1,240', 'detail' => '6.6% Rate | Above Target', 'trend' => '+12%', 'color' => 'amber', 'icon' => 'alert-triangle'],
                    ['label' => 'Plant Efficiency', 'value' => '86.4%', 'detail' => 'Target: 90% | -3.6% Variance', 'trend' => '-0.3%', 'color' => 'teal', 'icon' => 'trending-up'],
                ];
            @endphp
            @foreach($stats as $stat)
            <div class="bg-gradient-to-br from-white to-slate-50 rounded-xl border border-slate-200 p-5 hover:shadow-elevated transition-all hover-lift">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 rounded-lg bg-{{ $stat['color'] }}-50 flex items-center justify-center">
                                <i data-lucide="{{ $stat['icon'] }}" class="w-4 h-4 text-{{ $stat['color'] }}-600"></i>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-slate-600">{{ $stat['label'] }}</p>
                        <div class="flex items-baseline gap-2 mt-2">
                            <p class="text-2xl font-bold text-slate-900">{{ $stat['value'] }}</p>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">{{ $stat['detail'] }}</p>
                    </div>
                    <div class="text-sm font-semibold {{ str_starts_with($stat['trend'], '+') ? 'text-emerald-600' : (str_starts_with($stat['trend'], '-') ? 'text-rose-600' : 'text-slate-600') }}">
                        {{ $stat['trend'] }}
                    </div>
                </div>
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
                    <p class="text-sm text-slate-500">15 Machines | Real-time monitoring</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 bg-emerald-50 px-3 py-1.5 rounded-full">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                    <span class="text-sm font-medium text-emerald-700">Running: 12</span>
                </div>
                <div class="flex items-center gap-2 bg-rose-50 px-3 py-1.5 rounded-full">
                    <div class="w-2 h-2 bg-rose-500 rounded-full"></div>
                    <span class="text-sm font-medium text-rose-700">Stopped: 3</span>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @php
                $machines = [
                    ['id' => 'M-01', 'stage' => 'SF1', 'status' => 'running', 'coil' => 'C-2456', 'weight' => '125 Kg', 'time' => '7.2 hrs'],
                    ['id' => 'M-02', 'stage' => 'SF1', 'status' => 'running', 'coil' => 'C-2457', 'weight' => '87 Kg', 'time' => '6.8 hrs'],
                    ['id' => 'M-03', 'stage' => 'SF1', 'status' => 'running', 'coil' => 'C-2458', 'weight' => '210 Kg', 'time' => '5.5 hrs'],
                    ['id' => 'M-04', 'stage' => 'SF1', 'status' => 'running', 'coil' => 'C-2459', 'weight' => '95 Kg', 'time' => '8.1 hrs'],
                    ['id' => 'M-05', 'stage' => 'SF1', 'status' => 'running', 'coil' => 'C-2460', 'weight' => '0 Kg', 'time' => '0.5 hrs'],
                    ['id' => 'M-06', 'stage' => 'SF2', 'status' => 'running', 'coil' => 'C-2448', 'weight' => '165 Kg', 'time' => '4.2 hrs'],
                    ['id' => 'M-07', 'stage' => 'SF2', 'status' => 'stopped', 'coil' => 'C-2449', 'weight' => '0 Kg', 'time' => '2.5 hrs'],
                    ['id' => 'M-08', 'stage' => 'SF2', 'status' => 'running', 'coil' => 'C-2450', 'weight' => '72 Kg', 'time' => '6.3 hrs'],
                    ['id' => 'M-09', 'stage' => 'SF2', 'status' => 'stopped', 'coil' => 'C-2451', 'weight' => '0 Kg', 'time' => '4.2 hrs'],
                    ['id' => 'M-10', 'stage' => 'SF2', 'status' => 'running', 'coil' => 'C-2452', 'weight' => '188 Kg', 'time' => '5.9 hrs'],
                    ['id' => 'M-11', 'stage' => 'SF3', 'status' => 'running', 'coil' => 'C-2442', 'weight' => '110 Kg', 'time' => '7.8 hrs'],
                    ['id' => 'M-12', 'stage' => 'SF3', 'status' => 'stopped', 'coil' => 'C-2443', 'weight' => '0 Kg', 'time' => '1.8 hrs'],
                    ['id' => 'M-13', 'stage' => 'SF3', 'status' => 'running', 'coil' => 'C-2444', 'weight' => '95 Kg', 'time' => '6.5 hrs'],
                    ['id' => 'M-14', 'stage' => 'SF3', 'status' => 'running', 'coil' => 'C-2445', 'weight' => '205 Kg', 'time' => '4.9 hrs'],
                    ['id' => 'M-15', 'stage' => 'SF3', 'status' => 'running', 'coil' => 'C-2446', 'weight' => '0 Kg', 'time' => '0.2 hrs']
                ];
            @endphp
            @foreach($machines as $machine)
            <div class="bg-gradient-to-b from-white to-slate-50 rounded-xl border {{ $machine['status'] === 'running' ? 'border-l-4 border-emerald-500' : 'border-l-4 border-rose-500' }} p-4 hover:shadow-subtle transition-all hover-lift">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="text-lg font-bold text-slate-900">{{ $machine['id'] }}</div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs px-2 py-1 rounded-full {{ $machine['stage'] === 'SF1' ? 'bg-blue-100 text-blue-700' : ($machine['stage'] === 'SF2' ? 'bg-amber-100 text-amber-700' : 'bg-teal-100 text-teal-700') }} font-medium">{{ $machine['stage'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 px-2 py-1 rounded-full {{ $machine['status'] === 'running' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                        <div class="w-1.5 h-1.5 rounded-full {{ $machine['status'] === 'running' ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                        <span class="text-xs font-medium">{{ ucfirst($machine['status']) }}</span>
                    </div>
                </div>
                <div class="space-y-3 pt-3 border-t border-slate-100">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-500">Coil</span>
                        <span class="text-sm font-medium text-slate-900">{{ $machine['coil'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-500">Remaining</span>
                        <span class="text-sm font-semibold {{ $machine['weight'] === '0 Kg' ? 'text-amber-600' : 'text-slate-900' }}">{{ $machine['weight'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-500">Runtime</span>
                        <span class="text-xs font-medium text-slate-700">{{ $machine['time'] }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Lucide icons after content is loaded
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
