<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>405 - Method Not Allowed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .gradient-info {
            background: linear-gradient(135deg, #0e7490 0%, #0891b2 100%);
        }

        .animate-bounce-soft {
            animation: bounce-soft 2s ease-in-out infinite;
        }

        @keyframes bounce-soft {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
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
                    <div class="w-32 h-32 gradient-info rounded-full flex items-center justify-center animate-bounce-soft">
                        <i data-lucide="ban" class="w-16 h-16 text-white"></i>
                    </div>
                    <div class="absolute -top-2 -right-2 w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center">
                        <i data-lucide="hand" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>

            <!-- Error Code -->
            <div class="mb-4">
                <h1 class="text-8xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 to-sky-600">
                    405
                </h1>
            </div>

            <!-- Error Message -->
            <h2 class="text-3xl font-bold text-slate-900 mb-4">
                Method Not Allowed
            </h2>
            <p class="text-slate-600 text-lg mb-8">
                The request method used for this URL is not allowed. Please use a valid method and try again.
            </p>

            <!-- Request Info -->
            <div class="bg-cyan-50 border border-cyan-200 rounded-lg p-4 mb-8">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <i data-lucide="info" class="w-5 h-5 text-cyan-600"></i>
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-cyan-900 mb-1">Request Details</h3>
                        <ul class="text-sm text-cyan-800 space-y-1">
                            <li>• Attempted method: {{ request()->method() }}</li>
                            <li>• URL: {{ request()->fullUrl() }}</li>
                            <li>• Check that your form or API call uses the expected HTTP method</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Helpful Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 gradient-info text-white font-semibold rounded-lg hover:shadow-lg transition-all hover:scale-105">
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
                    If you were submitting a form, verify the form action and method.
                </p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
