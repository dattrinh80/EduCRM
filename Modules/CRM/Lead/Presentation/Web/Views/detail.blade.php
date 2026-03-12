@extends('layouts.app')

@section('title', 'Chi tiết Lead - ' . $lead->name)

@section('content')
<div class="space-y-8" x-data="{ 
    activeTab: 'info', 
    showTaskModal: false,
    setTab(tab) {
        this.activeTab = tab;
        this.$nextTick(() => {
            if (window.lucide) { lucide.createIcons(); }
        });
    }
}">
    {{-- Top Title & Context --}}
    <div class="flex flex-col gap-1 mb-8">
        <nav class="flex items-center gap-2 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600 transition-colors">Hệ thống</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <a href="{{ route('admin.leads.index') }}" class="hover:text-primary-600 transition-colors">Quản lý Leads</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-slate-800">Thông tin chi tiết</span>
        </nav>
        <div class="flex items-center gap-4 mt-2">
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Chi tiết hồ sơ Lead</h1>
            <div class="h-8 w-px bg-slate-200"></div>
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-slate-500">Mã Lead:</span>
                <span class="px-3 py-1 bg-slate-100 rounded-lg text-xs font-black text-slate-700 tracking-wider uppercase">{{ substr($lead->id, -8) }}</span>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-8">
        {{-- Full Width: Dynamic Feed --}}
        <div class="w-full flex flex-col gap-6">
            {{-- Flat Tab Navigation --}}
            <div class="border-b border-slate-200">
                <div class="flex flex-wrap md:flex-nowrap gap-8">
                    <button @click="setTab('info')" :class="activeTab === 'info' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="pb-4 text-sm font-bold border-b-2 transition-all flex items-center gap-2">
                        <i data-lucide="user-circle" class="w-4 h-4"></i> Hồ sơ Lead
                    </button>
                    <button @click="setTab('timeline')" :class="activeTab === 'timeline' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="pb-4 text-sm font-bold border-b-2 transition-all flex items-center gap-2 relative">
                        <i data-lucide="history" class="w-4 h-4"></i> Timeline
                        <span :class="activeTab === 'timeline' ? 'bg-primary-100 text-primary-600' : 'bg-slate-100 text-slate-500'" class="px-2 py-0.5 rounded-full text-[9px] font-black tabular-nums tracking-tighter">{{ $activities->total() }}</span>
                    </button>
                    <button @click="setTab('notes')" :class="activeTab === 'notes' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="pb-4 text-sm font-bold border-b-2 transition-all flex items-center gap-2 relative">
                        <i data-lucide="sticky-note" class="w-4 h-4"></i> Ghi chú
                        <span :class="activeTab === 'notes' ? 'bg-primary-100 text-primary-600' : 'bg-slate-100 text-slate-500'" class="px-2 py-0.5 rounded-full text-[9px] font-black tabular-nums tracking-tighter">{{ $notes->total() }}</span>
                    </button>
                    <button @click="setTab('assignments')" :class="activeTab === 'assignments' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="pb-4 text-sm font-bold border-b-2 transition-all flex items-center gap-2 relative">
                        <i data-lucide="repeat" class="w-4 h-4"></i> Bàn giao
                        <span :class="activeTab === 'assignments' ? 'bg-primary-100 text-primary-600' : 'bg-slate-100 text-slate-500'" class="px-2 py-0.5 rounded-full text-[9px] font-black tabular-nums tracking-tighter">{{ $lead->assignments?->count() ?? 0 }}</span>
                    </button>
                    <button @click="setTab('tasks')" :class="activeTab === 'tasks' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="pb-4 text-sm font-bold border-b-2 transition-all flex items-center gap-2 relative">
                        <i data-lucide="check-square" class="w-4 h-4"></i> Nhiệm vụ
                        <span :class="activeTab === 'tasks' ? 'bg-primary-100 text-primary-600' : 'bg-slate-100 text-slate-500'" class="px-2 py-0.5 rounded-full text-[9px] font-black tabular-nums tracking-tighter">{{ $tasks?->count() ?? 0 }}</span>
                    </button>
                </div>
            </div>

            {{-- Tab Contents --}}
            <div class="min-h-[400px]">
                {{-- Lead Info Tab --}}
                <div x-show="activeTab === 'info'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                    {{-- Simple Lead Header --}}
                    <div class="bg-white rounded-[2.5rem] border border-slate-200/60 p-8 shadow-sm">
                        <div class="flex flex-col md:flex-row justify-between items-start gap-8">
                            <div class="flex-1">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Họ và tên</p>
                                <div class="flex flex-wrap items-center gap-6">
                                     <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-tight">{{ $lead->name }}</h2>
                                     <div class="h-8 w-px bg-slate-200 hidden md:block"></div>
                                     <div class="flex items-center gap-3">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Trạng thái:</span>
                                        @php $st = $lead->leadStatus; @endphp
                                        <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-primary-50 text-primary-600 border border-primary-100">
                                            {{ $st ? $st->name : 'N/A' }}
                                        </span>
                                     </div>
                                </div>
                                
                                <div class="mt-6 flex flex-wrap items-center gap-3">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mr-1">Phân loại thẻ:</span>
                                    @foreach($lead->tags as $tag)
                                        <span class="px-3 py-1 rounded-lg text-[10px] font-bold border border-slate-100 bg-slate-50 text-slate-500 shadow-sm">
                                            #{{ $tag->name }}
                                        </span>
                                    @endforeach
                                    @if($lead->tags->isEmpty())
                                        <span class="text-[10px] text-slate-300 italic">Chưa gán thẻ</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 min-w-full sm:min-w-0 md:items-end self-center">
                                 <a href="{{ route('admin.leads.convert', $lead->id) }}" class="min-w-[240px] px-10 py-5 bg-primary-600 text-white rounded-[1.5rem] font-black text-sm flex items-center justify-center gap-4 hover:bg-primary-700 transition-all shadow-2xl shadow-primary-500/30 hover:-translate-y-1 active:scale-95 group">
                                    <i data-lucide="user-plus" class="w-5 h-5 group-hover:rotate-12 transition-transform"></i> 
                                    <span>Chuyển đổi học viên</span>
                                 </a>
                            </div>
                        </div>
                    </div>

                    {{-- Main Info & Quick Actions --}}
                    <div class="bg-white rounded-[2.5rem] border border-slate-200/60 p-10 shadow-sm relative overflow-hidden">
                        {{-- Lead Background Detail Card --}}
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 relative z-10">
                            {{-- Contact Section --}}
                            <div class="space-y-8">
                                <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-3">
                                    <div class="w-1.5 h-4 bg-primary-500 rounded-full"></div>
                                    Thông tin liên hệ
                                </h4>
                                <div class="space-y-6">
                                    <div class="flex items-center gap-5 group">
                                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm">
                                            <i data-lucide="phone" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Điện thoại</p>
                                            <p class="text-base font-black text-slate-800 tabular-nums">{{ $lead->phone }}</p>
                                        </div>
                                    </div>
                                    @if($lead->email)
                                    <div class="flex items-center gap-5 group">
                                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm">
                                            <i data-lucide="mail" class="w-5 h-5"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Email</p>
                                            <p class="text-base font-black text-slate-800 truncate" title="{{ $lead->email }}">{{ $lead->email }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    @if($lead->dob)
                                    <div class="flex items-center gap-5 group">
                                        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center group-hover:scale-110 group-hover:bg-rose-600 group-hover:text-white transition-all shadow-sm">
                                            <i data-lucide="cake" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Ngày sinh</p>
                                            <p class="text-base font-black text-slate-800">{{ \Carbon\Carbon::parse($lead->dob)->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Marketing Section --}}
                            <div class="space-y-8">
                                <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-3">
                                    <div class="w-1.5 h-4 bg-orange-500 rounded-full"></div>
                                    Marketing & Nguồn
                                </h4>
                                <div class="grid grid-cols-1 gap-5">
                                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 group hover:border-orange-200 transition-all hover:bg-white hover:shadow-lg hover:shadow-orange-500/5">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Nguồn khách hàng</p>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600">
                                                <i data-lucide="share-2" class="w-4 h-4"></i>
                                            </div>
                                            <span class="text-sm font-black text-slate-800">{{ $lead->leadSource?->name ?? '—' }}</span>
                                        </div>
                                    </div>
                                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 group hover:border-violet-200 transition-all hover:bg-white hover:shadow-lg hover:shadow-violet-500/5">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Phân loại nhu cầu</p>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600">
                                                <i data-lucide="list-todo" class="w-4 h-4"></i>
                                            </div>
                                            <span class="text-sm font-black text-slate-800">{{ $lead->interestType?->name ?? '—' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- System Section --}}
                            <div class="space-y-8">
                                <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-3">
                                    <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
                                    Vận hành hệ thống
                                </h4>
                                <div class="space-y-4">
                                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex items-center justify-between group hover:bg-white hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-500/5 transition-all">
                                        <span class="text-xs font-bold text-slate-500 group-hover:text-emerald-600 transition-colors">Cơ sở vận hành</span>
                                        <span class="text-xs font-black text-slate-800">
                                            @if($lead->center) [{{ $lead->center->code }}] {{ $lead->center->name }} @else — @endif
                                        </span>
                                    </div>
                                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex items-center justify-between group hover:bg-white hover:border-primary-200 hover:shadow-lg hover:shadow-primary-500/5 transition-all">
                                        <span class="text-xs font-bold text-slate-500 group-hover:text-primary-600 transition-colors">Người phụ trách</span>
                                        <span class="text-xs font-black text-primary-600">
                                            {{ $lead->assignTo->name ?? 'Chưa gán' }}
                                        </span>
                                    </div>
                                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex items-center justify-between group hover:bg-white hover:border-slate-300 hover:shadow-lg hover:shadow-slate-500/5 transition-all">
                                        <span class="text-xs font-bold text-slate-500 group-hover:text-slate-800 transition-colors">Thời gian tạo</span>
                                        <span class="text-xs font-black text-slate-800 tabular-nums">{{ $lead->created_at?->format('H:i - d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Quick Activity Grid (Nested for better scope visibility) --}}
                        <div class="mt-16 pt-12 border-t border-slate-100">
                            <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-[0.2em] mb-10 flex items-center gap-3">
                                <div class="w-1.5 h-4 bg-primary-500 rounded-full"></div>
                                Ghi nhận nhanh các hoạt động chăm sóc
                            </h4>
                            <form action="{{ route('admin.leads.activities.store', $lead->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                @csrf
                                <button type="submit" name="activity_type" value="call" class="flex items-center gap-5 p-6 rounded-3xl bg-blue-50/40 border border-blue-100 hover:border-blue-300 hover:bg-white hover:shadow-2xl hover:shadow-blue-500/10 transition-all group active:scale-95 text-left">
                                    <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center group-hover:scale-110 transition-transform shadow-xl shadow-blue-500/30">
                                        <i data-lucide="phone-call" class="w-7 h-7"></i>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-black text-blue-900 uppercase tracking-tight">Gọi điện</span>
                                        <span class="text-xs text-blue-600 font-bold opacity-60">Ghi nhận cuộc gọi</span>
                                    </div>
                                </button>
                                <button type="submit" name="activity_type" value="meeting" class="flex items-center gap-5 p-6 rounded-3xl bg-purple-50/40 border border-purple-100 hover:border-purple-300 hover:bg-white hover:shadow-2xl hover:shadow-purple-500/10 transition-all group active:scale-95 text-left">
                                    <div class="w-14 h-14 rounded-2xl bg-purple-600 text-white flex items-center justify-center group-hover:scale-110 transition-transform shadow-xl shadow-purple-500/30">
                                        <i data-lucide="calendar-days" class="w-7 h-7"></i>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-black text-purple-900 uppercase tracking-tight">Hẹn gặp</span>
                                        <span class="text-xs text-purple-600 font-bold opacity-60">Lên lịch hẹn mới</span>
                                    </div>
                                </button>
                                <button type="button" @click="setTab('notes')" class="flex items-center gap-5 p-6 rounded-3xl bg-emerald-50/40 border border-emerald-100 hover:border-emerald-300 hover:bg-white hover:shadow-2xl hover:shadow-emerald-500/10 transition-all group active:scale-95 text-left">
                                    <div class="w-14 h-14 rounded-2xl bg-emerald-600 text-white flex items-center justify-center group-hover:scale-110 transition-transform shadow-xl shadow-emerald-500/30">
                                        <i data-lucide="sticky-note" class="w-7 h-7"></i>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-black text-emerald-900 uppercase tracking-tight">Ghi chú</span>
                                        <span class="text-xs text-emerald-600 font-bold opacity-60">Viết lời nhắc</span>
                                    </div>
                                </button>
                                <button type="button" @click="setTab('tasks')" class="flex items-center gap-5 p-6 rounded-3xl bg-amber-50/40 border border-amber-100 hover:border-amber-300 hover:bg-white hover:shadow-2xl hover:shadow-amber-500/10 transition-all group active:scale-95 text-left">
                                    <div class="w-14 h-14 rounded-2xl bg-amber-600 text-white flex items-center justify-center group-hover:scale-110 transition-transform shadow-xl shadow-amber-500/30">
                                        <i data-lucide="check-square" class="w-7 h-7"></i>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-black text-amber-900 uppercase tracking-tight">Nhiệm vụ</span>
                                        <span class="text-xs text-amber-600 font-bold opacity-60">Giao việc mới</span>
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Timeline --}}
                <div x-show="activeTab === 'timeline'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                    @if($activities->isEmpty())
                        <div class="bg-white rounded-[2.5rem] p-12 text-center border border-slate-100 shadow-sm group">
                            <div class="w-24 h-24 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-500">
                                <i data-lucide="ghost" class="w-12 h-12 text-slate-200"></i>
                            </div>
                            <h3 class="text-xl font-black text-slate-800 tracking-tight">Vùng đất trống...</h3>
                            <p class="text-slate-400 text-sm mt-2 max-w-xs mx-auto">Mọi dấu vết tương tác với khách hàng này sẽ được khắc ghi tại đây một cách chi tiết.</p>
                        </div>
                    @else
                        <div class="relative pl-12 ml-4">
                            {{-- Central Running Line --}}
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-primary-500/20 via-slate-200 to-transparent rounded-full shadow-inner"></div>

                            <div class="space-y-12">
                                @foreach($activities as $activity)
                                    @php
                                        $config = match($activity->activity_type) {
                                            'call' => [
                                                'icon' => 'phone-call', 
                                                'color' => 'from-blue-500 to-indigo-600', 
                                                'bg' => 'bg-blue-50', 
                                                'text' => 'text-blue-600',
                                                'label' => 'Chiến dịch gọi',
                                                'shadow' => 'shadow-blue-500/30'
                                            ],
                                            'meeting' => [
                                                'icon' => 'calendar-check', 
                                                'color' => 'from-purple-500 to-pink-600', 
                                                'bg' => 'bg-purple-50', 
                                                'text' => 'text-purple-600',
                                                'label' => 'Hẹn gặp trực tiếp',
                                                'shadow' => 'shadow-purple-500/30'
                                            ],
                                            'status_change' => [
                                                'icon' => 'zap', 
                                                'color' => 'from-amber-400 to-orange-500', 
                                                'bg' => 'bg-amber-50', 
                                                'text' => 'text-amber-600',
                                                'label' => 'Chuyển trạng thái',
                                                'shadow' => 'shadow-amber-500/30'
                                            ],
                                            'note' => [
                                                'icon' => 'sticky-note', 
                                                'color' => 'from-emerald-400 to-teal-500', 
                                                'bg' => 'bg-emerald-50', 
                                                'text' => 'text-emerald-600',
                                                'label' => 'Ghi chú hệ thống',
                                                'shadow' => 'shadow-emerald-500/30'
                                            ],
                                            'conversion' => [
                                                'icon' => 'award', 
                                                'color' => 'from-rose-500 to-pink-600', 
                                                'bg' => 'bg-rose-50', 
                                                'text' => 'text-rose-600',
                                                'label' => 'Thành công (Converted)',
                                                'shadow' => 'shadow-rose-500/30'
                                            ],
                                            default => [
                                                'icon' => 'activity', 
                                                'color' => 'from-slate-400 to-slate-600', 
                                                'bg' => 'bg-slate-50', 
                                                'text' => 'text-slate-600',
                                                'label' => 'Cập nhật khác',
                                                'shadow' => 'shadow-slate-500/30'
                                            ]
                                        };
                                    @endphp
                                    <div class="relative group">
                                        {{-- Connector Dot --}}
                                        <div class="absolute -left-[54px] top-6 w-5 h-5 rounded-full border-[4px] border-slate-50 bg-gradient-to-br {{ $config['color'] }} shadow-lg ring-4 ring-white z-10 transition-all duration-500 group-hover:scale-125"></div>
                                        
                                        <div class="bg-white rounded-[2.5rem] p-8 border border-white shadow-xl shadow-slate-200/40 relative overflow-hidden transition-all duration-500 group-hover:-translate-y-1 group-hover:shadow-2xl group-hover:shadow-primary-500/5">
                                            {{-- Glassmorphism Flare --}}
                                            <div class="absolute -top-12 -right-12 w-48 h-48 bg-gradient-to-br {{ $config['color'] }} opacity-[0.03] rounded-full blur-3xl group-hover:opacity-[0.08] transition-opacity duration-700"></div>

                                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 relative">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br {{ $config['color'] }} flex items-center justify-center text-white shadow-xl {{ $config['shadow'] }} transform transition-transform group-hover:rotate-6">
                                                        <i data-lucide="{{ $config['icon'] }}" class="w-7 h-7"></i>
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <h4 class="text-lg font-black text-slate-800 tracking-tight">{{ $config['label'] }}</h4>
                                                            <span class="px-2 py-0.5 rounded-md {{ $config['bg'] }} {{ $config['text'] }} text-[9px] font-black uppercase tracking-widest">{{ $activity->activity_type }}</span>
                                                        </div>
                                                        <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-0.5 flex items-center gap-1.5">
                                                            <i data-lucide="clock" class="w-3 h-3 opacity-60"></i>
                                                            {{ $activity->created_at?->translatedFormat('H:i, d/m/Y') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 bg-slate-50 border border-slate-100 px-4 py-2 rounded-2xl self-start md:self-center">
                                                    <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                                                    <span class="text-[11px] font-black text-slate-500 uppercase tracking-tighter">{{ $activity->created_at?->diffForHumans() }}</span>
                                                </div>
                                            </div>

                                            <div class="md:pl-[4.5rem] relative">
                                                <div class="p-6 bg-slate-50/50 rounded-3xl border border-slate-100 group-hover:bg-white group-hover:border-primary-100/50 transition-colors duration-500">
                                                    <p class="text-sm font-medium text-slate-600 leading-relaxed italic group-hover:text-slate-800 transition-colors">
                                                        "{!! nl2br(e($activity->description)) !!}"
                                                    </p>
                                                </div>

                                                @if($activity->creator)
                                                    <div class="mt-4 flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-xl bg-white shadow-sm border border-slate-100 flex items-center justify-center text-[10px] font-black text-primary-600 uppercase">
                                                            {{ substr($activity->creator->name, 0, 1) }}
                                                        </div>
                                                        <div class="text-[11px]">
                                                            <span class="text-slate-400 font-bold">Thực hiện bởi:</span>
                                                            <span class="text-slate-700 font-black ml-1">{{ $activity->creator->name }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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

                {{-- Tasks --}}
                <div x-show="activeTab === 'tasks'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-800 uppercase text-xs tracking-widest">Danh sách nhiệm vụ</h3>
                    @can('leads.update')
                        <button @click="showTaskModal = true; $nextTick(() => { if (window.lucide) { lucide.createIcons(); } })" class="px-5 py-2.5 bg-primary-100 text-primary-700 rounded-xl font-bold text-xs flex items-center gap-2 hover:bg-primary-200 transition active:scale-95 shadow-sm border border-primary-200">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i> Thêm nhiệm vụ
                        </button>
                    @endcan
                    </div>

                    @if($tasks->isEmpty())
                        <div class="bg-white rounded-[2.5rem] p-12 text-center border border-slate-100 shadow-sm">
                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 shadow-inner">
                                <i data-lucide="check-square" class="w-8 h-8 text-slate-200"></i>
                            </div>
                            <p class="text-slate-400 font-medium italic">Chưa có nhiệm vụ nào cho Lead này.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($tasks as $t)
                                @php
                                    $pColor = match($t->priority) {
                                        'URGENT' => 'bg-red-500',
                                        'HIGH' => 'bg-orange-500',
                                        'MEDIUM' => 'bg-primary-500',
                                        default => 'bg-slate-300'
                                    };
                                @endphp
                                <div class="bg-white rounded-2xl p-4 border border-slate-100 flex items-center justify-between group hover:shadow-md transition-all hover:-translate-y-0.5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-1.5 h-10 rounded-full {{ $pColor }}" title="Ưu tiên: {{ $t->priority }}"></div>
                                        <div>
                                            <h4 class="font-bold text-slate-800 text-sm {{ $t->status === 'DONE' ? 'line-through opacity-50' : '' }}">{{ $t->title }}</h4>
                                             <div class="flex items-center gap-3 mt-1 text-[10px] font-bold text-slate-400 uppercase tracking-tight">
                                                <span class="flex items-center gap-1">
                                                    <i data-lucide="calendar" class="w-3 h-3"></i> 
                                                    {{ $t->start_date ? $t->start_date->format('d/m/Y') : '' }}
                                                    {!! $t->start_date && $t->due_date ? '<i data-lucide="arrow-right" class="w-2 h-2 mx-0.5 opacity-50"></i>' : '' !!}
                                                    {{ $t->due_date ? $t->due_date->format('d/m/Y') : ($t->start_date ? 'Chưa rõ hạn' : 'Không hạn') }}
                                                </span>
                                                <span class="flex items-center gap-1"><i data-lucide="user" class="w-3 h-3"></i> {{ $t->assignedTo->name ?? 'Tự làm' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $t->status === 'DONE' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-slate-50 text-slate-500 border-slate-100' }}">
                                            {{ $t->status }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @can('leads.update')
    {{-- Task Quick Add Modal --}}
<template x-teleport="body">
    <div x-show="showTaskModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="showTaskModal = false" x-transition.opacity></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden text-left"
             x-show="showTaskModal" 
             x-init="$watch('showTaskModal', value => { if(value && window.lucide) { setTimeout(() => lucide.createIcons(), 50) } })"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
            
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    </div>
                    Tạo nhiệm vụ cho Lead
                </h3>
                <button @click="showTaskModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form action="{{ route('admin.tasks.store') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="relation_id" value="{{ $lead->id }}">
                <input type="hidden" name="relation_type" value="Lead">
                <input type="hidden" name="center_id" value="{{ $lead->center_id }}">
                
                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Tiêu đề nhiệm vụ <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i data-lucide="type" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="title" required placeholder="Nhập tên nhiệm vụ..." 
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all text-sm font-medium text-slate-700">
                        </div>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Mô tả chi tiết</label>
                        <textarea name="description" rows="3" placeholder="Ghi chú thêm về nội dung công việc..." 
                                  class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all text-sm font-medium text-slate-600 leading-relaxed"></textarea>
                    </div>

                    <div class="px-4 py-2 bg-primary-50/50 border border-primary-100 rounded-xl flex items-center gap-2 mt-4">
                        <i data-lucide="link" class="w-4 h-4 text-primary-500"></i>
                        <span class="text-sm font-semibold text-primary-700">Đang liên kết: Lead {{ $lead->name }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-6">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Ngày bắt đầu</label>
                            <div class="relative">
                                <i data-lucide="calendar" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="date" name="start_date" 
                                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm font-medium text-slate-700">
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block text-primary-600">Hạn chót</label>
                            <div class="relative">
                                <i data-lucide="calendar-clock" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="date" name="due_date" 
                                       class="w-full pl-10 pr-4 py-2.5 bg-primary-50/30 border border-primary-100 rounded-xl focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm font-semibold text-primary-700">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Ưu tiên</label>
                            <div class="relative">
                                <i data-lucide="flag" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <select name="priority" class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm font-medium text-slate-700 appearance-none bg-white">
                                    <option value="LOW">Thấp</option>
                                    <option value="MEDIUM" selected>Trung bình</option>
                                    <option value="HIGH">Cao</option>
                                    <option value="URGENT">Khẩn cấp</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Giao cho nhân sự</label>
                            <div class="relative">
                                <i data-lucide="user-check" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <select name="assigned_to" class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm font-medium text-slate-700 appearance-none bg-white">
                                    <option value="{{ auth()->id() }}">Giao cho chính tôi</option>
                                    <option value="">-- Để trống --</option>
                                    @foreach($users ?? [] as $user)
                                        @if($user->id !== auth()->id() && $user->default_center_id == $lead->center_id)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 mt-6 border-t border-slate-100 flex gap-3 justify-end relative">
                    <button type="button" @click="showTaskModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
                    <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tạo nhiệm vụ
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
@endcan
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) { lucide.createIcons(); }
    });
</script>
@endpush
