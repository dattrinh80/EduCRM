@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Role: {{ $role->name }}</h1>
            <p class="text-slate-500 mt-1">Update role name and permissions</p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition flex items-center gap-2 text-sm font-medium w-fit">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to List</span>
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Role Name -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <div class="space-y-1 mb-4">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                        <i data-lucide="shield" class="w-5 h-5 text-primary-500"></i>
                        Role Information
                    </h3>
                </div>
                <div class="space-y-1">
                    <label for="name" class="text-sm font-medium text-slate-700 block">Role Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('name', $role->name) }}">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Permissions by Group -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                            <i data-lucide="key" class="w-5 h-5 text-primary-500"></i>
                            Permissions
                        </h3>
                        <p class="text-sm text-slate-500">Select which permissions this role should have</p>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-primary-600 font-medium">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-slate-300 text-primary-500 focus:ring-primary-500" onchange="toggleAll(this)">
                        Select All
                    </label>
                </div>

                <div class="space-y-4">
                    @foreach ($permissionGroups as $group)
                    @php
                        $groupPermIds = $group->permissions->pluck('id')->toArray();
                        $allChecked = !empty($groupPermIds) && empty(array_diff($groupPermIds, $assignedPermissionIds));
                    @endphp
                    <div class="border border-slate-100 rounded-xl overflow-hidden">
                        <div class="bg-slate-50 px-5 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center">
                                    <i data-lucide="folder" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="font-semibold text-slate-700 text-sm">{{ $group->name }}</span>
                                    @if($group->description)
                                        <p class="text-xs text-slate-400">{{ $group->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <label class="flex items-center gap-1.5 cursor-pointer text-xs text-slate-500">
                                <input type="checkbox" class="group-check w-3.5 h-3.5 rounded border-slate-300 text-primary-500 focus:ring-primary-500" data-group="{{ $group->id }}" onchange="toggleGroup(this, '{{ $group->id }}')" {{ $allChecked ? 'checked' : '' }}>
                                All
                            </label>
                        </div>
                        <div class="px-5 py-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            @foreach ($group->permissions as $perm)
                            <label class="flex items-start gap-2.5 cursor-pointer p-2.5 rounded-lg hover:bg-slate-50 transition">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" class="perm-check perm-{{ $group->id }} w-4 h-4 rounded border-slate-300 text-primary-500 focus:ring-primary-500 mt-0.5" {{ in_array($perm->id, old('permissions', $assignedPermissionIds)) ? 'checked' : '' }}>
                                <div>
                                    <span class="text-sm font-medium text-slate-700 block">{{ Str::afterLast($perm->name, '.') }}</span>
                                    @if($perm->description)
                                        <span class="text-xs text-slate-400">{{ $perm->description }}</span>
                                    @endif
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.roles.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition flex items-center gap-2 font-medium shadow-lg shadow-primary-500/30">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Update Role</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Top-down: Select All → check/uncheck everything
    function toggleAll(checkbox) {
        document.querySelectorAll('.perm-check, .group-check').forEach(el => el.checked = checkbox.checked);
    }

    // Top-down: Group All → check/uncheck group permissions
    function toggleGroup(checkbox, groupId) {
        document.querySelectorAll(`.perm-${groupId}`).forEach(el => el.checked = checkbox.checked);
        syncSelectAll();
    }

    // Bottom-up: When a permission checkbox changes
    function syncGroup(groupId) {
        const perms = document.querySelectorAll(`.perm-${groupId}`);
        const groupCheck = document.querySelector(`.group-check[data-group="${groupId}"]`);
        if (groupCheck) {
            groupCheck.checked = perms.length > 0 && [...perms].every(el => el.checked);
        }
        syncSelectAll();
    }

    // Sync global Select All based on all permission checkboxes
    function syncSelectAll() {
        const allPerms = document.querySelectorAll('.perm-check');
        const selectAll = document.getElementById('selectAll');
        if (selectAll) {
            selectAll.checked = allPerms.length > 0 && [...allPerms].every(el => el.checked);
        }
    }

    // On page load: attach listeners + sync initial state
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.perm-check').forEach(el => {
            el.addEventListener('change', () => {
                const cls = [...el.classList].find(c => c.startsWith('perm-') && c !== 'perm-check');
                if (cls) syncGroup(cls.replace('perm-', ''));
            });
        });
        // Sync initial state for pre-checked permissions
        document.querySelectorAll('.group-check').forEach(gc => syncGroup(gc.dataset.group));
    });
</script>
@endpush
@endsection
