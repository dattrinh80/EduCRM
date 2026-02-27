<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Edu CRM') - System</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
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
        .sidebar-link.active { background: linear-gradient(90deg, rgba(54,153,255,0.15) 0%, transparent 100%); border-left: 3px solid #3699FF; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -8px rgba(0,0,0,0.15); }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
        .slide-down { animation: slideDown 0.3s ease-out; }
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar for Sidebar */
        .sidebar-menu::-webkit-scrollbar { width: 5px; }
        .sidebar-menu::-webkit-scrollbar-track { background: transparent; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: transparent; border-radius: 20px; }
        .sidebar-menu:hover::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); }
        .sidebar-menu::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
    </style>
</head>
<body class="bg-slate-50 min-h-screen" x-data="{ sidebarOpen: false }">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-dark-100 h-screen fixed left-0 top-0 text-white z-40 transition-transform duration-300 transform flex flex-col"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            <!-- Logo -->
            <div class="h-16 flex items-center px-6 border-b border-dark-300 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-500 flex items-center justify-center">
                        <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                    </div>
                    <span class="font-semibold text-lg">Edu CRM</span>
                </div>
            </div>
            
            <!-- User Info -->
            <div class="p-4 border-b border-dark-300 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary-500/20 flex items-center justify-center text-primary-400 font-semibold">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-xs text-slate-400">{{ Auth::user()->email ?? '' }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="p-4 space-y-1 flex-1 overflow-y-auto sidebar-menu">
                <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-slate-300 hover:text-white rounded-lg hover:bg-dark-200 transition-colors">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    Dashboard
                </a>

                <!-- CRM -->
                <p class="px-3 pt-4 pb-1 text-[10px] uppercase tracking-widest text-slate-500 font-semibold">CRM</p>
                <a href="{{ url('/admin/leads') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('admin/leads*') ? 'text-primary-400 bg-dark-200 sidebar-link active' : 'text-slate-300 hover:text-white rounded-lg hover:bg-dark-200' }} transition-colors">
                    <i data-lucide="contact" class="w-5 h-5"></i>
                    Leads
                </a>

                <!-- System -->
                <p class="px-3 pt-4 pb-1 text-[10px] uppercase tracking-widest text-slate-500 font-semibold">System</p>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('admin/users*') ? 'text-primary-400 bg-dark-200 sidebar-link active' : 'text-slate-300 hover:text-white rounded-lg hover:bg-dark-200' }} transition-colors">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    Users
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 min-w-0 transition-all duration-300">
            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 -ml-2 text-slate-500 hover:text-slate-700 focus:outline-none">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <h1 class="text-xl font-semibold text-slate-800 truncate">@yield('title', 'Dashboard')</h1>
                </div>
                
                <div class="flex items-center gap-4">
                    <span class="hidden sm:inline text-sm text-slate-500">{{ now()->format('l, d/m/Y') }}</span>
                    
                     <!-- Separator -->
                     <div class="h-8 w-px bg-slate-200 mx-2"></div>

                    <!-- User Dropdown -->
                    <div class="relative flex items-center gap-3 cursor-pointer" x-data="{ open: false }" @click.away="open = false">
                        <div class="hidden sm:block text-right" @click="open = !open">
                            <p class="text-sm font-medium text-slate-700">{{ Auth::user()->name ?? 'User' }}</p>
                            <p class="text-xs text-slate-500">{{ Auth::user()->email ?? '' }}</p>
                        </div>
                        
                        <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold text-sm border border-indigo-200" @click="open = !open">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        
                         <!-- Dropdown Menu -->
                         <div x-show="open" x-transition x-cloak class="absolute right-0 top-12 w-48 bg-white rounded-lg shadow-lg py-1 border border-slate-100 z-50">
                            <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                                <i data-lucide="user" class="w-4 h-4"></i> Profile
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                    <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <div class="p-4 lg:p-6">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="p-4 lg:p-6 text-center text-sm text-slate-500 border-t border-slate-200 mt-8">
                &copy; {{ date('Y') }} Edu CRM. All rights reserved.
            </footer>
        </main>
        
        <!-- Overlay for mobile/sidebar -->
        <div class="fixed inset-0 bg-black/50 z-30 lg:hidden" x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" x-cloak></div>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
