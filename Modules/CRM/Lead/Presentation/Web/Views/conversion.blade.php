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
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Chuyển đổi Lead</h1>
            <p class="text-slate-500 mt-1">Lead: <span class="font-semibold text-primary-600">{{ $lead->name }}</span> ({{ $lead->phone }})</p>
        </div>
        <a href="{{ route('admin.leads.index') }}" class="text-slate-500 hover:text-slate-700 flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <form action="{{ route('admin.leads.convert.submit', $lead->getId()) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Guardian Info -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sticky top-24">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center text-primary-600">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                        </div>
                        <h2 class="text-lg font-bold text-slate-800">Người giám hộ</h2>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700">Họ tên <span class="text-red-500">*</span></label>
                            <input type="text" name="guardian[name]" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition" value="{{ old('guardian.name', $lead->name) }}">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="text" name="guardian[phone]" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition" value="{{ old('guardian.phone', $lead->phone) }}">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700">Email</label>
                            <input type="email" name="guardian[email]" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition" value="{{ old('guardian.email', $lead->email) }}">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700">Địa chỉ</label>
                            <textarea name="guardian[address]" rows="3" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition">{{ old('guardian.address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Student List -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                                <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                            </div>
                            <h2 class="text-lg font-bold text-slate-800">Danh sách Học viên</h2>
                        </div>
                        <button type="button" @click="addStudent" class="px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition flex items-center gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Thêm học viên
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="space-y-6">
                            <template x-for="(student, index) in students" :key="index">
                                <div class="relative p-6 rounded-2xl border border-slate-100 bg-slate-50/30 group animate-in fade-in slide-in-from-top-4 duration-300">
                                    <!-- Delete Button -->
                                    <button type="button" x-show="students.length > 1" @click="removeStudent(index)" class="absolute top-4 right-4 p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-1">
                                            <label class="text-sm font-medium text-slate-700">Họ tên học viên <span class="text-red-500">*</span></label>
                                            <input type="text" :name="`students[${index}][name]`" x-model="student.name" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition bg-white" placeholder="Nhập tên học viên">
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-sm font-medium text-slate-700">Ngày sinh</label>
                                            <input type="date" :name="`students[${index}][dob]`" x-model="student.dob" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition bg-white">
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-sm font-medium text-slate-700">Giới tính</label>
                                            <select :name="`students[${index}][gender]`" x-model="student.gender" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition bg-white">
                                                <option value="MALE">Nam</option>
                                                <option value="FEMALE">Nữ</option>
                                                <option value="OTHER">Khác</option>
                                            </select>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-sm font-medium text-slate-700">Mối quan hệ với giám hộ</label>
                                            <input type="text" :name="`students[${index}][relationship]`" x-model="student.relationship" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition bg-white" placeholder="Vd: Con, Cháu...">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="px-6 py-6 border-t border-slate-50 bg-slate-50/50 flex justify-end gap-3">
                        <a href="{{ route('admin.leads.index') }}" class="px-6 py-3 text-sm font-medium text-slate-600 hover:bg-white rounded-xl transition border border-transparent hover:border-slate-200">Hủy bỏ</a>
                        <button type="submit" class="px-8 py-3 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 font-bold flex items-center gap-2">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            Hoàn tất Chuyển đổi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
