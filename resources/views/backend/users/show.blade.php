@extends('backend.layout.app')

@section('title', 'View User')

@section('page-title', 'User Details')

@section('breadcrumb')
    <span class="text-slate-600">Users</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">View</span>
@endsection

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- User Profile Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center">
                            <i data-lucide="user" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">User Profile</h2>
                            <p class="text-sm text-slate-500">Complete user information</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-700 font-semibold rounded-lg hover:bg-amber-100 transition-all">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                            <span>Edit</span>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 font-semibold rounded-lg hover:bg-slate-200 transition-all">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            <span>Back</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="flex items-start gap-6">
                    <!-- Avatar -->
                    <div class="w-24 h-24 rounded-2xl gradient-primary flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-3xl">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    </div>

                    <!-- User Details -->
                    <div class="flex-1 space-y-4">
                        <div>
                            <h3 class="text-2xl font-bold text-slate-900">{{ $user->name }}</h3>
                            <p class="text-slate-600">{{ $user->email }}</p>
                        </div>

                        <div class="flex items-center gap-4">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium 
                                @if($user->role === 'Admin') bg-purple-100 text-purple-700
                                @else bg-slate-100 text-slate-700
                                @endif">
                                <i data-lucide="shield" class="w-4 h-4 mr-2"></i>
                                {{ $user->role === 'SF001' ? 'SF1' : ($user->role === 'SF002' ? 'SF2' : ($user->role === 'SF003' ? 'SF3' : ($user->role ?? 'User'))) }}
                            </span>
                            
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $user->status ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                <i data-lucide="{{ $user->status ? 'check-circle' : 'x-circle' }}" class="w-4 h-4 mr-2"></i>
                                {{ $user->status ? 'Active' : 'Inactive' }}
                            </span>
                            
                            @if($user->id === auth()->id())
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-blue-100 text-blue-700">
                                <i data-lucide="star" class="w-4 h-4 mr-2"></i>
                                Current User
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-900">Account Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">User ID</label>
                        <p class="text-slate-900">#{{ $user->id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                        <p class="text-slate-900">{{ $user->email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name</label>
                        <p class="text-slate-900">{{ $user->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Role</label>
                        <p class="text-slate-900">{{ $user->role === 'SF001' ? 'SF1' : ($user->role === 'SF002' ? 'SF2' : ($user->role === 'SF003' ? 'SF3' : ($user->role ?? 'User'))) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                        <p class="text-slate-900">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $user->status ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                {{ $user->status ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Account Created</label>
                        <p class="text-slate-900 flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                            {{ $user->created_at->format('F d, Y') }}
                        </p>
                        <p class="text-xs text-slate-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Last Updated</label>
                        <p class="text-slate-900 flex items-center gap-2">
                            <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                            {{ $user->updated_at->format('F d, Y') }}
                        </p>
                        <p class="text-xs text-slate-500 mt-1">{{ $user->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
            <div class="p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Quick Actions</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 font-medium rounded-lg hover:bg-blue-100 transition-all">
                        <i data-lucide="edit" class="w-4 h-4"></i>
                        <span>Edit User</span>
                    </a>
                    
                    @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline js-swal-delete-form" data-item-name="{{ $user->name }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-700 font-medium rounded-lg hover:bg-rose-100 transition-all">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            <span>Delete User</span>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();

    // SweetAlert for delete confirmation
    const deleteForm = document.querySelector('.js-swal-delete-form');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
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
                    deleteForm.submit();
                }
            });
        });
    }
});
</script>
@endpush
