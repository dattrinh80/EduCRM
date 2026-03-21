@extends('layouts.app')

@section('title', 'Quản lý Cơ sở')

@section('content')
<div class="space-y-6" x-data="centerManagementStore()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight">Quản lý Cơ sở</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-1.5 text-sm">
                <i data-lucide="info" class="w-3.5 h-3.5 opacity-60"></i>
                Quản lý danh sách các cơ sở / chi nhánh trong hệ thống
            </p>
        </div>
        @can('centers.create')
        <x-ui.button variant="primary" icon="plus-circle" @click="loadModal('{{ route('admin.centers.create') }}')">
            Thêm Cơ sở
        </x-ui.button>
        @endcan
    </div>


    <!-- Data List -->
    <x-ui.card bodyClass="p-0">
        @if($centers->isEmpty())
            <x-ui.empty-state 
                title="Chưa có cơ sở nào"
                description="Hệ thống chưa có dữ liệu cơ sở. Hãy bắt đầu bằng cách thêm cơ sở đầu tiên."
                icon="building-2"
                actionText="Thêm cơ sở mới"
                actionClick="loadModal('{{ route('admin.centers.create') }}')"
            />
        @else

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="p-4 px-6">Mã</th>
                        <th class="p-4 px-6">Tên cơ sở</th>
                        <th class="p-4 px-6">Điện thoại</th>
                        <th class="p-4 px-6">Email</th>
                        <th class="p-4 px-6">Trạng thái</th>
                        <th class="p-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($centers as $center)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="p-4 px-6 whitespace-nowrap">
                                <span class="px-2 py-1 bg-slate-100 rounded text-xs font-mono font-medium text-slate-600">{{ $center->code }}</span>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center">
                                        <i data-lucide="building-2" class="w-4 h-4 text-primary-600"></i>
                                    </div>
                                    <div class="font-medium text-slate-800">{{ $center->name }}</div>
                                </div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-slate-600">
                                @if($center->phone)
                                <div class="flex items-center gap-2">
                                    <i data-lucide="phone" class="w-4 h-4 text-slate-400"></i>
                                    {{ $center->phone }}
                                </div>
                                @else
                                <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-slate-600">
                                @if($center->email)
                                <div class="flex items-center gap-2">
                                    <i data-lucide="mail" class="w-4 h-4 text-slate-400"></i>
                                    {{ $center->email }}
                                </div>
                                @else
                                <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                @php
                                    $variant = match(strtolower($center->status)) {
                                        'active' => 'success',
                                        'inactive' => 'danger',
                                        default => 'warning'
                                    };
                                    $label = match(strtolower($center->status)) {
                                        'active' => 'Hoạt động',
                                        'inactive' => 'Ngừng hoạt động',
                                        default => ucfirst($center->status)
                                    };
                                @endphp
                                <x-ui.badge :variant="$variant" dot>{{ $label }}</x-ui.badge>
                            </td>

                            <td class="p-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                    @can('centers.update')
                                    <button type="button" @click="loadModal('{{ route('admin.centers.edit', $center->id) }}')" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition cursor-pointer" title="Sửa">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </button>
                                    @endcan
                                    @can('centers.delete')
                                    <form action="{{ route('admin.centers.destroy', $center->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(this, '{{ addslashes($center->name) }}')">
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
            
            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-xl mx-auto max-h-[90vh] overflow-hidden flex flex-col text-left border border-slate-100"
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
        Alpine.data('centerManagementStore', () => ({
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
