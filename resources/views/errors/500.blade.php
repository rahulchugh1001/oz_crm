<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-danger {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        }
        
        .animate-pulse-slow {
            animation: pulse-slow 2s ease-in-out infinite;
        }
        
        @keyframes pulse-slow {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
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
                    <div class="w-32 h-32 gradient-danger rounded-full flex items-center justify-center animate-pulse-slow">
                        <i data-lucide="server-crash" class="w-16 h-16 text-white"></i>
                    </div>
                    <div class="absolute -top-2 -right-2 w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- Error Code -->
            <div class="mb-4">
                <h1 class="text-8xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-rose-600 to-red-600">
                    500
                </h1>
            </div>
            
            <!-- Error Message -->
            <h2 class="text-3xl font-bold text-slate-900 mb-4">
                Internal Server Error
            </h2>
            <p class="text-slate-600 text-lg mb-8">
                Something went wrong on our end. Our team has been notified and we're working to fix the issue.
            </p>
            
            <!-- Error Details (only in development) -->
            @if(config('app.debug') && isset($exception))
            <div class="bg-rose-50 border border-rose-200 rounded-lg p-4 mb-8 text-left">
                <h3 class="font-semibold text-rose-900 mb-2 flex items-center gap-2">
                    <i data-lucide="bug" class="w-4 h-4"></i>
                    Error Details (Debug Mode)
                </h3>
                <p class="text-sm text-rose-700 font-mono break-all">
                    {{ $exception->getMessage() }}
                </p>
            </div>
            @endif
            
            <!-- Helpful Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 gradient-danger text-white font-semibold rounded-lg hover:shadow-lg transition-all hover:scale-105">
                    <i data-lucide="home" class="w-5 h-5"></i>
                    <span>Go to Homepage</span>
                </a>
                <button onclick="location.reload()" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all hover:scale-105">
                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                    <span>Try Again</span>
                </button>
            </div>
            
            <!-- Additional Help -->
            <div class="border-t border-slate-200 pt-6">
                <p class="text-sm text-slate-500">
                    If this problem persists, please 
                    <a href="mailto:support@example.com" class="text-rose-600 hover:text-rose-700 font-medium underline">
                        contact our support team
                    </a>
                </p>
            </div>
        </div>
        
        <!-- System Status -->
        <div class="mt-6 text-center">
            <p class="text-sm text-slate-600">
                <i data-lucide="clock" class="w-4 h-4 inline"></i>
                Error occurred at {{ now()->format('F d, Y H:i:s') }}
            </p>
        </div>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
