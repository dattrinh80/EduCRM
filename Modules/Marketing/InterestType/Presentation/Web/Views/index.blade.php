@extends('layouts.app')

@section('title', 'Loại Đăng Ký (Nhu Cầu)')

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }} }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight">Loại Đăng Ký (Nhu Cầu)</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-1.5 text-sm">
                <i data-lucide="info" class="w-3.5 h-3.5 opacity-60"></i>
                Quản lý danh mục các dịch vụ / chương trình học mà cơ sở kinh doanh
            </p>
        </div>
        @can('interest_types.create')
        <x-ui.button variant="primary" icon="plus-circle" @click="showCreateModal = true; $dispatch('refresh-icons')">
            Thêm Dịch Vụ Mới
        </x-ui.button>
        @endcan
    </div>


    <!-- Data List -->
    <x-ui.card bodyClass="p-0">
        @if($interestTypes->isEmpty() && !$search)
            <x-ui.empty-state 
                title="Chưa có loại dịch vụ nào"
                description="Hệ thống chưa có danh mục dịch vụ. Hãy bắt đầu bằng cách thêm dịch vụ đầu tiên."
                icon="list-todo"
                actionText="Thêm dịch vụ mới"
                actionClick="showCreateModal = true; $dispatch('refresh-icons')"
            />
        @else
        <!-- Filter Bar -->
        <div class="p-4 border-b border-slate-100 bg-slate-50/50">
            <form action="{{ route('admin.interest-types.index') }}" method="GET" class="flex items-center gap-3">
                <x-ui.input name="search" value="{{ $search }}" placeholder="Tìm kiếm tên dịch vụ..." icon="search" containerClass="flex-1 max-w-sm" />
                
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
                        <th class="p-4 px-6">Tên Dịch Vụ</th>
                        <th class="p-4 px-6">Mô tả</th>
                        <th class="p-4 px-6">Trạng thái</th>
                        <th class="p-4 px-6">Ngày tạo</th>
                        <th class="p-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($interestTypes as $type)
                        <tr class="hover:bg-slate-50 transition group" x-data="{ showEditModal: {{ $errors->any() && old('_method') == 'PUT' && old('interest_type_id') == $type->id ? 'true' : 'false' }} }">
                            <td class="p-4 px-6 whitespace-nowrap">
                                <div class="font-medium text-slate-800">{{ $type->name }}</div>
                            </td>
                            <td class="p-4 px-6 text-slate-600">
                                {{ $type->description ?? '—' }}
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                <x-ui.badge :variant="$type->is_active ? 'success' : 'danger'" dot>
                                    {{ $type->is_active ? 'Hoạt động' : 'Đã khóa' }}
                                </x-ui.badge>
                            </td>

                            <td class="p-4 px-6 whitespace-nowrap text-slate-500 text-sm">
                                {{ $type->created_at ? $type->created_at->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                    @can('interest_types.update')
                                    <button type="button" @click="showEditModal = true; $dispatch('refresh-icons')" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition cursor-pointer" title="Sửa">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </button>
                                    @endcan
                                    @can('interest_types.delete')
                                    <form action="{{ route('admin.interest-types.destroy', $type->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(this, '{{ addslashes($type->name) }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Xoá">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>

                                <!-- Edit Modal -->
                                @can('interest_types.update')
                                <template x-teleport="body">
                                    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                                        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showEditModal = false" x-transition.opacity></div>
                                        
                                        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden text-left" 
                                             x-show="showEditModal" 
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave="transition ease-in duration-200"
                                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                                             
                                            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                                                <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                                                    <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                                    </div>
                                                    Sửa Loại Dịch Vụ: {{ $type->name }}
                                                </h3>
                                                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                                                    <i data-lucide="x" class="w-5 h-5"></i>
                                                </button>
                                            </div>

                                            <form action="{{ route('admin.interest-types.update', $type->id) }}" method="POST" class="p-6">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="interest_type_id" value="{{ $type->id }}">
                                                
                                                <div class="space-y-4">
                                                    <div class="space-y-1">
                                                        <label class="text-sm font-medium text-slate-700 block">Tên dịch vụ <span class="text-red-500">*</span></label>
                                                        <div class="relative">
                                                            <i data-lucide="list-todo" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                            <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('interest_type_id') == $type->id ? old('name') : $type->name }}">
                                                        </div>
                                                        @if(old('interest_type_id') == $type->id)
                                                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                        @endif
                                                    </div>

                                                    <div class="space-y-1">
                                                        <label class="text-sm font-medium text-slate-700 block">Mô tả chi tiết</label>
                                                        <textarea name="description" rows="3" class="w-full p-4 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">{{ old('interest_type_id') == $type->id ? old('description') : $type->description }}</textarea>
                                                        @if(old('interest_type_id') == $type->id)
                                                            @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="space-y-1">
                                                        <label class="text-sm font-medium text-slate-700 block">Trạng thái <span class="text-red-500">*</span></label>
                                                        <div class="relative">
                                                            <i data-lucide="activity" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                            <select name="is_active" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                                                <option value="1" {{ (old('interest_type_id') == $type->id ? old('is_active') : $type->is_active) == 1 ? 'selected' : '' }}>Hoạt động</option>
                                                                <option value="0" {{ (old('interest_type_id') == $type->id ? old('is_active') : $type->is_active) == 0 ? 'selected' : '' }}>Đã khóa</option>
                                                            </select>
                                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                                        </div>
                                                        @if(old('interest_type_id') == $type->id)
                                                            @error('is_active') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <div class="pt-6 mt-6 border-t border-slate-100 flex gap-3 justify-end">
                                                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
                                                    <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
                                                        <i data-lucide="save" class="w-4 h-4"></i> Cập nhật
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </template>
                                @endcan
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


    <!-- Create Modal -->
    @can('interest_types.create')
    <template x-teleport="body">
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showCreateModal = false" x-transition.opacity></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden text-left" 
                 x-show="showCreateModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                 
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                            <i data-lucide="list-todo" class="w-4 h-4"></i>
                        </div>
                        Thêm Loại Dịch Vụ Mới
                    </h3>
                    <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('admin.interest-types.store') }}" method="POST" class="p-6">
                    @csrf
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 block">Tên dịch vụ <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i data-lucide="list-todo" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ !old('_method') ? old('name') : '' }}" placeholder="Vd: Tiếng Anh Giao Tiếp">
                            </div>
                            @if(!old('_method'))
                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 block">Mô tả chi tiết</label>
                            <textarea name="description" rows="3" placeholder="Nhập mô tả thêm..." class="w-full p-4 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">{{ !old('_method') ? old('description') : '' }}</textarea>
                            @if(!old('_method'))
                                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            @endif
                        </div>
                    </div>
                    
                    <div class="pt-6 mt-6 border-t border-slate-100 flex gap-3 justify-end">
                        <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
                        <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
                            <i data-lucide="plus" class="w-4 h-4"></i> Thêm Xong
                        </button>
                    </div>
                </form>
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
