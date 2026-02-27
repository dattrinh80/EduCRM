@extends('layouts.app')

@section('title', 'Roles Management')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Roles Management</h1>
            <p class="text-slate-500 mt-1">Manage roles and their permissions</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition flex items-center gap-2 shadow-lg shadow-primary-500/30 w-fit">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>New Role</span>
        </a>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 text-emerald-600 border border-emerald-200 px-4 py-3 rounded-xl flex items-center gap-3 slide-down">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 text-red-600 border border-red-200 px-4 py-3 rounded-xl flex items-center gap-3 slide-down">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Search -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
        <form action="{{ route('admin.roles.index') }}" method="GET" class="flex items-center gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search roles..." class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition flex items-center gap-2 text-sm font-medium">
                <i data-lucide="filter" class="w-4 h-4"></i> Filter
            </button>
            @if (!empty($search))
                <a href="{{ route('admin.roles.index') }}" class="px-5 py-2.5 text-slate-500 hover:text-slate-700 rounded-xl hover:bg-slate-50 transition flex items-center gap-2 text-sm font-medium">
                    <i data-lucide="x" class="w-4 h-4"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Roles Grid -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($roles->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="shield" class="w-8 h-8 text-slate-400"></i>
            </div>
            <p class="text-slate-500">No roles found</p>
            <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center gap-2 mt-4 text-primary-500 hover:text-primary-600 font-medium">
                <i data-lucide="plus" class="w-4 h-4"></i> Create new role
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="p-4 px-6">Role Name</th>
                        <th class="p-4 px-6">Permissions</th>
                        <th class="p-4 px-6">Created</th>
                        <th class="p-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($roles as $role)
                        <tr class="hover:bg-slate-50 transition group">
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
                                    {{ $role->permissions_count ?? 0 }} permissions
                                </span>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-sm text-slate-500">
                                {{ $role->created_at?->format('d/m/Y') }}
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Edit">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($roles->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $roles->appends(request()->query())->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
