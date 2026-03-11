@extends('layouts.app')

@section('title', 'Chi tiết Khách hàng - ' . $customer->name)

@section('content')
<div class="space-y-8" x-data="customerDetailStore()">
    {{-- Top Title & Context --}}
    <div class="flex flex-col gap-1 mb-8">
        <nav class="flex items-center gap-2 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600 transition-colors">Hệ thống</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <a href="{{ route('admin.customers.index') }}" class="hover:text-primary-600 transition-colors">Quản lý Khách hàng</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-slate-800">Thông tin chi tiết</span>
        </nav>
        <div class="flex items-center gap-4 mt-2">
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Chi tiết hồ sơ Khách hàng</h1>
            <div class="h-8 w-px bg-slate-200"></div>
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-slate-500">Mã KH:</span>
                <span class="px-3 py-1 bg-slate-100 rounded-lg text-xs font-black text-slate-700 tracking-wider uppercase">{{ substr($customer->id, -8) }}</span>
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
                        <i data-lucide="user-circle" class="w-4 h-4"></i> Hồ sơ KH
                    </button>
                    <button @click="setTab('timeline')" :class="activeTab === 'timeline' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="pb-4 text-sm font-bold border-b-2 transition-all flex items-center gap-2 relative">
                        <i data-lucide="history" class="w-4 h-4"></i> Timeline
                        <span :class="activeTab === 'timeline' ? 'bg-primary-100 text-primary-600' : 'bg-slate-100 text-slate-500'" class="px-2 py-0.5 rounded-full text-[9px] font-black tabular-nums tracking-tighter">{{ $activities->total() }}</span>
                    </button>
                    <button @click="setTab('notes')" :class="activeTab === 'notes' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="pb-4 text-sm font-bold border-b-2 transition-all flex items-center gap-2 relative">
                        <i data-lucide="sticky-note" class="w-4 h-4"></i> Ghi chú
                        <span :class="activeTab === 'notes' ? 'bg-primary-100 text-primary-600' : 'bg-slate-100 text-slate-500'" class="px-2 py-0.5 rounded-full text-[9px] font-black tabular-nums tracking-tighter">{{ $notes->total() }}</span>
                    </button>
                    <button @click="setTab('tasks')" :class="activeTab === 'tasks' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="pb-4 text-sm font-bold border-b-2 transition-all flex items-center gap-2 relative">
                        <i data-lucide="check-square" class="w-4 h-4"></i> Nhiệm vụ
                        <span :class="activeTab === 'tasks' ? 'bg-primary-100 text-primary-600' : 'bg-slate-100 text-slate-500'" class="px-2 py-0.5 rounded-full text-[9px] font-black tabular-nums tracking-tighter">{{ $tasks?->count() ?? 0 }}</span>
                    </button>
                </div>
            </div>

            {{-- Tab Contents --}}
            <div class="min-h-[400px]">
                {{-- Info Tab --}}
                <div x-show="activeTab === 'info'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                    {{-- Simple Header --}}
                    <div class="bg-white rounded-[2.5rem] border border-slate-200/60 p-8 shadow-sm">
                        <div class="flex flex-col md:flex-row justify-between items-start gap-8">
                            <div class="flex-1">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Họ và tên</p>
                                <div class="flex flex-wrap items-center gap-6">
                                     <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-tight">{{ $customer->name }}</h2>
                                     <div class="h-8 w-px bg-slate-200 hidden md:block"></div>
                                     <div class="flex items-center gap-3">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Loại khách hàng:</span>
                                        @if($customer->studentProfile)
                                            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-violet-50 text-violet-600 border border-violet-100">
                                                Học viên
                                            </span>
                                        @else
                                            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                Phụ huynh / Người giám hộ
                                            </span>
                                        @endif
                                     </div>
                                </div>
                                
                                <div class="mt-6 flex flex-wrap items-center gap-3">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mr-1">Phân loại thẻ:</span>
                                    @foreach($customer->tags as $tag)
                                        <span class="px-3 py-1 rounded-lg text-[10px] font-bold border border-slate-100 bg-slate-50 text-slate-500 shadow-sm" style="color: {{ $tag->color }}">
                                            #{{ $tag->name }}
                                        </span>
                                    @endforeach
                                    @if($customer->tags->isEmpty())
                                        <span class="text-[10px] text-slate-300 italic">Chưa gán thẻ</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Main Info & Quick Actions --}}
                    <div class="bg-white rounded-[2.5rem] border border-slate-200/60 p-10 shadow-sm relative overflow-hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 relative z-10">
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
                                            <p class="text-base font-black text-slate-800 tabular-nums">{{ $customer->phone }}</p>
                                        </div>
                                    </div>
                                    @if($customer->email)
                                    <div class="flex items-center gap-5 group">
                                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm">
                                            <i data-lucide="mail" class="w-5 h-5"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Email</p>
                                            <p class="text-base font-black text-slate-800 truncate" title="{{ $customer->email }}">{{ $customer->email }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    @if($customer->dob)
                                    <div class="flex items-center gap-5 group">
                                        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center group-hover:scale-110 group-hover:bg-rose-600 group-hover:text-white transition-all shadow-sm">
                                            <i data-lucide="cake" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Ngày sinh</p>
                                            <p class="text-base font-black text-slate-800">{{ \Carbon\Carbon::parse($customer->dob)->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    @if($customer->gender)
                                    <div class="flex items-center gap-5 group">
                                        <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-500 flex items-center justify-center group-hover:scale-110 group-hover:bg-teal-600 group-hover:text-white transition-all shadow-sm">
                                            <i data-lucide="user" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Giới tính</p>
                                            <p class="text-base font-black text-slate-800">
                                                {{ $customer->gender === 'M' ? 'Nam' : ($customer->gender === 'F' ? 'Nữ' : 'Khác') }}
                                            </p>
                                        </div>
                                    </div>
                                    @endif
                                    @if($customer->address)
                                    <div class="flex items-center gap-5 group">
                                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-sm">
                                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Địa chỉ</p>
                                            <p class="text-base font-black text-slate-800 truncate" title="{{ $customer->address }}">{{ $customer->address }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Student List Section --}}
                            <div class="space-y-8">
                                <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-3">
                                    <div class="w-1.5 h-4 bg-violet-500 rounded-full"></div>
                                    Danh sách học viên liên kết
                                </h4>
                                <div class="space-y-4">
                                    @if($customer->studentGuardians->isEmpty())
                                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 text-center flex items-center justify-center flex-col gap-2">
                                            <i data-lucide="user-x" class="w-8 h-8 text-slate-300"></i>
                                            <p class="text-sm font-medium text-slate-400">Khách hàng này chưa được xếp vào học viên nào.</p>
                                        </div>
                                    @else
                                        @foreach($customer->studentGuardians as $guardianData)
                                            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex items-center justify-between group hover:bg-white hover:border-violet-200 hover:shadow-lg hover:shadow-violet-500/5 transition-all">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center font-bold shadow-sm">
                                                        {{ substr($guardianData->student->customer->name ?? 'S', 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <a href="javascript:void(0)" class="text-sm font-black text-slate-800 hover:text-violet-600 transition">{{ $guardianData->student->customer->name ?? 'Học viên không xác định' }}</a>
                                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">
                                                            Vai trò: {{ $guardianData->relationship === 'father' ? 'Bố' : ($guardianData->relationship === 'mother' ? 'Mẹ' : 'Giám hộ') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 group-hover:text-violet-400 transition-colors"></i>
                                            </div>
                                        @endforeach
                                    @endif
                                    
                                    <button class="w-full py-4 rounded-2xl border-2 border-dashed border-slate-200 text-slate-500 text-sm font-bold hover:bg-slate-50 hover:border-slate-300 hover:text-slate-600 transition flex items-center justify-center gap-2">
                                        <i data-lucide="plus" class="w-4 h-4"></i> Gắn thêm học viên
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Quick Activity Grid --}}
                        <div class="mt-16 pt-12 border-t border-slate-100">
                            <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-[0.2em] mb-10 flex items-center gap-3">
                                <div class="w-1.5 h-4 bg-primary-500 rounded-full"></div>
                                Ghi nhận nhanh các hoạt động chăm sóc
                            </h4>
                            <form action="{{ route('admin.customers.activities.store', $customer->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                                @csrf
                                <button type="submit" name="activity_type" value="call" class="flex flex-col items-center gap-4 p-5 rounded-3xl bg-blue-50/40 border border-blue-100 hover:border-blue-300 hover:bg-white hover:shadow-2xl hover:shadow-blue-500/10 transition-all group active:scale-95 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg shadow-blue-500/30">
                                        <i data-lucide="phone" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-black text-blue-900 uppercase tracking-tight">Gọi điện</span>
                                    </div>
                                </button>
                                <button type="submit" name="activity_type" value="sms" class="flex flex-col items-center gap-4 p-5 rounded-3xl bg-indigo-50/40 border border-indigo-100 hover:border-indigo-300 hover:bg-white hover:shadow-2xl hover:shadow-indigo-500/10 transition-all group active:scale-95 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg shadow-indigo-500/30">
                                        <i data-lucide="message-square" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-black text-indigo-900 uppercase tracking-tight">Gửi SMS</span>
                                    </div>
                                </button>
                                <button type="submit" name="activity_type" value="email" class="flex flex-col items-center gap-4 p-5 rounded-3xl bg-sky-50/40 border border-sky-100 hover:border-sky-300 hover:bg-white hover:shadow-2xl hover:shadow-sky-500/10 transition-all group active:scale-95 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-sky-600 text-white flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg shadow-sky-500/30">
                                        <i data-lucide="mail" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-black text-sky-900 uppercase tracking-tight">Zalo / Email</span>
                                    </div>
                                </button>
                                <button type="submit" name="activity_type" value="meeting" class="flex flex-col items-center gap-4 p-5 rounded-3xl bg-purple-50/40 border border-purple-100 hover:border-purple-300 hover:bg-white hover:shadow-2xl hover:shadow-purple-500/10 transition-all group active:scale-95 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-purple-600 text-white flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg shadow-purple-500/30">
                                        <i data-lucide="calendar" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-black text-purple-900 uppercase tracking-tight">Hẹn gặp</span>
                                    </div>
                                </button>
                                <button type="button" @click="setTab('notes')" class="flex flex-col items-center gap-4 p-5 rounded-3xl bg-emerald-50/40 border border-emerald-100 hover:border-emerald-300 hover:bg-white hover:shadow-2xl hover:shadow-emerald-500/10 transition-all group active:scale-95 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg shadow-emerald-500/30">
                                        <i data-lucide="sticky-note" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-black text-emerald-900 uppercase tracking-tight">Ghi chú</span>
                                    </div>
                                </button>
                                <button type="button" @click="setTab('tasks')" class="flex flex-col items-center gap-4 p-5 rounded-3xl bg-amber-50/40 border border-amber-100 hover:border-amber-300 hover:bg-white hover:shadow-2xl hover:shadow-amber-500/10 transition-all group active:scale-95 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-600 text-white flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg shadow-amber-500/30">
                                        <i data-lucide="check-square" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-black text-amber-900 uppercase tracking-tight">Nhiệm vụ</span>
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
                                                'label' => 'Gọi điện',
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
                                            'sms' => [
                                                'icon' => 'message-square', 
                                                'color' => 'from-amber-400 to-orange-500', 
                                                'bg' => 'bg-amber-50', 
                                                'text' => 'text-amber-600',
                                                'label' => 'Gửi tin nhắn',
                                                'shadow' => 'shadow-amber-500/30'
                                            ],
                                            'email' => [
                                                'icon' => 'mail', 
                                                'color' => 'from-indigo-400 to-blue-500', 
                                                'bg' => 'bg-indigo-50', 
                                                'text' => 'text-indigo-600',
                                                'label' => 'Gửi Email',
                                                'shadow' => 'shadow-indigo-500/30'
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
                        <form action="{{ route('admin.customers.notes.store', $customer->id) }}" method="POST">
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
                             <p class="text-slate-400 text-sm italic">Chưa có ghi chú nào cho Khách hàng này.</p>
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

                {{-- Tasks --}}
                <div x-show="activeTab === 'tasks'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-800 uppercase text-xs tracking-widest">Danh sách nhiệm vụ</h3>
                        <button class="px-5 py-2.5 bg-primary-100 text-primary-700 rounded-xl font-bold text-xs flex items-center gap-2 hover:bg-primary-200 transition active:scale-95 shadow-sm border border-primary-200">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i> Thêm nhiệm vụ
                        </button>
                    </div>

                    @if($tasks->isEmpty())
                        <div class="bg-white rounded-[2.5rem] p-12 text-center border border-slate-100 shadow-sm">
                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 shadow-inner">
                                <i data-lucide="check-square" class="w-8 h-8 text-slate-200"></i>
                            </div>
                            <p class="text-slate-400 font-medium italic">Chưa có nhiệm vụ nào cho KH này.</p>
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
                                                <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-3 h-3"></i> {{ $t->due_date ? \Carbon\Carbon::parse($t->due_date)->format('d/m/Y') : 'Không hạn' }}</span>
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
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('customerDetailStore', () => ({
            activeTab: 'info',
            
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
