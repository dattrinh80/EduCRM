@extends('layouts.app')

@section('title', 'Chỉnh sửa Học viên: ' . ($student->customer?->name ?? 'N/A'))

@section('breadcrumb_items')
    <a href="{{ route('admin.students.index') }}" class="text-slate-400 hover:text-primary-500 transition-colors">Học vụ</a>
    <i data-lucide="chevron-right" class="w-2.5 h-2.5 opacity-50"></i>
    <span class="text-primary-500">Chỉnh sửa Học viên</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <x-ui.button variant="ghost" size="sm" icon="arrow-left" 
            :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.students.index'), 'tag' => 'a'])" />
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight">Chỉnh sửa Học viên</h1>
            <p class="text-slate-500 text-sm">Cập nhật thông tin học viên: <span class="font-bold text-slate-800">{{ $student->student_code }}</span></p>
        </div>
    </div>

    <form action="{{ route('admin.students.update', $student->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column: Student Info -->
            <div class="md:col-span-2 space-y-6">
                <x-ui.card>
                    <div class="p-6 space-y-6">
                        <div class="flex items-center gap-2 text-slate-800 font-bold tracking-tight">
                            <div class="p-1.5 bg-primary-50 text-primary-600 rounded-lg">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                            Thông tin cá nhân
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-ui.input 
                                name="name" 
                                label="Họ và tên" 
                                placeholder="Nhập họ tên học viên" 
                                required 
                                value="{{ old('name', $student->customer?->name) }}"
                                containerClass="sm:col-span-2"
                            />
                            
                            <x-ui.input 
                                name="phone" 
                                label="Số điện thoại" 
                                placeholder="090..." 
                                value="{{ old('phone', $student->customer?->phone) }}"
                            />
                            
                            <x-ui.input 
                                name="email" 
                                label="Email" 
                                type="email"
                                placeholder="email@example.com" 
                                value="{{ old('email', $student->customer?->email) }}"
                            />

                            <x-ui.input 
                                name="dob" 
                                label="Ngày sinh" 
                                type="date"
                                value="{{ old('dob', $student->customer?->dob?->format('Y-m-d')) }}"
                            />

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Giới tính</label>
                                <select name="gender" class="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-sm outline-none">
                                    <option value="MALE" {{ old('gender', $student->customer?->gender) == 'MALE' ? 'selected' : '' }}>Nam</option>
                                    <option value="FEMALE" {{ old('gender', $student->customer?->gender) == 'FEMALE' ? 'selected' : '' }}>Nữ</option>
                                    <option value="OTHER" {{ old('gender', $student->customer?->gender) == 'OTHER' ? 'selected' : '' }}>Khác</option>
                                </select>
                            </div>

                            <x-ui.input 
                                name="address" 
                                label="Địa chỉ" 
                                placeholder="Nhập địa chỉ" 
                                value="{{ old('address', $student->customer?->address) }}"
                                containerClass="sm:col-span-2"
                            />
                        </div>
                    </div>
                </x-ui.card>
            </div>

            <!-- Right Column: Settings -->
            <div class="space-y-6">
                <x-ui.card>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-2 text-slate-800 font-bold tracking-tight mb-2">
                            <div class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg">
                                <i data-lucide="settings" class="w-4 h-4"></i>
                            </div>
                            Cài đặt hệ thống
                        </div>

                        <div class="space-y-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</label>
                                <select name="status" required class="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-sm outline-none">
                                    <option value="NEW" {{ old('status', $student->status) == 'NEW' ? 'selected' : '' }}>Mới</option>
                                    <option value="ACTIVE" {{ old('status', $student->status) == 'ACTIVE' ? 'selected' : '' }}>Đang học</option>
                                    <option value="DROPPED" {{ old('status', $student->status) == 'DROPPED' ? 'selected' : '' }}>Thôi học</option>
                                    <option value="GRADUATED" {{ old('status', $student->status) == 'GRADUATED' ? 'selected' : '' }}>Tốt nghiệp</option>
                                </select>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                            <x-ui.button type="submit" variant="primary" class="w-full">
                                Cập nhật học viên
                            </x-ui.button>
                            <x-ui.button type="button" variant="ghost" class="w-full" :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.students.index'), 'tag' => 'a'])">
                                Hủy bỏ
                            </x-ui.button>
                        </div>
                    </div>
                </x-ui.card>

                <!-- Danger Zone -->
                <x-ui.card>
                    <div class="p-4 bg-red-50/30 rounded-2xl">
                        <div class="flex items-center gap-2 text-red-600 font-bold text-xs uppercase tracking-widest mb-3">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                            Khu vực nguy hiểm
                        </div>
                        <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa học viên này? Thao tác này có thể không hoàn tác được.')">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="ghost" size="sm" class="w-full text-red-500 hover:bg-red-50 border-red-100">
                                Xóa học viên
                            </x-ui.button>
                        </form>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </form>
</div>
@endsection
