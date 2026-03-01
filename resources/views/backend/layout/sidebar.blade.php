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

            <a href="{{ route('admin.production-reports.index') }}" class="w-full flex items-center gap-3 p-3 rounded-lg transition-all hover-lift {{ request()->routeIs('admin.production-reports.*') ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-gray-200' }}">
                <div class="w-8 h-8 rounded-lg {{ request()->routeIs('admin.production-reports.*') ? 'gradient-primary' : 'bg-white/5' }} flex items-center justify-center">
                    <i data-lucide="file-text" class="w-4 h-4 {{ request()->routeIs('admin.production-reports.*') ? 'text-white' : 'text-gray-400' }}"></i>
                </div>
                <span class="font-medium">Production Reports</span>
            </a>
            
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
