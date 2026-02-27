@extends('layouts.app')

@section('title', 'Users Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Users Management</h1>
            <p class="text-slate-500 mt-1">Manage users, roles and scopes</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition flex items-center gap-2 shadow-lg shadow-primary-500/30 w-fit">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>New User</span>
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search by name or email..." class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">
            </div>
            <select name="role_id" class="px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition min-w-[180px]">
                <option value="">All Roles</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" {{ ($roleId ?? '') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition flex items-center gap-2 text-sm font-medium">
                <i data-lucide="filter" class="w-4 h-4"></i>
                <span>Filter</span>
            </button>
            @if (!empty($search) || !empty($roleId))
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 text-slate-500 hover:text-slate-700 rounded-xl hover:bg-slate-50 transition flex items-center gap-2 text-sm font-medium">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    <span>Clear</span>
                </a>
            @endif
        </form>
    </div>

    <!-- Data List -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($users->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="users" class="w-8 h-8 text-slate-400"></i>
            </div>
            <p class="text-slate-500">No users found</p>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 mt-4 text-primary-500 hover:text-primary-600 font-medium">
                <i data-lucide="plus" class="w-4 h-4"></i> Create new user
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="p-4 px-6">Name</th>
                        <th class="p-4 px-6">Email</th>
                        <th class="p-4 px-6">Roles</th>
                        <th class="p-4 px-6">Scope</th>
                        <th class="p-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $user)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="p-4 px-6 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-semibold text-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="font-medium text-slate-800">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-slate-600">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="mail" class="w-4 h-4 text-slate-400"></i>
                                    {{ $user->email }}
                                </div>
                            </td>
                            <td class="p-4 px-6">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse ($user->userRoles as $userRole)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                            {{ $userRole->role->name ?? 'N/A' }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400 italic">No role</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="p-4 px-6">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse ($user->userRoles as $userRole)
                                        @php
                                            $scopeColor = $userRole->scope_type === 'ALL'
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : 'bg-amber-100 text-amber-700';
                                            $scopeLabel = $userRole->scope_type === 'ALL'
                                                ? 'Global'
                                                : 'Center: ' . Str::limit($userRole->scope_id ?? '—', 8);
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $scopeColor }}">
                                            {{ $scopeLabel }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400 italic">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                    @can('users.update')
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Edit">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    @endcan
                                    @can('users.delete')
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(this, '{{ $user->name }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $users->appends(request()->query())->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
