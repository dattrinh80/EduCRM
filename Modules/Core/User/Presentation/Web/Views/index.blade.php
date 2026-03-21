@extends('layouts.app')

@section('title', 'Quản lý Người dùng')

@section('content')
<div class="space-y-6" x-data="userManagementStore()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight">Quản lý Người dùng</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-1.5 text-sm">
                <i data-lucide="info" class="w-3.5 h-3.5 opacity-60"></i>
                Quản lý tài khoản, phân quyền và phạm vi truy cập
            </p>
        </div>
        @can('users.create')
        <x-ui.button variant="primary" icon="plus-circle" @click="loadModal('{{ route('admin.users.create') }}')">
            Tạo Tài khoản
        </x-ui.button>
        @endcan
    </div>


    <!-- Search & Filter Bar -->
    <x-ui.card bodyClass="p-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
            <x-ui.input name="search" id="search" placeholder="Tìm theo tên hoặc email..." value="{{ $search ?? '' }}" icon="search" containerClass="flex-1" />
            
            <x-ui.select name="role_id" id="role_id" containerClass="sm:w-64">
                <option value="">Tất cả các quyền</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" {{ ($roleId ?? '') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </x-ui.select>

            <div class="flex gap-2">
                <x-ui.button type="submit" variant="secondary" icon="filter">
                    Lọc
                </x-ui.button>
                @if (!empty($search) || !empty($roleId))
                    <x-ui.button variant="ghost" icon="x-circle" :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.users.index'), 'tag' => 'a'])">
                        Xoá lọc
                    </x-ui.button>
                @endif
            </div>
        </form>
    </x-ui.card>


    <!-- Data List -->
    <x-ui.card bodyClass="p-0">
        @if($users->isEmpty())
            <x-ui.empty-state 
                title="Không tìm thấy người dùng"
                description="Hệ thống không tìm thấy bất kỳ tài khoản người dùng nào khớp với tiêu chí tìm kiếm."
                icon="users"
                actionText="Tạo người dùng mới"
                actionClick="loadModal('{{ route('admin.users.create') }}')"
            />
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="p-4 px-6">Tên / Email</th>
                        <th class="p-4 px-6">Vai trò (Roles) & Phạm vi (Scopes)</th>
                        <th class="p-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $user)
                        <tr class="hover:bg-slate-50 transition group">
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
                                <div class="flex flex-col gap-2">
                                    @forelse ($user->userRoles as $userRole)
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-50 border border-indigo-100 text-indigo-700 shrink-0">
                                                {{ $userRole->role->name ?? 'N/A' }}
                                            </span>
                                            
                                            <i data-lucide="arrow-right" class="w-3 h-3 text-slate-300 shrink-0"></i>

                                            @php
                                                $scopeColor = $userRole->scope_type === 'SYSTEM'
                                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                                                    : 'bg-amber-50 text-amber-700 border-amber-100';
                                                $scopeLabel = $userRole->scope_type === 'SYSTEM' ? 'Toàn quyền Hệ thống' : 'Cơ sở';
                                            @endphp
                                            
                                            <div class="flex items-center gap-1.5">
                                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold border {{ $scopeColor }} shrink-0">
                                                    {{ $scopeLabel }}
                                                </span>
                                                @if($userRole->scope_type === 'CENTER')
                                                    @php $c = $centers->firstWhere('id', $userRole->scope_id); @endphp
                                                    <span class="text-xs text-slate-600 font-medium truncate">
                                                        {{ $c ? '['.$c->code.'] '.$c->name : 'N/A' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <span class="text-xs text-slate-400 italic">Chưa cấp quyền</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                    @can('users.update')
                                    <button type="button" @click="loadModal('{{ route('admin.users.edit', $user->id) }}')" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Sửa">
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
    </x-ui.card>

    <!-- Dynamic Modal Shell -->
    <template x-teleport="body">
        <div x-show="showDynamicModal" 
             x-cloak 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                 @click="showDynamicModal = false"
                 x-show="showDynamicModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"></div>
            
            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-4xl mx-auto max-h-[90vh] overflow-hidden flex flex-col text-left border border-slate-100"
                 x-show="showDynamicModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95">
                
                <div x-show="isLoadingModal" class="p-12 flex flex-col items-center justify-center space-y-4">
                    <div class="w-12 h-12 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
                    <p class="text-slate-500 font-medium animate-pulse">Đang tải dữ liệu...</p>
                </div>

                <div x-show="!isLoadingModal" x-html="modalContent" class="flex-1 overflow-hidden flex flex-col"></div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('userManagementStore', () => ({
            showDynamicModal: false,
            modalContent: '',
            isLoadingModal: false,

            async loadModal(url) {
                this.showDynamicModal = true;
                this.isLoadingModal = true;
                try {
                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    this.modalContent = await response.text();
                    this.isLoadingModal = false;
                    this.$nextTick(() => {
                        if (window.lucide) { lucide.createIcons(); }
                    });
                } catch (error) {
                    console.error('Error loading modal:', error);
                    this.modalContent = '<div class="p-8 text-center text-red-500">Đã có lỗi xảy ra khi tải dữ liệu.</div>';
                    this.isLoadingModal = false;
                }
            }
        }));

        window.addEventListener('refresh-icons', () => {
            setTimeout(() => {
                if (window.lucide) { lucide.createIcons(); }
            }, 50);
        });
    });

</script>
@endpush
@endsection
