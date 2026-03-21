@php
    $isGlobalScope = false;
    try { $isGlobalScope = app('is_global_scope'); } catch (\Exception $e) {}
@endphp

<div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
            <i data-lucide="megaphone" class="w-4 h-4"></i>
        </div>
        Thêm Chiến Dịch Mới
    </h3>
    <button type="button" @click="showDynamicModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
</div>

<div class="p-6 overflow-y-auto w-full flex-1">
    <form action="{{ route('admin.campaigns.store') }}" method="POST" id="create-form">
        @csrf
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 block">Tên chiến dịch <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="megaphone" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('name') }}" placeholder="Vd: Summer Ads">
                    </div>
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 block">Mã chiến dịch (Code)</label>
                    <div class="relative">
                        <i data-lucide="hash" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="code" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition font-mono uppercase" value="{{ old('code') }}" placeholder="Vd: SUMMER_2026">
                    </div>
                    @error('code') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 block">Kênh (Channel)</label>
                    <div class="relative">
                        <i data-lucide="monitor" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="channel" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('channel') }}" placeholder="Vd: Facebook, Google...">
                    </div>
                    @error('channel') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 block">Ngân sách (VNĐ)</label>
                    <div class="relative">
                        <i data-lucide="dollar-sign" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="number" step="0.01" name="budget" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('budget') }}">
                    </div>
                    @error('budget') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 block">Ngày bắt đầu</label>
                    <div class="relative">
                        <i data-lucide="calendar" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="date" name="start_date" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('start_date') }}">
                    </div>
                    @error('start_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 block">Ngày kết thúc</label>
                    <div class="relative">
                        <i data-lucide="calendar" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="date" name="end_date" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('end_date') }}">
                    </div>
                    @error('end_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            @if($isGlobalScope)
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block">Cơ sở <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i data-lucide="building-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <select name="center_id" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                        <option value="">-- Chọn cơ sở --</option>
                        @foreach($centers as $c)
                            <option value="{{ $c->id }}" {{ old('center_id') === $c->id ? 'selected' : '' }}>[{{ $c->code }}] {{ $c->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                </div>
                @error('center_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            @endif

        </div>
    </form>
</div>
<div class="px-6 py-4 border-t border-slate-100 flex gap-3 justify-end shrink-0 bg-slate-50">
    <button type="button" @click="showDynamicModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-200 rounded-xl transition">Hủy</button>
    <button type="submit" form="create-form" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
        <i data-lucide="plus" class="w-4 h-4"></i> Thêm Xong
    </button>
</div>
