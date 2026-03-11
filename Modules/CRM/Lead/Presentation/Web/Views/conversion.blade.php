@extends('layouts.app')

@section('title', 'Chuyển đổi Lead thành Học viên')

@section('content')
<div class="mx-auto" style="max-width: 1100px;" x-data="convertLeadApp()">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-bold text-slate-800 tracking-tight">Chuyển đổi Lead</h1>
            <p class="text-sm text-slate-500 mt-1 flex items-center gap-2">
                <i data-lucide="user" class="w-4 h-4 text-primary-500"></i>
                Hồ sơ: <span class="font-semibold text-slate-700">{{ $lead->name }}</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span class="tabular-nums">{{ $lead->phone }}</span>
            </p>
        </div>
        <a href="{{ route('admin.leads.index') }}" class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm active:scale-95">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    {{-- Error Alert --}}
    @if(session('error'))
    <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3 shadow-sm">
        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
            <i data-lucide="alert-circle" class="w-4 h-4"></i>
        </div>
        <span class="font-medium text-sm">{{ session('error') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl shadow-sm">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
            </div>
            <span class="font-semibold text-sm">Vui lòng kiểm tra lại thông tin:</span>
        </div>
        <ul class="ml-12 text-sm space-y-1 list-disc">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.leads.convert.submit', $lead->getId()) }}" method="POST">
        @csrf

        {{-- Lead Summary Bar --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-5">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center text-base font-bold flex-shrink-0">
                        {{ mb_substr($lead->name, 0, 1) }}
                    </div>
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-1.5 text-sm">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Điện thoại</span>
                            <span class="font-semibold text-slate-700 tabular-nums">{{ $lead->phone }}</span>
                        </div>
                        @if($lead->email)
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Email</span>
                            <span class="font-medium text-slate-600">{{ $lead->email }}</span>
                        </div>
                        @endif
                        @if($leadSourceName)
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Nguồn</span>
                            <span class="font-medium text-slate-600">{{ $leadSourceName }}</span>
                        </div>
                        @endif
                        @if($centerName)
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Cơ sở</span>
                            <span class="font-medium text-slate-600">{{ $centerName }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                <button type="button" @click="useLeadAsGuardian()" class="px-3.5 py-2 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-primary-500/25 hover:from-primary-600 hover:to-primary-700 transition-all active:scale-95 flex items-center gap-2 whitespace-nowrap">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Dùng Lead làm Giám hộ
                </button>
            </div>
        </div>

        {{-- Student Cards --}}
        <template x-for="(student, sIndex) in students" :key="sIndex">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-4 overflow-hidden">
                {{-- Student Card Header --}}
                <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2 cursor-pointer" @click="student.collapsed = !student.collapsed">
                        <i data-lucide="graduation-cap" class="w-4.5 h-4.5 text-primary-500"></i>
                        <span class="font-semibold text-slate-700 text-sm" x-text="'Học viên #' + (sIndex + 1)"></span>
                        <span class="text-xs text-slate-400 font-medium" x-show="student.name" x-text="'— ' + student.name"></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button type="button" x-show="students.length > 1" @click="removeStudent(sIndex)"
                            class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Xoá học viên">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                        <button type="button" @click="student.collapsed = !student.collapsed" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg transition-all">
                            <i data-lucide="chevron-up" class="w-4 h-4 transition-transform" :class="student.collapsed ? 'rotate-180' : ''"></i>
                        </button>
                    </div>
                </div>

                {{-- Student Card Body --}}
                <div x-show="!student.collapsed" x-transition class="p-4 space-y-4">
                    {{-- Student Info Row 1: Họ tên, SĐT, Email --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" :name="'students[' + sIndex + '][name]'" x-model="student.name" required
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none"
                                placeholder="Nhập họ và tên…">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Số điện thoại</label>
                            <input type="text" :name="'students[' + sIndex + '][phone]'" x-model="student.phone"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none tabular-nums"
                                placeholder="0901234567">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Email</label>
                            <input type="email" :name="'students[' + sIndex + '][email]'" x-model="student.email"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none"
                                placeholder="email@example.com">
                        </div>
                    </div>

                    {{-- Student Info Row 2: Ngày sinh, Giới tính (dropdown), Địa chỉ --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Ngày sinh</label>
                            <input type="date" :name="'students[' + sIndex + '][dob]'" x-model="student.dob"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Giới tính</label>
                            <select :name="'students[' + sIndex + '][gender]'" x-model="student.gender"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none">
                                <option value="">Chọn…</option>
                                <option value="MALE">Nam</option>
                                <option value="FEMALE">Nữ</option>
                                <option value="OTHER">Khác</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Địa chỉ</label>
                            <input type="text" :name="'students[' + sIndex + '][address]'" x-model="student.address"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none"
                                placeholder="Số nhà, đường, phường…">
                        </div>
                    </div>

                    {{-- Student Info Row 3: Trường, Khối/Lớp --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Trường đang học</label>
                            <input type="text" :name="'students[' + sIndex + '][school]'" x-model="student.school"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none"
                                placeholder="Tên trường học…">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Khối / Lớp</label>
                            <input type="text" :name="'students[' + sIndex + '][grade]'" x-model="student.grade"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none"
                                placeholder="Lớp 3, Khối 12…">
                        </div>
                    </div>

                    {{-- Guardians Sub-section --}}
                    <div class="border-t border-slate-100 pt-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <i data-lucide="users" class="w-4 h-4 text-slate-500"></i>
                                <span class="text-sm font-semibold text-slate-600">Giám hộ / Người liên hệ</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(guardian, gIndex) in student.guardians" :key="gIndex">
                                <div class="bg-slate-50 rounded-lg border border-slate-100 p-3.5">
                                    {{-- Guardian Header --}}
                                    <div class="flex items-center justify-between mb-2.5">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-slate-600" x-text="'Giám hộ #' + (gIndex + 1)"></span>
                                            <span x-show="guardian.isLead" class="px-2 py-0.5 bg-primary-100 text-primary-700 text-[10px] font-bold uppercase rounded-full tracking-wide">Lead</span>
                                            <span x-show="guardian.customerId" class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold uppercase rounded-full tracking-wide flex items-center gap-1">
                                                <i data-lucide="link" class="w-3 h-3"></i> Đã có trong hệ thống
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            {{-- Select existing customer button --}}
                                            <button type="button" x-show="!guardian.customerId && !guardian.isLead"
                                                @click="openGuardianPicker(sIndex, gIndex)"
                                                class="px-2.5 py-1 text-xs font-medium text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-lg transition-all flex items-center gap-1" title="Chọn từ danh sách">
                                                <i data-lucide="search" class="w-3 h-3"></i>
                                                Chọn có sẵn
                                            </button>
                                            {{-- Clear selected customer --}}
                                            <button type="button" x-show="guardian.customerId && !guardian.isLead"
                                                @click="clearSelectedGuardian(sIndex, gIndex)"
                                                class="px-2.5 py-1 text-xs font-medium text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition-all flex items-center gap-1" title="Huỷ chọn">
                                                <i data-lucide="unlink" class="w-3 h-3"></i>
                                                Huỷ liên kết
                                            </button>
                                            {{-- Delete guardian --}}
                                            <button type="button" x-show="student.guardians.length > 1" @click="removeGuardian(sIndex, gIndex)"
                                                class="p-1 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-md transition-all" title="Xoá giám hộ">
                                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Hidden field for linked customer ID --}}
                                    <input type="hidden" x-show="guardian.customerId"
                                        :name="'students[' + sIndex + '][guardians][' + gIndex + '][customer_id]'"
                                        :value="guardian.customerId">

                                    {{-- Guardian Fields Row 1: Họ tên, SĐT, Email, Quan hệ --}}
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2.5">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Họ tên <span class="text-red-500">*</span></label>
                                            <input type="text" :name="'students[' + sIndex + '][guardians][' + gIndex + '][name]'" x-model="guardian.name" required
                                                :disabled="guardian.customerId"
                                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed"
                                                placeholder="Tên giám hộ…">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Số điện thoại <span class="text-red-500">*</span></label>
                                            <input type="text" :name="'students[' + sIndex + '][guardians][' + gIndex + '][phone]'" x-model="guardian.phone" required
                                                :disabled="guardian.customerId"
                                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none tabular-nums disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed"
                                                placeholder="0901234567">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Email</label>
                                            <input type="email" :name="'students[' + sIndex + '][guardians][' + gIndex + '][email]'" x-model="guardian.email"
                                                :disabled="guardian.customerId"
                                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed"
                                                placeholder="email@…">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Quan hệ <span class="text-red-500">*</span></label>
                                            <select :name="'students[' + sIndex + '][guardians][' + gIndex + '][relationship]'" x-model="guardian.relationship" required
                                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none">
                                                <option value="">Chọn…</option>
                                                <option value="Father">Cha</option>
                                                <option value="Mother">Mẹ</option>
                                                <option value="Grandparent">Ông/Bà</option>
                                                <option value="Guardian">Người giám hộ</option>
                                                <option value="Sibling">Anh/Chị</option>
                                                <option value="Other">Khác</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Guardian Fields Row 2: Ngày sinh, Giới tính, Địa chỉ --}}
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2.5 mt-2.5">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Ngày sinh</label>
                                            <input type="date" :name="'students[' + sIndex + '][guardians][' + gIndex + '][dob]'" x-model="guardian.dob"
                                                :disabled="guardian.customerId"
                                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Giới tính</label>
                                            <select :name="'students[' + sIndex + '][guardians][' + gIndex + '][gender]'" x-model="guardian.gender"
                                                :disabled="guardian.customerId"
                                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed">
                                                <option value="">Chọn…</option>
                                                <option value="MALE">Nam</option>
                                                <option value="FEMALE">Nữ</option>
                                                <option value="OTHER">Khác</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Địa chỉ</label>
                                            <input type="text" :name="'students[' + sIndex + '][guardians][' + gIndex + '][address]'" x-model="guardian.address"
                                                :disabled="guardian.customerId"
                                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed"
                                                placeholder="Số nhà, đường, phường…">
                                        </div>
                                    </div>

                                    {{-- Primary Guardian Radio (only 1 per student) --}}
                                    <div class="mt-2.5 flex items-center gap-2">
                                        <input type="checkbox" :id="'primary_' + sIndex + '_' + gIndex"
                                            :name="'students[' + sIndex + '][guardians][' + gIndex + '][is_primary]'"
                                            :checked="guardian.is_primary" value="1"
                                            @change="setPrimaryGuardian(sIndex, gIndex)"
                                            class="w-4 h-4 rounded border-slate-300 text-primary-500 focus:ring-primary-500/20">
                                        <label :for="'primary_' + sIndex + '_' + gIndex" class="text-xs text-slate-500 cursor-pointer">
                                            Giám hộ chính <span class="text-slate-400">(có quyền đăng nhập Portal)</span>
                                        </label>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Add Guardian Button --}}
                        <button type="button" @click="addGuardian(sIndex)"
                            class="mt-3 w-full py-2 border-2 border-dashed border-slate-200 rounded-xl text-sm font-medium text-slate-400 hover:text-slate-600 hover:border-slate-300 hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                            Thêm Giám hộ
                        </button>
                    </div>

                    {{-- Enrollment Sub-section (Optional) --}}
                    <div class="border-t border-slate-100 pt-4">
                        <div class="flex items-center gap-2 mb-3">
                            <i data-lucide="book-open" class="w-4 h-4 text-slate-500"></i>
                            <span class="text-sm font-semibold text-slate-600">Ghi danh</span>
                            <span class="text-xs text-slate-400 font-medium">(Tùy chọn)</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Chương trình</label>
                                <select class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none text-slate-500">
                                    <option value="">Chọn chương trình…</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Khóa học</label>
                                <select class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none text-slate-500">
                                    <option value="">Chọn khóa học…</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Lớp học</label>
                                <select class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none text-slate-500">
                                    <option value="">Chọn lớp học…</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Add Student Button --}}
        <button type="button" @click="addStudent()"
            class="w-full py-3.5 border-2 border-dashed border-slate-200 rounded-xl text-sm font-medium text-slate-400 hover:text-primary-600 hover:border-primary-300 hover:bg-primary-50/50 transition-all flex items-center justify-center gap-2 mb-5">
            <i data-lucide="plus-circle" class="w-5 h-5"></i>
            Thêm Học viên mới
        </button>

        {{-- Footer Actions --}}
        <div class="border-t border-slate-200 pt-4 flex items-center justify-end gap-4">
            <a href="{{ route('admin.leads.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-500 hover:text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all">
                Hủy bỏ
            </a>
            <button type="submit"
                class="px-7 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all shadow-lg shadow-primary-500/25 font-bold text-sm flex items-center gap-2 active:scale-95">
                <i data-lucide="zap" class="w-4 h-4"></i>
                Xác nhận & Hoàn tất Chuyển đổi
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
        </div>
    </form>

    {{-- ========== Guardian Picker Modal ========== --}}
    <template x-teleport="body">
        <div x-show="pickerOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="pickerOpen = false" x-transition.opacity></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto overflow-hidden"
                 x-show="pickerOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                            <i data-lucide="users" class="w-4 h-4"></i>
                        </div>
                        Chọn Giám hộ Đã Có
                    </h3>
                    <button type="button" @click="pickerOpen = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                {{-- Search Bar --}}
                <div class="px-6 py-3 border-b border-slate-100">
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" x-model="pickerSearch" @input.debounce.300ms="searchCustomers()"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all"
                            placeholder="Tìm kiếm theo tên hoặc số điện thoại...">
                    </div>
                </div>

                {{-- Results List --}}
                <div class="overflow-y-auto px-6 py-3" style="max-height: 340px; min-height: 200px;">
                    {{-- Loading --}}
                    <div x-show="pickerLoading" class="flex items-center justify-center py-10">
                        <div class="w-6 h-6 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
                        <span class="ml-2 text-sm text-slate-400">Đang tìm kiếm…</span>
                    </div>

                    {{-- Empty --}}
                    <div x-show="!pickerLoading && pickerResults.length === 0 && pickerSearch.length > 0" class="text-center py-10">
                        <i data-lucide="user-x" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                        <p class="text-sm text-slate-400">Không tìm thấy khách hàng nào.</p>
                    </div>

                    {{-- Prompt --}}
                    <div x-show="!pickerLoading && pickerResults.length === 0 && pickerSearch.length === 0" class="text-center py-10">
                        <i data-lucide="search" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                        <p class="text-sm text-slate-400">Nhập tên hoặc SĐT để tìm khách hàng.</p>
                    </div>

                    {{-- Customer Items --}}
                    <template x-for="(cust, cIdx) in pickerResults" :key="cust.id">
                        <div @click="pickerSelectedId = cust.id"
                            class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all mb-2"
                            :class="pickerSelectedId === cust.id ? 'border-primary-500 bg-primary-50/50' : 'border-transparent hover:bg-slate-50'">
                            {{-- Avatar --}}
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0"
                                :class="pickerSelectedId === cust.id ? 'bg-primary-100 text-primary-700' : 'bg-slate-100 text-slate-500'"
                                x-text="cust.name ? cust.name.charAt(0).toUpperCase() : '?'"></div>
                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <span class="font-semibold text-sm text-slate-800 truncate block" x-text="cust.name"></span>
                                <div class="flex items-center gap-3 text-xs text-slate-400 mt-0.5">
                                    <span x-show="cust.phone" class="flex items-center gap-1">
                                        <i data-lucide="phone" class="w-3 h-3"></i>
                                        <span x-text="cust.phone" class="tabular-nums"></span>
                                    </span>
                                    <span x-show="cust.email" class="flex items-center gap-1">
                                        <i data-lucide="mail" class="w-3 h-3"></i>
                                        <span x-text="cust.email" class="truncate"></span>
                                    </span>
                                </div>
                            </div>
                            {{-- Check --}}
                            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all"
                                :class="pickerSelectedId === cust.id ? 'border-primary-500 bg-primary-500' : 'border-slate-300'">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-white" x-show="pickerSelectedId === cust.id"></i>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 border-t border-slate-100 flex gap-3 justify-end bg-slate-50/30">
                    <button type="button" @click="pickerOpen = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Huỷ
                    </button>
                    <button type="button" @click="confirmGuardianPick()" :disabled="!pickerSelectedId"
                        class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-lucide="user-check" class="w-4 h-4"></i>
                        Chọn Giám hộ
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
function convertLeadApp() {
    return {
        // ── Student data ───────────────────────────────────────────────
        students: [
            {
                name: @json($lead->name),
                phone: @json($lead->phone),
                dob: @json($lead->dob ?? ''),
                gender: @json($lead->gender ?? 'MALE'),
                email: @json($lead->email ?? ''),
                address: '',
                school: '',
                grade: '',
                collapsed: false,
                guardians: [
                    {
                        name: @json($lead->name),
                        phone: @json($lead->phone),
                        email: @json($lead->email ?? ''),
                        dob: @json($lead->dob ?? ''),
                        gender: @json($lead->gender ?? ''),
                        address: '',
                        relationship: 'Mother',
                        is_primary: true,
                        isLead: true,
                        customerId: null
                    }
                ]
            }
        ],

        // ── Guardian Picker state ──────────────────────────────────────
        pickerOpen: false,
        pickerSearch: '',
        pickerResults: [],
        pickerLoading: false,
        pickerSelectedId: null,
        pickerStudentIndex: null,
        pickerGuardianIndex: null,

        // ── Student CRUD ───────────────────────────────────────────────
        addStudent() {
            const newStudent = {
                name: '', phone: '', dob: '', gender: '', email: '', address: '',
                school: '', grade: '', collapsed: false,
                guardians: [
                    { name: '', phone: '', email: '', dob: '', gender: '', address: '', relationship: '', is_primary: true, isLead: false, customerId: null }
                ]
            };

            // Use system confirm dialog instead of browser default
            if (this.students.length > 0 && this.students[0].guardians.length > 0) {
                const self = this;
                showConfirm({
                    title: 'Sao chép giám hộ',
                    message: 'Bạn có muốn sao chép giám hộ từ Học viên #1 sang học viên mới không?',
                    confirmText: 'Sao chép',
                    type: 'info'
                }).then(ok => {
                    if (ok) {
                        newStudent.guardians = self.students[0].guardians.map(g => ({...g}));
                    }
                    self.students.push(newStudent);
                    self.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
                });
            } else {
                this.students.push(newStudent);
                this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
            }
        },

        removeStudent(index) {
            if (this.students.length > 1) {
                this.students.splice(index, 1);
            }
        },

        // ── Guardian CRUD ──────────────────────────────────────────────
        addGuardian(studentIndex) {
            this.students[studentIndex].guardians.push({
                name: '', phone: '', email: '', dob: '', gender: '', address: '',
                relationship: '', is_primary: false, isLead: false, customerId: null
            });
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },

        removeGuardian(studentIndex, guardianIndex) {
            if (this.students[studentIndex].guardians.length > 1) {
                const wasPrimary = this.students[studentIndex].guardians[guardianIndex].is_primary;
                this.students[studentIndex].guardians.splice(guardianIndex, 1);
                if (wasPrimary) {
                    this.students[studentIndex].guardians[0].is_primary = true;
                }
            }
        },

        setPrimaryGuardian(studentIndex, guardianIndex) {
            this.students[studentIndex].guardians.forEach((g, i) => {
                g.is_primary = (i === guardianIndex);
            });
        },

        useLeadAsGuardian() {
            const leadGuardian = {
                name: @json($lead->name),
                phone: @json($lead->phone),
                email: @json($lead->email ?? ''),
                dob: @json($lead->dob ?? ''),
                gender: @json($lead->gender ?? ''),
                address: '',
                relationship: '',
                is_primary: true,
                isLead: true,
                customerId: null
            };

            this.students.forEach(student => {
                const exists = student.guardians.some(g => g.phone === leadGuardian.phone);
                if (!exists) {
                    student.guardians.push({...leadGuardian});
                }
            });

            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },

        // ── Guardian Picker ────────────────────────────────────────────
        openGuardianPicker(sIndex, gIndex) {
            this.pickerStudentIndex = sIndex;
            this.pickerGuardianIndex = gIndex;
            this.pickerSearch = '';
            this.pickerResults = [];
            this.pickerSelectedId = null;
            this.pickerOpen = true;
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },

        async searchCustomers() {
            if (this.pickerSearch.length < 1) {
                this.pickerResults = [];
                return;
            }

            this.pickerLoading = true;
            try {
                const response = await fetch(`/admin/customers/search-json?q=${encodeURIComponent(this.pickerSearch)}`);
                this.pickerResults = await response.json();
            } catch (e) {
                this.pickerResults = [];
            }
            this.pickerLoading = false;
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },

        confirmGuardianPick() {
            const selected = this.pickerResults.find(c => c.id === this.pickerSelectedId);
            if (!selected) return;

            const guardian = this.students[this.pickerStudentIndex].guardians[this.pickerGuardianIndex];
            guardian.customerId = selected.id;
            guardian.name = selected.name || '';
            guardian.phone = selected.phone || '';
            guardian.email = selected.email || '';
            guardian.dob = selected.dob || '';
            guardian.gender = selected.gender || '';
            guardian.address = selected.address || '';
            guardian.isLead = false;

            this.pickerOpen = false;
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },

        clearSelectedGuardian(sIndex, gIndex) {
            const guardian = this.students[sIndex].guardians[gIndex];
            guardian.customerId = null;
            guardian.name = '';
            guardian.phone = '';
            guardian.email = '';
            guardian.dob = '';
            guardian.gender = '';
            guardian.address = '';
            guardian.isLead = false;
        }
    };
}
</script>
@endpush
