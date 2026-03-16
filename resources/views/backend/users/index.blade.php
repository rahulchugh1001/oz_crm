@extends('backend.layout.app')

@section('title', 'Users List')

@section('page-title', 'Users Management')

@section('breadcrumb')
    <span class="text-slate-600">Users</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">List</span>
@endsection

@section('content')
<div class="p-4">
    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-2.5">
        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
        <p class="text-xs text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
    <div class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-lg flex items-center gap-2.5">
        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0"></i>
        <p class="text-xs text-rose-800">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Header with Add Button -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
        <div class="p-4 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl gradient-primary flex items-center justify-center">
                        <i data-lucide="users" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">All Users</h2>
                        <p class="text-xs text-slate-500">Manage system users and their access</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.users.login-activity') }}" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 border border-slate-300 font-semibold rounded-lg hover:bg-slate-50 transition-all">
                        <i data-lucide="history" class="w-3.5 h-3.5"></i>
                        <span>Login Activity</span>
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs gradient-primary text-white font-semibold rounded-lg hover:shadow-lg transition-all">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Add New User</span>
                    </a>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mt-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.users.index', ['mode' => 'active']) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $mode === 'active' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                        Active
                    </a>
                    <a href="{{ route('admin.users.index', ['mode' => 'deleted']) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $mode === 'deleted' ? 'bg-rose-600 text-white border-rose-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                        Deleted
                    </a>
                    <a href="{{ route('admin.users.index', ['mode' => 'all']) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $mode === 'all' ? 'bg-slate-700 text-white border-slate-700' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                        All
                    </a>
                </div>

                <form action="{{ route('admin.users.index') }}" method="GET" class="w-full lg:w-auto">
                    <input type="hidden" name="mode" value="{{ $mode }}">
                    <div class="flex items-center gap-2">
                        <div class="relative w-full lg:w-80">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input
                                type="text"
                                name="search"
                                value="{{ $search ?? '' }}"
                                placeholder="Search name, email, role..."
                                class="w-full pl-10 pr-3 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all">
                            Search
                        </button>
                        @if(!empty($search))
                        <a href="{{ route('admin.users.index', ['mode' => $mode]) }}" class="px-3 py-1.5 text-xs font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">
                            Reset
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Joined Date</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-900 font-medium">#{{ $user->id }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full gradient-primary flex items-center justify-center">
                                    <span class="text-white font-semibold text-xs">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-900">{{ $user->name }}</p>
                                    @if($user->id === auth()->id())
                                    <span class="text-xs text-blue-600 font-medium">(You)</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium 
                                @if($user->role === 'Admin') bg-purple-100 text-purple-700
                                @else bg-slate-100 text-slate-700
                                @endif">
                                {{ $user->role === 'SF001' ? 'SF1' : ($user->role === 'SF002' ? 'SF2' : ($user->role === 'SF003' ? 'SF3' : ($user->role ?? 'User'))) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($user->id !== auth()->id())
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        class="sr-only peer status-toggle" 
                                        data-user-id="{{ $user->getRouteKey() }}"
                                        {{ $user->status ? 'checked' : '' }}
                                    >
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                    {{-- <span class="ms-3 text-sm font-medium text-slate-700">{{ $user->status ? 'Active' : 'Inactive' }}</span> --}}
                                </label>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                    Active
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="View">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit">
                                    <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    @if(!$user->is_deleted)
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline js-swal-delete-form" data-item-name="{{ $user->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Delete">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <i data-lucide="inbox" class="w-10 h-10 text-slate-300"></i>
                                <p class="text-xs text-slate-500">No users found</p>
                                <a href="{{ route('admin.users.create') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                    Create your first user
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="px-4 py-3 border-t border-slate-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();

    // SweetAlert for delete confirmation
    document.querySelectorAll('.js-swal-delete-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const itemName = this.getAttribute('data-item-name');
            
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete user "${itemName}". This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Status toggle functionality
    document.querySelectorAll('.status-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const userId = this.getAttribute('data-user-id');
            const isChecked = this.checked;
            const toggleElement = this;
            const labelText = this.parentElement.querySelector('span');

            // Confirm before toggling
            Swal.fire({
                title: 'Are you sure?',
                text: isChecked ? 'You are about to enable this user.' : 'You are about to disable this user.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: isChecked ? '#2563eb' : '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: isChecked ? 'Yes, enable' : 'Yes, disable',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (!result.isConfirmed) {
                    // Revert toggle state when cancelled
                    toggleElement.checked = !isChecked;
                    return;
                }

                // Prevent double clicks while request is in-flight
                toggleElement.disabled = true;
            
                // Send AJAX request
                fetch(`/admin/users/${userId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update label text
                        if (labelText) {
                            labelText.textContent = data.status ? 'Active' : 'Inactive';
                        }
                        // Show success message
                        toastr.success(data.message);
                    } else {
                        // Revert toggle state on error
                        toggleElement.checked = !isChecked;
                        toastr.error(data.message || 'An error occurred');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Revert toggle state on error
                    toggleElement.checked = !isChecked;
                    toastr.error('An unexpected error occurred');
                })
                .finally(() => {
                    toggleElement.disabled = false;
                });
            });
        });
    });
});
</script>
@endpush
