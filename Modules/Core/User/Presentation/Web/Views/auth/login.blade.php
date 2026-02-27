<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Edu CRM</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { 50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa', 500: '#3699FF', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a' },
                        dark: { 100: '#1E1E2D', 200: '#151521', 300: '#2D2D3A' }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.5s ease-out; }
    </style>
</head>
<body class="bg-dark-200 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md fade-in">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-primary-500 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary-500/30">
                <i data-lucide="graduation-cap" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">Edu CRM</h1>
            <p class="text-slate-400 mt-2 text-sm">Sign in to your account</p>
        </div>

        <!-- Login Card -->
        <div class="bg-dark-100 rounded-2xl border border-dark-300 shadow-2xl p-8">
            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl mb-6 flex items-center gap-3 text-sm">
                    <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div class="space-y-1">
                    <label for="email" class="text-sm font-medium text-slate-300 block">Email</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-500"></i>
                        <input type="email" name="email" id="email" required autofocus
                               class="w-full pl-10 pr-4 py-3 bg-dark-200 border border-dark-300 rounded-xl text-white placeholder-slate-500 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition"
                               value="{{ old('email') }}" placeholder="admin@educrm.vn">
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="password" class="text-sm font-medium text-slate-300 block">Password</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-500"></i>
                        <input type="password" name="password" id="password" required
                               class="w-full pl-10 pr-4 py-3 bg-dark-200 border border-dark-300 rounded-xl text-white placeholder-slate-500 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition"
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-dark-300 bg-dark-200 text-primary-500 focus:ring-primary-500">
                        <span class="text-sm text-slate-400">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition font-medium shadow-lg shadow-primary-500/30 flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-5 h-5"></i>
                    Sign In
                </button>
            </form>
        </div>

        <p class="text-center text-slate-500 text-xs mt-6">&copy; {{ date('Y') }} Edu CRM. All rights reserved.</p>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
