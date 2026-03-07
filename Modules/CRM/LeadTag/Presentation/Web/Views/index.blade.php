@extends('layouts.app')

@section('title', 'Quản lý Nhãn Lead')

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }} }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Nhãn (Tag) Lead</h1>
            <p class="text-slate-500 mt-1">Phân loại khách hàng bằng nhãn màu sắc</p>
        </div>
        @can('leads.update')
        <button type="button" @click="showCreateModal = true; $dispatch('refresh-icons')" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition flex items-center gap-2 shadow-lg shadow-primary-500/30 w-fit">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Thêm Nhãn Mới</span>
        </button>
        @endcan
    </div>

    <!-- Data List -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if(empty($tags))
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="tag" class="w-8 h-8 text-slate-400"></i>
            </div>
            <p class="text-slate-500 mb-4">Chưa có nhãn nào</p>
            @can('leads.update')
            <button type="button" @click="showCreateModal = true; $dispatch('refresh-icons')" class="inline-flex items-center gap-2 text-primary-500 hover:text-primary-600 font-medium cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i> Thêm nhãn mới
            </button>
            @endcan
        </div>
        @else
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($tags as $tag)
                <div class="group relative flex items-center justify-between p-4 rounded-2xl border border-slate-100 hover:border-primary-200 hover:shadow-md transition bg-slate-50/30" x-data="{ showEditModal: {{ $errors->any() && old('_method') == 'PUT' && old('tag_id') == $tag->getId() ? 'true' : 'false' }} }">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-8 rounded-full" style="background-color: {{ $tag->color ?? '#64748b' }}"></div>
                        <div>
                            <div class="font-semibold text-slate-800">{{ $tag->name }}</div>
                            <div class="text-[10px] uppercase tracking-wider text-slate-400 font-medium">{{ $tag->color ?? 'slate' }}</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                        @can('leads.update')
                        <button type="button" @click="showEditModal = true; $dispatch('refresh-icons')" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-white rounded-lg transition shadow-sm border border-transparent hover:border-slate-100">
                            <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                        </button>
                        @endcan
                        @can('leads.delete')
                        <form action="{{ route('admin.lead-tags.destroy', $tag->getId()) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xoá nhãn này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-white rounded-lg transition shadow-sm border border-transparent hover:border-slate-100">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
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
                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                                 
                                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                                    <h3 class="text-lg font-semibold text-slate-800">Sửa Nhãn</h3>
                                    <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                                        <i data-lucide="x" class="w-5 h-5"></i>
                                    </button>
                                </div>

                                <form action="{{ route('admin.lead-tags.update', $tag->getId()) }}" method="POST" class="p-6">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="tag_id" value="{{ $tag->getId() }}">
                                    
                                    <div class="space-y-4">
                                        <div class="space-y-1">
                                            <label class="text-sm font-medium text-slate-700 block">Tên nhãn <span class="text-red-500">*</span></label>
                                            <input type="text" name="name" required class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 text-sm transition" value="{{ old('tag_id') == $tag->getId() ? old('name') : $tag->name }}">
                                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="space-y-1">
                                            <label class="text-sm font-medium text-slate-700 block">Màu sắc (HEX / CSS color)</label>
                                            <div class="flex gap-2">
                                                <input type="color" name="color" class="w-12 h-10 p-1 border border-slate-200 rounded-xl" value="{{ old('tag_id') == $tag->getId() ? old('color') : ($tag->color ?? '#64748b') }}">
                                                <input type="text" x-model="$el.previousElementSibling.value" class="flex-1 px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 text-sm transition font-mono" value="{{ old('tag_id') == $tag->getId() ? old('color') : ($tag->color ?? '#64748b') }}">
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
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

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
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                        <i data-lucide="tag" class="w-5 h-5 text-primary-500"></i>
                        Thêm Nhãn Mới
                    </h3>
                    <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('admin.lead-tags.store') }}" method="POST" class="p-6">
                    @csrf
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 block">Tên nhãn <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 text-sm transition" value="{{ old('name') }}" placeholder="Vd: VIP, Hot, Spam...">
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 block">Màu sắc</label>
                            <div class="flex gap-2">
                                <input type="color" id="tag_color_input" name="color" class="w-12 h-10 p-1 border border-slate-200 rounded-xl" value="#64748b">
                                <input type="text" x-model="$el.previousElementSibling.value" oninput="document.getElementById('tag_color_input').value = this.value" class="flex-1 px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 text-sm transition font-mono" value="#64748b">
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-6 mt-6 border-t border-slate-100 flex gap-3 justify-end">
                        <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
                        <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 font-medium text-sm flex items-center gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i> Tạo nhãn
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    @endcan
</div>
@endsection
