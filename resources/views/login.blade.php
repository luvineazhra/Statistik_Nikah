<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login Admin • NikahInfo</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              colors: {
                primary: "#00236f",
                "primary-container": "#1e3a8a",
                "on-primary": "#ffffff",
                secondary: "#0058be",
                tertiary: "#0d9488",
                surface: "#f8f9ff",
                "on-surface": "#0b1c30",
                "on-surface-variant": "#444651",
                outline: "#757682",
                "slate-bg": "#f8fafc"
              },
              fontFamily: {
                headline: ["Plus Jakarta Sans", "sans-serif"],
                body: ["Inter", "sans-serif"]
              }
            }
          }
        };
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, .font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 min-h-screen text-slate-800 antialiased flex flex-col justify-between selection:bg-secondary selection:text-white relative overflow-x-hidden">

    <!-- Background Decoration Gradient & Shapes -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -right-20 w-96 h-96 bg-teal-500/15 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 left-1/3 w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:32px_32px] opacity-5"></div>
    </div>

    <!-- Quick Navbar / Brand Header -->
    <header class="relative z-10 w-full px-6 lg:px-12 py-5 flex items-center justify-between">
        <a href="{{ route('public.dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-blue-600/20 border border-blue-400/30 flex items-center justify-center text-teal-400 group-hover:scale-105 transition-transform">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                    <circle class="opacity-90" cx="19" cy="24" r="10" stroke="currentColor" stroke-width="2.6"></circle>
                    <circle class="opacity-80" cx="29" cy="24" r="10" stroke="#38bdf8" stroke-width="2.6"></circle>
                    <circle cx="24" cy="24" fill="#0d9488" r="3.5"></circle>
                </svg>
            </div>
            <div class="flex flex-col">
                <span class="font-heading font-extrabold text-xl text-white tracking-tight">NikahInfo</span>
                <span class="text-[11px] text-slate-400 font-medium tracking-wide">Portal Analitik Pernikahan</span>
            </div>
        </a>

        <a href="{{ route('public.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 px-3.5 py-2 rounded-lg border border-white/10">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Kembali ke Portal Publik</span>
        </a>
    </header>

    <!-- Main Container -->
    <main class="relative z-10 flex-grow flex items-center justify-center p-4 sm:p-6 my-auto">
        <div class="w-full max-w-md">

            <!-- Card Outer Container -->
            <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl shadow-black/40 border border-white/20 p-8 sm:p-10">

                <!-- Header Section -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-50 text-blue-700 border border-blue-100 mb-4 shadow-sm">
                        <span class="material-symbols-outlined text-[30px]">admin_panel_settings</span>
                    </div>
                    <h1 class="font-heading font-extrabold text-2xl text-slate-900 tracking-tight">
                        Login Admin
                    </h1>
                    <p class="text-sm text-slate-500 mt-1.5">
                        Masukkan kredensial Anda untuk mengakses panel administrator NikahInfo.
                    </p>
                </div>

                @if (session('error'))
                    <div class="mb-6 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">error</span>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login.post') }}" method="POST" class="space-y-5" id="adminLoginForm">
                    @csrf
                    
                    <!-- Username / Email Field -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2" for="identifierInput">
                            Username / Email Admin
                        </label>
                        <div class="relative flex items-center">
                            <div class="absolute left-3.5 flex items-center pointer-events-none text-slate-400">
                                <span class="material-symbols-outlined text-[20px]">person</span>
                            </div>
                            <input 
                                class="w-full h-12 pl-11 pr-4 rounded-xl border border-slate-300 bg-white text-slate-900 placeholder:text-slate-400 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all font-medium" 
                                id="identifierInput" 
                                name="identifier" 
                                placeholder="Masukkan username atau email admin" 
                                value="admin"
                                required 
                                type="text"
                                autofocus
                            />
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-slate-700 uppercase tracking-wider" for="passwordInput">
                                Kata Sandi
                            </label>
                        </div>
                        <div class="relative flex items-center">
                            <div class="absolute left-3.5 flex items-center pointer-events-none text-slate-400">
                                <span class="material-symbols-outlined text-[20px]">lock</span>
                            </div>
                            <input 
                                class="w-full h-12 pl-11 pr-11 rounded-xl border border-slate-300 bg-white text-slate-900 placeholder:text-slate-400 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all font-medium" 
                                id="passwordInput" 
                                name="password" 
                                value="password123" 
                                placeholder="••••••••"
                                required 
                                type="password"
                            />
                            <button 
                                aria-label="Toggle password visibility" 
                                class="absolute right-3 p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" 
                                id="passwordToggleBtn" 
                                type="button"
                            >
                                <span class="material-symbols-outlined text-[20px]" id="passwordToggleIcon">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password Options -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 cursor-pointer" id="rememberMeCheckbox" type="checkbox" checked/>
                            <span class="text-xs text-slate-600 font-medium">Ingat Sesi Saya</span>
                        </label>
                        <a class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors" href="#">
                            Lupa kata sandi?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        class="w-full h-12 bg-blue-600 hover:bg-blue-700 text-white font-heading font-bold text-sm rounded-xl flex items-center justify-center gap-2 shadow-lg shadow-blue-600/25 hover:shadow-blue-600/35 transition-all active:scale-[0.99] cursor-pointer" 
                        type="submit"
                    >
                        <span>Masuk Administrator</span>
                        <span class="material-symbols-outlined text-[18px]">login</span>
                    </button>
                </form>

                <!-- Admin Security Badge -->
                <div class="mt-6 pt-5 border-t border-slate-100 flex items-center justify-center gap-2 text-xs text-slate-400">
                    <span class="material-symbols-outlined text-[16px] text-teal-600">shield</span>
                    <span>Akses Terenkripsi & Terlindungi</span>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 w-full py-4 px-6 text-center text-xs text-slate-400">
        © 2026 <strong class="text-slate-300">NikahInfo</strong> • Admin Portal.
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('passwordInput');
            const passwordToggleBtn = document.getElementById('passwordToggleBtn');
            const passwordToggleIcon = document.getElementById('passwordToggleIcon');

            if (passwordToggleBtn && passwordInput && passwordToggleIcon) {
                passwordToggleBtn.addEventListener('click', function() {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    passwordToggleIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
                });
            }
        });
    </script>
</body>
</html>

