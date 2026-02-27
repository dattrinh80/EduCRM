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

        /* Toast */
        @keyframes toastIn { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }
        @keyframes toastOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(100%); } }
        .toast-enter { animation: toastIn 0.35s cubic-bezier(0.21, 1.02, 0.73, 1) forwards; }
        .toast-leave { animation: toastOut 0.3s ease-in forwards; }

        /* Confirm modal */
        @keyframes modalIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        .modal-enter { animation: modalIn 0.25s cubic-bezier(0.21, 1.02, 0.73, 1) forwards; }
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
                @canany(['leads.view'])
                <p class="px-3 pt-4 pb-1 text-[10px] uppercase tracking-widest text-slate-500 font-semibold">CRM</p>
                @can('leads.view')
                <a href="{{ url('/admin/leads') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('admin/leads*') ? 'text-primary-400 bg-dark-200 sidebar-link active' : 'text-slate-300 hover:text-white rounded-lg hover:bg-dark-200' }} transition-colors">
                    <i data-lucide="contact" class="w-5 h-5"></i>
                    Leads
                </a>
                @endcan
                @endcanany

                <!-- Management -->
                @canany(['centers.view'])
                <p class="px-3 pt-4 pb-1 text-[10px] uppercase tracking-widest text-slate-500 font-semibold">Quản lý</p>
                @can('centers.view')
                <a href="{{ route('admin.centers.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('admin/centers*') ? 'text-primary-400 bg-dark-200 sidebar-link active' : 'text-slate-300 hover:text-white rounded-lg hover:bg-dark-200' }} transition-colors">
                    <i data-lucide="building-2" class="w-5 h-5"></i>
                    Cơ sở
                </a>
                @endcan
                @endcanany

                <!-- System -->
                @canany(['users.view', 'roles.view'])
                <p class="px-3 pt-4 pb-1 text-[10px] uppercase tracking-widest text-slate-500 font-semibold">System</p>
                @can('users.view')
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('admin/users*') ? 'text-primary-400 bg-dark-200 sidebar-link active' : 'text-slate-300 hover:text-white rounded-lg hover:bg-dark-200' }} transition-colors">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    Users
                </a>
                @endcan
                @can('roles.view')
                <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('admin/roles*') ? 'text-primary-400 bg-dark-200 sidebar-link active' : 'text-slate-300 hover:text-white rounded-lg hover:bg-dark-200' }} transition-colors">
                    <i data-lucide="shield" class="w-5 h-5"></i>
                    Roles
                </a>
                @endcan
                @can('roles.view')
                <a href="{{ route('admin.permissions.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('admin/permissions*') ? 'text-primary-400 bg-dark-200 sidebar-link active' : 'text-slate-300 hover:text-white rounded-lg hover:bg-dark-200' }} transition-colors">
                    <i data-lucide="key" class="w-5 h-5"></i>
                    Permissions
                </a>
                @endcan
                @endcanany
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

    <!-- ═══ Confirm Dialog ═══ -->
    <div id="confirmModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="window.__confirmCancel()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 modal-enter">
            <div class="flex flex-col items-center text-center">
                <div id="confirmIcon" class="w-14 h-14 rounded-full bg-red-100 text-red-500 flex items-center justify-center mb-4">
                    <i data-lucide="alert-triangle" class="w-7 h-7"></i>
                </div>
                <h3 id="confirmTitle" class="text-lg font-semibold text-slate-800 mb-1">Xác nhận</h3>
                <p id="confirmMessage" class="text-sm text-slate-500 mb-6">Bạn có chắc chắn muốn thực hiện?</p>
            </div>
            <div class="flex gap-3">
                <button onclick="window.__confirmCancel()" class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Huỷ bỏ</button>
                <button id="confirmBtn" onclick="window.__confirmOk()" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-red-500 rounded-xl hover:bg-red-600 transition shadow-lg shadow-red-500/30">Xác nhận</button>
            </div>
        </div>
    </div>

    <!-- ═══ Toast Container ═══ -->
    <div id="toastContainer" class="fixed top-4 right-4 z-[110] flex flex-col gap-3 pointer-events-none" style="max-width: 400px;"></div>

    @if (session('success'))
    <template id="flashSuccess"><span>{{ session('success') }}</span></template>
    @endif
    @if (session('error'))
    <template id="flashError"><span>{{ session('error') }}</span></template>
    @endif

    <script>
        lucide.createIcons();

        /* ─── Toast System ─── */
        function showToast(message, type = 'success', duration = 4000) {
            const container = document.getElementById('toastContainer');
            const colors = {
                success: { bg: 'bg-emerald-50 border-emerald-200', text: 'text-emerald-700', icon: 'check-circle', iconBg: 'bg-emerald-100 text-emerald-600' },
                error:   { bg: 'bg-red-50 border-red-200', text: 'text-red-700', icon: 'alert-circle', iconBg: 'bg-red-100 text-red-600' },
                warning: { bg: 'bg-amber-50 border-amber-200', text: 'text-amber-700', icon: 'alert-triangle', iconBg: 'bg-amber-100 text-amber-600' },
                info:    { bg: 'bg-blue-50 border-blue-200', text: 'text-blue-700', icon: 'info', iconBg: 'bg-blue-100 text-blue-600' }
            };
            const c = colors[type] || colors.success;
            const el = document.createElement('div');
            el.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl border shadow-lg ${c.bg} toast-enter`;
            el.innerHTML = `
                <div class="w-8 h-8 rounded-lg ${c.iconBg} flex items-center justify-center flex-shrink-0"><i data-lucide="${c.icon}" class="w-4 h-4"></i></div>
                <span class="text-sm font-medium ${c.text} flex-1">${message}</span>
                <button class="p-1 ${c.text} opacity-50 hover:opacity-100 transition flex-shrink-0" onclick="dismissToast(this.parentElement)"><i data-lucide="x" class="w-4 h-4"></i></button>
            `;
            container.appendChild(el);
            lucide.createIcons({ nodes: [el] });
            setTimeout(() => dismissToast(el), duration);
        }
        function dismissToast(el) {
            if (!el || el.dataset.dismissed) return;
            el.dataset.dismissed = '1';
            el.classList.remove('toast-enter');
            el.classList.add('toast-leave');
            setTimeout(() => el.remove(), 300);
        }

        /* ─── Confirm Dialog ─── */
        let __confirmResolve = null;
        window.__confirmOk = () => { document.getElementById('confirmModal').classList.add('hidden'); if (__confirmResolve) __confirmResolve(true); };
        window.__confirmCancel = () => { document.getElementById('confirmModal').classList.add('hidden'); if (__confirmResolve) __confirmResolve(false); };

        function showConfirm({ title = 'Xác nhận', message = 'Bạn có chắc chắn?', confirmText = 'Xác nhận', type = 'danger' } = {}) {
            return new Promise((resolve) => {
                __confirmResolve = resolve;
                document.getElementById('confirmTitle').textContent = title;
                document.getElementById('confirmMessage').textContent = message;
                document.getElementById('confirmBtn').textContent = confirmText;
                const btnColors = { danger: 'bg-red-500 hover:bg-red-600 shadow-red-500/30', warning: 'bg-amber-500 hover:bg-amber-600 shadow-amber-500/30', info: 'bg-primary-500 hover:bg-primary-600 shadow-primary-500/30' };
                const iconColors = { danger: 'bg-red-100 text-red-500', warning: 'bg-amber-100 text-amber-500', info: 'bg-primary-100 text-primary-500' };
                const btn = document.getElementById('confirmBtn');
                btn.className = `flex-1 px-4 py-2.5 text-sm font-medium text-white rounded-xl transition shadow-lg ${btnColors[type] || btnColors.danger}`;
                document.getElementById('confirmIcon').className = `w-14 h-14 rounded-full flex items-center justify-center mb-4 ${iconColors[type] || iconColors.danger}`;
                document.getElementById('confirmModal').classList.remove('hidden');
            });
        }

        /* Helper: attach to delete forms */
        function confirmDelete(form, itemName = '') {
            showConfirm({
                title: 'Xác nhận xoá',
                message: itemName ? `Bạn có chắc chắn muốn xoá "${itemName}"? Hành động này không thể hoàn tác.` : 'Bạn có chắc chắn muốn xoá? Hành động này không thể hoàn tác.',
                confirmText: 'Xoá',
                type: 'danger'
            }).then(ok => { if (ok) form.submit(); });
            return false;
        }

        /* Auto-show flash toasts */
        document.addEventListener('DOMContentLoaded', () => {
            const s = document.getElementById('flashSuccess');
            const e = document.getElementById('flashError');
            if (s) showToast(s.content.textContent.trim(), 'success');
            if (e) showToast(e.content.textContent.trim(), 'error');
        });
    </script>
    @stack('scripts')
</body>
</html>
