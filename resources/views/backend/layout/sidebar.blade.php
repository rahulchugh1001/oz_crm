@php
    $userRole = auth()->user()->role;
    $isAdmin = $userRole === 'Admin';
    $canViewSf001 = $isAdmin || $userRole === 'SF001';
    $canViewSf002 = $isAdmin || $userRole === 'SF002';
    $canViewSf003 = $isAdmin || $userRole === 'SF003';

    $isSf001ProductionContext = request()->routeIs('admin.production-reports.sf001*')
        || request()->routeIs('admin.production-reports.index')
        || request()->routeIs('admin.production-reports.create')
        || request()->routeIs('admin.production-reports.show')
        || request()->routeIs('admin.production-reports.edit');
@endphp

<!-- Sidebar -->
<div class="w-64 bg-gradient-to-b from-slate-900 to-slate-800 text-gray-200 h-screen fixed left-0 top-0 flex flex-col z-40 shadow-elevated">
    <div class="p-6 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div>
                <img src="{{ asset('admin/images/ozone-logo-white.png') }}" alt="Logo" style="padding: 0px; height: 50px; object-fit: contain; margin-top: 2px;">
            </div>
        </div>
    </div>
    
    <nav class="flex-1 p-4 overflow-y-auto">
        <div class="space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center gap-3 p-3 rounded-lg transition-all hover-lift {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-gray-200' }}">
                <div class="w-8 h-8 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'gradient-primary' : 'bg-white/5' }} flex items-center justify-center">
                    <i data-lucide="home" class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400' }}"></i>
                </div>
                <span class="font-medium">Dashboard</span>
            </a>

            @if($isAdmin)
            <a href="{{ route('admin.items.index') }}" class="w-full flex items-center gap-3 p-3 rounded-lg transition-all hover-lift {{ request()->routeIs('admin.items.*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-gray-200' }}">
                <div class="w-8 h-8 rounded-lg {{ request()->routeIs('admin.items.*') ? 'gradient-primary' : 'bg-white/5' }} flex items-center justify-center">
                    <i data-lucide="box" class="w-4 h-4 {{ request()->routeIs('admin.items.*') ? 'text-white' : 'text-gray-400' }}"></i>
                </div>
                <span class="font-medium">Items</span>
            </a>

            <a href="{{ route('admin.machines.index') }}" class="w-full flex items-center gap-3 p-3 rounded-lg transition-all hover-lift {{ request()->routeIs('admin.machines.*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-gray-200' }}">
                <div class="w-8 h-8 rounded-lg {{ request()->routeIs('admin.machines.*') ? 'gradient-primary' : 'bg-white/5' }} flex items-center justify-center">
                    <i data-lucide="cog" class="w-4 h-4 {{ request()->routeIs('admin.machines.*') ? 'text-white' : 'text-gray-400' }}"></i>
                </div>
                <span class="font-medium">Machines</span>
            </a>
            @endif

            @if($canViewSf001)
            <div class="mt-2">
                <button onclick="toggleSF001Dropdown()" class="w-full flex items-center justify-between p-3 rounded-lg transition-all hover-lift {{ $isSf001ProductionContext ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-gray-200' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg {{ $isSf001ProductionContext ? 'gradient-primary' : 'bg-white/5' }} flex items-center justify-center">
                            <i data-lucide="layers" class="w-4 h-4 {{ $isSf001ProductionContext ? 'text-white' : 'text-gray-400' }}"></i>
                        </div>
                        <span class="font-medium">SF001</span>
                    </div>
                    <i data-lucide="chevron-right" id="sf001-chevron" class="w-4 h-4 text-gray-400 transition-transform"></i>
                </button>

                <div class="ml-10 mt-1 space-y-1 border-l border-white/10 pl-3 {{ $isSf001ProductionContext ? '' : 'hidden' }}" id="sf001-dropdown">
                    <a href="{{ route('admin.production-reports.sf001.coil-stock') }}" class="w-full flex items-center gap-2 p-2 rounded-lg transition-all hover:bg-white/10 text-gray-200 {{ request()->routeIs('admin.production-reports.sf001.coil-stock') ? 'bg-white/10 text-white' : '' }}">
                        <i data-lucide="chevrons-right" class="w-3 h-3"></i>
                        <span class="text-sm">Coil Stock</span>
                    </a>
                    <a href="{{ route('admin.production-reports.sf001') }}" class="w-full flex items-center gap-2 p-2 rounded-lg transition-all hover:bg-white/10 text-gray-200 {{ $isSf001ProductionContext && !request()->routeIs('admin.production-reports.sf001.coil-stock') && !request()->routeIs('admin.production-reports.sf001.stock*') ? 'bg-white/10 text-white' : '' }}">
                        <i data-lucide="chevrons-right" class="w-3 h-3"></i>
                        <span class="text-sm">Production</span>
                    </a>
                    <a href="{{ route('admin.production-reports.sf001.stock') }}" class="w-full flex items-center gap-2 p-2 rounded-lg transition-all hover:bg-white/10 text-gray-200 {{ request()->routeIs('admin.production-reports.sf001.stock*') ? 'bg-white/10 text-white' : '' }}">
                        <i data-lucide="chevrons-right" class="w-3 h-3"></i>
                        <span class="text-sm">Stock</span>
                    </a>
                </div>
            </div>
            @endif

            @if($canViewSf002)
            <div class="mt-2">
                <button onclick="toggleSF002Dropdown()" class="w-full flex items-center justify-between p-3 rounded-lg transition-all hover-lift {{ request()->routeIs('admin.production-reports.sf002*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-gray-200' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg {{ request()->routeIs('admin.production-reports.sf002*') ? 'gradient-primary' : 'bg-white/5' }} flex items-center justify-center">
                            <i data-lucide="layers" class="w-4 h-4 {{ request()->routeIs('admin.production-reports.sf002*') ? 'text-white' : 'text-gray-400' }}"></i>
                        </div>
                        <span class="font-medium">SF002</span>
                    </div>
                    <i data-lucide="chevron-right" id="sf002-chevron" class="w-4 h-4 text-gray-400 transition-transform"></i>
                </button>

                <div class="ml-10 mt-1 space-y-1 border-l border-white/10 pl-3 {{ request()->routeIs('admin.production-reports.sf002*') ? '' : 'hidden' }}" id="sf002-dropdown">
                    <a href="{{ route('admin.production-reports.sf002') }}" class="w-full flex items-center gap-2 p-2 rounded-lg transition-all hover:bg-white/10 text-gray-200 {{ request()->routeIs('admin.production-reports.sf002') ? 'bg-white/10 text-white' : '' }}">
                        <i data-lucide="chevrons-right" class="w-3 h-3"></i>
                        <span class="text-sm">Process <small>(Upcoming)</small></span>
                    </a>
                </div>
            </div>
            @endif

            @if($canViewSf003)
            <div class="mt-2">
                <button onclick="toggleSF003Dropdown()" class="w-full flex items-center justify-between p-3 rounded-lg transition-all hover-lift {{ request()->routeIs('admin.production-reports.sf003*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-gray-200' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg {{ request()->routeIs('admin.production-reports.sf003*') ? 'gradient-primary' : 'bg-white/5' }} flex items-center justify-center">
                            <i data-lucide="layers" class="w-4 h-4 {{ request()->routeIs('admin.production-reports.sf003*') ? 'text-white' : 'text-gray-400' }}"></i>
                        </div>
                        <span class="font-medium">SF003</span>
                    </div>
                    <i data-lucide="chevron-right" id="sf003-chevron" class="w-4 h-4 text-gray-400 transition-transform"></i>
                </button>

                <div class="ml-10 mt-1 space-y-1 border-l border-white/10 pl-3 {{ request()->routeIs('admin.production-reports.sf003*') ? '' : 'hidden' }}" id="sf003-dropdown">
                    <a href="{{ route('admin.production-reports.sf003') }}" class="w-full flex items-center gap-2 p-2 rounded-lg transition-all hover:bg-white/10 text-gray-200 {{ request()->routeIs('admin.production-reports.sf003') ? 'bg-white/10 text-white' : '' }}">
                        <i data-lucide="chevrons-right" class="w-3 h-3"></i>
                        <span class="text-sm">Process <small>(Upcoming)</small></span>
                    </a>
                </div>
            </div>
            @endif

            @if($isAdmin)
            <a href="{{ route('admin.users.index') }}" class="w-full flex items-center gap-3 p-3 rounded-lg transition-all hover-lift {{ request()->routeIs('admin.users.*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-gray-200' }}">
                <div class="w-8 h-8 rounded-lg {{ request()->routeIs('admin.users.*') ? 'gradient-primary' : 'bg-white/5' }} flex items-center justify-center">
                    <i data-lucide="users" class="w-4 h-4 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-gray-400' }}"></i>
                </div>
                <span class="font-medium">Users</span>
            </a>
            @endif

            <!-- Manage Profile Dropdown -->
            <div class="mt-2">
                <button onclick="toggleProfileDropdown()" class="w-full flex items-center justify-between p-3 rounded-lg transition-all hover-lift {{ request()->routeIs('admin.profile.*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-gray-200' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg {{ request()->routeIs('admin.profile.*') ? 'gradient-primary' : 'bg-white/5' }} flex items-center justify-center">
                            <i data-lucide="user-circle" class="w-4 h-4 {{ request()->routeIs('admin.profile.*') ? 'text-white' : 'text-gray-400' }}"></i>
                        </div>
                        <span class="font-medium">Manage Profile</span>
                    </div>
                    <i data-lucide="chevron-right" id="profile-chevron" class="w-4 h-4 text-gray-400 transition-transform"></i>
                </button>
                
                <div class="ml-10 mt-1 space-y-1 border-l border-white/10 pl-3 {{ request()->routeIs('admin.profile.*') ? '' : 'hidden' }}" id="profile-dropdown">
                    <a href="{{ route('admin.profile.manage-password') }}" class="w-full flex items-center gap-2 p-2 rounded-lg transition-all hover:bg-white/10 text-gray-200 {{ request()->routeIs('admin.profile.manage-password') ? 'bg-white/10 text-white' : '' }}">
                        <i data-lucide="key" class="w-3 h-3"></i>
                        <span class="text-sm">Manage Password</span>
                    </a>
                    <a href="{{ route('admin.profile.manage-profile') }}" class="w-full flex items-center gap-2 p-2 rounded-lg transition-all hover:bg-white/10 text-gray-200 {{ request()->routeIs('admin.profile.manage-profile') ? 'bg-white/10 text-white' : '' }}">
                        <i data-lucide="user" class="w-3 h-3"></i>
                        <span class="text-sm">Manage Profile</span>
                    </a>
                </div>
            </div>
            
            {{-- Hidden Menu Section
            <div class="mt-6">
                <div class="text-xs font-medium text-gray-400 uppercase tracking-wider px-3 py-2">SF1 Operations</div>
                <div class="space-y-1">
                    <a href="#" class="w-full flex items-center gap-3 p-3 rounded-lg transition-all hover-lift hover:bg-white/10 text-gray-200">
                        <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center">
                            <i data-lucide="database" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <span class="font-medium">Coil Stock</span>
                    </a>
                    <a href="#" class="w-full flex items-center gap-3 p-3 rounded-lg transition-all hover-lift hover:bg-white/10 text-gray-200">
                        <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center">
                            <i data-lucide="cog" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <span class="font-medium">Operations</span>
                    </a>
                    <a href="#" class="w-full flex items-center gap-3 p-3 rounded-lg transition-all hover-lift hover:bg-white/10 text-gray-200">
                        <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <span class="font-medium">Completed</span>
                    </a>
                </div>
            </div>
            
            <div class="mt-6">
                <button onclick="toggleMastersDropdown()" class="w-full flex items-center justify-between p-3 rounded-lg transition-all hover:bg-white/10">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center">
                            <i data-lucide="settings" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <span class="font-medium">Masters</span>
                    </div>
                    <i data-lucide="chevron-right" id="masters-chevron" class="w-4 h-4 text-gray-400"></i>
                </button>
                
                <div class="ml-10 mt-1 space-y-1 border-l border-white/10 pl-3 hidden" id="masters-dropdown">
                    <a href="#" class="w-full flex items-center gap-2 p-2 rounded-lg transition-all hover:bg-white/10 text-gray-200">
                        <i data-lucide="user" class="w-3 h-3"></i>
                        <span class="text-sm">Supervisor</span>
                    </a>
                    <a href="#" class="w-full flex items-center gap-2 p-2 rounded-lg transition-all hover:bg-white/10 text-gray-200">
                        <i data-lucide="clock" class="w-3 h-3"></i>
                        <span class="text-sm">Shift</span>
                    </a>
                    <a href="#" class="w-full flex items-center gap-2 p-2 rounded-lg transition-all hover:bg-white/10 text-gray-200">
                        <i data-lucide="users" class="w-3 h-3"></i>
                        <span class="text-sm">Operator</span>
                    </a>
                    <a href="{{ route('admin.machines.index') }}" class="w-full flex items-center gap-2 p-2 rounded-lg transition-all {{ request()->routeIs('admin.machines.*') ? 'bg-white/10 text-white' : 'hover:bg-white/10 text-gray-200' }}">
                        <i data-lucide="cog" class="w-3 h-3"></i>
                        <span class="text-sm">Machine No</span>
                    </a>
                    <a href="#" class="w-full flex items-center gap-2 p-2 rounded-lg transition-all hover:bg-white/10 text-gray-200">
                        <i data-lucide="ruler" class="w-3 h-3"></i>
                        <span class="text-sm">Coil Size</span>
                    </a>
                    <a href="#" class="w-full flex items-center gap-2 p-2 rounded-lg transition-all hover:bg-white/10 text-gray-200">
                        <i data-lucide="hash" class="w-3 h-3"></i>
                        <span class="text-sm">Coil Number</span>
                    </a>
                    <a href="#" class="w-full flex items-center gap-2 p-2 rounded-lg transition-all hover:bg-white/10 text-gray-200">
                        <i data-lucide="factory" class="w-3 h-3"></i>
                        <span class="text-sm">Coil Make</span>
                    </a>
                    <a href="#" class="w-full flex items-center gap-2 p-2 rounded-lg transition-all hover:bg-white/10 text-gray-200">
                        <i data-lucide="package" class="w-3 h-3"></i>
                        <span class="text-sm">Bin Detail</span>
                    </a>
                </div>
            </div>
            --}}
        </div>
    </nav>
    
    <div class="p-4 border-t border-white/10">
        <div class="flex items-center gap-3 p-3 rounded-lg bg-white/5">
            <div class="w-10 h-10 rounded-full gradient-primary flex items-center justify-center">
                <i data-lucide="user" class="w-5 h-5 text-white"></i>
            </div>
            <div class="flex-1">
                <p class="font-medium text-white">{{ auth()->user()->name ?? 'John Doe' }}</p>
                <p class="text-sm text-gray-400 capitalize">{{ auth()->user()->role ?? 'Admin' }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-1 hover:bg-white/10 rounded-lg">
                    <i data-lucide="log-out" class="w-4 h-4 text-gray-400"></i>
                </button>
            </form>
        </div>
    </div>
</div>
