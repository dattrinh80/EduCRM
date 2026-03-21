<div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
            <i data-lucide="edit-2" class="w-4 h-4"></i>
        </div>
        Sửa Nguồn: {{ $leadSource->name }}
    </h3>
    <button type="button" @click="showDynamicModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
</div>

<form action="{{ route('admin.lead-sources.update', $leadSource->getId()) }}" method="POST" id="editLeadSourceForm" class="p-6 flex-1 overflow-y-auto">
    @csrf
    @method('PUT')
    <input type="hidden" name="lead_source_id" value="{{ $leadSource->getId() }}">
    
    <div class="space-y-4">
        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-700 block">Tên nguồn <span class="text-red-500">*</span></label>
            <div class="relative">
                <i data-lucide="share-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('lead_source_id') == $leadSource->getId() ? old('name') : $leadSource->name }}">
            </div>
            @if(old('lead_source_id') == $leadSource->getId()) @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
        </div>

        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-700 block">Mã nguồn (Code) <span class="text-red-500">*</span></label>
            <div class="relative">
                <i data-lucide="hash" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="code" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition font-mono uppercase" value="{{ old('lead_source_id') == $leadSource->getId() ? old('code') : $leadSource->code }}">
            </div>
            @if(old('lead_source_id') == $leadSource->getId()) @error('code') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
        </div>
        
        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-700 block">Trạng thái <span class="text-red-500">*</span></label>
            <div class="relative">
                <i data-lucide="activity" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <select name="is_active" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                    <option value="1" {{ (old('lead_source_id') == $leadSource->getId() ? old('is_active') : ($leadSource->isActive ? 1 : 0)) == 1 ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ (old('lead_source_id') == $leadSource->getId() ? old('is_active') : ($leadSource->isActive ? 1 : 0)) == 0 ? 'selected' : '' }}>Đã khóa</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
            </div>
            @if(old('lead_source_id') == $leadSource->getId()) @error('is_active') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
        </div>
    </div>
    
    <div class="pt-6 mt-6 border-t border-slate-100 flex gap-3 justify-end">
        <button type="button" @click="showDynamicModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
        <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
            <i data-lucide="save" class="w-4 h-4"></i> Cập nhật
        </button>
    </div>
</form>
