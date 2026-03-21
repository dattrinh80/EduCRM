<div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
        <div class="p-1.5 bg-primary-100 text-primary-600 rounded-lg">
            <i data-lucide="edit-3" class="w-5 h-5"></i>
        </div>
        Chỉnh sửa Học viên
    </h3>
    <button type="button" @click="showDynamicModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
</div>

<form action="{{ route('admin.students.update', $student->id) }}" method="POST" id="editStudentForm" class="flex-1 flex flex-col overflow-hidden">
    @csrf
    @method('PUT')
    
    <div class="p-6 flex-1 overflow-y-auto space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Left Column: Student Info -->
            <div class="md:col-span-2 space-y-8">
                <div class="space-y-6">
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

                <!-- Guardian Info (Dynamic) -->
                @php
                    $guardiansData = $student->guardians->map(function($g) {
                        return [
                            'name' => $g->name,
                            'phone' => $g->phone,
                            'relationship' => $g->pivot?->relationship ?? 'Parent',
                            'is_primary' => (bool) ($g->pivot?->is_primary)
                        ];
                    })->toArray();
                    
                    if (empty($guardiansData)) {
                        $guardiansData = [['name' => '', 'phone' => '', 'relationship' => 'Parent', 'is_primary' => true]];
                    }
                @endphp
                <div x-data="{ 
                    guardians: {{ json_encode(old('guardians', $guardiansData)) }},
                    addGuardian() {
                        this.guardians.push({ name: '', phone: '', relationship: 'Guardian', is_primary: false });
                        this.$nextTick(() => { if(window.lucide) lucide.createIcons(); });
                    },
                    removeGuardian(index) {
                        this.guardians.splice(index, 1);
                    }
                }" class="mt-8 pt-8 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2 text-slate-800 font-bold tracking-tight">
                            <div class="p-1.5 bg-orange-50 text-orange-600 rounded-lg">
                                <i data-lucide="users" class="w-4 h-4"></i>
                            </div>
                            Thông tin người giám hộ
                        </div>
                        <button type="button" @click="addGuardian()" class="px-3 py-1.5 text-xs font-bold text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-lg transition flex items-center gap-1.5">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            Thêm người giám hộ
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(guardian, index) in guardians" :key="index">
                            <div class="p-5 bg-slate-50/50 rounded-2xl border border-slate-100 space-y-4 relative group hover:bg-white hover:shadow-sm transition-all duration-300">
                                <button type="button" @click="removeGuardian(index)" 
                                    class="absolute top-3 right-3 text-slate-300 hover:text-red-500 transition-colors"
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
                                            <option value="Parent" :selected="guardian.relationship === 'Parent'">Cha/Mẹ</option>
                                            <option value="Sibling" :selected="guardian.relationship === 'Sibling'">Anh/Chị/Em</option>
                                            <option value="Grandparent" :selected="guardian.relationship === 'Grandparent'">Ông/Bà</option>
                                            <option value="Other" :selected="guardian.relationship === 'Other'">Khác</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center h-full pt-6">
                                        <label class="flex items-center gap-2 cursor-pointer group/label">
                                            <input type="checkbox" ::name=\"'guardians[' + index + '][is_primary]'\" value="1" :checked="guardian.is_primary" class="w-4 h-4 text-primary-600 rounded border-slate-300 focus:ring-primary-500 transition-all">
                                            <span class="text-sm text-slate-600 font-medium group-hover/label:text-primary-600 transition-colors">Liên hệ chính</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings -->
            <div class="space-y-6">
                <!-- Status -->
                <div class="p-5 bg-indigo-50/30 rounded-2xl border border-indigo-100 space-y-4">
                    <div class="flex items-center gap-2 text-slate-800 font-bold tracking-tight mb-2">
                        <div class="p-1.5 bg-indigo-100 text-indigo-600 rounded-lg">
                            <i data-lucide="info" class="w-4 h-4"></i>
                        </div>
                        Trạng thái
                    </div>
                    <div class="space-y-1.5">
                        <select name="status" required class="w-full h-10 px-3 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-sm outline-none">
                            <option value="NEW" {{ old('status', $student->status) == 'NEW' ? 'selected' : '' }}>Mới</option>
                            <option value="ACTIVE" {{ old('status', $student->status) == 'ACTIVE' ? 'selected' : '' }}>Đang học</option>
                            <option value="DROPPED" {{ old('status', $student->status) == 'DROPPED' ? 'selected' : '' }}>Thôi học</option>
                            <option value="GRADUATED" {{ old('status', $student->status) == 'GRADUATED' ? 'selected' : '' }}>Tốt nghiệp</option>
                        </select>
                    </div>
                </div>

                <!-- Center Settings -->
                <div class="p-5 bg-slate-50/50 rounded-2xl border border-slate-100 space-y-4">
                    <div class="flex items-center gap-2 text-slate-800 font-bold tracking-tight mb-2">
                        <div class="p-1.5 bg-slate-100 text-slate-600 rounded-lg">
                            <i data-lucide="settings" class="w-4 h-4"></i>
                        </div>
                        Gán cơ sở
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Trung tâm quản lý</label>
                            <select name="center_id" required class="w-full h-10 px-3 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-sm outline-none font-medium">
                                @foreach($centers as $center)
                                    <option value="{{ $center->id }}" {{ old('center_id', $student->customer?->center_id) == $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <x-ui.input 
                            name="student_code" 
                            label="Mã học viên" 
                            placeholder="Mã số học viên" 
                            value="{{ old('student_code', $student->student_code) }}"
                            bgClass="bg-white"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-4 border-t border-slate-100 flex gap-3 justify-end shrink-0 bg-slate-50/80">
        <x-ui.button type="button" variant="ghost" @click="showDynamicModal = false">
            Hủy bỏ
        </x-ui.button>
        <x-ui.button type="submit" variant="primary">
            Cập nhật học viên
        </x-ui.button>
    </div>
</form>

<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>of lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
