@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit User</h1>
            <p class="text-slate-500 mt-1">Update user details and role assignments</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition flex items-center gap-2 text-sm font-medium w-fit">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to List</span>
        </a>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden max-w-3xl mx-auto p-6 lg:p-8">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6" id="userForm">
            @csrf
            @method('PUT')

            {{-- User Info Section --}}
            <div class="space-y-1">
                <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                    <i data-lucide="user" class="w-5 h-5 text-primary-500"></i>
                    User Information
                </h3>
                <p class="text-sm text-slate-500">Update account details</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1">
                    <label for="name" class="text-sm font-medium text-slate-700 block">Name <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="user" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="name" id="name" required class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('name', $user->name) }}" placeholder="Enter full name">
                    </div>
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label for="email" class="text-sm font-medium text-slate-700 block">Email <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="email" name="email" id="email" required class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('email', $user->email) }}" placeholder="Enter email address">
                    </div>
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label for="password" class="text-sm font-medium text-slate-700 block">New Password <span class="text-slate-400 font-normal">(Leave blank to keep current)</span></label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="password" name="password" id="password" class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" placeholder="Min 6 chars">
                    </div>
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label for="password_confirmation" class="text-sm font-medium text-slate-700 block">Confirm New Password</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" placeholder="Repeat password">
                    </div>
                </div>
            </div>

            {{-- Roles Section --}}
            <div class="pt-6 border-t border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="space-y-1">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                            <i data-lucide="shield" class="w-5 h-5 text-primary-500"></i>
                            Role Assignment
                        </h3>
                        <p class="text-sm text-slate-500">Manage roles and access scopes</p>
                    </div>
                    <button type="button" onclick="addRoleRow()" class="px-3 py-1.5 bg-primary-50 text-primary-600 rounded-lg hover:bg-primary-100 transition flex items-center gap-1.5 text-sm font-medium">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Add Role
                    </button>
                </div>

                <div id="rolesContainer" class="space-y-3">
                    {{-- Existing roles will be populated by JS below --}}
                </div>

                <div id="noRolesMessage" class="p-6 text-center border-2 border-dashed border-slate-200 rounded-xl" style="display: none;">
                    <i data-lucide="shield-off" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                    <p class="text-sm text-slate-400">No roles assigned. Click "Add Role" to assign.</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3 mt-8">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition flex items-center gap-2 font-medium shadow-lg shadow-primary-500/30">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Update User</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const availableRoles = @json($roles);
    const availableCenters = @json($centers);
    const existingUserRoles = @json($user->userRoles ?? []);
    let roleIndex = 0;

    function addRoleRow(existingRole = null) {
        const container = document.getElementById('rolesContainer');
        const noMsg = document.getElementById('noRolesMessage');
        if (noMsg) noMsg.style.display = 'none';

        const idx = roleIndex++;
        const div = document.createElement('div');
        div.className = 'flex flex-col sm:flex-row items-stretch sm:items-start gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100';
        div.id = `role-row-${idx}`;
        div.innerHTML = `
            <div class="flex-1 space-y-1">
                <label class="text-xs font-medium text-slate-500 uppercase tracking-wider">Role</label>
                <select name="roles[${idx}][role_id]" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                    <option value="">-- Select Role --</option>
                    ${availableRoles.map(r => `<option value="${r.id}" ${existingRole && existingRole.role_id === r.id ? 'selected' : ''}>${r.name}</option>`).join('')}
                </select>
            </div>
            <div class="flex-1 space-y-1">
                <label class="text-xs font-medium text-slate-500 uppercase tracking-wider">Scope Type</label>
                <select name="roles[${idx}][scope_type]" required onchange="toggleScopeId(this, ${idx})" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                    <option value="ALL" ${existingRole && existingRole.scope_type === 'ALL' ? 'selected' : ''}>ALL (Global)</option>
                    <option value="CENTER" ${existingRole && existingRole.scope_type === 'CENTER' ? 'selected' : ''}>CENTER</option>
                </select>
            </div>
            <div class="flex-1 space-y-1" id="scope-id-wrapper-${idx}" style="display: ${existingRole && existingRole.scope_type === 'CENTER' ? 'block' : 'none'}">
                <label class="text-xs font-medium text-slate-500 uppercase tracking-wider">Cơ sở</label>
                <select name="roles[${idx}][scope_id]" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                    <option value="">-- Chọn cơ sở --</option>
                    ${availableCenters.map(c => `<option value="${c.id}" ${existingRole && existingRole.scope_id === c.id ? 'selected' : ''}>[${c.code}] ${c.name}</option>`).join('')}
                </select>
            </div>
            <div class="flex items-end">
                <button type="button" onclick="removeRoleRow(${idx})" class="p-2.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition" title="Remove">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </div>
        `;
        container.appendChild(div);

        if (window.lucide) lucide.createIcons();
    }

    function removeRoleRow(idx) {
        const row = document.getElementById(`role-row-${idx}`);
        if (row) row.remove();

        const container = document.getElementById('rolesContainer');
        const noMsg = document.getElementById('noRolesMessage');
        if (container.children.length === 0 && noMsg) {
            noMsg.style.display = 'block';
        }
    }

    function toggleScopeId(select, idx) {
        const wrapper = document.getElementById(`scope-id-wrapper-${idx}`);
        wrapper.style.display = select.value === 'CENTER' ? 'block' : 'none';
        if (select.value === 'ALL') {
            wrapper.querySelector('select').value = '';
        }
    }

    // Initialize existing roles on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (existingUserRoles.length > 0) {
            existingUserRoles.forEach(ur => addRoleRow(ur));
        } else {
            document.getElementById('noRolesMessage').style.display = 'block';
        }
    });
</script>
@endpush
@endsection
