<div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
            <i data-lucide="list" class="w-4 h-4"></i>
        </div>
        Thêm Trạng thái Lead Mới
    </h3>
    <button type="button" @click="showDynamicModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
</div>

<form action="{{ route('admin.lead-statuses.store') }}" method="POST" id="createLeadStatusForm" class="p-6 flex-1 overflow-y-auto">
    @csrf
    <div class="space-y-4">
        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-700 block">Tên trạng thái <span class="text-red-500">*</span></label>
            <div class="relative">
                <i data-lucide="tag" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('name') }}" placeholder="Vd: Mới đăng ký">
            </div>
            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-700 block">Giai đoạn (Stage) <span class="text-red-500">*</span></label>
            <div class="relative text-sm">
                <select name="stage" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 appearance-none bg-white transition">
                    <option value="NEW" {{ old('stage') == 'NEW' ? 'selected' : '' }}>Mới (New)</option>
                    <option value="CONTACTED" {{ old('stage') == 'CONTACTED' ? 'selected' : '' }}>Đã liên hệ (Contacted)</option>
                    <option value="INTERESTED" {{ old('stage') == 'INTERESTED' ? 'selected' : '' }}>Quan tâm (Interested)</option>
                    <option value="QUALIFIED" {{ old('stage') == 'QUALIFIED' ? 'selected' : '' }}>Tiềm năng (Qualified)</option>
                    <option value="CONVERTED" {{ old('stage') == 'CONVERTED' ? 'selected' : '' }}>Đã chuyển đổi (Converted)</option>
                    <option value="LOST" {{ old('stage') == 'LOST' ? 'selected' : '' }}>Thất bại (Lost)</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
            </div>
            @error('stage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block">Sắp xếp</label>
                <input type="number" name="sort_order" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('sort_order', 0) }}">
                @error('sort_order') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block">Màu sắc</label>
                <input type="color" name="color" class="w-full h-10 border border-slate-200 rounded-xl cursor-pointer p-1" value="{{ old('color', '#6366f1') }}">
                @error('color') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-700 block">Trạng thái hoạt động</label>
            <select name="is_active" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 appearance-none bg-white text-sm transition">
                <option value="1">Kích hoạt</option>
                <option value="0">Tạm khoá</option>
            </select>
            @error('is_active') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
    </div>
    
    <div class="pt-6 mt-6 border-t border-slate-100 flex gap-3 justify-end">
        <button type="button" @click="showDynamicModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
        <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
            <i data-lucide="plus" class="w-4 h-4"></i> Thêm Xong
        </button>
    </div>
</form>
