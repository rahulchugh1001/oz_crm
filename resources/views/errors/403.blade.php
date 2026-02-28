<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Forbidden</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-warning {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
        }
        
        .shake {
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            25% {
                transform: translateX(-10px);
            }
            75% {
                transform: translateX(10px);
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12 text-center">
            <!-- Animated Icon -->
            <div class="mb-8 flex justify-center">
                <div class="relative">
                    <div class="w-32 h-32 gradient-warning rounded-full flex items-center justify-center shake">
                        <i data-lucide="shield-alert" class="w-16 h-16 text-white"></i>
                    </div>
                    <div class="absolute -top-2 -right-2 w-12 h-12 bg-rose-500 rounded-full flex items-center justify-center">
                        <i data-lucide="lock" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- Error Code -->
            <div class="mb-4">
                <h1 class="text-8xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-amber-600 to-orange-600">
                    403
                </h1>
            </div>
            
            <!-- Error Message -->
            <h2 class="text-3xl font-bold text-slate-900 mb-4">
                Access Forbidden
            </h2>
            <p class="text-slate-600 text-lg mb-8">
                You don't have permission to access this resource. Please contact your administrator if you believe this is an error.
            </p>
            
            <!-- Permission Info -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-8">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <i data-lucide="info" class="w-5 h-5 text-amber-600"></i>
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-amber-900 mb-1">Why am I seeing this?</h3>
                        <ul class="text-sm text-amber-800 space-y-1">
                            <li>• You may not have the required permissions</li>
                            <li>• Your account may need additional verification</li>
                            <li>• This resource might be restricted to certain roles</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Helpful Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 gradient-warning text-white font-semibold rounded-lg hover:shadow-lg transition-all hover:scale-105">
                    <i data-lucide="home" class="w-5 h-5"></i>
                    <span>Go to Homepage</span>
                </a>
                <button onclick="history.back()" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all hover:scale-105">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    <span>Go Back</span>
                </button>
            </div>
            
            <!-- Additional Help -->
            <div class="border-t border-slate-200 pt-6">
                <p class="text-sm text-slate-500">
                    Need access? 
                    <a href="{{ route('admin.dashboard') }}" class="text-amber-600 hover:text-amber-700 font-medium underline">
                        Request Permission
                    </a>
                </p>
            </div>
        </div>
        
        <!-- User Info (if authenticated) -->
        @auth
        <div class="mt-6 bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-slate-600">
                <i data-lucide="user" class="w-4 h-4 inline"></i>
                Logged in as <span class="font-semibold text-slate-900">{{ auth()->user()->name }}</span>
            </p>
        </div>
        @endauth
    </div>
    
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
