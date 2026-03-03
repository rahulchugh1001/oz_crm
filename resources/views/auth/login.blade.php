<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Manufacturing Traceability System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
        }
        
        .gradient-overlay {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.95) 0%, rgba(29, 78, 216, 0.9) 100%);
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .animate-float-delayed {
            animation: float 6s ease-in-out 2s infinite;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
            z-index: 10;
        }
        
        .input-with-icon {
            padding-left: 50px !important;
        }
        
        .shadow-brutal {
            box-shadow: 0 20px 60px rgba(30, 58, 138, 0.3);
        }
    </style>
</head>
<body class="bg-slate-50 h-screen overflow-hidden">
    <div class="h-screen flex">
        <!-- Left Side - Branding & Info -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800">
                <!-- Animated Circles -->
                <div class="absolute top-20 left-20 w-72 h-72 bg-blue-500/20 rounded-full blur-3xl animate-float"></div>
                <div class="absolute bottom-20 right-20 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl animate-float-delayed"></div>
            </div>
            
            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-center p-12 xl:p-20 text-white w-full">
                <!-- Logo -->
                <div class="mb-8">
                    <div class="mb-6">
                        <img src="{{ asset('front/images/ozone-logo-white.png') }}" alt="Logo" class="h-16 object-contain mb-3">
                        <p class="text-blue-200 text-sm">Traceability System</p>
                    </div>
                </div>
                
                <!-- Features -->
                <div class="space-y-6">
                    <h2 class="text-3xl font-bold leading-tight">
                        Welcome to Your<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-cyan-200">
                            Production Command Center
                        </span>
                    </h2>
                    
                    <p class="text-blue-100 leading-relaxed">
                        Monitor, manage, and optimize your manufacturing operations in real-time.
                    </p>
                    
                    <div class="space-y-3 pt-4">
                        <div class="flex items-center gap-3 p-3 bg-white/5 backdrop-blur-sm rounded-lg border border-white/10">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="activity" class="w-5 h-5 text-emerald-300"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white text-sm">Real-time Monitoring</h3>
                                <p class="text-xs text-blue-200">Track production metrics instantly</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3 p-3 bg-white/5 backdrop-blur-sm rounded-lg border border-white/10">
                            <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="shield-check" class="w-5 h-5 text-blue-300"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white text-sm">Complete Traceability</h3>
                                <p class="text-xs text-blue-200">Full visibility from coil to finish</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3 p-3 bg-white/5 backdrop-blur-sm rounded-lg border border-white/10">
                            <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="bar-chart-3" class="w-5 h-5 text-purple-300"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white text-sm">Advanced Analytics</h3>
                                <p class="text-xs text-blue-200">Data-driven decision making</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden mb-6 text-center">
                    <img src="{{ asset('front/images/ozone-logo-white.png') }}" alt="Logo" class="h-12 object-contain mx-auto mb-2 brightness-0">
                    <p class="text-sm text-slate-600">Traceability System</p>
                </div>
                
                <!-- Login Card -->
                <div class="bg-white rounded-2xl shadow-brutal p-6 md:p-8">
                    <!-- Header -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-slate-900 mb-1">Welcome Back</h2>
                        <p class="text-sm text-slate-600">Please sign in to access your dashboard</p>
                    </div>
                    
                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
                            <p class="text-sm text-emerald-800">{{ session('status') }}</p>
                        </div>
                    @endif
                    
                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        
                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                                Email Address
                            </label>
                            <div class="relative">
                                <i data-lucide="mail" class="input-icon w-5 h-5"></i>
                                <input 
                                    id="email" 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    autofocus 
                                    autocomplete="username"
                                    class="input-with-icon w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-slate-900 placeholder-slate-400"
                                    placeholder="john@example.com"
                                >
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <!-- Password Input -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <i data-lucide="lock" class="input-icon w-5 h-5"></i>
                                <input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    required 
                                    autocomplete="current-password"
                                    class="input-with-icon w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-slate-900 placeholder-slate-400"
                                    placeholder="••••••••"
                                >
                                <button 
                                    type="button" 
                                    onclick="togglePassword()"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
                                >
                                    <i data-lucide="eye" id="eye-icon" class="w-5 h-5"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-rose-600 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input 
                                    id="remember_me" 
                                    type="checkbox" 
                                    name="remember"
                                    class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer"
                                >
                                <span class="text-sm text-slate-600 group-hover:text-slate-900 transition-colors">
                                    Remember me
                                </span>
                            </label>
                            
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                                    Forgot password?
                                </a>
                            @endif
                        </div>
                        
                        <!-- Submit Button -->
                        <button 
                            type="submit"
                            class="w-full gradient-primary text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2 group"
                        >
                            <span>Sign In</span>
                            <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>
                </div>
                
                {{-- <!-- Footer Links - Hidden -->
                <div class="mt-6 text-center text-sm text-slate-600">
                    Don't have an account? 
                    <a href="#" class="font-medium text-blue-600 hover:text-blue-700 transition-colors">
                        Contact Administrator
                    </a>
                </div>
                --}}
            </div>
        </div>
    </div>
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            
            lucide.createIcons();
        }
    </script>
</body>
</html>
