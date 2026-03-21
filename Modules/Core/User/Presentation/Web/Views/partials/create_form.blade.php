<div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
        </div>
        Tạo Tài khoản Mới
    </h3>
    <button type="button" @click="showDynamicModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
</div>

<div class="p-6 overflow-y-auto flex-1" x-data="{ 
    assignedRoles: {{ json_encode(old('roles', [])) }} 
}">
    <form action="{{ route('admin.users.store') }}" method="POST" id="createForm">
        @csrf
        <div class="space-y-6">
            <!-- Thong tin ca nhan -->
            <div class="grid grid-cols-2 gap-5 p-5 bg-slate-50/50 rounded-xl border border-slate-100">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 block">Họ và tên <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="user" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('name') }}" placeholder="Nguyễn Văn A">
                    </div>
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 block">Email <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="email" name="email" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('email') }}" placeholder="email@domain.com">
                    </div>
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 block">Mật khẩu <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="password" name="password" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" autocomplete="new-password">
                    </div>
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 block">Nhập lại mật khẩu <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="password" name="password_confirmation" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">
                    </div>
                </div>
                <div class="space-y-1 col-span-2">
                    <label class="text-sm font-medium text-slate-700 block">Cơ sở mặc định</label>
                    <div class="relative">
                        <i data-lucide="building-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <select name="default_center_id" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                            <option value="">-- Không gán cơ sở mặc định --</option>
                            @foreach($centers as $c)
                                <option value="{{ $c->id }}" {{ old('default_center_id') === $c->id ? 'selected' : '' }}>[{{ $c->code }}] {{ $c->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                    </div>
                    @error('default_center_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Roles -->
            <div>
                <div class="flex items-center justify-between xl mb-3">
                    <label class="text-base font-semibold text-slate-800">Phân quyền (Roles) & Phạm vi (Scopes)</label>
                    <button type="button" @click="assignedRoles.push({role_id: '', scope_type: 'SYSTEM', scope_id: ''}); $nextTick(() => { if (window.lucide) { lucide.createIcons(); } });" class="px-3 py-1.5 bg-primary-50 text-primary-600 rounded-lg font-medium text-sm hover:bg-primary-100 transition flex items-center gap-1.5">
                        <i data-lucide="plus" class="w-4 h-4"></i> Thêm quyền
                    </button>
                </div>
                
                <div class="space-y-3">
                    <template x-for="(role, index) in assignedRoles" :key="index">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-start gap-3 p-4 bg-white rounded-xl border border-slate-200 shadow-sm relative">
                            <div class="flex-1 space-y-1">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Vai trò (Role)</label>
                                <select x-model="role.role_id" :name="'roles['+index+'][role_id]'" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 bg-white text-slate-700">
                                    <option value="">-- Chọn Role --</option>
                                    @foreach($roles as $r)
                                        @php
                                            $canManageSystemOwner = app(\Modules\Core\User\Application\Services\AuthorizationServiceInterface::class)->hasPermission(auth()->id() ?? '', 'MANAGE_SYSTEM_OWNER', 'SYSTEM');
                                        @endphp
                                        <option value="{{ $r->id }}"
                                            x-show="'{{ $r->name }}' !== 'SYSTEM_OWNER' || {{ $canManageSystemOwner ? 'true' : 'false' }} || role.role_id === '{{ $r->id }}'"
                                            :disabled="'{{ $r->name }}' === 'SYSTEM_OWNER' && !{{ $canManageSystemOwner ? 'true' : 'false' }} && role.role_id !== '{{ $r->id }}'"
                                        >{{ $r->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-full sm:w-40 space-y-1 shrink-0">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Phạm vi (Scope)</label>
                                <select x-model="role.scope_type" :name="'roles['+index+'][scope_type]'" @change="if(role.scope_type === 'SYSTEM') role.scope_id = ''" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 bg-white text-slate-700 font-medium">
                                    <option value="SYSTEM">Toàn quyền (SYSTEM)</option>
                                    <option value="CENTER">Theo Cơ sở (CENTER)</option>
                                </select>
                            </div>
                            <div class="flex-1 space-y-1" x-show="role.scope_type === 'CENTER'">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Chọn Cơ sở</label>
                                <select x-model="role.scope_id" :name="'roles['+index+'][scope_id]'" :required="role.scope_type === 'CENTER'" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 bg-white text-slate-700">
                                    <option value="">-- Chọn Cơ sở --</option>
                                    @foreach($centers as $c)
                                        <option value="{{ $c->id }}">[{{ $c->code }}] {{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end justify-end shrink-0 sm:pt-5">
                                <button type="button" @click="assignedRoles.splice(index, 1)" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Xoá">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                    <div x-show="assignedRoles.length === 0" class="p-6 text-center border-2 border-dashed border-slate-200 rounded-xl bg-slate-50">
                        <p class="text-sm text-slate-400 font-medium">Chưa cấp quyền nào, hãy bấm Thêm quyền ở trên.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div> <!-- /body -->

<div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex gap-3 justify-end shrink-0">
    <button type="button" @click="showDynamicModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-200 rounded-xl transition">Hủy</button>
    <button type="submit" form="createForm" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
        <i data-lucide="check" class="w-4 h-4"></i> Khởi tạo
    </button>
</div>
