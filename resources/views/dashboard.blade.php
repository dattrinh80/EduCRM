@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
            <p class="text-slate-500 mt-1">Chào mừng bạn quay trở lại Edu CRM</p>
        </div>
    </div>

    <!-- Welcome Content -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center sm:text-left flex flex-col sm:flex-row items-center gap-8">
        <div class="w-32 h-32 bg-primary-50 rounded-full flex items-center justify-center shrink-0">
            <i data-lucide="layout-dashboard" class="w-12 h-12 text-primary-500"></i>
        </div>
        <div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Xin chào, {{ auth()->user()->name }}! 👋</h3>
            <p class="text-slate-500 max-w-xl">
                Đây là giao diện tổng quan của hệ thống Edu CRM. Tại đây bạn sẽ có thể theo dõi nhanh các chỉ số về Lead, Chiến dịch, và tổng quan hiệu suất làm việc. 
                Các widget và báo cáo chi tiết đang trong quá trình phát triển và sẽ được cập nhật sớm.
            </p>
            <div class="mt-6 flex flex-wrap gap-3 justify-center sm:justify-start">
                <a href="{{ route('admin.leads.index') }}" class="px-5 py-2.5 bg-primary-50 text-primary-600 font-medium rounded-xl hover:bg-primary-100 transition flex items-center gap-2 text-sm">
                    <i data-lucide="users" class="w-4 h-4"></i> Xem danh sách Leads
                </a>
                <a href="{{ route('admin.campaigns.index') }}" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-medium rounded-xl hover:bg-slate-50 transition flex items-center gap-2 text-sm shadow-sm">
                    <i data-lucide="megaphone" class="w-4 h-4"></i> Xem Chiến dịch
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
