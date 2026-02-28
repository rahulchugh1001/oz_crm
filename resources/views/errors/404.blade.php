<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
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
                    <div class="w-32 h-32 gradient-primary rounded-full flex items-center justify-center animate-float">
                        <i data-lucide="search-x" class="w-16 h-16 text-white"></i>
                    </div>
                    <div class="absolute -top-2 -right-2 w-12 h-12 bg-rose-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-xl">!</span>
                    </div>
                </div>
            </div>
            
            <!-- Error Code -->
            <div class="mb-4">
                <h1 class="text-8xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">
                    404
                </h1>
            </div>
            
            <!-- Error Message -->
            <h2 class="text-3xl font-bold text-slate-900 mb-4">
                Page Not Found
            </h2>
            <p class="text-slate-600 text-lg mb-8">
                Oops! The page you're looking for doesn't exist. It might have been moved or deleted.
            </p>
            
            <!-- Helpful Links -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 gradient-primary text-white font-semibold rounded-lg hover:shadow-lg transition-all hover:scale-105">
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
                    Need help? 
                    <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-700 font-medium underline">
                        Contact Support
                    </a>
                </p>
            </div>
        </div>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
