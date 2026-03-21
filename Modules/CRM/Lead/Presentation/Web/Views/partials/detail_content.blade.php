<div x-data="{ activeTab: 'info' }" class="flex flex-col flex-1 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center text-lg font-bold border border-primary-200">
                {{ strtoupper(substr($lead->name, 0, 1)) }}
            </div>
            <div>
                <h3 class="text-lg font-semibold text-slate-800">{{ $lead->name }}</h3>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Mã Lead:</span>
                    <span class="px-2 py-0.5 bg-slate-100 rounded-md text-[10px] font-black text-slate-700 uppercase tracking-wider">{{ substr($lead->id, -8) }}</span>
                </div>
            </div>
        </div>
        <button type="button" @click="showDynamicModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <div class="p-6 flex-1 overflow-y-auto">
        <div class="border-b border-slate-200 mb-6">
            <div class="flex gap-6">
                <button @click="activeTab = 'info'" 
                        :class="activeTab === 'info' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'" 
                        class="pb-3 text-xs font-bold border-b-2 transition-all flex items-center gap-2">
                    <i data-lucide="user-circle" class="w-3.5 h-3.5"></i> Hồ sơ
                </button>
                <button @click="activeTab = 'timeline'" 
                        :class="activeTab === 'timeline' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'" 
                        class="pb-3 text-xs font-bold border-b-2 transition-all flex items-center gap-2">
                    <i data-lucide="history" class="w-3.5 h-3.5"></i> Timeline
                </button>
                <button @click="activeTab = 'notes'" 
                        :class="activeTab === 'notes' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'" 
                        class="pb-3 text-xs font-bold border-b-2 transition-all flex items-center gap-2">
                    <i data-lucide="sticky-note" class="w-3.5 h-3.5"></i> Ghi chú
                </button>
            </div>
        </div>

        <div>
            {{-- Info Tab --}}
            <div x-show="activeTab === 'info'" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="w-1 h-3 bg-primary-500 rounded-full"></span>
                            Liên hệ
                        </h4>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center">
                                    <i data-lucide="phone" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase">Điện thoại</p>
                                    <p class="text-sm font-bold text-slate-800">{{ $lead->phone }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center">
                                    <i data-lucide="mail" class="w-4 h-4"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[9px] font-bold text-slate-400 uppercase">Email</p>
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ $lead->email ?? '—' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center">
                                    <i data-lucide="cake" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase">Ngày sinh</p>
                                    <p class="text-sm font-bold text-slate-800">{{ $lead->dob ? \Carbon\Carbon::parse($lead->dob)->format('d/m/Y') : '—' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="w-1 h-3 bg-orange-500 rounded-full"></span>
                            Marketing
                        </h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-xs font-bold text-slate-500">Trạng thái</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-primary-50 text-primary-600">
                                    {{ $lead->leadStatus->name ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-xs font-bold text-slate-500">Nguồn</span>
                                <span class="text-xs font-black text-slate-800">{{ $lead->leadSource->name ?? '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-xs font-bold text-slate-500">Nhu cầu</span>
                                <span class="text-xs font-black text-slate-800">{{ $lead->interestType->name ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-primary-50/50 rounded-2xl border border-primary-100 flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase mb-0.5">Người phụ trách</p>
                        <p class="text-sm font-black text-primary-700">{{ $lead->assignTo->name ?? 'Chưa gán' }}</p>
                    </div>
                    @if($lead->center)
                    <div class="text-right">
                        <p class="text-[9px] font-bold text-slate-400 uppercase mb-0.5">Cơ sở</p>
                        <p class="text-xs font-bold text-slate-700">[{{ $lead->center->code }}] {{ $lead->center->name }}</p>
                    </div>
                    @endif
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($lead->tags as $tag)
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-100 bg-white text-slate-500 shadow-sm">
                        #{{ $tag->name }}
                    </span>
                    @endforeach
                </div>
            </div>

            {{-- Timeline Tab --}}
            <div x-show="activeTab === 'timeline'" class="space-y-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                @if($activities->isEmpty())
                    <p class="text-center py-8 text-slate-400 text-xs italic">Chưa có hoạt động nào được ghi nhận.</p>
                @else
                    <div class="relative pl-6 space-y-6">
                        <div class="absolute left-1.5 top-2 bottom-2 w-0.5 bg-slate-100"></div>
                        @foreach($activities as $activity)
                        <div class="relative">
                            <div class="absolute -left-[22px] top-1.5 w-3 h-3 rounded-full border-2 border-white bg-primary-500 shadow-sm"></div>
                            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-black uppercase text-primary-600 tracking-widest">{{ $activity->activity_type }}</span>
                                    <span class="text-[9px] font-bold text-slate-400">{{ $activity->created_at->format('H:i - d/m/Y') }}</span>
                                </div>
                                <p class="text-xs text-slate-600 italic leading-relaxed">"{!! nl2br(e($activity->description)) !!}"</p>
                                @if($activity->creator)
                                <p class="mt-2 text-[9px] text-slate-400 font-bold text-right uppercase tracking-tighter">Bởi: {{ $activity->creator->name }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Notes Tab --}}
            <div x-show="activeTab === 'notes'" class="space-y-4">
                <div class="max-h-[500px] overflow-y-auto pr-2 custom-scrollbar space-y-4">
                @if($notes->isEmpty())
                    <p class="text-center py-8 text-slate-400 text-xs italic">Chưa có ghi chú nào.</p>
                @else
                    @foreach($notes as $note)
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <p class="text-xs text-slate-700 leading-relaxed mb-3">{!! nl2br(e($note->content)) !!}</p>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-200/50">
                            <p class="text-[9px] font-bold text-slate-800">{{ $note->creator->name ?? 'N/A' }}</p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase">{{ $note->created_at->format('H:i - d/m/Y') }}</p>
                        </div>
                    </div>
                    @endforeach
                @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
