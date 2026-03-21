<div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
    <div class="flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center text-lg font-bold border border-primary-200">
            {{ strtoupper(substr($lead->name, 0, 1)) }}
        </div>
        <div>
            <h3 class="text-lg font-semibold text-slate-800">Chỉnh sửa Lead</h3>
            <p class="text-slate-500 text-xs font-medium">{{ $lead->name }}</p>
        </div>
    </div>
    <button type="button" @click="showDynamicModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
</div>

<form action="{{ route('admin.leads.update', $lead->id) }}" method="POST" class="p-6 flex-1 overflow-y-auto space-y-6">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1">
            <label class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Họ và tên <span class="text-red-500">*</span></label>
            <div class="relative">
                <i data-lucide="user" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="name" required value="{{ old('name', $lead->name) }}"
                       class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none">
            </div>
            @error('name') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-1">
            <label class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Số điện thoại <span class="text-red-500">*</span></label>
            <div class="relative">
                <i data-lucide="phone" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="phone" required value="{{ old('phone', $lead->phone) }}"
                       class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none tabular-nums">
            </div>
            @error('phone') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1">
            <label class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Email</label>
            <div class="relative">
                <i data-lucide="mail" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="email" name="email" value="{{ old('email', $lead->email) }}"
                       class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none">
            </div>
            @error('email') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-1">
            <label class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Ngày sinh</label>
            <div class="relative">
                <i data-lucide="calendar" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="date" name="dob" value="{{ old('dob', $lead->dob ? \Carbon\Carbon::parse($lead->dob)->format('Y-m-d') : '') }}"
                       class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none">
            </div>
            @error('dob') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1">
            <label class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Trạng thái <span class="text-red-500">*</span></label>
            <div class="relative">
                <i data-lucide="tag" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <select name="status_id" required class="w-full pl-10 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none">
                    @foreach($statuses as $st)
                        <option value="{{ $st->getId() }}" {{ old('status_id', $lead->status_id) == $st->getId() ? 'selected' : '' }}>{{ $st->name }}</option>
                    @endforeach
                </select>
                <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Nguồn</label>
            <div class="relative">
                <i data-lucide="share-2" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <select name="lead_source_id" class="w-full pl-10 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none">
                    <option value="">-- Chưa rõ nguồn --</option>
                    @foreach($leadSources as $source)
                        <option value="{{ $source->id }}" {{ old('lead_source_id', $lead->lead_source_id) == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                    @endforeach
                </select>
                <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1">
            <label class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Nhu cầu</label>
            <div class="relative">
                <i data-lucide="list-todo" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <select name="interest_type_id" class="w-full pl-10 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none">
                    <option value="">-- Chưa rõ nhu cầu --</option>
                    @foreach($interestTypes as $interest)
                        <option value="{{ $interest->id }}" {{ old('interest_type_id', $lead->interest_type_id) == $interest->id ? 'selected' : '' }}>{{ $interest->name }}</option>
                    @endforeach
                </select>
                <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Chiến dịch</label>
            <div class="relative">
                <i data-lucide="megaphone" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <select name="campaign_id" class="w-full pl-10 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none">
                    <option value="">-- Không --</option>
                    <template x-for="c in allCampaigns.filter(i => !selectedCenterId || i.center_id == selectedCenterId || !i.center_id)">
                        <option :value="c.id" x-text="c.name" :selected="c.id == '{{ $lead->campaign_id }}'"></option>
                    </template>
                </select>
                <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        @if($isGlobalScope)
        <div class="space-y-1">
            <label class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Cơ sở <span class="text-red-500">*</span></label>
            <div class="relative">
                <i data-lucide="building-2" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <select name="center_id" required x-model="selectedCenterId" class="w-full pl-10 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none">
                    @foreach($centers as $center)
                        <option value="{{ $center->id }}" {{ old('center_id', $lead->center_id) == $center->id ? 'selected' : '' }}>[{{ $center->code }}] {{ $center->name }}</option>
                    @endforeach
                </select>
                <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            </div>
        </div>
        @endif

        <div class="space-y-1">
            <label class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Người phụ trách</label>
            <div class="relative">
                <i data-lucide="user-check" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <select name="assigned_to" class="w-full pl-10 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none">
                    <option value="">-- Chưa giao --</option>
                    <template x-for="u in allUsers.filter(i => !selectedCenterId || i.center_id == selectedCenterId || !i.center_id)">
                        <option :value="u.id" x-text="u.name" :selected="u.id == '{{ $lead->assigned_to }}'"></option>
                    </template>
                </select>
                <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            </div>
        </div>
    </div>

    <div class="space-y-1 text-xs font-bold text-slate-600 uppercase tracking-wider">Tags</div>
    <div class="flex flex-wrap gap-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
        @php $leadTagIds = $lead->tags->pluck('id')->toArray(); @endphp
        @foreach($allTags as $tag)
        <label class="relative flex items-center group cursor-pointer">
            <input type="checkbox" name="tag_ids[]" value="{{ $tag->getId() }}" {{ in_array($tag->getId(), old('tag_ids', $leadTagIds)) ? 'checked' : '' }} class="peer appearance-none absolute">
            <span class="px-3 py-1.5 rounded-lg text-[10px] font-bold border-2 transition-all duration-200 flex items-center gap-1.5
                peer-checked:bg-[var(--tag-color)] peer-checked:border-[var(--tag-color)] peer-checked:text-white
                hover:border-[var(--tag-color)] hover:bg-[var(--tag-color)]/5"
                style="--tag-color: {{ $tag->color }}; border-color: {{ $tag->color }}30; color: {{ $tag->color }}">
                <i data-lucide="tag" class="w-3 h-3"></i>
                {{ $tag->name }}
            </span>
        </label>
        @endforeach
    </div>

    <div class="pt-6 border-t border-slate-100 flex gap-3 justify-end">
        <button type="button" @click="showDynamicModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
        <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-bold">
            <i data-lucide="save" class="w-4 h-4"></i> Lưu cập nhật
        </button>
    </div>
</form>
