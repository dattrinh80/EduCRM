@extends('layouts.app')

@section('title', 'Phân quyền (Roles)')

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }} }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight">Quản lý Phân quyền</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-1.5 text-sm">
                <i data-lucide="info" class="w-3.5 h-3.5 opacity-60"></i>
                Quản lý các nhóm quyền (Roles) và chi tiết quyền (Permissions) của hệ thống
            </p>
        </div>
        @can('roles.create')
        <x-ui.button variant="primary" icon="plus-circle" @click="showCreateModal = true; $dispatch('refresh-icons')">
            Thêm Role mới
        </x-ui.button>
        @endcan
    </div>


    <!-- Search -->
    <x-ui.card bodyClass="p-4">
        <form action="{{ route('admin.roles.index') }}" method="GET" class="flex items-center gap-3">
            <x-ui.input name="search" id="search" placeholder="Tìm kiếm tên role..." value="{{ $search ?? '' }}" icon="search" containerClass="flex-1" />
            
            <x-ui.button type="submit" variant="secondary" icon="filter">
                Lọc
            </x-ui.button>
            @if (!empty($search))
                <x-ui.button variant="ghost" icon="x-circle" :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.roles.index'), 'tag' => 'a'])">
                    Xoá lọc
                </x-ui.button>
            @endif
        </form>
    </x-ui.card>


    @php
        // Prepare global data once for Alpine JS
        $groupPermIds = [];
        $allPermIds = [];
        foreach ($permissionGroups as $group) {
            $ids = $group->permissions->pluck('id')->toArray();
            $groupPermIds[$group->id] = $ids;
            $allPermIds = array_merge($allPermIds, $ids);
        }
    @endphp

    <!-- Roles Grid -->
    <x-ui.card bodyClass="p-0">
        @if($roles->isEmpty())
            <x-ui.empty-state 
                title="Chưa có Role nào"
                description="Hệ thống chưa có nhóm quyền nào được định nghĩa."
                icon="shield"
                actionText="Tạo Role mới"
                actionClick="showCreateModal = true; $dispatch('refresh-icons')"
            />
        @else

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="p-4 px-6">Tên Role</th>
                        <th class="p-4 px-6">Số quyền</th>
                        <th class="p-4 px-6">Ngày tạo</th>
                        <th class="p-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($roles as $role)
                        <tr class="hover:bg-slate-50 transition group" x-data="{ showEditModal: {{ $errors->any() && old('_method') == 'PUT' && old('role_id') == $role->id ? 'true' : 'false' }} }">
                            <td class="p-4 px-6 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-violet-100 text-violet-600 flex items-center justify-center">
                                        <i data-lucide="shield" class="w-4 h-4"></i>
                                    </div>
                                    <span class="font-medium text-slate-800">{{ $role->name }}</span>
                                </div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $role->permissions_count ?? 0 }} quyền
                                </span>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-sm text-slate-500">
                                {{ $role->created_at?->format('d/m/Y') }}
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                    @if(!$role->is_system_role)
                                        @can('roles.update')
                                        <button type="button" @click="showEditModal = true; $dispatch('refresh-icons')" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Sửa">
                                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        </button>
                                        @endcan
                                        @can('roles.delete')
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(this, '{{ addslashes($role->name) }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Xoá">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    @else
                                        <span class="text-xs text-slate-400 bg-slate-100 px-2 py-1 rounded-lg">System Role</span>
                                    @endif
                                </div>

                                <!-- Edit Modal -->
                                @if(!$role->is_system_role)
                                    @can('roles.update')
                                <template x-teleport="body">
                                    <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                                        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showEditModal = false" x-transition.opacity></div>
                                        
                                        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col mx-auto overflow-hidden text-left" 
                                             x-show="showEditModal" 
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave="transition ease-in duration-200"
                                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                                             
                                            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
                                                <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                                                    <div class="w-8 h-8 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center">
                                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                                    </div>
                                                    Sửa Role: {{ $role->name }}
                                                </h3>
                                                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                                                    <i data-lucide="x" class="w-5 h-5"></i>
                                                </button>
                                            </div>

                                            <div class="p-6 overflow-y-auto" x-data="{
                                                perms: {{ json_encode(old('role_id') == $role->id ? old('permissions', []) : $role->permissions->pluck('id')->toArray()) }},
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
                                                <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" id="editRoleForm_{{ $role->id }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="role_id" value="{{ $role->id }}">
                                                    
                                                    <div class="space-y-6">
                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block">Tên Role (Role Name) <span class="text-red-500">*</span></label>
                                                            <div class="relative">
                                                                <input type="text" name="name" required class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('role_id') == $role->id ? old('name') : $role->name }}">
                                                            </div>
                                                            @if(old('role_id') == $role->id) @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
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
                                                <button type="button" @click="showEditModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-200 rounded-xl transition">Hủy</button>
                                                <button type="submit" form="editRoleForm_{{ $role->id }}" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
                                                    <i data-lucide="save" class="w-4 h-4"></i> Cập nhật Role
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @endif
    </x-ui.card>


    <!-- Create Modal -->
    @can('roles.create')
    <template x-teleport="body">
        <div x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showCreateModal = false" x-transition.opacity></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col mx-auto overflow-hidden text-left" 
                 x-show="showCreateModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                 
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                        </div>
                        Thêm Role Mới
                    </h3>
                    <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto" x-data="{
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
                    <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-200 rounded-xl transition">Hủy</button>
                    <button type="submit" form="createRoleForm" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
                        <i data-lucide="check" class="w-4 h-4"></i> Tạo Role
                    </button>
                </div>
            </div>
        </div>
    </template>
    @endcan
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        window.addEventListener('refresh-icons', () => {
            setTimeout(() => {
                if (window.lucide) { lucide.createIcons(); }
            }, 50);
        });
    });
</script>
@endpush
@endsection
