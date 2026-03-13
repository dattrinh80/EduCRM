@extends('layouts.app')

@section('title', 'Thêm Học viên mới')

@section('breadcrumb_items')
    <a href="{{ route('admin.students.index') }}" class="text-slate-400 hover:text-primary-500 transition-colors">Học vụ</a>
    <i data-lucide="chevron-right" class="w-2.5 h-2.5 opacity-50"></i>
    <span class="text-primary-500">Thêm Học viên mới</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <x-ui.button variant="ghost" size="sm" icon="arrow-left" 
            :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.students.index'), 'tag' => 'a'])" />
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight">Thêm Học viên mới</h1>
            <p class="text-slate-500 text-sm">Nhập thông tin cá nhân và người giám hộ cho học viên mới</p>
        </div>
    </div>

    <form action="{{ route('admin.students.store') }}" method="POST">
        @csrf
        
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
                                value="{{ old('name') }}"
                                containerClass="sm:col-span-2"
                            />
                            
                            <x-ui.input 
                                name="phone" 
                                label="Số điện thoại" 
                                placeholder="090..." 
                                value="{{ old('phone') }}"
                            />
                            
                            <x-ui.input 
                                name="email" 
                                label="Email" 
                                type="email"
                                placeholder="email@example.com" 
                                value="{{ old('email') }}"
                            />

                            <x-ui.input 
                                name="dob" 
                                label="Ngày sinh" 
                                type="date"
                                value="{{ old('dob') }}"
                            />

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Giới tính</label>
                                <select name="gender" class="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-sm outline-none">
                                    <option value="MALE" {{ old('gender') == 'MALE' ? 'selected' : '' }}>Nam</option>
                                    <option value="FEMALE" {{ old('gender') == 'FEMALE' ? 'selected' : '' }}>Nữ</option>
                                    <option value="OTHER" {{ old('gender') == 'OTHER' ? 'selected' : '' }}>Khác</option>
                                </select>
                            </div>

                            <x-ui.input 
                                name="address" 
                                label="Địa chỉ" 
                                placeholder="Nhập địa chỉ" 
                                value="{{ old('address') }}"
                                containerClass="sm:col-span-2"
                            />
                        </div>
                    </div>
                </x-ui.card>

                <!-- Guardian Info (Dynamic) -->
                <x-ui.card x-data="{ 
                    guardians: {{ json_encode(old('guardians', [['name' => '', 'phone' => '', 'relationship' => 'Parent', 'is_primary' => true]])) }},
                    addGuardian() {
                        this.guardians.push({ name: '', phone: '', relationship: 'Guardian', is_primary: false });
                    },
                    removeGuardian(index) {
                        this.guardians.splice(index, 1);
                    }
                }">
                    <div class="p-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-slate-800 font-bold tracking-tight">
                                <div class="p-1.5 bg-orange-50 text-orange-600 rounded-lg">
                                    <i data-lucide="users" class="w-4 h-4"></i>
                                </div>
                                Thông tin người giám hộ
                            </div>
                            <x-ui.button type="button" variant="ghost" size="xs" icon="plus" @click="addGuardian()">
                                Thêm người giám hộ
                            </x-ui.button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(guardian, index) in guardians" :key="index">
                                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-4 relative group">
                                    <button type="button" @click="removeGuardian(index)" 
                                        class="absolute top-2 right-2 text-slate-300 hover:text-red-500 transition-colors"
                                        x-show="guardians.length > 1">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <x-ui.input 
                                            ::name=\"'guardians[' + index + '][name]'\"
                                            label="Họ tên người giám hộ" 
                                            placeholder="Nhập họ tên" 
                                            ::value=\"guardian.name\"
                                            required
                                        />
                                        <x-ui.input 
                                            ::name=\"'guardians[' + index + '][phone]'\"
                                            label="Số điện thoại" 
                                            placeholder="090..." 
                                            ::value=\"guardian.phone\"
                                            required
                                        />
                                        <div class="space-y-1.5">
                                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mối quan hệ</label>
                                            <select ::name=\"'guardians[' + index + '][relationship]'\" class="w-full h-10 px-3 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-sm outline-none">
                                                <option value="Parent">Cha/Mẹ</option>
                                                <option value="Sibling">Anh/Chị/Em</option>
                                                <option value="Grandparent">Ông/Bà</option>
                                                <option value="Other">Khác</option>
                                            </select>
                                        </div>
                                        <div class="flex items-center h-full pt-6">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" ::name=\"'guardians[' + index + '][is_primary]'\" value="1" :checked="guardian.is_primary" class="w-4 h-4 text-primary-600 rounded border-slate-300 focus:ring-primary-500">
                                                <span class="text-sm text-slate-600 font-medium">Liên hệ chính</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </template>
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
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Trung tâm quản lý</label>
                                <select name="center_id" required class="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-sm outline-none">
                                    @foreach($centers as $center)
                                        <option value="{{ $center->id }}" {{ old('center_id') == $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <x-ui.input 
                                name="student_code" 
                                label="Mã học viên (Tự động nếu để trống)" 
                                placeholder="VD: STU00001" 
                                value="{{ old('student_code') }}"
                            />
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                            <x-ui.button type="submit" variant="primary" class="w-full">
                                Lưu học viên
                            </x-ui.button>
                            <x-ui.button type="button" variant="ghost" class="w-full" :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.students.index'), 'tag' => 'a'])">
                                Hủy bỏ
                            </x-ui.button>
                        </div>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </form>
</div>
@endsection
