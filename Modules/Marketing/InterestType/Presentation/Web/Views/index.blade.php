@extends('layouts.app')

@section('title', 'Loại Đăng Ký (Nhu Cầu)')

@section('content')
<div class="space-y-6" x-data="interestTypeManagementStore()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight">Nhu cầu / Loại dịch vụ</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-1.5 text-sm">
                <i data-lucide="info" class="w-3.5 h-3.5 opacity-60"></i>
                Quản lý các loại nhu cầu, dịch vụ khách hàng quan tâm
            </p>
        </div>
        @can('interest_types.create')
        <x-ui.button variant="primary" icon="plus-circle" @click="loadModal('{{ route('admin.interest-types.create') }}')">
            Thêm Nhu cầu Mới
        </x-ui.button>
        @endcan
    </div>


    <!-- Data List -->
    <x-ui.card bodyClass="p-0">
        @if($interestTypes->isEmpty() && !$search)
            <x-ui.empty-state 
                title="Chưa có thông tin nhu cầu"
                description="Hệ thống chưa có danh mục nhu cầu dịch vụ. Hãy bắt đầu bằng cách thêm mục đầu tiên."
                icon="tag"
                actionText="Thêm mới"
                actionClick="loadModal('{{ route('admin.interest-types.create') }}')"
            />
        @else
        <!-- Filter Bar -->
        <div class="p-4 border-b border-slate-100 bg-slate-50/50">
            <form action="{{ route('admin.interest-types.index') }}" method="GET" class="flex items-center gap-3">
                <x-ui.input name="search" value="{{ $search }}" placeholder="Tìm kiếm tên nhu cầu..." icon="search" containerClass="flex-1 max-w-sm" />
                
                <div class="flex items-center gap-2">
                    <x-ui.button type="submit" variant="secondary" icon="filter">
                        Lọc
                    </x-ui.button>
                    @if(!empty($search))
                    <x-ui.button variant="ghost" icon="x-circle" :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.interest-types.index'), 'tag' => 'a'])">
                        Xoá lọc
                    </x-ui.button>
                    @endif
                </div>
            </form>
        </div>


        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="p-4 px-6">Tên Nhu cầu</th>
                        <th class="p-4 px-6">Mô tả</th>
                        <th class="p-4 px-6">Trạng thái</th>
                        <th class="p-4 px-6">Ngày tạo</th>
                        <th class="p-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($interestTypes as $interestType)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="p-4 px-6 whitespace-nowrap">
                                <div class="font-medium text-slate-800">{{ $interestType->name }}</div>
                            </td>
                            <td class="p-4 px-6 text-sm text-slate-500">
                                <div class="max-w-xs truncate" title="{{ $interestType->description }}">
                                    {{ $interestType->description ?: '—' }}
                                </div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                <x-ui.badge :variant="$interestType->is_active ? 'success' : 'danger'" dot>
                                    {{ $interestType->is_active ? 'Hoạt động' : 'Đã khóa' }}
                                </x-ui.badge>
                            </td>

                            <td class="p-4 px-6 whitespace-nowrap text-slate-500 text-sm">
                                {{ $interestType->created_at ? $interestType->created_at->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                    @can('interest_types.update')
                                    <button type="button" @click="loadModal('{{ route('admin.interest-types.edit', $interestType->id) }}')" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Sửa">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </button>
                                    @endcan
                                    @can('interest_types.delete')
                                    <form action="{{ route('admin.interest-types.destroy', $interestType->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(this, '{{ addslashes($interestType->name) }}')">
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
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-500">
                                Không tìm thấy kết quả phù hợp với "{{ $search }}"
                            </td>
                        </tr>
                    @endforelse
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
            
            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md mx-auto max-h-[90vh] overflow-hidden flex flex-col text-left border border-slate-100"
                 x-show="showDynamicModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95">
                
                <div x-show="isLoadingModal" class="p-12 flex flex-col items-center justify-center space-y-4">
                    <div class="w-12 h-12 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
                    <p class="text-slate-500 font-medium animate-pulse">Đang tải biểu mẫu...</p>
                </div>

                <div x-show="!isLoadingModal" x-html="modalContent" class="flex-1 overflow-hidden flex flex-col"></div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('interestTypeManagementStore', () => ({
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
