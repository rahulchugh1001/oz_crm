@extends('backend.layout.app')

@section('title', 'Manage Password')

@section('page-title', 'Manage Password')

@section('breadcrumb')
    <span class="text-slate-400">Profile</span>
    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
    <span class="font-medium text-slate-900">Manage Password</span>
@endsection

@section('content')
<div class="p-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle p-6 hover-lift transition-all">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-xl gradient-primary flex items-center justify-center">
                    <i data-lucide="key" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Change Password</h2>
                    <p class="text-sm text-slate-500">Update your account password</p>
                </div>
            </div>

            <form id="passwordForm" class="space-y-5">
                @csrf
                
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-slate-700 mb-2">
                        Current Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="current_password" 
                            name="current_password" 
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all pr-10"
                            placeholder="Enter current password"
                            required
                        >
                        <button type="button" onclick="togglePasswordVisibility('current_password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i data-lucide="eye" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <span class="text-sm text-rose-500 mt-1 hidden" id="current_password_error"></span>
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                        New Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all pr-10"
                            placeholder="Enter new password"
                            required
                        >
                        <button type="button" onclick="togglePasswordVisibility('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i data-lucide="eye" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <span class="text-sm text-rose-500 mt-1 hidden" id="password_error"></span>
                    <p class="text-xs text-slate-500 mt-2">Password must be at least 8 characters long</p>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">
                        Confirm New Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all pr-10"
                            placeholder="Confirm new password"
                            required
                        >
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i data-lucide="eye" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <span class="text-sm text-rose-500 mt-1 hidden" id="password_confirmation_error"></span>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-medium transition-all hover-lift shadow-subtle flex items-center gap-2"
                    >
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Update Password</span>
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

        <!-- Password Guidelines Card -->
        <div class="mt-6 bg-blue-50 rounded-xl border border-blue-200 p-5">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="info" class="w-4 h-4 text-white"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-900 mb-2">Password Guidelines</h3>
                    <ul class="text-sm text-slate-600 space-y-1">
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-blue-600 mt-0.5"></i>
                            <span>Minimum 8 characters long</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-blue-600 mt-0.5"></i>
                            <span>Include uppercase and lowercase letters</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-blue-600 mt-0.5"></i>
                            <span>Include at least one number</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-blue-600 mt-0.5"></i>
                            <span>Include at least one special character</span>
                        </li>
                    </ul>
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

// Handle form submission via AJAX
document.getElementById('passwordForm').addEventListener('submit', function(e) {
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
    fetch('{{ route("admin.profile.update-password") }}', {
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
            
            // Reset form
            document.getElementById('passwordForm').reset();
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
    document.getElementById('passwordForm').reset();
    clearErrors();
}
</script>
@endpush
