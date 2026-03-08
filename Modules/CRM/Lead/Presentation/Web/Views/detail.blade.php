@extends('layouts.app')

@section('title', 'Chi tiết Lead - ' . $lead->name)

@section('content')
<div class="space-y-8" x-data="leadDetailStore()">
    {{-- Top Action Bar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <nav class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.leads.index') }}" class="text-slate-400 hover:text-primary-600 transition-colors flex items-center gap-1.5 group">
                <i data-lucide="chevron-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
                Danh sách Leads
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-bold tracking-tight">{{ $lead->name }}</span>
        </nav>

        <div class="flex items-center gap-3">
            @can('leads.update')
            <a href="{{ route('admin.leads.edit', $lead->id) }}" class="px-5 py-2.5 bg-slate-100/80 text-slate-700 rounded-xl hover:bg-slate-200 transition-all font-bold text-sm flex items-center gap-2 border border-white shadow-sm active:scale-95">
                <i data-lucide="edit-3" class="w-4 h-4"></i>
                Chỉnh sửa
            </a>
            @endcan
            <a href="{{ route('admin.leads.convert', $lead->id) }}" class="px-5 py-2.5 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/20 flex items-center gap-2 text-sm">
                <i data-lucide="user-plus" class="w-4 h-4"></i> Chuyển đổi Học viên
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Left Column: Profile Card --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="glass overflow-hidden rounded-[2.5rem] border border-white/40 shadow-xl shadow-slate-200/50">
                <div class="bg-gradient-to-br from-primary-500/10 to-transparent p-8 text-center border-b border-slate-100">
                    <div class="relative inline-block mb-4">
                        <div class="w-24 h-24 rounded-3xl bg-white shadow-xl shadow-primary-500/10 flex items-center justify-center text-4xl font-extrabold text-primary-600 border-2 border-primary-100">
                            {{ strtoupper(substr($lead->name, 0, 1)) }}
                        </div>
                        @php
                            $st = $lead->leadStatus;
                            $statusColor = $st ? $st->color : '#94a3b8';
                        @endphp
                        <div class="absolute -bottom-2 -right-2 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter shadow-lg border-2 border-white" 
                             style="background-color: {{ $statusColor }}; color: white;">
                            {{ $st ? $st->name : 'N/A' }}
                        </div>
                    </div>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">{{ $lead->name }}</h2>
                    <p class="text-slate-500 text-sm font-medium mt-1">{{ $lead->phone }}</p>
                    
                    @if($lead->tags->isNotEmpty())
                    <div class="mt-4 flex flex-wrap justify-center gap-1.5">
                        @foreach($lead->tags as $tag)
                            <span class="px-3 py-1 rounded-lg text-[10px] font-bold text-white shadow-sm transition-transform hover:scale-110" style="background-color: {{ $tag->color ?: 'gray' }}">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                    @endif
                </div>

                <div class="p-8 space-y-8">
                    {{-- Contact Group --}}
                    <div>
                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Thông tin liên hệ</h4>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 group">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center group-hover:bg-primary-50 group-hover:text-primary-600 transition-colors">
                                    <i data-lucide="phone" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Điện thoại</p>
                                    <p class="text-sm font-bold text-slate-700">{{ $lead->phone }}</p>
                                </div>
                            </div>
                            @if($lead->email)
                            <div class="flex items-center gap-4 group">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center group-hover:bg-primary-50 group-hover:text-primary-600 transition-colors">
                                    <i data-lucide="mail" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email</p>
                                    <p class="text-sm font-bold text-slate-700 truncate max-w-[180px]">{{ $lead->email }}</p>
                                </div>
                            </div>
                            @endif
                            @if($lead->dob)
                            <div class="flex items-center gap-4 group">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center group-hover:bg-primary-50 group-hover:text-primary-600 transition-colors">
                                    <i data-lucide="cake" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ngày sinh</p>
                                    <p class="text-sm font-bold text-slate-700">{{ \Carbon\Carbon::parse($lead->dob)->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Marketing Group --}}
                    <div class="pt-6 border-t border-slate-100">
                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Marketing & Nhu cầu</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-orange-50/50 rounded-2xl border border-orange-100">
                                <i data-lucide="share-2" class="w-4 h-4 text-orange-400 mb-2"></i>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nguồn</p>
                                <p class="text-xs font-bold text-slate-700 mt-0.5">{{ $lead->leadSource?->name ?? '—' }}</p>
                            </div>
                            <div class="p-4 bg-violet-50/50 rounded-2xl border border-violet-100">
                                <i data-lucide="list-todo" class="w-4 h-4 text-violet-400 mb-2"></i>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nhu cầu</p>
                                <p class="text-xs font-bold text-slate-700 mt-0.5">{{ $lead->interestType?->name ?? '—' }}</p>
                            </div>
                            <div class="col-span-2 p-4 bg-pink-50/50 rounded-2xl border border-pink-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <i data-lucide="megaphone" class="w-4 h-4 text-pink-400"></i>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Chiến dịch</p>
                                </div>
                                @php
                                    $campaign = $lead->campaign_id ? $campaigns->firstWhere('id', $lead->campaign_id) : null;
                                @endphp
                                <p class="text-xs font-bold text-slate-700">{{ $campaign?->name ?? '—' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Management Group --}}
                    <div class="pt-6 border-t border-slate-100">
                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Quản lý hệ thống</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                                        <i data-lucide="building-2" class="w-4 h-4"></i>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-600">Cơ sở vận hành</span>
                                </div>
                                <span class="text-xs font-bold text-slate-800">
                                    @if($lead->center)
                                        [{{ $lead->center->code }}] {{ $lead->center->name }}
                                    @else — @endif
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                        <i data-lucide="user-check" class="w-4 h-4"></i>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-600">Phụ trách viên</span>
                                </div>
                                <span class="text-xs font-bold text-slate-800">
                                    @if($lead->assignTo)
                                        {{ $lead->assignTo->name }}
                                    @else
                                        <span class="text-slate-400 italic">Chưa gán</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[10px] font-medium text-slate-400">ID: {{ substr($lead->id, -8) }}</span>
                    <span class="text-[10px] font-medium text-slate-400">Tạo: {{ $lead->created_at?->format('d/m/Y') }}</span>
                </div>
            </div>

            {{-- Activity Recorder --}}
            <div class="bg-white rounded-[2rem] border border-slate-200/60 shadow-sm p-8">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <i data-lucide="zap" class="w-4 h-4 text-primary-500"></i> Ghi nhận nhanh
                </h3>
                <form action="{{ route('admin.leads.activities.store', $lead->id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <button type="submit" name="activity_type" value="call" class="p-4 rounded-2xl border border-slate-100 bg-slate-50/30 hover:border-blue-200 hover:bg-blue-50/50 hover:shadow-md transition-all group">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform"><i data-lucide="phone-call" class="w-5 h-5"></i></div>
                            <p class="text-xs font-bold text-slate-600">Cuộc gọi</p>
                        </button>
                        <button type="submit" name="activity_type" value="meeting" class="p-4 rounded-2xl border border-slate-100 bg-slate-50/30 hover:border-purple-200 hover:bg-purple-50/50 hover:shadow-md transition-all group">
                            <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform"><i data-lucide="calendar-days" class="w-5 h-5"></i></div>
                            <p class="text-xs font-bold text-slate-600">Cuộc hẹn</p>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Column: Main Feed --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Tab Controls --}}
            <div class="bg-white rounded-[2.5rem] border border-slate-200/60 shadow-sm overflow-hidden p-2">
                <div class="flex gap-1">
                    <button @click="setTab('timeline')" :class="activeTab === 'timeline' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/20' : 'text-slate-500 hover:bg-slate-50'" class="flex-1 py-3 text-sm font-bold rounded-2xl transition-all flex items-center justify-center gap-2">
                        <i data-lucide="history" class="w-4 h-4"></i> Timeline
                        <span :class="activeTab === 'timeline' ? 'bg-white/20' : 'bg-slate-100'" class="px-2 py-0.5 rounded-full text-[10px]">{{ $activities->total() }}</span>
                    </button>
                    <button @click="setTab('notes')" :class="activeTab === 'notes' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/20' : 'text-slate-500 hover:bg-slate-50'" class="flex-1 py-3 text-sm font-bold rounded-2xl transition-all flex items-center justify-center gap-2">
                        <i data-lucide="sticky-note" class="w-4 h-4"></i> Ghi chú
                        <span :class="activeTab === 'notes' ? 'bg-white/20' : 'bg-slate-100'" class="px-2 py-0.5 rounded-full text-[10px]">{{ $notes->total() }}</span>
                    </button>
                    <button @click="setTab('assignments')" :class="activeTab === 'assignments' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/20' : 'text-slate-500 hover:bg-slate-50'" class="flex-1 py-3 text-sm font-bold rounded-2xl transition-all flex items-center justify-center gap-2">
                        <i data-lucide="repeat" class="w-4 h-4"></i> Bàn giao
                        <span :class="activeTab === 'assignments' ? 'bg-white/20' : 'bg-slate-100'" class="px-2 py-0.5 rounded-full text-[10px]">{{ $lead->assignments->count() }}</span>
                    </button>
                </div>
            </div>

            {{-- Tab Contents --}}
            <div class="min-h-[600px]">
                {{-- Timeline --}}
                <div x-show="activeTab === 'timeline'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                    @if($activities->isEmpty())
                        <div class="bg-white rounded-[2.5rem] p-12 text-center border border-slate-100 shadow-sm">
                            <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-6">
                                <i data-lucide="activity" class="w-10 h-10 text-slate-200"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Chưa có lịch sử hoạt động</h3>
                            <p class="text-slate-400 text-sm mt-2">Mọi tương tác với Lead này sẽ được lưu giữ tại đây.</p>
                        </div>
                    @else
                        <div class="relative pl-8 border-l-2 border-slate-100 ml-4 space-y-10 py-4">
                            @foreach($activities as $activity)
                                @php
                                    $config = match($activity->activity_type) {
                                        'call' => ['icon' => 'phone-call', 'color' => 'bg-blue-500', 'label' => 'Cuộc gọi'],
                                        'meeting' => ['icon' => 'calendar-check', 'color' => 'bg-purple-500', 'label' => 'Cuộc hẹn'],
                                        'status_change' => ['icon' => 'refresh-cw', 'color' => 'bg-indigo-500', 'label' => 'Thay đổi trạng thái'],
                                        'note' => ['icon' => 'sticky-note', 'color' => 'bg-amber-500', 'label' => 'Ghi chú'],
                                        'conversion' => ['icon' => 'sparkles', 'color' => 'bg-emerald-500', 'label' => 'Chuyển đổi'],
                                        default => ['icon' => 'activity', 'color' => 'bg-slate-500', 'label' => 'Hoạt động']
                                    };
                                @endphp
                                <div class="relative">
                                    {{-- Time Dot --}}
                                    <div class="absolute -left-[41px] top-0 w-[18px] h-[18px] rounded-full border-4 border-white shadow-md {{ $config['color'] }} z-10 transition-transform hover:scale-150"></div>
                                    
                                    <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
                                        {{-- Visual Flair --}}
                                        <div class="absolute top-0 right-0 w-32 h-32 {{ $config['color'] }} opacity-[0.03] rounded-full -mr-16 -mt-16 group-hover:opacity-[0.05] transition-opacity"></div>
                                        
                                        <div class="flex items-start justify-between gap-4 mb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $config['color'] }} text-white shadow-lg">
                                                    <i data-lucide="{{ $config['icon'] }}" class="w-5 h-5"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-black text-slate-800 tracking-tight">{{ $config['label'] }}</h4>
                                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $activity->created_at?->translatedFormat('H:i - d/m/Y') }}</p>
                                                </div>
                                            </div>
                                            <span class="text-[11px] font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-full">{{ $activity->created_at?->diffForHumans() }}</span>
                                        </div>
                                        <div class="pl-13">
                                            <p class="text-sm text-slate-600 leading-relaxed font-medium">
                                                {!! nl2br(e($activity->description)) !!}
                                            </p>
                                            @if($activity->creator)
                                                <div class="mt-4 flex items-center gap-2 pt-4 border-t border-slate-50">
                                                    <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-600 uppercase">
                                                        {{ substr($activity->creator->name, 0, 1) }}
                                                    </div>
                                                    <span class="text-[11px] font-bold text-slate-500">{{ $activity->creator->name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Notes --}}
                <div x-show="activeTab === 'notes'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                    <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
                        <form action="{{ route('admin.leads.notes.store', $lead->id) }}" method="POST">
                            @csrf
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 ml-4">Để lại ghi chú mới</label>
                            <div class="relative">
                                <textarea name="content" rows="4" required class="w-full rounded-[2rem] bg-slate-50/50 border-slate-100 p-6 text-sm text-slate-700 placeholder-slate-300 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500/30 transition-all outline-none resize-none" placeholder="Nhập nội dung ghi chú ở đây..."></textarea>
                                <div class="absolute bottom-4 right-4">
                                    <button type="submit" class="w-12 h-12 rounded-2xl bg-primary-600 text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 hover:scale-105 active:scale-95 transition-all flex items-center justify-center">
                                        <i data-lucide="send-horizontal" class="w-5 h-5"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if($notes->isEmpty())
                        <div class="text-center py-12">
                             <p class="text-slate-400 text-sm italic">Chưa có ghi chú nào cho Lead này.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($notes as $note)
                                <div class="bg-amber-50/50 rounded-[2rem] p-6 border-2 border-amber-100/30 relative group hover:rotate-1 transition-transform">
                                    <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i data-lucide="pin" class="w-4 h-4 text-amber-300"></i>
                                    </div>
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-7 h-7 rounded-full bg-amber-200 flex items-center justify-center text-[10px] font-black text-amber-700 uppercase">
                                            {{ substr($note->creator?->name ?? '?', 0, 1) }}
                                        </div>
                                        <span class="text-xs font-bold text-amber-800">{{ $note->creator?->name }}</span>
                                        <span class="text-[10px] text-amber-500/60 ml-auto">{{ $note->created_at?->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-amber-900/80 leading-relaxed italic line-clamp-6">"{{ $note->content }}"</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Assignments --}}
                <div x-show="activeTab === 'assignments'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                    @if($lead->assignments->isEmpty())
                        <div class="bg-white rounded-[2.5rem] p-12 text-center border border-slate-100 shadow-sm">
                            <i data-lucide="user-plus" class="w-12 h-12 text-slate-200 mx-auto mb-4"></i>
                            <p class="text-slate-400">Lead này chưa từng được bàn giao.</p>
                        </div>
                    @else
                        @foreach($lead->assignments as $assignment)
                            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center gap-6">
                                <div class="flex -space-x-4">
                                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 border-4 border-white shadow-sm flex items-center justify-center text-indigo-600 font-black" title="Bàn giao tới">
                                        {{ substr($assignment->assignedToUser->name ?? '?', 0, 1) }}
                                    </div>
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 border-4 border-white shadow-sm flex items-center justify-center text-slate-400 font-black" title="Người bàn giao">
                                        {{ substr($assignment->assignedByUser->name ?? '?', 0, 1) }}
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm">
                                        <span class="font-black text-slate-800">{{ $assignment->assignedByUser->name ?? 'Hệ thống' }}</span>
                                        <span class="text-slate-400 mx-1">đã gán cho</span>
                                        <span class="font-black text-primary-600">{{ $assignment->assignedToUser->name ?? 'Chưa rõ' }}</span>
                                    </p>
                                    <p class="text-[11px] text-slate-400 font-bold mt-1 uppercase tracking-tighter">{{ $assignment->created_at->translatedFormat('H:i - d/m/Y') }} ({{ $assignment->created_at->diffForHumans() }})</p>
                                </div>
                                @if($assignment->notes)
                                    <div class="max-w-xs p-3 bg-slate-50 rounded-xl border border-slate-100 text-[11px] text-slate-500 font-medium italic">
                                        {{ $assignment->notes }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('leadDetailStore', () => ({
            activeTab: 'timeline',
            
            setTab(tab) {
                this.activeTab = tab;
                this.$nextTick(() => {
                    if (window.lucide) { lucide.createIcons(); }
                });
            }
        }));
    });

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) { lucide.createIcons(); }
    });
</script>
@endpush
