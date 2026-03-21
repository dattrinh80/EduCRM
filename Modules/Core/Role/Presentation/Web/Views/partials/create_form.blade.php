<div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
            <i data-lucide="shield" class="w-4 h-4"></i>
        </div>
        Thêm Role Mới
    </h3>
    <button type="button" @click="showDynamicModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
</div>

<div class="p-6 overflow-y-auto flex-1" x-data="{
    perms: {{ json_encode(!old('_method') ? old('permissions', []) : []) }},
    groupPermIds: {{ json_encode($groupPermIds) }},
    allPermIds: {{ json_encode($allPermIds) }},
    toggleGroup(groupId, checked) {
        const groupIds = this.groupPermIds[groupId];
        if (checked) {
            const set = new Set([...this.perms, ...groupIds]);
            this.perms = Array.from(set);
        } else {
            this.perms = this.perms.filter(id => !groupIds.includes(id));
        }
    },
    toggleAll(checked) {
        this.perms = checked ? [...this.allPermIds] : [];
    },
    isGroupFullyChecked(groupId) {
        const groupIds = this.groupPermIds[groupId] || [];
        return groupIds.length > 0 && groupIds.every(id => this.perms.includes(id));
    }
}">
    <form action="{{ route('admin.roles.store') }}" method="POST" id="createRoleForm">
        @csrf
        <div class="space-y-6">
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block">Tên Role (Role Name) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ !old('_method') ? old('name') : '' }}" placeholder="Vd: Admin Center, Giáo viên..">
                </div>
                @if(!old('_method')) @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
            </div>

            <!-- Permissions by Group -->
            <div class="space-y-4 mt-6">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <label class="text-base font-semibold text-slate-800">Cấp quyền (Permissions)</label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-primary-600 font-medium">
                        <input type="checkbox" :checked="perms.length === allPermIds.length && allPermIds.length > 0" @change="toggleAll($event.target.checked)" class="w-4 h-4 rounded border-slate-300 text-primary-500 focus:ring-primary-500 bg-white">
                        Chọn tất cả
                    </label>
                </div>

                <div class="space-y-3">
                    @foreach ($permissionGroups as $group)
                    <div class="border border-slate-100 rounded-xl overflow-hidden bg-white shadow-sm">
                        <div class="bg-slate-50 px-5 py-3 flex items-center justify-between border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                    <i data-lucide="folder" class="w-3.5 h-3.5"></i>
                                </div>
                                <span class="font-semibold text-slate-700 text-sm">{{ $group->name }}</span>
                            </div>
                            <label class="flex items-center gap-1.5 cursor-pointer text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <input type="checkbox" :checked="isGroupFullyChecked('{{ $group->id }}')" @change="toggleGroup('{{ $group->id }}', $event.target.checked)" class="w-3.5 h-3.5 rounded border-slate-300 text-primary-500 focus:ring-primary-500 bg-white">
                                Cấp hết nhóm này
                            </label>
                        </div>
                        <div class="px-5 py-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            @foreach ($group->permissions as $perm)
                            <label class="flex items-start gap-2.5 cursor-pointer p-2 rounded-lg hover:bg-slate-50 transition">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" x-model="perms" class="w-4 h-4 rounded border-slate-300 text-primary-500 focus:ring-primary-500 mt-0.5 bg-white">
                                <div>
                                    <span class="text-sm font-medium text-slate-700 block">{{ Str::afterLast($perm->name, '.') }}</span>
                                    @if($perm->description) <span class="text-xs text-slate-400 block mt-0.5 leading-tight">{{ $perm->description }}</span> @endif
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </form>
</div> 

<div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex gap-3 justify-end shrink-0">
    <button type="button" @click="showDynamicModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-200 rounded-xl transition">Hủy</button>
    <button type="submit" form="createRoleForm" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
        <i data-lucide="check" class="w-4 h-4"></i> Tạo Role
    </button>
</div>
