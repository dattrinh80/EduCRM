@extends('layouts.app')

@section('title', 'Quản lý Phân quyền')

@section('content')
<div class="space-y-6" x-data="roleManagementStore()">
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
        <x-ui.button variant="primary" icon="plus-circle" @click="loadModal('{{ route('admin.roles.create') }}')">
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


    <!-- Roles Grid -->
    <x-ui.card bodyClass="p-0">
        @if($roles->isEmpty())
            <x-ui.empty-state 
                title="Chưa có Role nào"
                description="Hệ thống chưa có nhóm quyền nào được định nghĩa."
                icon="shield"
                actionText="Tạo Role mới"
                actionClick="loadModal('{{ route('admin.roles.create') }}')"
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
                                        <button type="button" @click="loadModal('{{ route('admin.roles.edit', $role->id) }}')" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Sửa">
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
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
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
            
            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-5xl mx-auto max-h-[90vh] overflow-hidden flex flex-col text-left border border-slate-100"
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
        Alpine.data('roleManagementStore', () => ({
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
