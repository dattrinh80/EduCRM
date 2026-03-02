@extends('layouts.app')

@section('title', 'Nguồn khách hàng')

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }} }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Nguồn khách hàng</h1>
            <p class="text-slate-500 mt-1">Quản lý danh mục các nguồn mang về Lead</p>
        </div>
        @can('sources.create')
        <button type="button" @click="showCreateModal = true; $dispatch('refresh-icons')" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition flex items-center gap-2 shadow-lg shadow-primary-500/30 w-fit">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Thêm Nguồn Mới</span>
        </button>
        @endcan
    </div>

    <!-- Data List -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($sources->isEmpty() && !$search)
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="share-2" class="w-8 h-8 text-slate-400"></i>
            </div>
            <p class="text-slate-500 mb-4">Chưa có nguồn khách hàng nào</p>
            @can('sources.create')
            <button type="button" @click="showCreateModal = true; $dispatch('refresh-icons')" class="inline-flex items-center gap-2 text-primary-500 hover:text-primary-600 font-medium cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i> Thêm nguồn mới
            </button>
            @endcan
        </div>
        @else
        <!-- Search bar -->
        <div class="p-4 border-b border-slate-100 bg-slate-50/50">
            <form action="{{ route('admin.sources.index') }}" method="GET" class="relative max-w-sm">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Tìm kiếm tên hoặc mã nguồn..." class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="p-4 px-6">Mã Nguồn</th>
                        <th class="p-4 px-6">Tên Nguồn</th>
                        <th class="p-4 px-6">Trạng thái</th>
                        <th class="p-4 px-6">Ngày tạo</th>
                        <th class="p-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($sources as $source)
                        <tr class="hover:bg-slate-50 transition group" x-data="{ showEditModal: {{ $errors->any() && old('_method') == 'PUT' && old('source_id') == $source->id ? 'true' : 'false' }} }">
                            <td class="p-4 px-6 whitespace-nowrap">
                                <span class="px-2 py-1 bg-slate-100 rounded text-xs font-mono font-medium text-slate-600">{{ $source->code }}</span>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                <div class="font-medium text-slate-800">{{ $source->name }}</div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                @if($source->is_active)
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Hoạt động</span>
                                @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Đã khóa</span>
                                @endif
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-slate-500 text-sm">
                                {{ $source->created_at ? $source->created_at->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                    @can('sources.update')
                                    <button type="button" @click="showEditModal = true; $dispatch('refresh-icons')" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition cursor-pointer" title="Sửa">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </button>
                                    @endcan
                                    @can('sources.delete')
                                    <form action="{{ route('admin.sources.destroy', $source->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(this, '{{ addslashes($source->name) }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Xoá">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>

                                <!-- Edit Modal -->
                                @can('sources.update')
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
                                                    Sửa Nguồn: {{ $source->name }}
                                                </h3>
                                                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                                                    <i data-lucide="x" class="w-5 h-5"></i>
                                                </button>
                                            </div>

                                            <form action="{{ route('admin.sources.update', $source->id) }}" method="POST" class="p-6">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="source_id" value="{{ $source->id }}">
                                                
                                                <div class="space-y-4">
                                                    <div class="space-y-1">
                                                        <label class="text-sm font-medium text-slate-700 block">Tên nguồn <span class="text-red-500">*</span></label>
                                                        <div class="relative">
                                                            <i data-lucide="share-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                            <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('source_id') == $source->id ? old('name') : $source->name }}">
                                                        </div>
                                                        @if(old('source_id') == $source->id)
                                                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                        @endif
                                                    </div>

                                                    <div class="space-y-1">
                                                        <label class="text-sm font-medium text-slate-700 block">Mã nguồn (Code) <span class="text-red-500">*</span></label>
                                                        <div class="relative">
                                                            <i data-lucide="hash" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                            <input type="text" name="code" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition font-mono uppercase" value="{{ old('source_id') == $source->id ? old('code') : $source->code }}">
                                                        </div>
                                                        @if(old('source_id') == $source->id)
                                                            @error('code') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="space-y-1">
                                                        <label class="text-sm font-medium text-slate-700 block">Trạng thái <span class="text-red-500">*</span></label>
                                                        <div class="relative">
                                                            <i data-lucide="activity" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                            <select name="is_active" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                                                <option value="1" {{ (old('source_id') == $source->id ? old('is_active') : $source->is_active) == 1 ? 'selected' : '' }}>Hoạt động</option>
                                                                <option value="0" {{ (old('source_id') == $source->id ? old('is_active') : $source->is_active) == 0 ? 'selected' : '' }}>Đã khóa</option>
                                                            </select>
                                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                                        </div>
                                                        @if(old('source_id') == $source->id)
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
    </div>

    <!-- Create Modal -->
    @can('sources.create')
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
                            <i data-lucide="share-2" class="w-4 h-4"></i>
                        </div>
                        Thêm Nguồn Mới
                    </h3>
                    <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('admin.sources.store') }}" method="POST" class="p-6">
                    @csrf
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 block">Tên nguồn <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i data-lucide="share-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ !old('_method') ? old('name') : '' }}" placeholder="Vd: Facebook Ads">
                            </div>
                            @if(!old('_method'))
                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 block">Mã nguồn (Code) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i data-lucide="hash" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" name="code" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition font-mono uppercase" value="{{ !old('_method') ? old('code') : '' }}" placeholder="Vd: FB_ADS">
                            </div>
                            @if(!old('_method'))
                                @error('code') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
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
