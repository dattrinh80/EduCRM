@extends('layouts.app')

@section('title', 'Bảng điều khiển')

@section('breadcrumb_items')
    <span class="text-primary-500">Dashboard</span>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Header with Welcome -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Chào buổi sáng, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h1>
            <p class="text-slate-500 mt-1">Dưới đây là tổng quan hoạt động tại trung tâm của bạn hôm nay.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 transition-all shadow-sm flex items-center gap-2 text-sm">
                <i data-lucide="download" class="w-4 h-4 text-slate-400"></i> Xuất báo cáo
            </button>
            <button class="px-4 py-2.5 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/20 flex items-center gap-2 text-sm">
                <i data-lucide="plus" class="w-4 h-4"></i> Thêm Lead mới
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- Stat Card 1 -->
        <div class="glass p-6 rounded-3xl border border-slate-200/60 shadow-sm group hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="contact" class="w-6 h-6"></i>
                </div>
                <span class="flex items-center gap-1 text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">
                    <i data-lucide="trending-up" class="w-3 h-3"></i> 12%
                </span>
            </div>
            <h3 class="text-slate-500 text-sm font-medium">Tổng số Leads</h3>
            <p class="text-2xl font-bold text-slate-800 mt-1">1,284</p>
        </div>

        <!-- Stat Card 2 -->
        <div class="glass p-6 rounded-3xl border border-slate-200/60 shadow-sm group hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                </div>
                <span class="flex items-center gap-1 text-xs font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded-lg">
                    +5 tháng này
                </span>
            </div>
            <h3 class="text-slate-500 text-sm font-medium">Học viên đang học</h3>
            <p class="text-2xl font-bold text-slate-800 mt-1">452</p>
        </div>

        <!-- Stat Card 3 -->
        <div class="glass p-6 rounded-3xl border border-slate-200/60 shadow-sm group hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="target" class="w-6 h-6"></i>
                </div>
                <span class="flex items-center gap-1 text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">
                    <i data-lucide="trending-up" class="w-3 h-3"></i> 8%
                </span>
            </div>
            <h3 class="text-slate-500 text-sm font-medium">Tỷ lệ chuyển đổi</h3>
            <p class="text-2xl font-bold text-slate-800 mt-1">24.5%</p>
        </div>

        <!-- Stat Card 4 -->
        <div class="glass p-6 rounded-3xl border border-slate-200/60 shadow-sm group hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="dollar-sign" class="w-6 h-6"></i>
                </div>
                <span class="flex items-center gap-1 text-xs font-bold text-rose-500 bg-rose-50 px-2 py-1 rounded-lg">
                    <i data-lucide="trending-down" class="w-3 h-3"></i> 2%
                </span>
            </div>
            <h3 class="text-slate-500 text-sm font-medium">Doanh thu dự kiến</h3>
            <p class="text-2xl font-bold text-slate-800 mt-1">1.25B</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <!-- Revenue Trend Chart -->
        <div class="bg-white rounded-[2.5rem] border border-slate-200/60 shadow-sm p-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="font-bold text-slate-800">Doanh thu & Lead dự kiến</h3>
                    <p class="text-xs text-slate-500 mt-1">Dữ liệu cập nhật theo thời gian thực</p>
                </div>
                <div class="flex gap-2">
                    <button class="px-3 py-1.5 text-[10px] font-bold bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition">7 ngày</button>
                    <button class="px-3 py-1.5 text-[10px] font-bold bg-primary-600 text-white rounded-lg transition shadow-md shadow-primary-500/20">30 ngày</button>
                </div>
            </div>
            <div class="h-[300px] w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Funnel / Activity Chart -->
        <div class="bg-white rounded-[2.5rem] border border-slate-200/60 shadow-sm p-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="font-bold text-slate-800">Tỷ lệ chuyển đổi</h3>
                    <p class="text-xs text-slate-500 mt-1">Phân tích phễu khách hàng</p>
                </div>
                <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-400 cursor-pointer"></i>
            </div>
            <div class="h-[300px] w-full">
                <canvas id="funnelChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Details Row -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mt-12">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-[2.5rem] border border-slate-200/60 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800">Khách hàng tiềm năng mới nhất</h3>
                    <a href="{{ url('/admin/leads') }}" class="text-primary-600 text-sm font-semibold hover:underline">Xem tất cả</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[11px] uppercase tracking-widest text-slate-400 font-bold bg-slate-50/50">
                                <th class="px-8 py-4">Khách hàng</th>
                                <th class="px-6 py-4">Ngày tạo</th>
                                <th class="px-6 py-4">Nguồn</th>
                                <th class="px-6 py-4 text-right">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <!-- Template Row 1 -->
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-xs">NL</div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-700">Nguyễn Lan Anh</p>
                                            <p class="text-[11px] text-slate-400">0987 123 456</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">Hôm nay, 08:30</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 text-[11px] font-bold">Facebook Ads</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-[11px] font-bold">Mới</span>
                                </td>
                            </tr>
                            <!-- Template Row 2 -->
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">TV</div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-700">Trần Văn Tú</p>
                                            <p class="text-[11px] text-slate-400">0912 345 678</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">Hôm qua, 17:45</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg bg-purple-50 text-purple-600 text-[11px] font-bold">Hotline</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 text-[11px] font-bold">Đang chăm sóc</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Area: Quick Tools -->
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-[2.5rem] p-8 text-white shadow-xl relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-primary-500/20 rounded-full blur-3xl group-hover:bg-primary-500/30 transition-all"></div>
                <h4 class="text-lg font-bold mb-2">Thống kê nhanh</h4>
                <p class="text-slate-400 text-sm mb-6">Bạn đã xử lý 15 leads trong tuần này. Tuyệt vời!</p>
                <div class="space-y-4 relative z-10">
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/10">
                        <div class="flex items-center gap-3">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-400"></i>
                            <span class="text-sm font-medium">Đã hoàn thành</span>
                        </div>
                        <span class="font-bold">12</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/10">
                        <div class="flex items-center gap-3">
                            <i data-lucide="clock" class="w-5 h-5 text-amber-400"></i>
                            <span class="text-sm font-medium">Đang chờ xử lý</span>
                        </div>
                        <span class="font-bold">3</span>
                    </div>
                </div>
                <button class="w-full mt-6 py-3 bg-white text-slate-900 font-bold rounded-2xl hover:bg-slate-100 transition-all text-sm">
                    Xem báo cáo chi tiết
                </button>
            </div>

            <!-- Shortcuts -->
            <div class="space-y-3">
                <h4 class="px-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Truy cập nhanh</h4>
                <a href="{{ route('admin.leads.index') }}" class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200/60 shadow-sm hover:border-primary-500/30 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                        </div>
                        <span class="text-sm font-bold text-slate-700">Thêm Lead mới</span>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 group-hover:text-primary-500 transition-colors"></i>
                </a>
                <a href="{{ route('admin.students.index') }}" class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200/60 shadow-sm hover:border-primary-500/30 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i data-lucide="user-check" class="w-5 h-5"></i>
                        </div>
                        <span class="text-sm font-bold text-slate-700">Ghi danh học viên</span>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 group-hover:text-primary-500 transition-colors"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Revenue & Leads Chart ---
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        const revenueGradient = ctxRevenue.createLinearGradient(0, 0, 0, 300);
        revenueGradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
        revenueGradient.addColorStop(1, 'rgba(37, 99, 235, 0.02)');

        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: ['01/03', '02/03', '03/03', '04/03', '05/03', '06/03', '07/03'],
                datasets: [
                    {
                        label: 'Doanh thu',
                        data: [650, 780, 720, 950, 880, 1100, 1050],
                        borderColor: '#2563eb',
                        borderWidth: 3,
                        backgroundColor: revenueGradient,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#2563eb',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2
                    },
                    {
                        label: 'Leads mới',
                        data: [45, 52, 48, 70, 65, 80, 75],
                        borderColor: '#10b981',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.4,
                        pointRadius: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        padding: 12,
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { size: 10, weight: '700' },
                        bodyFont: { size: 12 },
                        cornerRadius: 10
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 2], color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { size: 10 }, color: '#94a3b8' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8' }
                    }
                }
            }
        });

        // --- Funnel Chart ---
        const ctxFunnel = document.getElementById('funnelChart').getContext('2d');
        new Chart(ctxFunnel, {
            type: 'doughnut',
            data: {
                labels: ['Mới', 'Đang chăm sóc', 'Đã chốt', 'Thất bại'],
                datasets: [{
                    data: [450, 320, 180, 90],
                    backgroundColor: ['#3b82f6', '#8b5cf6', '#10b981', '#f43f5e'],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 11, weight: '600' }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
