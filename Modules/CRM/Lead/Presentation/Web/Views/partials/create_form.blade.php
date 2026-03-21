<div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
        </div>
        Tạo Lead Mới
    </h3>
    <button type="button" @click="showDynamicModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
</div>

<form action="{{ route('admin.leads.store') }}" method="POST" class="p-6">
    @csrf
    <div class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Họ tên <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i data-lucide="user" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('name') }}">
                </div>
                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Số điện thoại <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i data-lucide="phone" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="phone" required class="w-full pl-10 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition tabular-nums" value="{{ old('phone') }}">
                </div>
                @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block">Email</label>
                <div class="relative">
                    <i data-lucide="mail" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="email" name="email" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('email') }}">
                </div>
                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Ngày sinh</label>
                <div class="relative">
                    <i data-lucide="calendar" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="date" name="dob" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('dob') }}">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Giới tính</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="gender" value="MALE" class="peer hidden" {{ old('gender') === 'MALE' ? 'checked' : '' }}>
                        <div class="flex items-center justify-center py-2 rounded-xl border border-slate-200 peer-checked:bg-primary-50 peer-checked:border-primary-500 peer-checked:text-primary-700 hover:bg-slate-50 transition text-sm">Nam</div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="gender" value="FEMALE" class="peer hidden" {{ old('gender') === 'FEMALE' ? 'checked' : '' }}>
                        <div class="flex items-center justify-center py-2 rounded-xl border border-slate-200 peer-checked:bg-primary-50 peer-checked:border-primary-500 peer-checked:text-primary-700 hover:bg-slate-50 transition text-sm">Nữ</div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="gender" value="OTHER" class="peer hidden" {{ old('gender') === 'OTHER' ? 'checked' : '' }}>
                        <div class="flex items-center justify-center py-2 rounded-xl border border-slate-200 peer-checked:bg-primary-50 peer-checked:border-primary-500 peer-checked:text-primary-700 hover:bg-slate-50 transition text-sm">Khác</div>
                    </label>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block">Nguồn</label>
                <div class="relative">
                    <i data-lucide="share-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <select name="lead_source_id" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                        <option value="">-- Chọn Nguồn --</option>
                        @foreach($leadSources as $leadSource)
                            <option value="{{ $leadSource->id }}" {{ old('lead_source_id') === $leadSource->id ? 'selected' : '' }}>{{ $leadSource->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                </div>
                @error('lead_source_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block">Nhu cầu (Dịch vụ)</label>
                <div class="relative">
                    <i data-lucide="list-todo" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <select name="interest_type_id" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                        <option value="">-- Chọn Nhu cầu --</option>
                        @foreach($interestTypes as $interest)
                            <option value="{{ $interest->id }}" {{ old('interest_type_id') === $interest->id ? 'selected' : '' }}>{{ $interest->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                </div>
                @error('interest_type_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            @if($isGlobalScope)
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Cơ sở <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i data-lucide="building-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <select name="center_id" required x-model="selectedCenterId" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                        <option value="">-- Chọn cơ sở --</option>
                        @foreach($centers as $center)
                            <option value="{{ $center->id }}" {{ old('center_id') === $center->id ? 'selected' : '' }}>[{{ $center->code }}] {{ $center->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                </div>
                @error('center_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            @endif

            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Người phụ trách</label>
                <div class="relative">
                    <i data-lucide="user-check" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <select name="assigned_to" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                        <option value="">-- Chưa giao --</option>
                        <template x-for="u in allUsers.filter(i => !selectedCenterId || i.center_id == selectedCenterId || !i.center_id)">
                            <option :value="u.id" x-text="u.name" :selected="u.id == '{{ old('assigned_to') }}'"></option>
                        </template>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                </div>
                @error('assigned_to') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Chiến dịch</label>
                <div class="relative">
                    <i data-lucide="megaphone" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <select name="campaign_id" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                        <option value="">-- Chọn chiến dịch/category --</option>
                        <template x-for="c in allCampaigns.filter(i => !selectedCenterId || i.center_id == selectedCenterId || !i.center_id)">
                            <option :value="c.id" x-text="c.name" :selected="c.id == '{{ old('campaign_id') }}'"></option>
                        </template>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                </div>
                @error('campaign_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Trạng thái</label>
                <div class="relative">
                    <i data-lucide="tag" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <select name="status_id" class="w-full pl-10 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                        <option value="">-- Mặc định (New) --</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st->getId() }}" {{ old('status_id') === $st->getId() ? 'selected' : '' }}>{{ $st->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                </div>
                @error('status_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Tags Section --}}
        <div class="space-y-1 mt-4">
            <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Phân loại (Tags)</label>
            <div class="flex flex-wrap gap-2 p-3 border border-slate-100 rounded-xl bg-slate-50/50">
                @foreach($allTags as $tag)
                <label class="relative flex items-center group cursor-pointer">
                    <input type="checkbox" name="tag_ids[]" value="{{ $tag->getId() }}" {{ in_array($tag->getId(), old('tag_ids', [])) ? 'checked' : '' }} class="peer appearance-none absolute">
                    <span class="px-3 py-1.5 rounded-lg text-xs font-bold border-2 transition-all duration-200 flex items-center gap-1.5
                        peer-checked:bg-[var(--tag-color)] peer-checked:border-[var(--tag-color)] peer-checked:text-white peer-checked:shadow-md
                        hover:border-[var(--tag-color)] hover:bg-[var(--tag-color)]/5"
                        style="--tag-color: {{ $tag->color }}; border-color: {{ $tag->color }}40; color: {{ $tag->color }}">
                        <i data-lucide="tag" class="w-3 h-3"></i>
                        {{ $tag->name }}
                    </span>
                </label>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="pt-6 mt-6 border-t border-slate-100 flex gap-3 justify-end">
        <button type="button" @click="showDynamicModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
        <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
            <i data-lucide="save" class="w-4 h-4"></i> Tạo Lead
        </button>
    </div>
</form>
