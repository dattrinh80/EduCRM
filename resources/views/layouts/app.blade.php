<!DOCTYPE html>
<html lang="vi">
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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    },
                    colors: {
                        primary: { 
                            50: 'hsl(215 100% 97%)', 
                            100: 'hsl(215 100% 92%)', 
                            200: 'hsl(215 100% 85%)', 
                            300: 'hsl(215 100% 75%)', 
                            400: 'hsl(215 100% 60%)', 
                            500: 'hsl(215 94% 48%)',  // Premium Blue
                            600: 'hsl(215 100% 40%)', 
                            700: 'hsl(215 100% 30%)', 
                            800: 'hsl(215 100% 20%)', 
                            900: 'hsl(215 100% 12%)' 
                        },
                        dark: { 
                            100: 'hsl(222 47% 11%)', // Richer Dark
                            200: 'hsl(222 47% 15%)', 
                            300: 'hsl(222 47% 25%)' 
                        }
                    },
                    boxShadow: {
                        'premium': '0 20px 40px -15px rgba(0, 114, 255, 0.2)',
                        'glass': '0 8px 32px 0 rgba(15, 23, 42, 0.08)',
                    }
                }
            }
        }
    </script>
    
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; scroll-behavior: smooth; }
        .sidebar-link.active { 
            background: linear-gradient(90deg, hsla(215, 100%, 50%, 0.1) 0%, transparent 100%); 
            border-left: 3px solid hsl(215, 94%, 48%);
            color: hsl(215, 100%, 75%) !important;
            box-shadow: inset 4px 0 10px -5px hsla(215, 100%, 50%, 0.3);
        }
        .glass { 
            background: rgba(255, 255, 255, 0.82); 
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .glass-dark {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .slide-down { animation: slideDown 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
        [x-cloak] { display: none !important; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.1); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0, 0, 0, 0.2); }

        .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); }
        .sidebar-menu:hover::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); }

        /* Toast & Modal */
        @keyframes toastIn { from { opacity: 0; transform: translateX(100%) scale(0.9); } to { opacity: 1; transform: translateX(0) scale(1); } }
        @keyframes toastOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(100%); } }
        .toast-enter { animation: toastIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
        .toast-leave { animation: toastOut 0.3s ease-in forwards; }

        @keyframes modalIn { from { opacity: 0; transform: scale(0.9) translateY(20px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        .modal-enter { animation: modalIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        /* Skeleton Loading */
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        .skeleton { 
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%); 
            background-size: 200% 100%; 
            animation: shimmer 1.5s infinite; 
            border-radius: 0.5rem;
        }

        .sidebar-group-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: hsl(222, 47%, 45%);
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            padding: 0 1rem;
            opacity: 0.8;
        }
        
        .sidebar-link {
            display: flex;
            items-center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.75rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            color: hsl(215, 20%, 75%);
        }
        
        .sidebar-link:hover {
            background-color: hsla(215, 100%, 50%, 0.08);
            color: white;
            transform: translateX(4px);
        }
        
        .sidebar-link.active {
            background: linear-gradient(90deg, hsla(215, 100%, 50%, 0.15) 0%, transparent 100%);
            border-left: 3px solid hsl(215, 94%, 48%);
            color: hsl(215, 100%, 80%) !important;
            box-shadow: inset 4px 0 12px -6px hsla(215, 100%, 50%, 0.4);
            transform: translateX(4px);
        }

        .sidebar-link i {
            transition: transform 0.2s;
        }

        .sidebar-link:hover i {
            transform: scale(1.15) rotate(3deg);
        }

        /* Chart Tooltip Customization */
        .chart-tooltip { 
            opacity: 0; 
            position: absolute; 
            background: rgba(15, 23, 42, 0.9); 
            border-radius: 12px; 
            color: white; 
            transition: all .1s ease; 
            pointer-events: none; 
            transform: translate(-50%, 0); 
            z-index: 100; 
            padding: 8px 12px; 
            border: 1px solid rgba(255,255,255,0.1); 
            backdrop-filter: blur(8px); 
        }
        .chart-container { position: relative; height: 300px; width: 100%; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen" x-data="{ sidebarOpen: false }">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-72 bg-dark-100 h-screen fixed left-0 top-0 text-white z-40 transition-transform duration-300 transform flex flex-col border-r border-white/5 shadow-2xl"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            <!-- Logo -->
            <div class="h-20 flex items-center px-6 border-b border-white/5 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center shadow-lg shadow-primary-500/20">
                        <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-white">Edu <span class="text-primary-400">CRM</span></span>
                </div>
            </div>
            

            
            <!-- Navigation -->
            <nav class="p-4 space-y-1 flex-1 overflow-y-auto sidebar-menu custom-scrollbar">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Tổng quan hệ thống</span>
                </a>

                <!-- CRM Section -->
                <div class="sidebar-group-title">CRM & Tuyển sinh</div>
                @can('leads.view')
                <a href="{{ url('/admin/leads') }}" class="sidebar-link {{ request()->is('admin/leads*') ? 'active' : '' }}">
                    <i data-lucide="contact" class="w-5 h-5 text-emerald-400/80"></i>
                    <span>Khách hàng tiềm năng</span>
                </a>
                @endcan
                <a href="{{ route('admin.customers.index') }}" class="sidebar-link {{ request()->is('admin/customers*') ? 'active' : '' }}">
                    <i data-lucide="users" class="w-5 h-5 text-blue-400/80"></i>
                    <span>Phụ huynh & Học sinh</span>
                </a>

                <!-- Education Section -->
                <div class="sidebar-group-title">Đào tạo & Học vụ</div>
                @can('students.view')
                <a href="{{ route('admin.students.index') }}" class="sidebar-link {{ request()->is('admin/students*') ? 'active' : '' }}">
                    <i data-lucide="graduation-cap" class="w-5 h-5 text-indigo-400/80"></i>
                    <span>Hồ sơ học viên</span>
                </a>
                @endcan
                @can('interest_types.view')
                <a href="{{ route('admin.interest-types.index') }}" class="sidebar-link {{ request()->is('admin/interest-types*') ? 'active' : '' }}">
                    <i data-lucide="list-todo" class="w-5 h-5 text-amber-400/80"></i>
                    <span>Dịch vụ & Nhu cầu</span>
                </a>
                @endcan

                <!-- Marketing Section -->
                <div class="sidebar-group-title">Marketing</div>
                <a href="#" class="sidebar-link hover:text-white/60 opacity-60">
                    <i data-lucide="megaphone" class="w-5 h-5"></i>
                    <span>Chiến dịch (Coming)</span>
                </a>

                <!-- System Section -->
                <div class="sidebar-group-title">Cấu hình & Hệ thống</div>
                @can('centers.view')
                <a href="{{ route('admin.centers.index') }}" class="sidebar-link {{ request()->is('admin/centers*') ? 'active' : '' }}">
                    <i data-lucide="building-2" class="w-5 h-5 text-slate-400/80"></i>
                    <span>Danh sách cơ sở</span>
                </a>
                @endcan
                @can('users.view')
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                    <i data-lucide="user-cog" class="w-5 h-5 text-slate-400/80"></i>
                    <span>Nhân sự & Tài khoản</span>
                </a>
                @endcan
                @can('roles.view')
                <a href="{{ route('admin.roles.index') }}" class="sidebar-link {{ request()->is('admin/roles*') ? 'active' : '' }}">
                    <i data-lucide="shield-check" class="w-5 h-5 text-rose-400/80"></i>
                    <span>Quyền hạn hệ thống</span>
                </a>
                @endcan

            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 lg:ml-72 min-w-0 transition-all duration-300">
            <!-- Top Header -->
            <header class="h-20 glass border-b border-slate-200/60 flex items-center justify-between px-6 lg:px-8 sticky top-0 z-30 shadow-sm">
                <div class="flex flex-col gap-0.5">
                    <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-0.5">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-500 transition-colors">Hệ thống</a>
                        <i data-lucide="chevron-right" class="w-2.5 h-2.5 opacity-50"></i>
                        @yield('breadcrumb_items')
                    </div>
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2.5 -ml-2 text-slate-500 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all focus:outline-none">
                            <i data-lucide="menu" class="w-6 h-6"></i>
                        </button>
                        <h1 class="text-xl font-bold text-slate-800 tracking-tight transition-all">@yield('title', 'Bảng điều khiển')</h1>
                    </div>
                </div>

                <!-- Global Search & Actions -->
                <div class="hidden md:flex items-center flex-1 max-w-md mx-8">
                    <div class="relative w-full group cursor-pointer" @click="showToast('Tính năng Command Palette (Ctrl+K) đang được phát triển...', 'info')">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 group-hover:text-primary-500 transition-colors"></i>
                        </div>
                        <input type="text" readonly
                            class="block w-full pl-10 pr-4 py-2 bg-slate-100/50 border border-transparent group-hover:border-slate-200 group-hover:bg-white rounded-xl text-sm transition-all cursor-pointer" 
                            placeholder="Tìm kiếm nhanh...">
                        <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none">
                            <kbd class="hidden sm:inline-flex items-center px-1.5 py-0.5 border border-slate-200 rounded text-[10px] font-bold text-slate-400 bg-white group-hover:border-primary-500/30 group-hover:text-primary-500 transition-all">Ctrl K</kbd>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-4 lg:gap-6">
                    <!-- Quick Add Action -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                            class="w-10 h-10 flex items-center justify-center bg-primary-600 text-white rounded-xl shadow-lg shadow-primary-500/20 hover:bg-primary-700 hover:scale-105 active:scale-95 transition-all">
                            <i data-lucide="plus" class="w-6 h-6"></i>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" 
                            class="absolute right-0 mt-3 w-56 glass glass-dark rounded-2xl shadow-xl border border-slate-200/60 p-2 z-50 overflow-hidden"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                            <p class="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tạo mới nhanh</p>
                            <a href="{{ url('/admin/leads') }}?action=create" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-all">
                                <i data-lucide="user-plus" class="w-4 h-4"></i> Khách hàng tiềm năng
                            </a>
                            <a href="{{ route('admin.students.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-all">
                                <i data-lucide="graduation-cap" class="w-4 h-4"></i> Hồ sơ học viên
                            </a>
                        </div>
                    </div>

                    <span class="hidden xl:inline text-xs font-semibold text-slate-400 flex items-center gap-2 uppercase tracking-widest whitespace-nowrap">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 opacity-60"></i>
                        {{ now()->translatedFormat('l, d/m/Y') }}
                    </span>
                    
                    <!-- Center Context Indicator & Switcher -->
                    @auth
                    @php

                        
                        $hasGlobalScope = false;
                        try { $hasGlobalScope = app('has_global_scope'); } catch (\Exception $e) {}
                        
                        $allowedCenterIds = [];
                        try { $allowedCenterIds = app('allowed_center_ids'); } catch (\Exception $e) {}

                        $currentCenterId = session('current_center_id');
                        $allCenters = app(\Modules\Core\Center\Application\Queries\GetActiveCentersHandler::class)
                            ->handle(new \Modules\Core\Center\Application\Queries\GetActiveCentersQuery());
                        // Filter available centers for the dropdown
                        $availableCenters = $allCenters->whereIn('id', $allowedCenterIds);

                        $currentCenter = $currentCenterId ? $availableCenters->firstWhere('id', $currentCenterId) : null;
                    @endphp
                    
                    @if(($hasGlobalScope ? 1 : 0) + $availableCenters->count() > 1)
                    <div class="relative" x-data="{ openCenter: false }" @click.away="openCenter = false">
                        <button @click="openCenter = !openCenter" class="flex items-center gap-2 px-4 py-2 rounded-xl border transition-all text-sm font-semibold shadow-sm hover:shadow-md {{ ($hasGlobalScope && !$currentCenter) ? 'bg-emerald-50 border-emerald-200 text-emerald-700 hover:bg-emerald-100 hover:border-emerald-300' : 'bg-primary-50 border-primary-200 text-primary-700 hover:bg-primary-100 hover:border-primary-300' }}">
                            <i data-lucide="{{ ($hasGlobalScope && !$currentCenter) ? 'globe' : 'building-2' }}" class="w-4 h-4"></i>
                            @if($hasGlobalScope && !$currentCenter)
                                <span class="hidden lg:inline">Toàn hệ thống</span>
                                <span class="lg:hidden">ALL</span>
                            @elseif($currentCenter)
                                <span class="hidden lg:inline">{{ $currentCenter->name }}</span>
                                <span class="lg:hidden">{{ $currentCenter->code }}</span>
                            @else
                                <span class="hidden lg:inline">Chọn cơ sở…</span>
                                <span class="lg:hidden">N/A</span>
                            @endif
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 opacity-50 transition-transform" :class="openCenter ? 'rotate-180' : ''"></i>
                        </button>


                        <!-- Center Dropdown -->
                        <div x-show="openCenter" x-transition x-cloak class="absolute right-0 top-10 w-64 bg-white rounded-xl shadow-xl py-1.5 border border-slate-100 z-50">
                            <div class="px-3 py-2 text-[10px] uppercase tracking-wider text-slate-400 font-bold">Chuyển Scope / Cơ sở</div>
                            @if($hasGlobalScope)
                            <form action="{{ route('auth.switch-center') }}" method="POST">
                                @csrf
                                <input type="hidden" name="center_id" value="">
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm hover:bg-emerald-50 flex items-center gap-3 transition-colors {{ !$currentCenterId ? 'text-emerald-700 font-bold bg-emerald-50/50' : 'text-slate-600' }}">
                                    <i data-lucide="globe" class="w-4 h-4 {{ !$currentCenterId ? 'text-emerald-500' : 'text-slate-400' }}"></i> 
                                    Toàn hệ thống
                                    @if(!$currentCenterId)
                                    <i data-lucide="check" class="w-4 h-4 ml-auto text-emerald-500"></i>
                                    @endif
                                </button>
                            </form>
                            <div class="border-t border-slate-100 my-1"></div>
                            @endif
                            @foreach($availableCenters as $c)
                            <form action="{{ route('auth.switch-center') }}" method="POST">
                                @csrf
                                <input type="hidden" name="center_id" value="{{ $c->id }}">
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-primary-50 flex items-center gap-2 transition {{ $currentCenterId === $c->id ? 'text-primary-600 font-semibold bg-primary-50' : 'text-slate-700' }}">
                                    <i data-lucide="building-2" class="w-4 h-4 {{ $currentCenterId === $c->id ? 'text-primary-500' : 'text-slate-400' }}"></i>
                                    [{{ $c->code }}] {{ $c->name }}
                                    @if($currentCenterId === $c->id)
                                    <i data-lucide="check" class="w-4 h-4 ml-auto text-primary-500"></i>
                                    @endif
                                </button>
                            </form>
                            @endforeach
                        </div>
                    </div>
                    @else
                        <!-- Switcher is hidden if only 1 option -->
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl border {{ ($hasGlobalScope && !$currentCenter) ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-primary-50 border-primary-200 text-primary-700' }} select-none text-sm font-medium">
                            <i data-lucide="{{ ($hasGlobalScope && !$currentCenter) ? 'globe' : 'building-2' }}" class="w-4 h-4"></i>
                            @if($hasGlobalScope && !$currentCenter)
                                <span class="hidden sm:inline">Hệ thống (Toàn bộ cơ sở)</span>
                                <span class="sm:hidden">ALL</span>
                            @elseif($currentCenter)
                                <span class="hidden sm:inline">[{{ $currentCenter->code }}] {{ $currentCenter->name }}</span>
                                <span class="sm:hidden">{{ $currentCenter->code }}</span>
                            @endif
                        </div>
                    @endif
                    @endauth

                     <!-- Separator -->
                     <div class="h-8 w-px bg-slate-200 mx-2"></div>

                    <!-- User Menu -->
                    <div class="relative flex items-center gap-3" x-data="{ open: false }" @click.away="open = false">
                        <div class="hidden sm:flex flex-col items-end cursor-pointer" @click="open = !open">
                            <p class="text-sm font-bold text-slate-800 leading-tight">{{ Auth::user()->name ?? 'Người dùng' }}</p>
                            <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider opacity-70">{{ Auth::user()->roles->first()?->name ?? 'Staff' }}</p>
                        </div>
                        
                        <div @click="open = !open" class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm border border-indigo-200 shadow-sm cursor-pointer hover:shadow-md transition-all active:scale-95">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        
                         <!-- User Dropdown -->
                         <div x-show="open" x-transition x-cloak class="absolute right-0 top-14 w-56 bg-white rounded-2xl shadow-xl py-2 border border-slate-100 z-50 slide-down">
                            <div class="px-4 py-2 border-b border-slate-50 mb-1">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tài khoản</p>
                            </div>
                            <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                                <i data-lucide="user-cog" class="w-4 h-4 opacity-70"></i> Hồ sơ cá nhân
                            </a>
                            <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                                <i data-lucide="settings" class="w-4 h-4 opacity-70"></i> Cài đặt
                            </a>
                            <div class="border-t border-slate-50 my-2"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-3 transition-colors font-medium">
                                    <i data-lucide="log-out" class="w-4 h-4"></i> Đăng xuất
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
