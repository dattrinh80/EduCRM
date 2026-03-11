@extends('layouts.app')

@section('title', 'Chuyển đổi Lead thành Học viên')

@section('content')
<div class="max-w-5xl mx-auto" x-data="convertLeadApp()">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Chuyển đổi Lead</h1>
            <p class="text-sm text-slate-500 mt-1 flex items-center gap-2">
                <i data-lucide="user" class="w-4 h-4 text-primary-500"></i>
                Hồ sơ: <span class="font-semibold text-slate-700">{{ $lead->name }}</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span class="tabular-nums">{{ $lead->phone }}</span>
            </p>
        </div>
        <a href="{{ route('admin.leads.index') }}" class="px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm active:scale-95">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại danh sách
        </a>
    </div>

    {{-- Error Alert --}}
    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3 shadow-sm">
        <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
        </div>
        <span class="font-medium text-sm">{{ session('error') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl shadow-sm">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            <span class="font-semibold text-sm">Vui lòng kiểm tra lại thông tin:</span>
        </div>
        <ul class="ml-14 text-sm space-y-1 list-disc">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.leads.convert.submit', $lead->getId()) }}" method="POST">
        @csrf

        {{-- Lead Summary Bar --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center text-lg font-bold flex-shrink-0">
                        {{ mb_substr($lead->name, 0, 1) }}
                    </div>
                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
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
                <button type="button" @click="useLeadAsGuardian()" class="px-4 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-primary-500/25 hover:from-primary-600 hover:to-primary-700 transition-all active:scale-95 flex items-center gap-2 whitespace-nowrap">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Dùng Lead làm Giám hộ
                </button>
            </div>
        </div>

        {{-- Student Cards --}}
        <template x-for="(student, sIndex) in students" :key="sIndex">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-5 overflow-hidden">
                {{-- Student Card Header --}}
                <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5 cursor-pointer" @click="student.collapsed = !student.collapsed">
                        <i data-lucide="graduation-cap" class="w-5 h-5 text-primary-500"></i>
                        <span class="font-semibold text-slate-700 text-sm" x-text="'Học viên #' + (sIndex + 1)"></span>
                        <span class="text-xs text-slate-400 font-medium" x-show="student.name" x-text="'— ' + student.name"></span>
                    </div>
                    <div class="flex items-center gap-2">
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
                <div x-show="!student.collapsed" x-transition class="p-5 space-y-5">
                    {{-- Student Info --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Họ và tên <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i data-lucide="user" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" :name="'students[' + sIndex + '][name]'" x-model="student.name" required
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none"
                                    placeholder="Nhập họ và tên…">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Ngày sinh <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i data-lucide="calendar" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="date" :name="'students[' + sIndex + '][dob]'" x-model="student.dob"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Giới tính</label>
                            <div class="flex items-center gap-1 mt-1">
                                <template x-for="g in [{val:'MALE',label:'Nam'},{val:'FEMALE',label:'Nữ'},{val:'OTHER',label:'Khác'}]" :key="g.val">
                                    <label class="relative cursor-pointer">
                                        <input type="radio" :name="'students[' + sIndex + '][gender]'" :value="g.val" x-model="student.gender" class="sr-only peer">
                                        <span class="block px-4 py-2.5 text-sm font-medium rounded-xl border transition-all
                                            peer-checked:bg-primary-50 peer-checked:border-primary-500 peer-checked:text-primary-700
                                            border-slate-200 text-slate-500 hover:bg-slate-50" x-text="g.label"></span>
                                    </label>
                                </template>
                            </div>
                            <input type="hidden" :name="'students[' + sIndex + '][gender]'" x-model="student.gender">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Trường đang học</label>
                            <div class="relative">
                                <i data-lucide="building" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" :name="'students[' + sIndex + '][school]'" x-model="student.school"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none"
                                    placeholder="Tên trường học…">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Khối / Lớp</label>
                            <div class="relative">
                                <i data-lucide="hash" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" :name="'students[' + sIndex + '][grade]'" x-model="student.grade"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none"
                                    placeholder="Ví dụ: Lớp 3, Khối 12…">
                            </div>
                        </div>
                    </div>

                    {{-- Guardians Sub-section --}}
                    <div class="border-t border-slate-100 pt-5">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <i data-lucide="users" class="w-4 h-4 text-slate-500"></i>
                                <span class="text-sm font-semibold text-slate-600">Giám hộ / Người liên hệ</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(guardian, gIndex) in student.guardians" :key="gIndex">
                                <div class="bg-slate-50 rounded-lg border border-slate-100 p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-slate-600" x-text="'Giám hộ #' + (gIndex + 1)"></span>
                                            <span x-show="guardian.isLead" class="px-2 py-0.5 bg-primary-100 text-primary-700 text-[10px] font-bold uppercase rounded-full tracking-wide">Lead</span>
                                        </div>
                                        <button type="button" x-show="student.guardians.length > 1" @click="removeGuardian(sIndex, gIndex)"
                                            class="p-1 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-md transition-all" title="Xoá giám hộ">
                                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Họ tên <span class="text-red-500">*</span></label>
                                            <input type="text" :name="'students[' + sIndex + '][guardians][' + gIndex + '][name]'" x-model="guardian.name" required
                                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none"
                                                placeholder="Tên giám hộ…">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Số điện thoại <span class="text-red-500">*</span></label>
                                            <input type="text" :name="'students[' + sIndex + '][guardians][' + gIndex + '][phone]'" x-model="guardian.phone" required
                                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none tabular-nums"
                                                placeholder="0901234567">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Email</label>
                                            <input type="email" :name="'students[' + sIndex + '][guardians][' + gIndex + '][email]'" x-model="guardian.email"
                                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none"
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

                                    {{-- Primary Guardian Radio (only 1 per student) --}}
                                    <div class="mt-3 flex items-center gap-2">
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
                            class="mt-3 w-full py-2.5 border-2 border-dashed border-slate-200 rounded-xl text-sm font-medium text-slate-400 hover:text-slate-600 hover:border-slate-300 hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                            Thêm Giám hộ
                        </button>
                    </div>

                    {{-- Enrollment Sub-section (Optional) --}}
                    <div class="border-t border-slate-100 pt-5">
                        <div class="flex items-center gap-2 mb-4">
                            <i data-lucide="book-open" class="w-4 h-4 text-slate-500"></i>
                            <span class="text-sm font-semibold text-slate-600">Ghi danh</span>
                            <span class="text-xs text-slate-400 font-medium">(Tùy chọn)</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Chương trình</label>
                                <select class="w-full px-3 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none text-slate-500">
                                    <option value="">Chọn chương trình…</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Khóa học</label>
                                <select class="w-full px-3 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none text-slate-500">
                                    <option value="">Chọn khóa học…</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Lớp học</label>
                                <select class="w-full px-3 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none text-slate-500">
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
            class="w-full py-4 border-2 border-dashed border-slate-200 rounded-xl text-sm font-medium text-slate-400 hover:text-primary-600 hover:border-primary-300 hover:bg-primary-50/50 transition-all flex items-center justify-center gap-2 mb-6">
            <i data-lucide="plus-circle" class="w-5 h-5"></i>
            Thêm Học viên mới
        </button>

        {{-- Footer Actions --}}
        <div class="border-t border-slate-200 pt-5 flex items-center justify-end gap-4">
            <a href="{{ route('admin.leads.index') }}" class="px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all">
                Hủy bỏ giao dịch
            </a>
            <button type="submit"
                class="px-8 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all shadow-lg shadow-primary-500/25 font-bold text-sm flex items-center gap-2.5 active:scale-95">
                <i data-lucide="zap" class="w-4 h-4"></i>
                Xác nhận & Hoàn tất Chuyển đổi
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function convertLeadApp() {
    return {
        students: [
            {
                name: @json($lead->name),
                dob: @json($lead->dob ?? ''),
                gender: @json($lead->gender ?? 'MALE'),
                school: '',
                grade: '',
                collapsed: false,
                guardians: [
                    {
                        name: @json($lead->name),
                        phone: @json($lead->phone),
                        email: @json($lead->email ?? ''),
                        relationship: 'Mother',
                        is_primary: true,
                        isLead: true
                    }
                ]
            }
        ],

        addStudent() {
            const newStudent = {
                name: '',
                dob: '',
                gender: 'MALE',
                school: '',
                grade: '',
                collapsed: false,
                guardians: [
                    { name: '', phone: '', email: '', relationship: '', is_primary: true, isLead: false }
                ]
            };

            // Offer to reuse guardians from the first student
            if (this.students.length > 0 && this.students[0].guardians.length > 0) {
                if (confirm('Bạn có muốn sao chép giám hộ từ Học viên #1 không?')) {
                    newStudent.guardians = this.students[0].guardians.map(g => ({...g}));
                }
            }

            this.students.push(newStudent);

            // Re-init Lucide icons after DOM update
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },

        removeStudent(index) {
            if (this.students.length > 1) {
                this.students.splice(index, 1);
            }
        },

        addGuardian(studentIndex) {
            this.students[studentIndex].guardians.push({
                name: '', phone: '', email: '', relationship: '', is_primary: false, isLead: false
            });
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },

        removeGuardian(studentIndex, guardianIndex) {
            if (this.students[studentIndex].guardians.length > 1) {
                const wasPrimary = this.students[studentIndex].guardians[guardianIndex].is_primary;
                this.students[studentIndex].guardians.splice(guardianIndex, 1);
                // If removed guardian was primary, make the first one primary
                if (wasPrimary) {
                    this.students[studentIndex].guardians[0].is_primary = true;
                }
            }
        },

        setPrimaryGuardian(studentIndex, guardianIndex) {
            // Only one primary guardian per student
            this.students[studentIndex].guardians.forEach((g, i) => {
                g.is_primary = (i === guardianIndex);
            });
        },

        useLeadAsGuardian() {
            const leadGuardian = {
                name: @json($lead->name),
                phone: @json($lead->phone),
                email: @json($lead->email ?? ''),
                relationship: '',
                is_primary: true,
                isLead: true
            };

            // Add to all students that don't already have a guardian with this phone
            this.students.forEach(student => {
                const exists = student.guardians.some(g => g.phone === leadGuardian.phone);
                if (!exists) {
                    student.guardians.push({...leadGuardian});
                }
            });

            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        }
    };
}
</script>
@endpush
