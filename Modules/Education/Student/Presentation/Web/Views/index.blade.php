@extends('layouts.app')

@section('title', 'Quản lý Học viên')

@section('breadcrumb_items')
    <a href="{{ route('admin.students.index') }}" class="text-slate-400 hover:text-primary-500 transition-colors">Học vụ</a>
    <i data-lucide="chevron-right" class="w-2.5 h-2.5 opacity-50"></i>
    <span class="text-primary-500">Student Profiles</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight">Quản lý Học viên</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-1.5 text-sm">
                <i data-lucide="info" class="w-3.5 h-3.5 opacity-60"></i>
                Quản lý hồ sơ học tập và thông tin cá nhân của học viên
            </p>
        </div>
        <div>
            <x-ui.button variant="primary" icon="plus-circle">
                Thêm Học viên
            </x-ui.button>
        </div>
    </div>

    <!-- Filter Bar -->
    <x-ui.card bodyClass="p-4">
        <form action="{{ route('admin.students.index') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label for="search" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Tìm kiếm</label>
                <div class="relative w-full group">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                    <input type="text" name="search" id="search" placeholder="Họ tên, mã học viên…" value="{{ request('search') }}"
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none">
                </div>
            </div>
            
            <div class="flex gap-2 w-full md:w-auto">
                <x-ui.button type="submit" variant="secondary" icon="filter">
                    Lọc dữ liệu
                </x-ui.button>
                @if(request()->has('search'))
                    <x-ui.button variant="ghost" icon="x-circle" :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.students.index'), 'tag' => 'a'])">
                        Xoá lọc
                    </x-ui.button>
                @endif
            </div>
        </form>
    </x-ui.card>

    @if(count($students) > 0)
        <!-- Data Table -->
        <x-ui.card bodyClass="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase text-slate-500 font-bold tracking-widest whitespace-nowrap">
                            <th class="px-6 py-4">Học viên</th>
                            <th class="px-6 py-4 text-center">Mã học viên</th>
                            <th class="px-6 py-4 text-center">Trạng thái</th>
                            <th class="px-6 py-4">Ngày tạo</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($students as $student)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center text-primary-600 font-bold text-sm border border-primary-200 shadow-sm">
                                        {{ strtoupper(substr($student->studentName ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="font-bold text-slate-800 tracking-tight">{{ $student->studentName ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 bg-slate-50 text-slate-600 rounded-lg font-mono text-xs font-bold border border-slate-100 tabular-nums">{{ $student->studentCode }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <x-ui.badge variant="success" :dot="true">
                                    {{ $student->status }}
                                </x-ui.badge>
                            </td>
                            <td class="px-6 py-4 text-slate-400 text-[11px] font-bold flex flex-col uppercase tracking-wider tabular-nums">
                                 {{ \Carbon\Carbon::parse($student->createdAt)->translatedFormat('d/m/Y') }}
                                 <span class="text-[9px] opacity-60 font-medium normal-case tracking-normal">{{ \Carbon\Carbon::parse($student->createdAt)->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1 opacity-10 sm:opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:translate-x-0 translate-x-4">
                                    <x-ui.button variant="ghost" size="xs" icon="eye" class="text-slate-400" title="Chi tiết" />
                                    <x-ui.button variant="ghost" size="xs" icon="layout-grid" class="text-slate-400" title="Lớp học" />
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    @else
        <x-ui.empty-state 
            title="Chưa có học viên nào"
            description="Hệ thống chưa ghi nhận thông tin học viên nào. Hãy thử chuyển đổi từ Lead hoặc nhập liệu mới."
            icon="graduation-cap"
            actionText="Thêm học viên đầu tiên"
        />
    @endif
</div>
@endsection
