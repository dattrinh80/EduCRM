@extends('layouts.app')

@section('title', 'Users Management')

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }} }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Quản lý Người dùng</h1>
            <p class="text-slate-500 mt-1">Quản lý tài khoản, phân quyền và phạm vi truy cập</p>
        </div>
        @can('users.create')
        <button type="button" @click="showCreateModal = true; $dispatch('refresh-icons')" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition flex items-center gap-2 shadow-lg shadow-primary-500/30 w-fit">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tạo Tài khoản</span>
        </button>
        @endcan
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Tìm theo tên hoặc email..." class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">
            </div>
            <select name="role_id" class="px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition min-w-[180px] bg-white text-slate-700">
                <option value="">Tất cả các quyền</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" {{ ($roleId ?? '') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition flex items-center justify-center gap-2 text-sm font-medium">
                <i data-lucide="filter" class="w-4 h-4"></i>
                <span>Lọc</span>
            </button>
            @if (!empty($search) || !empty($roleId))
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 text-slate-500 hover:text-slate-700 rounded-xl hover:bg-slate-50 transition flex items-center justify-center gap-2 text-sm font-medium">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    <span>Xoá lọc</span>
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
            <p class="text-slate-500 mb-4">Không tìm thấy người dùng nào</p>
            @can('users.create')
            <button type="button" @click="showCreateModal = true; $dispatch('refresh-icons')" class="inline-flex items-center gap-2 text-primary-500 hover:text-primary-600 font-medium cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i> Tạo người dùng mới
            </button>
            @endcan
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="p-4 px-6">Tên / Email</th>
                        <th class="p-4 px-6">Vai trò (Roles)</th>
                        <th class="p-4 px-6">Phạm vi truy cập</th>
                        <th class="p-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $user)
                        <tr class="hover:bg-slate-50 transition group" x-data="{ showEditModal: {{ $errors->any() && old('_method') == 'PUT' && old('user_id') == $user->id ? 'true' : 'false' }} }">
                            <td class="p-4 px-6 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-800">{{ $user->name }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-1"><i data-lucide="mail" class="w-3 h-3 text-slate-400"></i>{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 px-6">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse ($user->userRoles as $userRole)
                                        <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-50 border border-indigo-100 text-indigo-700">
                                            {{ $userRole->role->name ?? 'N/A' }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400 italic">Chưa cấp quyền</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="p-4 px-6">
                                <div class="flex flex-col gap-1.5">
                                    @forelse ($user->userRoles as $userRole)
                                        @php
                                            $scopeColor = $userRole->scope_type === 'ALL'
                                                ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                                                : 'bg-amber-50 text-amber-700 border-amber-100';
                                            $scopeLabel = $userRole->scope_type === 'ALL' ? 'Toàn quyền (Tất cả cơ sở)' : 'Cơ sở';
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold border {{ $scopeColor }}">
                                                {{ $scopeLabel }}
                                            </span>
                                            @if($userRole->scope_type === 'CENTER')
                                                @php $c = $centers->firstWhere('id', $userRole->scope_id); @endphp
                                                <span class="text-xs text-slate-600 font-medium">
                                                    {{ $c ? '['.$c->code.'] '.$c->name : 'N/A' }}
                                                </span>
                                            @endif
                                        </div>
                                    @empty
                                        <span class="text-xs text-slate-400 italic">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                    @can('users.update')
                                    <button type="button" @click="showEditModal = true; $dispatch('refresh-icons')" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Sửa">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </button>
                                    @endcan
                                    @can('users.delete')
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(this, '{{ addslashes($user->name) }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Xoá">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>

                                <!-- Edit Modal -->
                                @can('users.update')
                                <template x-teleport="body">
                                    <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                                        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showEditModal = false" x-transition.opacity></div>
                                        
                                        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col mx-auto overflow-hidden text-left" 
                                             x-show="showEditModal" 
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave="transition ease-in duration-200"
                                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                                             
                                            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
                                                <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                                                    <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                                    </div>
                                                    Sửa Tài Khoản: {{ $user->name }}
                                                </h3>
                                                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                                                    <i data-lucide="x" class="w-5 h-5"></i>
                                                </button>
                                            </div>

                                            <div class="p-6 overflow-y-auto" x-data="{ 
                                                assignedRoles: {{ json_encode(old('user_id') == $user->id ? old('roles', []) : $user->userRoles->map(function($ur) { return ['role_id' => $ur->role_id, 'scope_type' => $ur->scope_type, 'scope_id' => $ur->scope_id]; })->toArray()) }} 
                                            }">
                                                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" id="editForm_{{ $user->id }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                    
                                                    <div class="space-y-6">
                                                        <!-- Thong tin ca nhan -->
                                                        <div class="grid grid-cols-2 gap-5 p-5 bg-slate-50/50 rounded-xl border border-slate-100">
                                                            <div class="space-y-1">
                                                                <label class="text-sm font-medium text-slate-700 block">Họ và tên <span class="text-red-500">*</span></label>
                                                                <div class="relative">
                                                                    <i data-lucide="user" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                    <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('user_id') == $user->id ? old('name') : $user->name }}">
                                                                </div>
                                                                @if(old('user_id') == $user->id) @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
                                                            </div>
                                                            <div class="space-y-1">
                                                                <label class="text-sm font-medium text-slate-700 block">Email <span class="text-red-500">*</span></label>
                                                                <div class="relative">
                                                                    <i data-lucide="mail" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                    <input type="email" name="email" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('user_id') == $user->id ? old('email') : $user->email }}">
                                                                </div>
                                                                @if(old('user_id') == $user->id) @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
                                                            </div>
                                                            <div class="space-y-1">
                                                                <label class="text-sm font-medium text-slate-700 block">Mật khẩu mới <span class="text-slate-400 font-normal">(Bỏ trống nếu không đổi)</span></label>
                                                                <div class="relative">
                                                                    <i data-lucide="lock" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                    <input type="password" name="password" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" autocomplete="new-password">
                                                                </div>
                                                                @if(old('user_id') == $user->id) @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
                                                            </div>
                                                            <div class="space-y-1">
                                                                <label class="text-sm font-medium text-slate-700 block">Nhập lại mật khẩu mới</label>
                                                                <div class="relative">
                                                                    <i data-lucide="lock" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                    <input type="password" name="password_confirmation" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">
                                                                </div>
                                                            </div>
                                                            <div class="space-y-1 col-span-2">
                                                                <label class="text-sm font-medium text-slate-700 block">Cơ sở mặc định</label>
                                                                <div class="relative">
                                                                    <i data-lucide="building-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                    <select name="default_center_id" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                                                        <option value="">-- Không gán cơ sở mặc định --</option>
                                                                        @foreach($centers as $c)
                                                                            <option value="{{ $c->id }}" {{ (old('user_id') == $user->id ? old('default_center_id') : $user->default_center_id) === $c->id ? 'selected' : '' }}>[{{ $c->code }}] {{ $c->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                                                </div>
                                                                @if(old('user_id') == $user->id) @error('default_center_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
                                                            </div>
                                                        </div>

                                                        <!-- Roles -->
                                                        <div>
                                                            <div class="flex items-center justify-between xl mb-3">
                                                                <label class="text-base font-semibold text-slate-800">Phân quyền (Roles) & Phạm vi (Scopes)</label>
                                                                <button type="button" @click="assignedRoles.push({role_id: '', scope_type: 'ALL', scope_id: ''}); $nextTick(() => { if (window.lucide) { lucide.createIcons(); } });" class="px-3 py-1.5 bg-primary-50 text-primary-600 rounded-lg font-medium text-sm hover:bg-primary-100 transition flex items-center gap-1.5">
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
                                                                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="w-full sm:w-40 space-y-1 shrink-0">
                                                                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Phạm vi (Scope)</label>
                                                                            <select x-model="role.scope_type" :name="'roles['+index+'][scope_type]'" @change="if(role.scope_type === 'ALL') role.scope_id = ''" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 bg-white text-slate-700 font-medium">
                                                                                <option value="ALL">Toàn quyền (ALL)</option>
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
                                                                    <p class="text-sm text-slate-400 font-medium">Chưa cấp quyền nào, hãy bấm Thêm quyền.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex gap-3 justify-end shrink-0">
                                                <button type="button" @click="showEditModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-200 rounded-xl transition">Hủy</button>
                                                <button type="submit" form="editForm_{{ $user->id }}" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
                                                    <i data-lucide="save" class="w-4 h-4"></i> Cập nhật user
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                @endcan
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

    <!-- Create Modal -->
    @can('users.create')
    <template x-teleport="body">
        <div x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showCreateModal = false" x-transition.opacity></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col mx-auto overflow-hidden text-left" 
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
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                        </div>
                        Tạo Tài khoản Mới
                    </h3>
                    <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto" x-data="{ 
                    assignedRoles: {{ json_encode(!old('_method') ? old('roles', []) : []) }} 
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
                                        <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ !old('_method') ? old('name') : '' }}" placeholder="Nguyễn Văn A">
                                    </div>
                                    @if(!old('_method')) @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
                                </div>
                                <div class="space-y-1">
                                    <label class="text-sm font-medium text-slate-700 block">Email <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <i data-lucide="mail" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                        <input type="email" name="email" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ !old('_method') ? old('email') : '' }}" placeholder="email@domain.com">
                                    </div>
                                    @if(!old('_method')) @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
                                </div>
                                <div class="space-y-1">
                                    <label class="text-sm font-medium text-slate-700 block">Mật khẩu <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <i data-lucide="lock" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                        <input type="password" name="password" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" autocomplete="new-password">
                                    </div>
                                    @if(!old('_method')) @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
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
                                                <option value="{{ $c->id }}" {{ (!old('_method') && old('default_center_id') === $c->id) ? 'selected' : '' }}>[{{ $c->code }}] {{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                    </div>
                                    @if(!old('_method')) @error('default_center_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
                                </div>
                            </div>

                            <!-- Roles -->
                            <div>
                                <div class="flex items-center justify-between xl mb-3">
                                    <label class="text-base font-semibold text-slate-800">Phân quyền (Roles) & Phạm vi (Scopes)</label>
                                    <button type="button" @click="assignedRoles.push({role_id: '', scope_type: 'ALL', scope_id: ''}); $nextTick(() => { if (window.lucide) { lucide.createIcons(); } });" class="px-3 py-1.5 bg-primary-50 text-primary-600 rounded-lg font-medium text-sm hover:bg-primary-100 transition flex items-center gap-1.5">
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
                                                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="w-full sm:w-40 space-y-1 shrink-0">
                                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Phạm vi (Scope)</label>
                                                <select x-model="role.scope_type" :name="'roles['+index+'][scope_type]'" @change="if(role.scope_type === 'ALL') role.scope_id = ''" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 bg-white text-slate-700 font-medium">
                                                    <option value="ALL">Toàn quyền (ALL)</option>
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
                    <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-200 rounded-xl transition">Hủy</button>
                    <button type="submit" form="createForm" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
                        <i data-lucide="check" class="w-4 h-4"></i> Khởi tạo
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
