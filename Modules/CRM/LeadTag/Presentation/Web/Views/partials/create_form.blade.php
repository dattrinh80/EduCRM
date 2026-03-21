<div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
            <i data-lucide="tag" class="w-4 h-4"></i>
        </div>
        Thêm Nhãn Mới
    </h3>
    <button type="button" @click="showDynamicModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
</div>

<form action="{{ route('admin.lead-tags.store') }}" method="POST" id="createLeadTagForm" class="p-6 flex-1 overflow-y-auto">
    @csrf
    <div class="space-y-4">
        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-700 block">Tên nhãn <span class="text-red-500">*</span></label>
            <div class="relative">
                <i data-lucide="type" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('name') }}" placeholder="Vd: Quan tâm cao">
            </div>
            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-700 block">Màu sắc</label>
            <div class="grid grid-cols-5 gap-2">
                @foreach(['slate', 'red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'] as $color)
                    <label class="relative flex items-center justify-center cursor-pointer group">
                        <input type="radio" name="color" value="{{ $color }}" class="peer sr-only" {{ old('color', 'slate') == $color ? 'checked' : '' }}>
                        <div class="w-8 h-8 rounded-full bg-{{ $color }}-500 ring-offset-2 peer-checked:ring-2 peer-checked:ring-{{ $color }}-500 transition-all group-hover:scale-110 shadow-sm"></div>
                    </label>
                @endforeach
            </div>
            @error('color') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
    </div>
    
    <div class="pt-6 mt-6 border-t border-slate-100 flex gap-3 justify-end">
        <button type="button" @click="showDynamicModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
        <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
            <i data-lucide="plus" class="w-4 h-4"></i> Thêm Xong
        </button>
    </div>
</form>
