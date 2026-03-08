@extends('layouts.app')

@section('title', 'Chỉnh sửa Lead - ' . $lead->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-8" x-data="leadEditStore()">
    {{-- Top Action Bar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <nav class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.leads.index') }}" class="text-slate-400 hover:text-primary-600 transition-colors flex items-center gap-1.5 group">
                <i data-lucide="chevron-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
                Danh sách Leads
            </a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('admin.leads.show', $lead->id) }}" class="text-slate-400 hover:text-primary-600 transition-colors">
                {{ $lead->name }}
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-bold tracking-tight">Chỉnh sửa</span>
        </nav>
    </div>

    <div class="glass rounded-[2.5rem] border border-white/40 shadow-xl shadow-slate-200/50 overflow-hidden">
        <div class="bg-gradient-to-br from-primary-500/10 to-transparent p-8 border-b border-slate-100">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 rounded-3xl bg-white shadow-xl shadow-primary-500/10 flex items-center justify-center text-3xl font-extrabold text-primary-600 border-2 border-primary-100">
                    {{ strtoupper(substr($lead->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Chỉnh sửa thông tin</h2>
                    <p class="text-slate-500 text-sm font-medium mt-1">Cập nhật hồ sơ cho {{ $lead->name }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.leads.update', $lead->id) }}" method="POST" class="p-8 space-y-8">
            @csrf
            @method('PUT')
            
            {{-- Basic Information Group --}}
            <div class="space-y-6">
                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Thông tin cơ bản</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-600 ml-1">Họ và tên <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <i data-lucide="user" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                            <input type="text" name="name" required value="{{ old('name', $lead->name) }}"
                                   class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none">
                        </div>
                        @error('name') <span class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-600 ml-1">Số điện thoại <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <i data-lucide="phone" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                            <input type="text" name="phone" required value="{{ old('phone', $lead->phone) }}"
                                   class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none tabular-nums">
                        </div>
                        @error('phone') <span class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-600 ml-1">Địa chỉ Email</label>
                        <div class="relative group">
                            <i data-lucide="mail" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                            <input type="email" name="email" value="{{ old('email', $lead->email) }}"
                                   class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none">
                        </div>
                        @error('email') <span class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-600 ml-1">Ngày sinh</label>
                        <div class="relative group">
                            <i data-lucide="calendar" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                            <input type="date" name="dob" value="{{ old('dob', $lead->dob ? \Carbon\Carbon::parse($lead->dob)->format('Y-m-d') : '') }}"
                                   class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none">
                        </div>
                        @error('dob') <span class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Marketing & Status Group --}}
            <div class="space-y-6">
                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Marketing & Phân loại</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-600 ml-1">Trạng thái hiện tại <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <i data-lucide="tag" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                            <select name="status_id" required class="w-full pl-12 pr-10 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none appearance-none">
                                @foreach($statuses as $st)
                                    <option value="{{ $st->getId() }}" {{ old('status_id', $lead->status_id) == $st->getId() ? 'selected' : '' }}>{{ $st->name }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                        @error('status_id') <span class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-600 ml-1">Nguồn khách hàng</label>
                        <div class="relative group">
                            <i data-lucide="share-2" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                            <select name="lead_source_id" class="w-full pl-12 pr-10 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none appearance-none">
                                <option value="">-- Chưa rõ nguồn --</option>
                                @foreach($leadSources as $source)
                                    <option value="{{ $source->id }}" {{ old('lead_source_id', $lead->lead_source_id) == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-600 ml-1">Nhu cầu quan tâm</label>
                        <div class="relative group">
                            <i data-lucide="list-todo" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                            <select name="interest_type_id" class="w-full pl-12 pr-10 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none appearance-none">
                                <option value="">-- Chưa rõ nhu cầu --</option>
                                @foreach($interestTypes as $interest)
                                    <option value="{{ $interest->id }}" {{ old('interest_type_id', $lead->interest_type_id) == $interest->id ? 'selected' : '' }}>{{ $interest->name }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-600 ml-1">Chiến dịch Marketing</label>
                        <div class="relative group">
                            <i data-lucide="megaphone" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                            <select name="campaign_id" class="w-full pl-12 pr-10 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none appearance-none">
                                <option value="">-- Không thuộc chiến dịch --</option>
                                @foreach($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}" {{ old('campaign_id', $lead->campaign_id) == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                {{-- Tags Input --}}
                <div class="space-y-2 mt-4">
                    <label class="text-xs font-bold text-slate-600 ml-1">Phân loại nâng cao (Tags)</label>
                    <div class="flex flex-wrap gap-2.5 p-6 bg-slate-50/50 rounded-3xl border border-dashed border-slate-200">
                        @php $leadTagIds = $lead->tags->pluck('id')->toArray(); @endphp
                        @foreach($allTags as $tag)
                        <label class="relative flex items-center group cursor-pointer h-10">
                            <input type="checkbox" name="tag_ids[]" value="{{ $tag->getId() }}" {{ in_array($tag->getId(), old('tag_ids', $leadTagIds)) ? 'checked' : '' }} class="peer appearance-none absolute">
                            <span class="px-5 py-2 rounded-xl text-xs font-black border-2 transition-all duration-300 flex items-center gap-2 group-hover:scale-105
                                peer-checked:bg-[var(--tag-color)] peer-checked:border-[var(--tag-color)] peer-checked:text-white peer-checked:shadow-xl peer-checked:shadow-[var(--tag-color)]/20
                                hover:border-[var(--tag-color)] hover:bg-[var(--tag-color)]/5"
                                style="--tag-color: {{ $tag->color }}; border-color: {{ $tag->color }}30; color: {{ $tag->color }}">
                                <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                                {{ $tag->name }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- System & Assignment Group --}}
            <div class="space-y-6">
                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Hệ thống & Phụ trách</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-600 ml-1">Nhân sự phụ trách</label>
                        <div class="relative group">
                            <i data-lucide="user-check" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                            <select name="assigned_to" class="w-full pl-12 pr-10 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none appearance-none">
                                <option value="">-- Để trống (Chưa bàn giao) --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to', $lead->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    @if($isGlobalScope)
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-600 ml-1">Cơ sở vận hành <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <i data-lucide="building-2" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                            <select name="center_id" required class="w-full pl-12 pr-10 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none appearance-none">
                                @foreach($centers as $center)
                                    <option value="{{ $center->id }}" {{ old('center_id', $lead->center_id) == $center->id ? 'selected' : '' }}>[{{ $center->code }}] {{ $center->name }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="pt-10 flex flex-col sm:flex-row gap-4 justify-end">
                <a href="{{ route('admin.leads.show', $lead->id) }}" class="px-8 py-3.5 text-sm font-bold text-slate-500 hover:bg-slate-50 rounded-2xl transition-all text-center">Hủy thay đổi</a>
                <button type="submit" class="px-10 py-3.5 bg-primary-600 text-white font-black rounded-2xl hover:bg-primary-700 transition-all shadow-xl shadow-primary-500/20 hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Lưu các thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function leadEditStore() {
        return {
            // Add any form logic if needed
        };
    }
    document.addEventListener('alpine:initialized', () => { setTimeout(() => lucide.createIcons(), 100); });
</script>
@endpush
