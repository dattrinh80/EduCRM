@extends('layouts.app')

@section('title', 'Chuyển đổi Lead thành Học viên')

@section('content')
<div class="max-w-5xl mx-auto" x-data="{ 
    students: [
        { name: '{{ $lead->name }}', dob: '{{ $lead->dob }}', gender: 'MALE', relationship: 'Child' }
    ],
    addStudent() {
        this.students.push({ name: '', dob: '', gender: 'MALE', relationship: 'Child' });
    },
    removeStudent(index) {
        if (this.students.length > 1) {
            this.students.splice(index, 1);
        }
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Chuyển đổi Lead</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-2">
                <i data-lucide="user" class="w-4 h-4 text-primary-500"></i>
                Hồ sơ: <span class="font-bold text-slate-700">{{ $lead->name }}</span> 
                <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                <span class="tabular-nums text-slate-500">{{ $lead->phone }}</span>
            </p>
        </div>
        <a href="{{ route('admin.leads.index') }}" class="px-5 py-2.5 text-slate-600 hover:text-slate-900 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm active:scale-95 font-medium">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại danh sách
        </a>
    </div>

    @if(session('error'))
    <div class="mb-8 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl flex items-center gap-4 shadow-sm slide-down">
        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
        </div>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif

    <form action="{{ route('admin.leads.convert.submit', $lead->getId()) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left Column: Guardian Info -->
            <div class="lg:col-span-4">
                <div class="bg-white rounded-3xl border border-slate-100 shadow-premium p-8 sticky top-28">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center shadow-inner">
                            <i data-lucide="user-plus" class="w-6 h-6"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800 tracking-tight">Người giám hộ</h2>
                    </div>

                    <div class="space-y-5">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Họ tên <span class="text-red-500">*</span></label>
                            <input type="text" name="guardian[name]" required class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none font-medium text-slate-800" value="{{ old('guardian.name', $lead->name) }}" placeholder="Họ và tên phụ huynh…">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="text" name="guardian[phone]" required class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none tabular-nums font-medium text-slate-800" value="{{ old('guardian.phone', $lead->phone) }}" placeholder="090…">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email</label>
                            <input type="email" name="guardian[email]" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none font-medium text-slate-800" value="{{ old('guardian.email', $lead->email) }}" placeholder="email@example.com…">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Địa chỉ thường trú</label>
                            <textarea name="guardian[address]" rows="4" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none font-medium text-slate-800" placeholder="Số nhà, đường, phường/xã…">{{ old('guardian.address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Student List -->
            <div class="lg:col-span-8 space-y-8">
                <div class="bg-white rounded-3xl border border-slate-100 shadow-premium overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-inner">
                                <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-800 tracking-tight">Danh sách Học viên</h2>
                                <p class="text-xs text-slate-400 font-medium">Bạn có thể thêm nhiều học viên cùng lúc</p>
                            </div>
                        </div>
                        <button type="button" @click="addStudent" class="px-5 py-2.5 text-sm font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-all flex items-center gap-2 active:scale-95">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            Thêm học viên
                        </button>
                    </div>

                    <div class="p-8">
                        <div class="space-y-8">
                            <template x-for="(student, index) in students" :key="index">
                                <div class="relative p-8 rounded-[2rem] border border-slate-100 bg-slate-50/50 group animate-in fade-in slide-in-from-top-6 duration-500">
                                    <!-- Delete Button -->
                                    <button type="button" x-show="students.length > 1" @click="removeStudent(index)" class="absolute top-6 right-6 p-2.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-2xl transition-all group-hover:scale-110 active:scale-90">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <div class="space-y-1.5">
                                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Họ tên học viên <span class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="type" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                                <input type="text" :name="`students[${index}][name]`" x-model="student.name" required class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-bold text-slate-700" placeholder="Nhập tên học viên…">
                                            </div>
                                        </div>
                                        <div class="space-y-1.5">
                                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Ngày sinh</label>
                                            <div class="relative">
                                                <i data-lucide="calendar" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                                <input type="date" :name="`students[${index}][dob]`" x-model="student.dob" class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-medium text-slate-700">
                                            </div>
                                        </div>
                                        <div class="space-y-1.5">
                                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Giới tính</label>
                                            <div class="relative">
                                                <i data-lucide="users" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"></i>
                                                <select :name="`students[${index}][gender]`" x-model="student.gender" class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none appearance-none font-bold text-slate-700">
                                                    <option value="MALE">Nam</option>
                                                    <option value="FEMALE">Nữ</option>
                                                    <option value="OTHER">Khác</option>
                                                </select>
                                                <i data-lucide="chevron-down" class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"></i>
                                            </div>
                                        </div>
                                        <div class="space-y-1.5">
                                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Mối quan hệ với giám hộ</label>
                                            <div class="relative">
                                                <i data-lucide="heart" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                                <input type="text" :name="`students[${index}][relationship]`" x-model="student.relationship" class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-medium text-slate-700" placeholder="Vd: Con, Cháu, Anh/Chị…">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="px-8 py-8 border-t border-slate-50 bg-slate-50/50 flex justify-end items-center gap-6">
                        <a href="{{ route('admin.leads.index') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Hủy bỏ giao dịch</a>
                        <button type="submit" class="px-10 py-4 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-2xl hover:from-primary-600 hover:to-primary-700 transition-all shadow-2xl shadow-primary-500/30 font-bold flex items-center gap-3 active:scale-95 group">
                            <span>Xác nhận & Hoàn tất Chuyển đổi</span>
                            <i data-lucide="chevron-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
