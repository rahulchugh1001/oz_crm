<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Manufacturing Traceability System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap">
    <style>
        * {
            font-family: 'Outfit', sans-serif;
        }
        
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: #020617; /* Deepest Dark */
        }

        /* Animated Mesh Gradient Background (Dark) */
        .mesh-gradient {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-color: #020617;
            background-image: 
                radial-gradient(at 0% 0%, rgba(30, 58, 138, 0.5) 0, transparent 50%), 
                radial-gradient(at 100% 0%, rgba(88, 28, 135, 0.4) 0, transparent 50%), 
                radial-gradient(at 50% 50%, rgba(15, 23, 42, 0.9) 0, transparent 80%),
                radial-gradient(at 0% 100%, rgba(30, 58, 138, 0.4) 0, transparent 50%), 
                radial-gradient(at 100% 100%, rgba(88, 28, 135, 0.4) 0, transparent 50%);
            background-size: 200% 200%;
            animation: meshMove 15s ease infinite alternate;
        }

        @keyframes meshMove {
            0% { background-position: 0% 0%; transform: scale(1); }
            50% { background-position: 100% 100%; transform: scale(1.1); }
            100% { background-position: 0% 100%; transform: scale(1); }
        }

        .glow-orb {
            position: fixed;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            animation: orbFloat 20s infinite alternate ease-in-out;
        }

        @keyframes orbFloat {
            0% { transform: translate(-20%, -20%) scale(1); }
            50% { transform: translate(20%, 20%) scale(1.2); }
            100% { transform: translate(-20%, 20%) scale(1); }
        }

        .tech-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-image: 
                linear-gradient(rgba(59, 130, 246, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.05) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
            opacity: 0.5;
        }

        @keyframes gridMove {
            0% { transform: translateY(0); }
            100% { transform: translateY(50px); }
        }

        .scan-line {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.2), transparent);
            z-index: -1;
            animation: scanMove 8s linear infinite;
        }

        @keyframes scanMove {
            0% { top: -10%; }
            100% { top: 110%; }
        }

        .bubble {
            position: fixed;
            bottom: -100px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            pointer-events: none;
            z-index: -1;
            animation: bubbleRise 15s infinite ease-in;
            backdrop-filter: blur(2px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        @keyframes bubbleRise {
            0% { transform: translateY(0) translateX(0) scale(1); opacity: 0; }
            10% { opacity: 0.5; }
            50% { transform: translateY(-50vh) translateX(50px) scale(1.2); }
            100% { transform: translateY(-120vh) translateX(-20px) scale(0.8); opacity: 0; }
        }

        .glass-container {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.7);
        }

        .branding-side {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e1b4b 100%);
            position: relative;
            overflow: hidden;
        }

        .form-side {
            background: #ffffff; /* Clean Light Side */
        }

        .typewriter {
            display: inline-block;
            overflow: hidden;
            border-right: .15em solid #3b82f6;
            white-space: nowrap;
            margin: 0;
            animation: 
                typing 4s steps(40, end),
                blink-caret .75s step-end infinite;
        }

        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }

        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: #3b82f6; }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }

        .input-premium {
            background: #f8fafc !important;
            border: 2px solid #e2e8f0 !important;
            color: #0f172a !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-premium:focus {
            background: #ffffff !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.1) !important;
            transform: translateY(-1px);
        }

        .btn-premium {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(30, 58, 138, 0.4);
            filter: brightness(1.1);
        }

        .btn-premium::after {
            content: "";
            position: absolute;
            top: -50%;
            left: -100%;
            width: 100%;
            height: 200%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transform: rotate(35deg);
            animation: shine 4s infinite;
        }

        @keyframes shine {
            0% { left: -100%; }
            20% { left: 100%; }
            100% { left: 100%; }
        }

        .floating-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.4;
            animation: float 12s infinite alternate ease-in-out;
        }

        .branding-side::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                120deg,
                transparent,
                rgba(255, 255, 255, 0.05),
                transparent
            );
            transition: all 0.5s;
            animation: sweep 8s infinite;
        }

        @keyframes sweep {
            0% { left: -100%; }
            20% { left: 100%; }
            100% { left: 100%; }
        }

        .particle {
            position: absolute;
            background: white;
            border-radius: 50%;
            opacity: 0.3;
            pointer-events: none;
            animation: particleFloat infinite linear;
        }

        @keyframes particleFloat {
            from { transform: translateY(0) scale(1); opacity: 0.3; }
            to { transform: translateY(-100vh) scale(0); opacity: 0; }
        }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(60px, 60px) scale(1.3); }
        }

        .animate-fade-in-left {
            animation: fadeInLeft 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .icon-pulse {
            animation: iconPulse 2s infinite ease-in-out;
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .heartbeat {
            animation: heartbeat 1.5s ease-in-out infinite;
        }

        @keyframes heartbeat {
            0% { transform: scale(1); }
            14% { transform: scale(1.1); }
            28% { transform: scale(1); }
            42% { transform: scale(1.1); }
            70% { transform: scale(1); }
        }

        .marquee-bar {
            background: rgba(15, 23, 42, 0.9);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            color: #60a5fa;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="mesh-gradient"></div>
    <div class="tech-grid"></div>
    <div class="scan-line"></div>

    <!-- Floating Bubbles -->
    <div class="bubble w-20 h-20 left-[10%]" style="animation-duration: 12s; animation-delay: 0s"></div>
    <div class="bubble w-32 h-32 left-[30%]" style="animation-duration: 18s; animation-delay: -2s"></div>
    <div class="bubble w-16 h-16 left-[55%]" style="animation-duration: 15s; animation-delay: -5s"></div>
    <div class="bubble w-24 h-24 left-[80%]" style="animation-duration: 20s; animation-delay: -8s"></div>
    <div class="bubble w-12 h-12 left-[40%]" style="animation-duration: 10s; animation-delay: -3s"></div>
    <div class="bubble w-28 h-28 left-[70%]" style="animation-duration: 22s; animation-delay: -12s"></div>

    <div class="glow-orb" style="top: 10%; left: 10%; animation-duration: 25s"></div>
    <div class="glow-orb" style="top: 10%; left: 10%; animation-duration: 25s"></div>
    <div class="glow-orb" style="bottom: 10%; right: 10%; animation-duration: 30s; animation-delay: -5s"></div>
    
    <!-- Dark Background Blobs -->
    <div class="floating-blob w-96 h-96 bg-blue-900/40 top-[-10%] left-[-10%]" style="animation-duration: 18s"></div>
    <div class="floating-blob w-80 h-80 bg-indigo-900/40 bottom-[-5%] right-[-5%]" style="animation-duration: 15s; animation-delay: -3s"></div>

    <div class="w-full max-w-[1100px] flex flex-col lg:flex-row items-stretch justify-center gap-0 glass-container rounded-[2.5rem] overflow-hidden animate-fade-in-up">
        
        <!-- Left Side - Dark Branding -->
        <div class="hidden lg:flex lg:w-5/12 branding-side p-12 flex-col justify-between relative overflow-hidden">
            <!-- Internal Floating Bubbles -->
            <div class="bubble w-12 h-12 left-[10%] opacity-20" style="animation-duration: 8s; animation-delay: 0s"></div>
            <div class="bubble w-20 h-20 left-[40%] opacity-10" style="animation-duration: 12s; animation-delay: -3s"></div>
            <div class="bubble w-16 h-16 left-[70%] opacity-15" style="animation-duration: 10s; animation-delay: -5s"></div>
            <div class="bubble w-10 h-10 left-[85%] opacity-10" style="animation-duration: 15s; animation-delay: -2s"></div>

            <!-- Floating Particles -->
            <div class="particle w-1 h-1 top-[20%] left-[10%]" style="animation-duration: 10s"></div>
            <div class="particle w-2 h-2 top-[50%] left-[30%]" style="animation-duration: 15s"></div>
            <div class="particle w-1 h-1 top-[80%] left-[20%]" style="animation-duration: 12s"></div>
            <div class="particle w-1.5 h-1.5 top-[40%] left-[80%]" style="animation-duration: 20s"></div>
            <div class="particle w-1 h-1 top-[10%] left-[70%]" style="animation-duration: 8s"></div>

            <div class="relative z-10">
                <img src="{{ asset('front/images/ozone-logo-white.png') }}" alt="Logo" class="h-14 object-contain mb-10 animate-fade-in-left">
                
                <h2 class="text-4xl font-extrabold text-white leading-tight mb-8 animate-fade-in-left delay-100">
                    Smart<br>
                    <span class="text-blue-400">Traceability</span><br>
                    Platform
                </h2>
                
                <div class="space-y-8 mt-12">
                    <div class="flex items-center gap-5 group animate-fade-in-left delay-200">
                        <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center group-hover:bg-blue-500/20 transition-all duration-300 border border-white/10">
                            <i data-lucide="shield-check" class="w-7 h-7 text-blue-300 icon-pulse"></i>
                        </div>
                        <div>
                            <p class="text-white font-bold text-lg">Secure Auth</p>
                            <p class="text-blue-200/60 text-sm">Military-grade protection</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-5 group animate-fade-in-left delay-300">
                        <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center group-hover:bg-indigo-500/20 transition-all duration-300 border border-white/10">
                            <i data-lucide="activity" class="w-7 h-7 text-indigo-300 heartbeat"></i>
                        </div>
                        <div>
                            <p class="text-white font-bold text-lg">Live Sync</p>
                            <p class="text-blue-200/60 text-sm">Zero latency updates</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 mt-12 animate-fade-in-left delay-400">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-xs font-mono text-emerald-400 uppercase tracking-widest">System Status: Online</span>
                </div>
                <p class="text-blue-300/30 text-[10px] font-mono uppercase tracking-[0.3em]">© 2026 Ozone Manufacturing Traceability</p>
            </div>
            
            <!-- Abstract background shape -->
            <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Right Side - Light Form -->
        <div class="w-full lg:w-7/12 form-side p-8 md:p-16 flex flex-col justify-center">
            <div class="max-w-md mx-auto w-full">
                <!-- Mobile Logo -->
                <div class="lg:hidden mb-12 text-center">
                    <img src="{{ asset('front/images/ozone-logo.png') }}" alt="Logo" class="h-12 object-contain mx-auto mb-4">
                </div>

                <div class="mb-10 animate-fade-in-up delay-100">
                    <h1 class="text-4xl font-black text-slate-900 mb-3 tracking-tight">Login.</h1>
                    <div class="h-1.5 w-12 bg-blue-600 rounded-full mb-6"></div>
                    <p class="text-slate-500 font-medium">
                        <span class="typewriter">Enter your credentials to proceed</span>
                    </p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-8 p-4 bg-emerald-50 border-l-4 border-emerald-500 flex items-center gap-3 animate-fade-in-up">
                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
                        <p class="text-sm text-emerald-800 font-medium">{{ session('status') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Email -->
                    <div class="animate-fade-in-up delay-200">
                        <label for="email" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Work Email</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none z-10">
                                <i data-lucide="mail" class="w-5 h-5 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                            </div>
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus 
                                class="input-premium w-full pl-14 pr-5 py-4 rounded-2xl focus:outline-none placeholder-slate-300 font-medium relative z-0"
                                placeholder="name@company.com"
                            >
                        </div>
                        @error('email')
                            <p class="mt-2 text-xs text-rose-500 font-bold ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="animate-fade-in-up delay-300">
                        <div class="flex justify-between mb-2 ml-1">
                            <label for="password" class="block text-xs font-bold text-slate-400 uppercase tracking-widest">Password</label>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none z-10">
                                <i data-lucide="lock" class="w-5 h-5 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                            </div>
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                required 
                                class="input-premium w-full pl-14 pr-14 py-4 rounded-2xl focus:outline-none placeholder-slate-300 font-medium relative z-0"
                                placeholder="••••••••••••"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-5 flex items-center text-slate-300 hover:text-slate-600 transition-colors z-10"
                            >
                                <i data-lucide="eye" id="eye-icon" class="w-5 h-5"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-xs text-rose-500 font-bold ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember & Action -->
                    <div class="flex items-center justify-between animate-fade-in-up delay-400 px-1">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative">
                                <input id="remember_me" type="checkbox" name="remember" class="peer sr-only">
                                <div class="w-6 h-6 bg-slate-100 border-2 border-slate-200 rounded-lg peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all"></div>
                                <i data-lucide="check" class="absolute inset-0 w-6 h-6 text-white scale-0 peer-checked:scale-75 transition-transform"></i>
                            </div>
                            <span class="text-sm text-slate-500 group-hover:text-slate-900 transition-colors font-bold">Keep me signed in</span>
                        </label>
                        <span class="text-sm font-bold text-slate-300 cursor-not-allowed">Recovery?</span>
                    </div>

                    <!-- Button -->
                    <div class="pt-4 animate-fade-in-up delay-500">
                        <button 
                            id="login-button"
                            type="submit"
                            class="btn-premium w-full text-white font-extrabold text-lg py-4 px-6 rounded-2xl flex items-center justify-center gap-3 shadow-xl"
                        >
                            <span id="button-text">Sign In to Dashboard</span>
                            <i data-lucide="arrow-right" id="button-icon" class="w-6 h-6 group-hover:translate-x-2 transition-transform"></i>
                            <i data-lucide="loader-2" id="button-loader" class="w-6 h-6 animate-spin hidden"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Status Bar -->
    <div class="fixed bottom-0 left-0 w-full marquee-bar py-3 px-4 z-50 overflow-hidden">
        <div class="whitespace-nowrap inline-block animate-marquee font-mono text-[11px] uppercase tracking-[0.4em] font-black opacity-80">
            • SYSTEM SECURE • ENCRYPTION ACTIVE • NODE-04 ONLINE • TRACEABILITY MODULE LOADED • VERSION 3.0.1 • AUTHENTICATION SERVER READY • SYSTEM SECURE • ENCRYPTION ACTIVE • NODE-04 ONLINE • TRACEABILITY MODULE LOADED • VERSION 3.0.1 • AUTHENTICATION SERVER READY
        </div>
    </div>

    <style>
        .animate-marquee {
            animation: marquee 50s linear infinite;
        }
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
    </style>

    <script>
        lucide.createIcons();
        
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

        document.querySelector('form').addEventListener('submit', function() {
            const button = document.getElementById('login-button');
            const text = document.getElementById('button-text');
            const icon = document.getElementById('button-icon');
            const loader = document.getElementById('button-loader');
            button.disabled = true;
            text.textContent = 'Verifying...';
            icon.classList.add('hidden');
            loader.classList.remove('hidden');
        });

        // Parallax for Background Blobs
        document.addEventListener('mousemove', (e) => {
            const x = e.clientX / window.innerWidth;
            const y = e.clientY / window.innerHeight;
            document.querySelectorAll('.floating-blob, .branding-side').forEach((el, i) => {
                const speed = (i + 1) * 20;
                if (el.classList.contains('branding-side')) return; // skip side for now
                el.style.transform = `translate(${(x - 0.5) * speed}px, ${(y - 0.5) * speed}px)`;
            });
        });
    </script>
</body>
</html>
