@extends('layouts.app')

@section('title', 'Chi tiết Học viên: ' . ($student->customer?->name ?? 'N/A'))

@section('breadcrumb_items')
    <a href="{{ route('admin.students.index') }}" class="text-slate-400 hover:text-primary-500 transition-colors">Học vụ</a>
    <i data-lucide="chevron-right" class="w-2.5 h-2.5 opacity-50"></i>
    <span class="text-primary-500">Chi tiết Học viên</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header/Cover Area -->
    <div class="relative">
        <div class="h-32 bg-gradient-to-r from-primary-600 to-indigo-600 rounded-3xl shadow-lg border border-primary-500/20"></div>
        <div class="px-8 -mt-12 flex flex-col sm:flex-row items-end gap-6">
            <div class="w-24 h-24 rounded-3xl bg-white p-1.5 shadow-xl border border-slate-100 flex-shrink-0">
                <div class="w-full h-full rounded-2xl bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center text-primary-600 text-3xl font-display font-bold">
                    {{ strtoupper(substr($student->customer?->name ?? 'S', 0, 1)) }}
                </div>
            </div>
            <div class="flex-grow pb-2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-display font-bold text-slate-800 tracking-tight">{{ $student->customer?->name ?? 'N/A' }}</h1>
                        <div class="flex flex-wrap items-center gap-4 mt-2">
                            <span class="flex items-center gap-1.5 text-sm font-medium text-slate-500">
                                <i data-lucide="hash" class="w-4 h-4 opacity-70"></i>
                                {{ $student->student_code }}
                            </span>
                            <span class="flex items-center gap-1.5 text-sm font-medium text-slate-500">
                                <i data-lucide="map-pin" class="w-4 h-4 opacity-70"></i>
                                {{ $student->customer?->center?->name ?? 'Phòng ban/Trung tâm' }}
                            </span>
                             <x-ui.badge variant="success" :dot="true" class="px-3 py-1">
                                {{ $student->status }}
                            </x-ui.badge>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-ui.button variant="secondary" icon="edit" :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.students.edit', $student->id), 'tag' => 'a'])">
                            Chỉnh sửa
                        </x-ui.button>
                        <x-ui.button variant="primary" icon="layers">
                            Đăng ký lớp
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Primary Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Student Information Card -->
            <x-ui.card>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-display font-bold text-slate-800 flex items-center gap-2">
                            <i data-lucide="user" class="w-5 h-5 text-primary-500"></i>
                            Thông tin cá nhân
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
                        <div class="space-y-1">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Họ và tên</span>
                            <div class="text-sm font-bold text-slate-700">{{ $student->customer?->name ?? 'N/A' }}</div>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Số điện thoại</span>
                            <div class="text-sm font-bold text-slate-700 tabular-nums">{{ $student->customer?->phone ?? 'N/A' }}</div>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Email</span>
                            <div class="text-sm font-bold text-slate-700 underline decoration-primary-200 decoration-2 underline-offset-4">{{ $student->customer?->email ?? 'N/A' }}</div>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Ngày sinh</span>
                            <div class="text-sm font-bold text-slate-700 tabular-nums">
                                {{ $student->customer?->dob ? $student->customer->dob->format('d/m/Y') : 'N/A' }}
                                @if($student->customer?->dob)
                                    <span class="text-[10px] text-slate-400 font-medium normal-case bg-slate-50 px-1.5 py-0.5 rounded ml-1 tracking-tight">
                                        {{ $student->customer->dob->age }} tuổi
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Giới tính</span>
                            <div class="flex items-center gap-1.5">
                                @if(($student->customer?->gender ?? '') === 'MALE')
                                    <i data-lucide="user" class="w-4 h-4 text-blue-500"></i>
                                    <span class="text-sm font-bold text-slate-700">Nam</span>
                                @elseif(($student->customer?->gender ?? '') === 'FEMALE')
                                    <i data-lucide="user" class="w-4 h-4 text-pink-500"></i>
                                    <span class="text-sm font-bold text-slate-700">Nữ</span>
                                @else
                                    <span class="text-sm font-bold text-slate-700">Khác</span>
                                @endif
                            </div>
                        </div>
                        <div class="sm:col-span-2 space-y-1">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Địa chỉ</span>
                            <div class="text-sm font-bold text-slate-700 leading-relaxed">{{ $student->customer?->address ?? 'Chưa cập nhật' }}</div>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <!-- Guardian Information -->
            <x-ui.card>
                <div class="p-6">
                    <h3 class="text-lg font-display font-bold text-slate-800 flex items-center gap-2 mb-6">
                        <i data-lucide="users" class="w-5 h-5 text-orange-500"></i>
                        Người giám hộ
                    </h3>
                    @if($student->guardians && count($student->guardians) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($student->guardians as $guardian)
                                <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:shadow-md transition-all duration-300 group">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-primary-500 transition-colors">
                                                <i data-lucide="user" class="w-5 h-5"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-slate-800 tracking-tight">{{ $guardian->name }}</div>
                                                <div class="text-[10px] font-bold text-primary-600 uppercase tracking-widest mt-0.5">
                                                    {{ $guardian->pivot->relationship ?? 'Người thân' }}
                                                    @if($guardian->pivot->is_primary)
                                                        <span class="ml-1 text-[9px] px-1.5 py-0.5 bg-green-100 text-green-600 rounded">Liên hệ chính</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-2 gap-2">
                                        <div class="flex items-center gap-1.5 text-slate-500">
                                            <i data-lucide="phone" class="w-3.5 h-3.5 opacity-60"></i>
                                            <span class="text-xs tabular-nums font-medium">{{ $guardian->phone ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-slate-500">
                                            <i data-lucide="mail" class="w-3.5 h-3.5 opacity-60"></i>
                                            <span class="text-xs truncate font-medium">{{ $guardian->email ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 flex flex-col items-center justify-center text-center space-y-2 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                            <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-slate-300">
                                <i data-lucide="users-2" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-800">Chưa có thông tin người giám hộ</div>
                                <p class="text-[11px] text-slate-400">Vui lòng cập nhật thông tin liên hệ để nhận các thông báo học vụ.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </x-ui.card>
        </div>

        <!-- Right Column: Sidebar Stats/Info -->
        <div class="space-y-6">
            <!-- Summary Info -->
            <x-ui.card>
                <div class="p-6 space-y-6">
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Thông tin hệ thống</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Ngày gia nhập</span>
                                <span class="text-xs font-bold text-slate-800 tabular-nums">{{ \Carbon\Carbon::parse($student->created_at)->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Mã định danh</span>
                                <span class="text-[10px] font-mono font-bold bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded tracking-tighter">{{ $student->id }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Hoạt động gần đây</h4>
                        <div class="space-y-4">
                            <div class="flex gap-3">
                                <div class="w-1 h-auto bg-green-400 rounded-full shrink-0"></div>
                                <div>
                                    <div class="text-[11px] font-bold text-slate-800 tracking-tight">Hồ sơ được tạo</div>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($student->created_at)->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-ui.card>

             <!-- Quick Actions/Notes Placeholder -->
             <x-ui.card class="bg-indigo-50/30 border-indigo-100">
                <div class="p-6">
                    <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                        <i data-lucide="lightbulb" class="w-3.5 h-3.5"></i>
                        Ghi chú học tập
                    </h4>
                    <p class="text-xs text-indigo-600/70 leading-relaxed italic font-medium">
                        "Tính năng quản lý lộ trình học tập, điểm số và điểm danh đang được phát triển..."
                    </p>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection
