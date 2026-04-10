@extends('backend.layout.app')

@section('title', 'Create User')

@section('page-title', 'Create New User')

@section('breadcrumb')
    <span class="text-slate-600">Users</span>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Create</span>
@endsection

@section('content')
<div class="p-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle">
            <!-- Header -->
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center">
                        <i data-lucide="user-plus" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Add New User</h2>
                        <p class="text-sm text-slate-500">Fill in the details below</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        required
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('name') border-rose-500 @enderror"
                        placeholder="Enter full name"
                    >
                    @error('name')
                        <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="email"
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        required
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('email') border-rose-500 @enderror"
                        placeholder="user@example.com"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                <!-- Role & Status in one row -->
                <div class="flex gap-4">
                    <!-- Role -->
                    <div class="w-1/2">
                        <label for="role" class="block text-sm font-semibold text-slate-700 mb-2">
                            Role <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            id="role" 
                            name="role" 
                            required
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('role') border-rose-500 @enderror"
                        >
                            <option value="">Select Role</option>
                            <option value="Admin" {{ old('role') === 'Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="SF001" {{ old('role') === 'SF001' ? 'selected' : '' }}>SF1</option>
                            <option value="SF002" {{ old('role') === 'SF002' ? 'selected' : '' }}>SF2</option>
                            <option value="SF003" {{ old('role') === 'SF003' ? 'selected' : '' }}>SF3</option>
                            <option value="Stock" {{ old('role') === 'Stock' ? 'selected' : '' }}>Stock</option>
                            <option value="PPC" {{ old('role') === 'PPC' ? 'selected' : '' }}>PPC</option>
                        </select>
                        @error('role')
                            <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <!-- Status -->
                    <div class="w-1/2">
                        <label for="status" class="block text-sm font-semibold text-slate-700 mb-2">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            id="status" 
                            name="status" 
                            required
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('status') border-rose-500 @enderror"
                        >
                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Password & Confirm Password -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all pr-10 @error('password') border-rose-500 @enderror"
                                placeholder="Enter password"
                            >
                            <button type="button" onclick="togglePasswordVisibility('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i data-lucide="eye" class="w-5 h-5"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-2 text-xs text-slate-500">Minimum 8 characters</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">
                            Confirm Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                required
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all pr-10"
                                placeholder="Confirm password"
                            >
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i data-lucide="eye" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Notify via Email Toggle (temporarily disabled) --}}
                {{--
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                <i data-lucide="mail" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <label for="notify_via_email" class="block text-sm font-semibold text-slate-700">
                                    Send Credentials via Email
                                </label>
                                <p class="text-xs text-slate-500">
                                    Notify the user with their login credentials
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    id="notify_via_email" 
                                    name="notify_via_email" 
                                    value="1"
                                    {{ old('notify_via_email') ? 'checked' : '' }}
                                    class="sr-only peer"
                                >
                                <div class="w-14 h-7 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-blue-500 peer-checked:to-purple-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
                --}}

                <!-- Action Buttons -->
                <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
                    <button 
                        type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 gradient-primary text-white font-semibold rounded-lg hover:shadow-lg transition-all hover:scale-105"
                    >
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Create User</span>
                    </button>
                    <a 
                        href="{{ route('admin.users.index') }}" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 text-slate-700 font-semibold rounded-lg hover:bg-slate-200 transition-all"
                    >
                        <i data-lucide="x" class="w-4 h-4"></i>
                        <span>Cancel</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});

// Disable submit button and show loader on form submit
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action*="users"][method="POST"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                // Optionally add a loader spinner
                let loader = document.createElement('span');
                loader.className = 'ml-2 animate-spin';
                loader.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>';
                submitBtn.appendChild(loader);
            }
        });
    }
});

// Optionally, you can enable the button via JS after validation if needed

// Toggle password visibility
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    
    if (field.type === 'password') {
        field.type = 'text';
        button.innerHTML = '<i data-lucide="eye-off" class="w-5 h-5"></i>';
    } else {
        field.type = 'password';
        button.innerHTML = '<i data-lucide="eye" class="w-5 h-5"></i>';
    }
    lucide.createIcons();
}
</script>
@endpush
