@extends('layouts.app')

@section('title', 'Lead Detail - ' . $lead->name)

@section('content')
<div class="space-y-6" x-data="leadDetailStore()">
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('admin.leads.index') }}" class="hover:text-primary-600 transition flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Danh sách Leads
        </a>
        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300"></i>
        <span class="text-slate-800 font-medium">{{ $lead->name }}</span>
    </div>

    {{-- Header Section --}}
    <div class="flex flex-col lg:flex-row gap-6">
        {{-- LEFT COLUMN: Lead Info --}}
        <div class="lg:w-1/3 space-y-6">
            {{-- Lead Info Card --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-primary-50 to-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-primary-500 text-white flex items-center justify-center text-lg font-bold shadow-lg shadow-primary-500/30">
                            {{ strtoupper(substr($lead->name, 0, 1)) }}
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">{{ $lead->name }}</h2>
                            @php
                                $st = $lead->leadStatus;
                                $statusName = $st ? $st->name : 'N/A';
                                $statusColor = $st ? $st->color : '#94a3b8';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border" style="background-color: {{ $statusColor }}20; color: {{ $statusColor }}; border-color: {{ $statusColor }}40">
                                {{ $statusName }}
                            </span>
                            @if($lead->tags->isNotEmpty())
                            <div class="mt-2 flex flex-wrap gap-1">
                                @foreach($lead->tags as $tag)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold text-white shadow-sm" style="background-color: {{ $tag->color ?: 'gray' }}">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @can('leads.update')
                    <a href="{{ route('admin.leads.index', ['edit' => $lead->id]) }}" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Chỉnh sửa">
                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                    </a>
                    @endcan
                </div>

                <div class="p-6 space-y-4">
                    {{-- Contact Info --}}
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="phone" class="w-4 h-4 text-slate-500"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Điện thoại</p>
                                <p class="text-slate-800 font-medium">{{ $lead->phone }}</p>
                            </div>
                        </div>

                        @if($lead->email)
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="mail" class="w-4 h-4 text-slate-500"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Email</p>
                                <p class="text-slate-800">{{ $lead->email }}</p>
                            </div>
                        </div>
                        @endif

                        @if($lead->dob)
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="calendar" class="w-4 h-4 text-slate-500"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Ngày sinh</p>
                                <p class="text-slate-800">{{ \Carbon\Carbon::parse($lead->dob)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="border-t border-slate-100 pt-4 space-y-3">
                        {{-- Source --}}
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="share-2" class="w-4 h-4 text-orange-500"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Nguồn</p>
                                <p class="text-slate-800">{{ $lead->leadSource?->name ?? '—' }}</p>
                            </div>
                        </div>

                        {{-- Interest Type --}}
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="list-todo" class="w-4 h-4 text-violet-500"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Nhu cầu</p>
                                <p class="text-slate-800">{{ $lead->interestType?->name ?? '—' }}</p>
                            </div>
                        </div>

                        {{-- Campaign --}}
                        @php
                            $campaign = $lead->campaign_id ? $campaigns->firstWhere('id', $lead->campaign_id) : null;
                        @endphp
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-pink-50 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="megaphone" class="w-4 h-4 text-pink-500"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Chiến dịch</p>
                                <p class="text-slate-800">{{ $campaign?->name ?? '—' }}</p>
                            </div>
                        </div>

                        {{-- Center --}}
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="building-2" class="w-4 h-4 text-teal-500"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Cơ sở</p>
                                <p class="text-slate-800">
                                    @if($lead->center)
                                        [{{ $lead->center->code }}] {{ $lead->center->name }}
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Assigned To --}}
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="user-check" class="w-4 h-4 text-indigo-500"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Người phụ trách</p>
                                <p class="text-slate-800">
                                    @if($lead->assignTo)
                                        {{ $lead->assignTo->name }}
                                    @else
                                        <span class="text-slate-400 italic">Chưa gán</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4">
                        <div class="flex items-center gap-3 text-xs text-slate-400">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                            <span>Tạo lúc: {{ $lead->created_at?->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($lead->updated_at && $lead->updated_at != $lead->created_at)
                        <div class="flex items-center gap-3 text-xs text-slate-400 mt-1">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                            <span>Cập nhật: {{ $lead->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick Actions Card --}}
            @can('leads.update')
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-slate-800 uppercase tracking-wider mb-4">Ghi nhận hoạt động</h3>
                <form action="{{ route('admin.leads.activities.store', $lead->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-2">
                        <button type="submit" name="activity_type" value="call" class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 text-sm font-medium text-slate-700 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition cursor-pointer">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center"><i data-lucide="phone-call" class="w-4 h-4"></i></div>
                            Cuộc gọi
                        </button>
                        <button type="submit" name="activity_type" value="meeting" class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 text-sm font-medium text-slate-700 hover:bg-purple-50 hover:border-purple-200 hover:text-purple-700 transition cursor-pointer">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center"><i data-lucide="calendar-check" class="w-4 h-4"></i></div>
                            Cuộc hẹn
                        </button>
                        <button type="submit" name="activity_type" value="email" class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 text-sm font-medium text-slate-700 hover:bg-orange-50 hover:border-orange-200 hover:text-orange-700 transition cursor-pointer">
                            <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center"><i data-lucide="mail" class="w-4 h-4"></i></div>
                            Email
                        </button>
                        <button type="submit" name="activity_type" value="sms" class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 text-sm font-medium text-slate-700 hover:bg-green-50 hover:border-green-200 hover:text-green-700 transition cursor-pointer">
                            <div class="w-8 h-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center"><i data-lucide="message-square" class="w-4 h-4"></i></div>
                            SMS
                        </button>
                    </div>
                </form>
            </div>
            @endcan
        </div>

        {{-- RIGHT COLUMN: Timeline + Notes --}}
        <div class="lg:w-2/3 space-y-6">
            {{-- Tab Navigation --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex border-b border-slate-100">
                    <button @click="activeTab = 'timeline'" :class="activeTab === 'timeline' ? 'border-primary-500 text-primary-600 bg-primary-50/50' : 'border-transparent text-slate-500 hover:text-slate-700'" class="flex-1 px-6 py-3.5 text-sm font-semibold border-b-2 transition flex items-center justify-center gap-2">
                        <i data-lucide="activity" class="w-4 h-4"></i>
                        Timeline
                        <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full text-xs" x-text="'{{ $activities->total() }}'"></span>
                    </button>
                    <button @click="activeTab = 'notes'" :class="activeTab === 'notes' ? 'border-primary-500 text-primary-600 bg-primary-50/50' : 'border-transparent text-slate-500 hover:text-slate-700'" class="flex-1 px-6 py-3.5 text-sm font-semibold border-b-2 transition flex items-center justify-center gap-2">
                        <i data-lucide="sticky-note" class="w-4 h-4"></i>
                        Ghi chú
                        <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full text-xs" x-text="'{{ $notes->total() }}'"></span>
                    </button>
                    <button @click="activeTab = 'assignments'" :class="activeTab === 'assignments' ? 'border-primary-500 text-primary-600 bg-primary-50/50' : 'border-transparent text-slate-500 hover:text-slate-700'" class="flex-1 px-6 py-3.5 text-sm font-semibold border-b-2 transition flex items-center justify-center gap-2">
                        <i data-lucide="user-check" class="w-4 h-4"></i>
                        Bàn giao
                        <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full text-xs" x-text="'{{ $lead->assignments->count() }}'"></span>
                    </button>
                </div>

                {{-- Timeline Tab --}}
                <div x-show="activeTab === 'timeline'" class="p-6">
                    @if($activities->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="activity" class="w-8 h-8 text-slate-300"></i>
                        </div>
                        <p class="text-slate-400 text-sm">Chưa có hoạt động nào được ghi nhận</p>
                    </div>
                    @else
                    <div class="relative">
                        {{-- Timeline line --}}
                        <div class="absolute left-4 top-2 bottom-2 w-0.5 bg-gradient-to-b from-primary-200 via-slate-200 to-transparent"></div>

                        <div class="space-y-6">
                            @foreach($activities as $activity)
                            @php
                                $activityConfig = match($activity->activity_type) {
                                    'call' => ['icon' => 'phone-call', 'color' => 'bg-blue-100 text-blue-600 border-blue-200', 'label' => 'Cuộc gọi'],
                                    'meeting' => ['icon' => 'calendar-check', 'color' => 'bg-purple-100 text-purple-600 border-purple-200', 'label' => 'Cuộc hẹn'],
                                    'email' => ['icon' => 'mail', 'color' => 'bg-orange-100 text-orange-600 border-orange-200', 'label' => 'Email'],
                                    'sms' => ['icon' => 'message-square', 'color' => 'bg-green-100 text-green-600 border-green-200', 'label' => 'SMS'],
                                    'note' => ['icon' => 'sticky-note', 'color' => 'bg-amber-100 text-amber-600 border-amber-200', 'label' => 'Ghi chú'],
                                    'status_change' => ['icon' => 'refresh-cw', 'color' => 'bg-indigo-100 text-indigo-600 border-indigo-200', 'label' => 'Thay đổi trạng thái'],
                                    'assignment' => ['icon' => 'user-check', 'color' => 'bg-teal-100 text-teal-600 border-teal-200', 'label' => 'Gán lead'],
                                    'conversion' => ['icon' => 'sparkles', 'color' => 'bg-emerald-100 text-emerald-600 border-emerald-200', 'label' => 'Chuyển đổi'],
                                    default => ['icon' => 'circle', 'color' => 'bg-slate-100 text-slate-600 border-slate-200', 'label' => $activity->activity_type],
                                };
                            @endphp
                            <div class="relative pl-12">
                                {{-- Timeline dot --}}
                                <div class="absolute left-0 w-8 h-8 rounded-full border-2 {{ $activityConfig['color'] }} flex items-center justify-center z-10 bg-white">
                                    <i data-lucide="{{ $activityConfig['icon'] }}" class="w-3.5 h-3.5"></i>
                                </div>

                                <div class="bg-slate-50/80 rounded-xl p-4 border border-slate-100 hover:border-slate-200 transition">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <span class="text-sm font-semibold text-slate-800">{{ $activityConfig['label'] }}</span>
                                            @if($activity->description)
                                            <p class="text-sm text-slate-600 mt-1">{{ $activity->description }}</p>
                                            @endif
                                        </div>
                                        <span class="text-xs text-slate-400 whitespace-nowrap flex-shrink-0">{{ $activity->created_at?->diffForHumans() }}</span>
                                    </div>
                                    @if($activity->creator)
                                    <div class="flex items-center gap-2 mt-2 text-xs text-slate-400">
                                        <div class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-semibold text-slate-600 uppercase">
                                            {{ substr($activity->creator->name, 0, 1) }}
                                        </div>
                                        {{ $activity->creator->name }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Notes Tab --}}
                <div x-show="activeTab === 'notes'" x-cloak class="p-6">
                    {{-- Add Note Form --}}
                    @can('leads.update')
                    <form action="{{ route('admin.leads.notes.store', $lead->id) }}" method="POST" class="mb-6">
                        @csrf
                        <div class="flex gap-3">
                            <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold text-sm flex-shrink-0">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <textarea name="content" rows="3" required placeholder="Viết ghi chú..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition outline-none resize-none"></textarea>
                                @error('content') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                <div class="flex justify-end mt-2">
                                    <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition text-sm font-medium flex items-center gap-2 shadow-lg shadow-primary-500/30">
                                        <i data-lucide="send" class="w-4 h-4"></i> Gửi ghi chú
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    @endcan

                    {{-- Notes List --}}
                    @if($notes->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="sticky-note" class="w-8 h-8 text-slate-300"></i>
                        </div>
                        <p class="text-slate-400 text-sm">Chưa có ghi chú nào</p>
                    </div>
                    @else
                    <div class="space-y-4">
                        @foreach($notes as $note)
                        <div class="flex gap-3 group">
                            <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-semibold text-sm flex-shrink-0 border border-slate-200">
                                {{ strtoupper(substr($note->creator?->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="flex-1 bg-slate-50 rounded-xl p-4 border border-slate-100 group-hover:border-slate-200 transition">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-semibold text-slate-700">{{ $note->creator?->name ?? 'Hệ thống' }}</span>
                                    <span class="text-xs text-slate-400">{{ $note->created_at?->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-slate-600 whitespace-pre-wrap">{{ $note->content }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Assignments Tab --}}
                <div x-show="activeTab === 'assignments'" x-cloak class="p-6">
                    @if($lead->assignments->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="user-check" class="w-8 h-8 text-slate-300"></i>
                        </div>
                        <p class="text-slate-400 text-sm">Chưa có lịch sử bàn giao nào</p>
                    </div>
                    @else
                    <div class="relative">
                        <div class="absolute left-4 top-2 bottom-2 w-0.5 bg-slate-100"></div>
                        <div class="space-y-6">
                            @foreach($lead->assignments as $assignment)
                            <div class="relative pl-12">
                                <div class="absolute left-0 w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center z-10">
                                    <i data-lucide="log-in" class="w-3.5 h-3.5 text-slate-400"></i>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center -space-x-2">
                                                {{-- User who was assigned --}}
                                                <div class="w-8 h-8 rounded-full bg-primary-100 border-2 border-white flex items-center justify-center text-xs font-bold text-primary-600" title="Assigned to">
                                                    {{ substr($assignment->assignedToUser->name ?? ($assignment->assigned_to ? '?' : 'Un'), 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-sm">
                                                    <span class="font-bold text-slate-800">
                                                        @if($assignment->assigned_to)
                                                            {{ $assignment->assignedToUser->name ?? 'User (Removed)' }}
                                                        @else
                                                            <span class="text-slate-400 italic">Unassigned</span>
                                                        @endif
                                                    </span>
                                                    @if($assignment->assigned_by)
                                                        <span class="text-slate-400 mx-1">/ bởi</span>
                                                        <span class="text-xs font-medium text-slate-600">{{ $assignment->assignedByUser->name ?? 'User (Removed)' }}</span>
                                                    @endif
                                                </p>
                                                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ $assignment->created_at->format('H:i d/m/Y') }}</p>
                                            </div>
                                        </div>
                                        <span class="text-[11px] px-2 py-0.5 bg-white border border-slate-200 rounded-full text-slate-500">{{ $assignment->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($assignment->notes)
                                    <div class="pl-3 border-l-2 border-slate-200 py-1">
                                        <p class="text-[13px] text-slate-600 italic">{{ $assignment->notes }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
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
    function leadDetailStore() {
        return {
            activeTab: 'timeline',
        };
    }

    // Re-render icons after Alpine hydration
    document.addEventListener('alpine:initialized', () => {
        setTimeout(() => lucide.createIcons(), 100);
    });
</script>
@endpush
