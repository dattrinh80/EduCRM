<div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
            <i data-lucide="edit" class="w-4 h-4"></i>
        </div>
        Sửa Cơ sở: {{ $center->name }}
    </h3>
    <button type="button" @click="showDynamicModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
</div>

<form action="{{ route('admin.centers.update', $center->id) }}" method="POST" id="editCenterForm" class="flex-1 flex flex-col overflow-hidden">
    @csrf
    @method('PUT')
    <input type="hidden" name="center_id" value="{{ $center->id }}">
    
    <div class="p-6 flex-1 overflow-y-auto space-y-6">
        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block">Mã cơ sở <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i data-lucide="hash" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="code" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition font-mono uppercase" value="{{ old('center_id') == $center->id ? old('code') : $center->code }}">
                </div>
                @if(old('center_id') == $center->id) @error('code') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block">Tên cơ sở <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i data-lucide="building-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('center_id') == $center->id ? old('name') : $center->name }}">
                </div>
                @if(old('center_id') == $center->id) @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block">Điện thoại</label>
                <div class="relative">
                    <i data-lucide="phone" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="phone" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('center_id') == $center->id ? old('phone') : $center->phone }}">
                </div>
                @if(old('center_id') == $center->id) @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
            </div>
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block">Email</label>
                <div class="relative">
                    <i data-lucide="mail" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="email" name="email" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('center_id') == $center->id ? old('email') : $center->email }}">
                </div>
                @if(old('center_id') == $center->id) @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-700 block">Địa chỉ</label>
            <div class="relative">
                <i data-lucide="map-pin" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="address" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('center_id') == $center->id ? old('address') : $center->address }}">
            </div>
            @if(old('center_id') == $center->id) @error('address') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
        </div>

        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-700 block">Trạng thái <span class="text-red-500">*</span></label>
            <div class="relative">
                <i data-lucide="activity" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <select name="status" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                    <option value="active" {{ (old('center_id') == $center->id ? old('status') : $center->status) === 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ (old('center_id') == $center->id ? old('status') : $center->status) === 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
            </div>
            @if(old('center_id') == $center->id) @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror @endif
        </div>
    </div>
    
    <div class="px-6 py-4 border-t border-slate-100 flex gap-3 justify-end shrink-0 bg-slate-50">
        <button type="button" @click="showDynamicModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
        <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
            <i data-lucide="save" class="w-4 h-4"></i> Cập nhật
        </button>
    </div>
</form>
