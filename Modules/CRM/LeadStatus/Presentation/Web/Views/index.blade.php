@extends('layouts.app')

@section('title', 'Cấu hình Trạng thái Lead')

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }} }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight">Trạng thái Lead</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-1.5 text-sm">
                <i data-lucide="info" class="w-3.5 h-3.5 opacity-60"></i>
                Quản lý quy trình xử lý Lead (Pipeline) trong hệ thống
            </p>
        </div>
        @can('leads.update')
        <x-ui.button variant="primary" icon="plus-circle" @click="showCreateModal = true; $dispatch('refresh-icons')">
            Thêm Trạng Thái
        </x-ui.button>
        @endcan
    </div>


    <!-- Data List -->
    <x-ui.card bodyClass="p-0">
        @if(empty($statuses))
            <x-ui.empty-state 
                title="Chưa có trạng thái nào"
                description="Hệ thống chưa có cấu hình trạng thái lead. Hãy bắt đầu bằng cách thêm trạng thái đầu tiên."
                icon="list"
                actionText="Thêm mới"
                actionClick="showCreateModal = true; $dispatch('refresh-icons')"
            />
        @else

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="p-4 px-6 w-16">STT</th>
                        <th class="p-4 px-6">Tên Trạng Thái</th>
                        <th class="p-4 px-6">Giai đoạn</th>
                        <th class="p-4 px-6">Trạng thái</th>
                        <th class="p-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($statuses as $status)
                        <tr class="hover:bg-slate-50 transition group" x-data="{ showEditModal: {{ $errors->any() && old('_method') == 'PUT' && old('status_id') == $status->getId() ? 'true' : 'false' }} }">
                            <td class="p-4 px-6 whitespace-nowrap text-slate-500 font-mono text-sm">
                                {{ $status->sortOrder }}
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $status->color ?? '#64748b' }}"></div>
                                    <div class="font-medium text-slate-800">{{ $status->name }}</div>
                                </div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-xs font-semibold">
                                    {{ $status->stage }}
                                </span>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                <x-ui.badge :variant="$status->isActive ? 'success' : 'danger'" dot>
                                    {{ $status->isActive ? 'Hoạt động' : 'Đã khóa' }}
                                </x-ui.badge>
                            </td>

                            <td class="p-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                    @can('leads.update')
                                    <button type="button" @click="showEditModal = true; $dispatch('refresh-icons')" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Sửa">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </button>
                                    @endcan
                                    @can('leads.delete')
                                    <form action="{{ route('admin.lead-statuses.destroy', $status->getId()) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xoá trạng thái này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Xoá">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>

                                <!-- Edit Modal -->
                                @can('leads.update')
                                <template x-teleport="body">
                                    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                                        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showEditModal = false" x-transition.opacity></div>
                                        
                                        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden text-left" 
                                             x-show="showEditModal" 
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave="transition ease-in duration-200">
                                             
                                            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                                                <h3 class="text-lg font-semibold text-slate-800">Sửa Trạng Thái</h3>
                                                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                                                    <i data-lucide="x" class="w-5 h-5"></i>
                                                </button>
                                            </div>

                                            <form action="{{ route('admin.lead-statuses.update', $status->getId()) }}" method="POST" class="p-6">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status_id" value="{{ $status->getId() }}">
                                                
                                                <div class="space-y-4">
                                                    <div class="space-y-1">
                                                        <label class="text-sm font-medium text-slate-700 block">Tên trạng thái <span class="text-red-500">*</span></label>
                                                        <input type="text" name="name" required class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 text-sm transition" value="{{ old('status_id') == $status->getId() ? old('name') : $status->name }}">
                                                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block">Giai đoạn <span class="text-red-500">*</span></label>
                                                            <select name="stage" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 text-sm transition bg-white">
                                                                <option value="NEW" {{ (old('status_id') == $status->getId() ? old('stage') : $status->stage) == 'NEW' ? 'selected' : '' }}>Mới</option>
                                                                <option value="CONTACTED" {{ (old('status_id') == $status->getId() ? old('stage') : $status->stage) == 'CONTACTED' ? 'selected' : '' }}>Đã liên lạc</option>
                                                                <option value="INTERESTED" {{ (old('status_id') == $status->getId() ? old('stage') : $status->stage) == 'INTERESTED' ? 'selected' : '' }}>Quan tâm</option>
                                                                <option value="QUALIFIED" {{ (old('status_id') == $status->getId() ? old('stage') : $status->stage) == 'QUALIFIED' ? 'selected' : '' }}>Tiềm năng</option>
                                                                <option value="PARTIALLY_CONVERTED" {{ (old('status_id') == $status->getId() ? old('stage') : $status->stage) == 'PARTIALLY_CONVERTED' ? 'selected' : '' }}>Chuyển đổi một phần</option>
                                                                <option value="CONVERTED" {{ (old('status_id') == $status->getId() ? old('stage') : $status->stage) == 'CONVERTED' ? 'selected' : '' }}>Thành công</option>
                                                                <option value="LOST" {{ (old('status_id') == $status->getId() ? old('stage') : $status->stage) == 'LOST' ? 'selected' : '' }}>Thất bại</option>
                                                            </select>
                                                        </div>
                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block">Thứ tự</label>
                                                            <input type="number" name="sort_order" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 text-sm transition" value="{{ old('status_id') == $status->getId() ? old('sort_order') : $status->sortOrder }}">
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block">Màu sắc</label>
                                                            <input type="color" name="color" class="w-full h-10 p-1 border border-slate-200 rounded-xl" value="{{ old('status_id') == $status->getId() ? old('color') : ($status->color ?? '#64748b') }}">
                                                        </div>
                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block">Trạng thái <span class="text-red-500">*</span></label>
                                                            <select name="is_active" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 text-sm transition bg-white">
                                                                <option value="1" {{ (old('status_id') == $status->getId() ? old('is_active') : $status->isActive) == 1 ? 'selected' : '' }}>Hoạt động</option>
                                                                <option value="0" {{ (old('status_id') == $status->getId() ? old('is_active') : $status->isActive) == 0 ? 'selected' : '' }}>Khóa</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="pt-6 mt-6 border-t border-slate-100 flex gap-3 justify-end">
                                                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
                                                    <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 font-medium">Cập nhật</button>
                                                </div>
                                            </form>
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
        @endif
    </x-ui.card>


    <!-- Create Modal -->
    @can('leads.update')
    <template x-teleport="body">
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showCreateModal = false" x-transition.opacity></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden text-left" 
                 x-show="showCreateModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                 
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-lg font-semibold text-slate-800">Thêm Trạng Thái Mới</h3>
                    <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('admin.lead-statuses.store') }}" method="POST" class="p-6">
                    @csrf
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 block">Tên trạng thái <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 text-sm transition" value="{{ old('name') }}" placeholder="Vd: Đang tư vấn">
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Giai đoạn <span class="text-red-500">*</span></label>
                                <select name="stage" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 text-sm transition bg-white">
                                    <option value="NEW">Mới</option>
                                    <option value="CONTACTED">Đã liên lạc</option>
                                    <option value="INTERESTED">Quan tâm</option>
                                    <option value="QUALIFIED">Tiềm năng</option>
                                    <option value="PARTIALLY_CONVERTED">Chuyển đổi một phần</option>
                                    <option value="CONVERTED">Thành công</option>
                                    <option value="LOST">Thất bại</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Thứ tự</label>
                                <input type="number" name="sort_order" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 text-sm transition" value="{{ old('sort_order', 0) }}">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Màu sắc</label>
                                <input type="color" name="color" class="w-full h-10 p-1 border border-slate-200 rounded-xl" value="#64748b">
                            </div>
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Trạng thái <span class="text-red-500">*</span></label>
                                <select name="is_active" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 text-sm transition bg-white">
                                    <option value="1">Hoạt động</option>
                                    <option value="0">Khóa</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-6 mt-6 border-t border-slate-100 flex gap-3 justify-end">
                        <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
                        <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 font-medium text-sm flex items-center gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i> Tạo trạng thái
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    @endcan
</div>
@endsection
