@extends('backend.layout.app')

@section('title', 'Manage Profile')

@section('page-title', 'Manage Profile')

@section('breadcrumb')
    <span class="text-slate-400">Profile</span>
    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
    <span class="font-medium text-slate-900">Manage Profile</span>
@endsection

@section('content')
<div class="p-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle p-6 hover-lift transition-all">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-xl gradient-primary flex items-center justify-center">
                    <i data-lucide="user" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Profile Information</h2>
                    <p class="text-sm text-slate-500">Update your account details</p>
                </div>
            </div>

            <form id="profileForm" class="space-y-5">
                @csrf
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ $user->name }}"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="Enter your full name"
                        required
                    >
                    <span class="text-sm text-rose-500 mt-1 hidden" id="name_error"></span>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ $user->email }}"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="Enter your email address"
                        required
                    >
                    <span class="text-sm text-rose-500 mt-1 hidden" id="email_error"></span>
                </div>

                <!-- Role (Read-only) -->
                <div>
                    <label for="role" class="block text-sm font-medium text-slate-700 mb-2">
                        Role
                    </label>
                    <input 
                        type="text" 
                        id="role" 
                        value="{{ ucfirst($user->role ?? 'Admin') }}"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg bg-slate-50 cursor-not-allowed"
                        disabled
                        readonly
                    >
                    <p class="text-xs text-slate-500 mt-2">Your role cannot be changed</p>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-medium transition-all hover-lift shadow-subtle flex items-center gap-2"
                    >
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Update Profile</span>
                    </button>
                    <button 
                        type="button" 
                        onclick="resetForm()"
                        class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition-all"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Account Information Card -->
        <div class="mt-6 bg-slate-50 rounded-xl border border-slate-200 p-5">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-slate-600 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="info" class="w-4 h-4 text-white"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-900 mb-2">Account Information</h3>
                    <div class="text-sm text-slate-600 space-y-1">
                        <p><strong>Account Created:</strong> {{ $user->created_at->format('F d, Y') }}</p>
                        <p><strong>Last Updated:</strong> {{ $user->updated_at->format('F d, Y') }}</p>
                    </div>
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
});

// Handle form submission via AJAX
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Clear previous errors
    clearErrors();
    
    const submitBtn = document.getElementById('submitBtn');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> <span>Updating...</span>';
    lucide.createIcons();
    
    // Get form data
    const formData = new FormData(this);
    
    // Send AJAX request
    fetch('{{ route("admin.profile.update-profile") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            toastr.success(data.message);
        } else {
            // Show validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(key => {
                    const errorElement = document.getElementById(key + '_error');
                    const inputElement = document.getElementById(key);
                    
                    if (errorElement) {
                        errorElement.textContent = data.errors[key][0];
                        errorElement.classList.remove('hidden');
                    }
                    
                    if (inputElement) {
                        inputElement.classList.add('border-rose-500');
                    }
                });
            } else {
                toastr.error(data.message || 'An error occurred');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('An unexpected error occurred');
    })
    .finally(() => {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        lucide.createIcons();
    });
});

// Clear all errors
function clearErrors() {
    document.querySelectorAll('[id$="_error"]').forEach(element => {
        element.textContent = '';
        element.classList.add('hidden');
    });
    
    document.querySelectorAll('input').forEach(input => {
        input.classList.remove('border-rose-500');
    });
}

// Reset form
function resetForm() {
    window.location.reload();
}
</script>
@endpush
