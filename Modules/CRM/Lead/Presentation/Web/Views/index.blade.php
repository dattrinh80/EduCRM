@extends('layouts.app')

@section('title', 'Quản lý Khách hàng tiềm năng')

@section('breadcrumb_items')
    <a href="{{ url('/admin/leads') }}" class="text-slate-400 hover:text-primary-500 transition-colors">CRM</a>
    <i data-lucide="chevron-right" class="w-2.5 h-2.5 opacity-50"></i>
    <span class="text-primary-500">Quản lý Lead</span>
@endsection

@section('content')
<div class="space-y-6" x-data="leadManagementStore()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Khách hàng Tiềm năng</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-1.5 text-sm">
                <i data-lucide="info" class="w-3.5 h-3.5 opacity-60"></i>
                Quản lý và theo dõi danh sách khách hàng tiềm năng toàn diện
            </p>
        </div>
        <div class="flex gap-2">
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl">
                <a href="{{ route('admin.leads.index', array_merge(request()->query(), ['view' => 'list'])) }}" 
                   class="px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ $view === 'list' ? 'bg-white text-primary-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    <i data-lucide="list" class="w-4 h-4"></i>
                    <span>Danh sách</span>
                </a>
                <a href="{{ route('admin.leads.index', array_merge(request()->query(), ['view' => 'kanban'])) }}" 
                   class="px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ $view === 'kanban' ? 'bg-white text-primary-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    <i data-lucide="layout-kanban" class="w-4 h-4"></i>
                    <span>Kanban</span>
                </a>
            </div>

            @can('leads.export')
            <div x-data="{ open: false }" class="relative">
                <button type="button" @click="open = !open" @click.away="open = false" class="px-5 py-2.5 bg-white text-emerald-700 hover:text-white rounded-xl hover:bg-emerald-600 transition-all duration-300 flex items-center gap-2 border border-emerald-100 shadow-sm hover:shadow-emerald-200/50 font-bold whitespace-nowrap active:scale-95 group">
                    <i data-lucide="download" class="w-4 h-4 group-hover:bounce"></i>
                    <span class="hidden sm:inline">Xuất dữ liệu</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-cloak 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50">
                    <a href="{{ route('admin.leads.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                        <i data-lucide="sheet" class="w-4 h-4 opacity-70"></i>
                        Xuất file Excel
                    </a>
                    <a href="{{ route('admin.leads.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4 opacity-70"></i>
                        Xuất file PDF
                    </a>
                </div>
            </div>
            @endcan

            @can('leads.create')
            <button type="button" @click="showImportModal = true; $dispatch('refresh-icons')" class="px-5 py-2.5 bg-slate-50 text-slate-600 hover:text-slate-900 rounded-xl hover:bg-slate-100 transition-all duration-300 flex items-center gap-2 border border-slate-200 font-bold shadow-sm whitespace-nowrap active:scale-95">
                <i data-lucide="file-down" class="w-4 h-4 opacity-70"></i>
                <span class="hidden sm:inline">Nhập Excel</span>
            </button>
            <button type="button" @click="showCreateModal = true; $dispatch('refresh-icons')" class="px-6 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-300 flex items-center gap-2 shadow-lg shadow-primary-500/25 whitespace-nowrap font-bold active:scale-95 group">
                <i data-lucide="plus-circle" class="w-5 h-5 group-hover:rotate-90 transition-transform"></i>
                <span>Thêm Lead mới</span>
            </button>
            @endcan
        </div>
    </div>

    <!-- Filter/Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row gap-4 items-end">
        <form action="{{ route('admin.leads.index') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4 items-end">
            <input type="hidden" name="view" value="{{ $view }}">
            <div class="w-full md:w-1/4">
                <label for="search" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Họ tên</label>
                <div class="relative w-full group">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                    <input type="text" name="search" id="search" placeholder="Số điện thoại, họ tên…" value="{{ request('search') }}"
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none">
                </div>
            </div>
            <div class="w-full md:w-1/4">
                <label for="phone" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Số điện thoại</label>
                <div class="relative w-full group">
                    <i data-lucide="phone" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                    <input type="text" name="phone" id="phone" placeholder="Số điện thoại…" value="{{ request('phone') }}"
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none tabular-nums">
                </div>
            </div>
            @if($isGlobalScope)
            <div class="w-full md:w-1/4">
                <label for="center_id" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Cơ sở</label>
                <div class="relative w-full group">
                    <i data-lucide="building-2" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                    <select name="center_id" id="center_id" class="w-full pl-10 pr-8 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none">
                        <option value="">Tất cả cơ sở</option>
                        @foreach($centers as $c)
                            <option value="{{ $c->id }}" {{ request('center_id') == $c->id ? 'selected' : '' }}>[{{ $c->code }}] {{ $c->name }}</option>
                        @endforeach
                    </select>
                    <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none opacity-60"></i>
                </div>
            </div>
            @endif
            <div class="w-full md:w-1/4">
                <label for="status_id" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Trạng thái</label>
                <div class="relative w-full group">
                    <i data-lucide="list-checks" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                    <select name="status_id" id="status_id" class="w-full pl-10 pr-8 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none">
                        <option value="">Tất cả trạng thái</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st->getId() }}" {{ request('status_id') == $st->getId() ? 'selected' : '' }}>{{ $st->name }}</option>
                        @endforeach
                    </select>
                    <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none opacity-60"></i>
                </div>
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="px-4 py-2 bg-primary-50 text-primary-600 hover:bg-primary-100 rounded-lg transition font-medium text-sm flex items-center gap-2 whitespace-nowrap">
                    <i data-lucide="filter" class="w-4 h-4"></i> Lọc
                </button>
                @if(request()->hasAny(['search', 'phone', 'center_id', 'status_id']))
                <a href="{{ route('admin.leads.index') }}" class="px-4 py-2 bg-slate-50 text-slate-600 hover:bg-slate-100 rounded-lg transition font-medium text-sm flex items-center gap-2 border border-slate-200 whitespace-nowrap">
                    <i data-lucide="x-circle" class="w-4 h-4 font-bold"></i> Xoá
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data List / Kanban -->
    <div class="{{ $view === 'list' ? 'bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden' : '' }}">
        @if($view === 'kanban')
            @include('lead::kanban')
        @elseif($leads->isEmpty())
        <div class="p-16 text-center">
            <div class="w-24 h-24 rounded-3xl bg-slate-50 flex items-center justify-center mx-auto mb-6 transform rotate-3 group-hover:rotate-0 transition-all duration-500 shadow-inner">
                <i data-lucide="users-2" class="w-12 h-12 text-slate-300"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Chưa có khách hàng tiềm năng</h3>
            <p class="text-slate-500 max-w-sm mx-auto mb-8 font-medium italic">Hệ thống chưa ghi nhận dữ liệu Lead nào khớp với bộ lọc của bạn.</p>
            @can('leads.create')
            <button type="button" @click="showCreateModal = true; $dispatch('refresh-icons')" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-500 text-white rounded-xl font-bold hover:bg-primary-600 transition shadow-lg shadow-primary-500/25 active:scale-95">
                <i data-lucide="plus" class="w-5 h-5"></i> 
                Tạo Lead mới ngay
            </button>
            @endcan
        </div>
        @else
        <div class="overflow-x-auto">
            <!-- Bulk Action Header -->
            <div x-show="selectedItems.length > 0" x-cloak class="bg-primary-50 px-6 py-3 border-b border-primary-100 flex items-center justify-between transition-all">
                <div class="text-sm font-bold text-primary-800 flex items-center gap-2">
                    <span class="bg-primary-200 text-primary-700 px-2 py-0.5 rounded-md text-xs" x-text="selectedItems.length"></span>
                    <span>Lead đã chọn</span>
                </div>
                <div class="flex items-center gap-2">
                    @can('leads.update')
                    <button type="button" x-show="selectedItems.length > 0" @click="showMassEditModal = true; $dispatch('refresh-icons')" class="px-3 py-1.5 bg-white border border-primary-200 text-primary-600 rounded-xl text-xs font-bold hover:bg-primary-50 transition flex items-center gap-1.5 shadow-sm active:scale-95">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Sửa hàng loạt
                    </button>
                    <button type="button" x-show="selectedItems.length > 1" @click="showMergeModal = true; $dispatch('refresh-icons')" class="px-3 py-1.5 bg-white border border-primary-200 text-primary-600 rounded-xl text-xs font-bold hover:bg-primary-50 transition flex items-center gap-1.5 shadow-sm active:scale-95">
                        <i data-lucide="merge" class="w-3.5 h-3.5"></i> Gộp Lead
                    </button>
                    <button type="button" x-show="selectedItems.length > 0" @click="showAssignModal = true; $dispatch('refresh-icons')" class="px-3 py-1.5 bg-white border border-primary-200 text-primary-600 rounded-xl text-xs font-bold hover:bg-primary-50 transition flex items-center gap-1.5 shadow-sm active:scale-95">
                        <i data-lucide="user-check" class="w-3.5 h-3.5"></i> Giao nhân sự
                    </button>
                    @endcan
                </div>
            </div>

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="p-4 px-6 w-10">
                            <input type="checkbox" :checked="isAllSelected" @change="toggleAll" class="rounded border-slate-300 text-primary-500 focus:ring-primary-500 w-4 h-4 cursor-pointer">
                        </th>
                        @php
                            $sortableHeaders = [
                                'name' => 'Họ tên',
                                'phone' => 'Số điện thoại',
                            ];
                        @endphp
                        @foreach($sortableHeaders as $col => $label)
                        <th class="p-4 px-6">
                            <a href="{{ route('admin.leads.index', array_merge(request()->query(), ['sort_by' => $col, 'sort_dir' => ($sortBy === $col && $sortDir === 'asc') ? 'desc' : 'asc', 'page' => 1])) }}"
                               class="inline-flex items-center gap-1.5 hover:text-primary-600 transition group/sort cursor-pointer select-none">
                                {{ $label }}
                                @if($sortBy === $col)
                                    @if($sortDir === 'asc')
                                        <i data-lucide="arrow-up" class="w-3 h-3 text-primary-500"></i>
                                    @else
                                        <i data-lucide="arrow-down" class="w-3 h-3 text-primary-500"></i>
                                    @endif
                                @else
                                    <i data-lucide="chevrons-up-down" class="w-3 h-3 text-slate-300 opacity-0 group-hover/sort:opacity-100 transition"></i>
                                @endif
                            </a>
                        </th>
                        @endforeach
                        <th class="p-4 px-6">Cơ sở</th>
                        <th class="p-4 px-6 text-center">Nguồn / Chiến dịch</th>
                        <th class="p-4 px-6 text-center">Nhân sự</th>
                        <th class="p-4 px-6">
                            <a href="{{ route('admin.leads.index', array_merge(request()->query(), ['sort_by' => 'status_id', 'sort_dir' => ($sortBy === 'status_id' && $sortDir === 'asc') ? 'desc' : 'asc', 'page' => 1])) }}"
                               class="inline-flex items-center gap-1.5 hover:text-primary-600 transition group/sort cursor-pointer select-none">
                                Trạng thái
                                @if($sortBy === 'status_id')
                                    @if($sortDir === 'asc')
                                        <i data-lucide="arrow-up" class="w-3 h-3 text-primary-500"></i>
                                    @else
                                        <i data-lucide="arrow-down" class="w-3 h-3 text-primary-500"></i>
                                    @endif
                                @else
                                    <i data-lucide="chevrons-up-down" class="w-3 h-3 text-slate-300 opacity-0 group-hover/sort:opacity-100 transition"></i>
                                @endif
                            </a>
                        </th>
                        <th class="p-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($leads as $lead)
                        <tr class="hover:bg-slate-50 transition group" :class="{ 'bg-primary-50/30': selectedItems.includes('{{ $lead->id }}') }" x-data="{ showEditModal: {{ $errors->any() && old('_method') == 'PUT' && old('lead_id') == $lead->id ? 'true' : 'false' }} }">
                            <td class="p-4 px-6 whitespace-nowrap">
                                <input type="checkbox" value="{{ $lead->id }}" x-model="selectedItems" class="rounded border-slate-300 text-primary-500 focus:ring-primary-500 w-4 h-4 cursor-pointer">
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                <a href="{{ route('admin.leads.show', $lead->id) }}" class="font-medium text-slate-800 hover:text-primary-600 transition">{{ $lead->name }}</a>
                                @if($lead->tags->isNotEmpty())
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($lead->tags as $tag)
                                        <div class="w-2 h-2 rounded-full" style="background-color: {{ $tag->color }}" title="{{ $tag->name }}"></div>
                                    @endforeach
                                </div>
                                @endif
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-slate-600 tabular-nums">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                                    {{ $lead->phone }}
                                </div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-slate-600">
                                @php
                                    $center = $centers->firstWhere('id', $lead->center_id);
                                @endphp
                                @if($center)
                                    <div class="flex items-center gap-1.5 text-sm">
                                        <i data-lucide="building-2" class="w-4 h-4 text-slate-400"></i>
                                        <span>[{{ $center->code }}] {{ $center->name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-sm italic">N/A</span>
                                @endif
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                <div class="flex flex-col gap-1.5 items-center">
                                    <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-50 border border-slate-100 group-hover:border-slate-200 transition-colors">
                                        <i data-lucide="share-2" class="w-3 h-3 text-slate-400"></i>
                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">{{ $lead->leadSource->name ?? 'N/A' }}</span>
                                    </div>
                                    @if($lead->campaign)
                                    <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-primary-50 border border-primary-100 group-hover:bg-primary-100/50 transition-colors">
                                        <i data-lucide="megaphone" class="w-3 h-3 text-primary-500"></i>
                                        <span class="text-[10px] text-primary-600 font-bold uppercase tracking-widest">{{ $lead->campaign->name }}</span>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-center">
                                @php
                                    $assignedUser = $lead->assigned_to ? $users->firstWhere('id', $lead->assigned_to) : null;
                                @endphp
                                @if($assignedUser)
                                    <div class="inline-flex items-center gap-2 px-2 py-1 rounded-lg border border-slate-100 bg-slate-50/50">
                                        <div class="w-5 h-5 rounded-full bg-primary-500 flex items-center justify-center text-[10px] font-bold text-white uppercase">
                                            {{ substr($assignedUser->name, 0, 1) }}
                                        </div>
                                        <span class="text-xs font-semibold text-slate-700">{{ $assignedUser->name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-300 text-[11px] italic">Chưa giao</span>
                                @endif
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                @php
                                    $st = $lead->leadStatus;
                                    $statusName = $st ? $st->name : 'N/A';
                                    $statusColor = $st ? $st->color : '#94a3b8';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border" style="background-color: {{ $statusColor }}10; color: {{ $statusColor }}; border-color: {{ $statusColor }}30">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5" style="background-color: {{ $statusColor }}"></span>
                                    {{ $statusName }}
                                </span>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:translate-x-0 translate-x-4">
                                    <button @click="showToast('Tính năng gọi điện đang được kết nối...', 'info')" class="p-2 text-emerald-500 hover:bg-emerald-50 rounded-xl transition-all hover:scale-110 active:scale-95" title="Gọi điện">
                                        <i data-lucide="phone-forwarded" class="w-4 h-4"></i>
                                    </button>
                                    <button @click="showToast('Tính năng gửi tin nhắn nhanh...', 'info')" class="p-2 text-blue-500 hover:bg-blue-50 rounded-xl transition-all hover:scale-110 active:scale-95" title="Gửi Zalo/SMS">
                                        <i data-lucide="message-square" class="w-4 h-4"></i>
                                    </button>
                                    <div class="w-px h-4 bg-slate-200 mx-1"></div>
                                    <a href="{{ route('admin.leads.show', $lead->id) }}" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all hover:scale-110 active:scale-95" title="Xem chi tiết">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    @can('leads.update')
                                    <a href="{{ route('admin.leads.convert', $lead->id) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all hover:scale-110 active:scale-95" title="Chuyển đổi thành Học viên">
                                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                                    </a>
                                    @endcan
                                    @can('leads.update')
                                    <button type="button" @click="showEditModal = true; $dispatch('refresh-icons')" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all hover:scale-110 active:scale-95 cursor-pointer" title="Sửa">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </button>
                                    @endcan
                                    @can('leads.delete')
                                    <form action="{{ route('admin.leads.destroy', $lead->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(this, '{{ addslashes($lead->name) }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                                
                                <!-- Edit Modal -->
                                @can('leads.update')
                                <template x-teleport="body">
                                    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                                        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showEditModal = false" x-transition.opacity></div>
                                        
                                        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl mx-auto overflow-hidden text-left" 
                                             x-show="showEditModal" 
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave="transition ease-in duration-200"
                                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                                             
                                            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                                                <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                                                    <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                                    </div>
                                                    Sửa Lead: {{ $lead->name }}
                                                </h3>
                                                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                                                    <i data-lucide="x" class="w-5 h-5"></i>
                                                </button>
                                            </div>

                                            <form action="{{ route('admin.leads.update', $lead->id) }}" method="POST" class="p-6">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                                
                                                <div class="space-y-4">
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Họ tên <span class="text-red-500">*</span></label>
                                                            <div class="relative">
                                                                <i data-lucide="user" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('lead_id') == $lead->id ? old('name') : $lead->name }}">
                                                            </div>
                                                            @if(old('lead_id') == $lead->id)
                                                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                            @endif
                                                        </div>

                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Số điện thoại <span class="text-red-500">*</span></label>
                                                            <div class="relative">
                                                                <i data-lucide="phone" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <input type="text" name="phone" required class="w-full pl-10 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition tabular-nums" value="{{ old('lead_id') == $lead->id ? old('phone') : $lead->phone }}">
                                                            </div>
                                                            @if(old('lead_id') == $lead->id)
                                                                @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block">Email</label>
                                                            <div class="relative">
                                                                <i data-lucide="mail" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <input type="email" name="email" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('lead_id') == $lead->id ? old('email') : $lead->email }}">
                                                            </div>
                                                            @if(old('lead_id') == $lead->id)
                                                                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                            @endif
                                                        </div>

                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block">Ngày sinh</label>
                                                            <div class="relative">
                                                                <i data-lucide="calendar" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <input type="date" name="dob" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('lead_id') == $lead->id ? old('dob') : ($lead->dob ? \Carbon\Carbon::parse($lead->dob)->format('Y-m-d') : '') }}">
                                                            </div>
                                                            @if(old('lead_id') == $lead->id)
                                                                @error('dob') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                            @endif
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
                                                                        <option value="{{ $leadSource->id }}" {{ (old('lead_id') == $lead->id ? old('lead_source_id') : $lead->lead_source_id) === $leadSource->id ? 'selected' : '' }}>{{ $leadSource->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                                            </div>
                                                            @if(old('lead_id') == $lead->id)
                                                                @error('lead_source_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                            @endif
                                                        </div>

                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block">Nhu cầu (Dịch vụ)</label>
                                                            <div class="relative">
                                                                <i data-lucide="list-todo" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <select name="interest_type_id" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                                                    <option value="">-- Chọn Nhu cầu --</option>
                                                                    @foreach($interestTypes as $interest)
                                                                        <option value="{{ $interest->id }}" {{ (old('lead_id') == $lead->id ? old('interest_type_id') : $lead->interest_type_id) === $interest->id ? 'selected' : '' }}>{{ $interest->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                                            </div>
                                                            @if(old('lead_id') == $lead->id)
                                                                @error('interest_type_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                            @endif
                                                        </div>
                                                    </div>

                                                        <div class="grid grid-cols-2 gap-4">
                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block">Người phụ trách</label>
                                                            <div class="relative">
                                                                <i data-lucide="user-check" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <select name="assigned_to" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                                                    <option value="">-- Chưa giao --</option>
                                                                    @foreach($users as $user)
                                                                        <option value="{{ $user->id }}" {{ (old('lead_id') == $lead->id ? old('assigned_to') : $lead->assigned_to) === $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                                            </div>
                                                            @if(old('lead_id') == $lead->id)
                                                                @error('assigned_to') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                            @endif
                                                        </div>

                                                        @if($isGlobalScope)
                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block">Cơ sở <span class="text-red-500">*</span></label>
                                                            <div class="relative">
                                                                <i data-lucide="building-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <select name="center_id" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                                                    <option value="">-- Chọn cơ sở --</option>
                                                                    @foreach($centers as $center)
                                                                        <option value="{{ $center->id }}" {{ (old('lead_id') == $lead->id ? old('center_id') : $lead->center_id) === $center->id ? 'selected' : '' }}>[{{ $center->code }}] {{ $center->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                                            </div>
                                                            @if(old('lead_id') == $lead->id)
                                                                @error('center_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                            @endif
                                                        </div>
                                                        @endif
                                                        </div>
                                                    
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block">Chiến dịch</label>
                                                            <div class="relative">
                                                                <i data-lucide="megaphone" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <select name="campaign_id" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                                                    <option value="">-- Chọn chiến dịch --</option>
                                                                    @foreach($campaigns as $campaign)
                                                                        <option value="{{ $campaign->id }}" {{ (old('lead_id') == $lead->id ? old('campaign_id') : $lead->campaign_id) === $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                                            </div>
                                                            @if(old('lead_id') == $lead->id)
                                                                @error('campaign_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                            @endif
                                                        </div>
                                                        
                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Trạng thái <span class="text-red-500">*</span></label>
                                                            <div class="relative">
                                                                <i data-lucide="tag" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <select name="status_id" required class="w-full pl-10 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                                                    @foreach($statuses as $st)
                                                                        <option value="{{ $st->getId() }}" {{ (old('lead_id') == $lead->id ? old('status_id') : $lead->status_id) === $st->getId() ? 'selected' : '' }}>{{ $st->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                                            </div>
                                                            @if(old('lead_id') == $lead->id)
                                                                @error('status_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Tags Section --}}
                                                    <div class="space-y-1">
                                                        <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest mt-4">Phân loại (Tags)</label>
                                                        <div class="flex flex-wrap gap-2 p-3 border border-slate-100 rounded-xl bg-slate-50/50">
                                                            @php $leadTagIds = $lead->tags->pluck('id')->toArray(); @endphp
                                                            @foreach($allTags as $tag)
                                                            <label class="relative flex items-center group cursor-pointer">
                                                                <input type="checkbox" name="tag_ids[]" value="{{ $tag->getId() }}" {{ in_array($tag->getId(), old('lead_id') == $lead->id ? old('tag_ids', $leadTagIds) : $leadTagIds) ? 'checked' : '' }} class="peer appearance-none absolute">
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
                                                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
                                                    <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
                                                        <i data-lucide="save" class="w-4 h-4"></i> Cập nhật
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </template>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @include('partials.pagination', ['paginator' => $leads])
        @endif
    </div>

    <!-- Create Modal -->
    @can('leads.create')
    <template x-teleport="body">
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showCreateModal = false" x-transition.opacity></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl mx-auto overflow-hidden text-left" 
                 x-show="showCreateModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                 
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                        </div>
                        Tạo Lead Mới
                    </h3>
                    <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
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
                                    <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ !old('_method') ? old('name') : '' }}">
                                </div>
                                @if(!old('_method'))
                                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @endif
                            </div>

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Số điện thoại <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i data-lucide="phone" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <input type="text" name="phone" required class="w-full pl-10 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition tabular-nums" value="{{ !old('_method') ? old('phone') : '' }}">
                                </div>
                                @if(!old('_method'))
                                    @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Email</label>
                                <div class="relative">
                                    <i data-lucide="mail" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <input type="email" name="email" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ !old('_method') ? old('email') : '' }}">
                                </div>
                                @if(!old('_method'))
                                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @endif
                            </div>

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Ngày sinh</label>
                                <div class="relative">
                                    <i data-lucide="calendar" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <input type="date" name="dob" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ !old('_method') ? old('dob') : '' }}">
                                </div>
                                @if(!old('_method'))
                                    @error('dob') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @endif
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
                                            <option value="{{ $leadSource->id }}" {{ (!old('_method') && old('lead_source_id') === $leadSource->id) ? 'selected' : '' }}>{{ $leadSource->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                </div>
                                @if(!old('_method'))
                                    @error('lead_source_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @endif
                            </div>

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Nhu cầu (Dịch vụ)</label>
                                <div class="relative">
                                    <i data-lucide="list-todo" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <select name="interest_type_id" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                        <option value="">-- Chọn Nhu cầu --</option>
                                        @foreach($interestTypes as $interest)
                                            <option value="{{ $interest->id }}" {{ (!old('_method') && old('interest_type_id') === $interest->id) ? 'selected' : '' }}>{{ $interest->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                </div>
                                @if(!old('_method'))
                                    @error('interest_type_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            @if($isGlobalScope)
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Cơ sở <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i data-lucide="building-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <select name="center_id" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                        <option value="">-- Chọn cơ sở --</option>
                                        @foreach($centers as $center)
                                            <option value="{{ $center->id }}" {{ (!old('_method') && old('center_id') === $center->id) ? 'selected' : '' }}>[{{ $center->code }}] {{ $center->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                </div>
                                @if(!old('_method'))
                                    @error('center_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @endif
                            </div>
                            @endif

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Người phụ trách</label>
                                <div class="relative">
                                    <i data-lucide="user-check" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <select name="assigned_to" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                        <option value="">-- Chưa giao --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ (!old('_method') && old('assigned_to') === $user->id) ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                </div>
                                @if(!old('_method'))
                                    @error('assigned_to') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Chiến dịch</label>
                                <div class="relative">
                                    <i data-lucide="megaphone" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <select name="campaign_id" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                        <option value="">-- Chọn chiến dịch --</option>
                                        @foreach($campaigns as $campaign)
                                            <option value="{{ $campaign->id }}" {{ (!old('_method') && old('campaign_id') === $campaign->id) ? 'selected' : '' }}>{{ $campaign->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                </div>
                                @if(!old('_method'))
                                    @error('campaign_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @endif
                            </div>
                            
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Trạng thái</label>
                                <div class="relative">
                                    <i data-lucide="tag" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <select name="status_id" class="w-full pl-10 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                        <option value="">-- Mặc định (New) --</option>
                                        @foreach($statuses as $st)
                                            <option value="{{ $st->getId() }}" {{ (!old('_method') && old('status_id') === $st->getId()) ? 'selected' : '' }}>{{ $st->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                </div>
                                @if(!old('_method'))
                                    @error('status_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @endif
                            </div>
                        </div>

                        {{-- Tags Section --}}
                        <div class="space-y-1 mt-4">
                            <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Phân loại (Tags)</label>
                            <div class="flex flex-wrap gap-2 p-3 border border-slate-100 rounded-xl bg-slate-50/50">
                                @foreach($allTags as $tag)
                                <label class="relative flex items-center group cursor-pointer">
                                    <input type="checkbox" name="tag_ids[]" value="{{ $tag->getId() }}" {{ (!old('_method') && in_array($tag->getId(), old('tag_ids', []))) ? 'checked' : '' }} class="peer appearance-none absolute">
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
                        <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
                        <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
                            <i data-lucide="plus" class="w-4 h-4"></i> Tạo Lead
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    @endcan

    @can('leads.create')
    <!-- Import Modal -->
    <template x-teleport="body">
        <div x-show="showImportModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showImportModal = false" x-transition.opacity></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto overflow-hidden text-left" 
                 x-show="showImportModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                 
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                            <i data-lucide="file-down" class="w-4 h-4"></i>
                        </div>
                        Import Leads từ Excel
                    </h3>
                    <button @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('admin.leads.import') }}" method="POST" class="p-6" enctype="multipart/form-data" @submit.prevent="submitImport">
                    @csrf
                    <input type="hidden" name="import" value="1">
                    
                    <div x-show="!isImporting && importProgress !== 100" class="space-y-4">
                        <div class="p-4 bg-primary-50 rounded-xl text-primary-800 text-sm flex gap-3 items-start border border-primary-100">
                            <i data-lucide="info" class="w-5 h-5 mt-0.5 shrink-0 text-primary-600"></i>
                            <div>
                                <p class="font-semibold mb-1">Hướng dẫn Import:</p>
                                <ul class="list-disc pl-4 space-y-1 text-primary-700/90 text-[13px]">
                                    <li>Cột bắt buộc: <code class="bg-white/60 px-1 rounded font-mono">name</code>, <code class="bg-white/60 px-1 rounded font-mono">phone</code>, <code class="bg-white/60 px-1 rounded font-mono">center_code</code>.</li>
                                    <li>Cột tùy chọn: <code class="bg-white/60 px-1 rounded font-mono">email</code>, <code class="bg-white/60 px-1 rounded font-mono">dob</code>, <code class="bg-white/60 px-1 rounded font-mono">lead_source_code</code>, <code class="bg-white/60 px-1 rounded font-mono">campaign_code</code>, <code class="bg-white/60 px-1 rounded font-mono">interest_type_code</code>.</li>
                                </ul>
                                <a href="{{ route('admin.leads.template') }}" class="inline-flex items-center gap-1 mt-3 px-3 py-1.5 bg-white text-primary-600 hover:bg-primary-100 rounded-lg text-[13px] font-medium transition shadow-sm border border-primary-200">
                                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                    Download File Mẫu
                                </a>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 block mt-4">File Excel (.xlsx, .xls, .csv)</label>
                            <input type="file" x-ref="importFile" name="file" required accept=".xlsx,.xls,.csv" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all border border-slate-200 rounded-xl cursor-pointer">
                            @error('file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Progress Section -->
                    <div x-show="isImporting || importProgress === 100" x-cloak class="space-y-4 mt-2">
                         <div class="flex items-center justify-between text-sm font-medium text-slate-700 mb-1">
                             <span>Tiến trình Import</span>
                             <span x-text="importProgress + '%'" class="text-primary-600 font-bold"></span>
                         </div>
                         <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                              <div class="bg-primary-500 h-2.5 rounded-full transition-all duration-300 ease-out" :style="`width: ${importProgress}%`"></div>
                         </div>
                         
                         <div class="grid grid-cols-2 gap-4 mt-3">
                             <div class="p-3 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 flex justify-between items-center">
                                 <div class="font-medium text-sm">Thành công</div>
                                 <div class="text-lg font-bold" x-text="successCount">0</div>
                             </div>
                             <div class="p-3 bg-red-50 text-red-700 rounded-xl border border-red-100 flex justify-between items-center">
                                 <div class="font-medium text-sm">Lỗi</div>
                                 <div class="text-lg font-bold" x-text="errorCount">0</div>
                             </div>
                         </div>
                         
                         <div class="space-y-1 mt-4">
                             <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-wider">Chi tiết thực thi</label>
                             <textarea x-ref="logbox" readonly class="w-full p-3 bg-slate-800 text-slate-300 font-mono text-xs rounded-xl h-40 resize-none focus:outline-none" :value="importLogs"></textarea>
                         </div>
                    </div>
                    
                    <div class="pt-6 mt-6 border-t border-slate-100 flex gap-3 justify-end relative">
                        <div x-show="isImporting" class="absolute inset-0 bg-white/60 backdrop-blur-[1px] z-10 rounded"></div>
                        <button type="button" @click="showImportModal = false; if(importProgress === 100) window.location.reload();" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">
                            <span x-text="importProgress === 100 ? 'Đóng' : 'Hủy'"></span>
                        </button>
                        <button type="submit" x-show="importProgress !== 100" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
                            <i data-lucide="upload" class="w-4 h-4"></i> Import Dữ liệu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    @endcan

    @can('leads.update')
    <!-- Bulk Assign Modal -->
    <template x-teleport="body">
        <div x-show="showAssignModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showAssignModal = false" x-transition.opacity></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-auto overflow-hidden text-left" 
                 x-show="showAssignModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                 
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <i data-lucide="user-check" class="w-4 h-4"></i>
                        </div>
                        Assign Leads
                    </h3>
                    <button @click="showAssignModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('admin.leads.assign') }}" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" name="assign" value="1">
                    <template x-for="id in selectedItems" :key="id">
                        <input type="hidden" name="lead_ids[]" :value="id">
                    </template>
                    
                    <div class="space-y-4">
                        <p class="text-sm text-slate-600">You are about to assign <span class="font-bold text-primary-600" x-text="selectedItems.length"></span> lead(s) to a user.</p>
                        
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 block">Select User / Sales</label>
                            <div class="relative">
                                <i data-lucide="user" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <select name="assigned_to" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                    <option value="">-- Unassign (Trống) --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-6 mt-6 border-t border-slate-100 flex gap-3 justify-end relative">
                        <button type="button" @click="showAssignModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 transition shadow-lg shadow-emerald-500/30 flex items-center gap-2 font-medium">
                            <i data-lucide="check" class="w-4 h-4"></i> Confirm Assign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    @endcan

    @can('leads.update')
    <!-- Bulk Merge Modal -->
    <template x-teleport="body">
        <div x-show="showMergeModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showMergeModal = false" x-transition.opacity></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto overflow-hidden text-left" 
                 x-show="showMergeModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                 
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                            <i data-lucide="merge" class="w-4 h-4"></i>
                        </div>
                        Merge Leads
                    </h3>
                    <button @click="showMergeModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('admin.leads.merge') }}" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" name="merge" value="1">
                    <template x-for="id in selectedItems" :key="id">
                        <input type="hidden" name="slave_lead_ids[]" :value="id">
                    </template>
                    
                    <div class="space-y-4">
                        <div class="p-3 bg-orange-50 text-orange-800 rounded-xl text-sm border border-orange-100 font-medium leading-relaxed">
                            <i data-lucide="alert-triangle" class="w-5 h-5 inline-block -mt-1 mr-1 text-orange-500"></i>
                            Gộp <span class="font-bold text-orange-700" x-text="selectedItems.length"></span> liên hệ đã chọn lại với nhau. Các liên hệ không được chọn làm Primary sẽ chuyển sang trạng thái "Merged".
                        </div>
                        
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 block mt-4">Chọn Lead Chính (Primary)</label>
                            <div class="relative">
                                <i data-lucide="star" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <select name="master_lead_id" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                    <template x-for="lead in selectedLeads" :key="lead.id">
                                        <option :value="lead.id" x-text="`${lead.name} (${lead.phone})`"></option>
                                    </template>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-6 mt-6 border-t border-slate-100 flex gap-3 justify-end relative">
                        <button type="button" @click="showMergeModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
                        <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition shadow-lg shadow-orange-500/30 flex items-center gap-2 font-medium">
                            <i data-lucide="check" class="w-4 h-4"></i> Xác nhận Gộp
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    @endcan

    @can('leads.update')
    <!-- Bulk Edit Modal -->
    <template x-teleport="body">
        <div x-show="showMassEditModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showMassEditModal = false" x-transition.opacity></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl mx-auto overflow-hidden text-left" 
                 x-show="showMassEditModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                 
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </div>
                        Mass Edit Leads
                    </h3>
                    <button @click="showMassEditModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('admin.leads.bulk-update') }}" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" name="bulk_update" value="1">
                    <template x-for="id in selectedItems" :key="id">
                        <input type="hidden" name="lead_ids[]" :value="id">
                    </template>
                    
                    <div class="space-y-4">
                        <div class="p-3 bg-primary-50 text-primary-800 rounded-xl text-sm border border-primary-100 font-medium leading-relaxed">
                            <i data-lucide="info" class="w-5 h-5 inline-block -mt-1 mr-1 text-primary-500"></i>
                            Cập nhật <span class="font-bold text-primary-700" x-text="selectedItems.length"></span> liên hệ đã chọn. Chỉ những trường được chọn giá trị mới được cập nhật.
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Nguồn</label>
                                <div class="relative">
                                    <i data-lucide="share-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <select name="lead_source_id" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                        <option value="">-- Giữ nguyên --</option>
                                        <option value="null">-- Trống --</option>
                                        @foreach($leadSources as $leadSource)
                                            <option value="{{ $leadSource->id }}">{{ $leadSource->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Nhu cầu (Dịch vụ)</label>
                                <div class="relative">
                                    <i data-lucide="list-todo" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <select name="interest_type_id" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                        <option value="">-- Giữ nguyên --</option>
                                        <option value="null">-- Trống --</option>
                                        @foreach($interestTypes as $interest)
                                            <option value="{{ $interest->id }}">{{ $interest->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            @if($isGlobalScope)
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Cơ sở</label>
                                <div class="relative">
                                    <i data-lucide="building-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <select name="center_id" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                        <option value="">-- Giữ nguyên --</option>
                                        @foreach($centers as $center)
                                            <option value="{{ $center->id }}">[{{ $center->code }}] {{ $center->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                </div>
                            </div>
                            @endif

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Người phụ trách</label>
                                <div class="relative">
                                    <i data-lucide="user-check" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <select name="assigned_to" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                        <option value="">-- Giữ nguyên --</option>
                                        <option value="null">-- Chưa giao --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Chiến dịch</label>
                                <div class="relative">
                                    <i data-lucide="megaphone" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <select name="campaign_id" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                        <option value="">-- Giữ nguyên --</option>
                                        <option value="null">-- Trống --</option>
                                        @foreach($campaigns as $campaign)
                                            <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Status</label>
                                <div class="relative">
                                    <i data-lucide="tag" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <select name="status_id" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                        <option value="">-- Giữ nguyên --</option>
                                        @foreach($statuses as $st)
                                            <option value="{{ $st->getId() }}">{{ $st->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-6 mt-6 border-t border-slate-100 flex gap-3 justify-end relative">
                        <button type="button" @click="showMassEditModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
                        <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
                            <i data-lucide="check" class="w-4 h-4"></i> Xác nhận Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    @endcan
</div>

@push('scripts')
<script>
    window.allLeads = {!! json_encode($leads->map(fn($l) => ['id' => $l->id, 'name' => $l->name, 'phone' => $l->phone])) !!};
    document.addEventListener('alpine:init', () => {
        Alpine.data('leadManagementStore', () => ({
            showCreateModal: {{ $errors->any() && !old('_method') && !old('import') && !old('assign') && !old('merge') && !old('bulk_update') ? 'true' : 'false' }}, 
            showImportModal: {{ $errors->any() && old('import') ? 'true' : 'false' }},
            showAssignModal: {{ $errors->any() && old('assign') ? 'true' : 'false' }},
            showMergeModal: {{ $errors->any() && old('merge') ? 'true' : 'false' }},
            showMassEditModal: {{ $errors->any() && old('bulk_update') ? 'true' : 'false' }},
            
            selectedItems: [],
            availableIds: [{!! $leads->pluck('id')->map(fn($id) => "'{$id}'")->join(',') !!}],

            get isAllSelected() {
                return this.selectedItems.length === this.availableIds.length && this.availableIds.length > 0;
            },

            toggleAll() {
                if (this.isAllSelected) {
                    this.selectedItems = [];
                } else {
                    this.selectedItems = [...this.availableIds];
                }
            },

            get selectedLeads() {
                // Get full lead details for selected IDs using data injected to window or traversing dom. 
                // For simplicity, we just use the IDs here to form the option list.
                // In a real app we might want the names too, but we can just use the DOM elements or a global leads array.
                return window.allLeads.filter(l => this.selectedItems.includes(l.id));
            },

            isImporting: false,
            importProgress: 0,
            importLogs: '',
            successCount: 0,
            errorCount: 0,
            
            submitImport(e) {
                const form = e.target;
                const fileInput = this.$refs.importFile;
                if(!fileInput.files.length) return;
                
                const formData = new FormData(form);
                
                this.isImporting = true;
                this.importProgress = 0;
                this.importLogs = "Đang tải file lên máy chủ...\n";
                this.successCount = 0;
                this.errorCount = 0;
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.error) {
                        this.importLogs += "Lỗi khởi tạo: " + data.error + "\n";
                        this.isImporting = false;
                        return;
                    }
                    
                    const importId = data.import_id;
                    const total = data.total;
                    this.importLogs += "Kết quả phân tích: " + total + " dòng.\nBắt đầu xử lý import dữ liệu...\n";
                    
                    if (total === 0) {
                         this.importLogs += "File Excel rỗng hoặc không có dữ liệu để import.\n";
                         this.isImporting = false;
                         return;
                    }
                    
                    this.processChunk(importId, 0, total);
                })
                .catch(err => {
                    this.importLogs += "Lỗi kết nối upload: " + err + "\n";
                    this.isImporting = false;
                });
            },
            
            processChunk(importId, offset, total) {
                fetch('{{ route("admin.leads.import.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ import_id: importId, offset: offset, limit: 20 })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.error) {
                         this.importLogs += "Lỗi trong quá trình import: " + data.error + "\n";
                         this.isImporting = false;
                         return;
                    }
                    
                    this.successCount += data.success_count;
                    this.errorCount += data.error_count;
                    
                    if(data.logs.length > 0) {
                        this.importLogs += data.logs.join("\n") + "\n";
                    }
                    
                    const logbox = this.$refs.logbox;
                    if(logbox) logbox.scrollTop = logbox.scrollHeight;
                    
                    let nextOffset = Math.min(offset + 20, total);
                    this.importProgress = Math.round((nextOffset / total) * 100);
                    
                    if(!data.is_finished) {
                        this.processChunk(importId, data.next_offset, total);
                    } else {
                        this.importProgress = 100;
                        this.isImporting = false;
                        this.importLogs += "\n==============\nHOÀN TẤT IMPORT!\nThành công: " + this.successCount + " phiếu\nLỗi: " + this.errorCount + " phiếu\n";
                        if(logbox) logbox.scrollTop = logbox.scrollHeight;
                    }
                })
                .catch(err => {
                    this.importLogs += "Lỗi hệ thống khi xử lý: " + err + "\n";
                    this.isImporting = false;
                });
            }
        }));

        window.addEventListener('refresh-icons', () => {
            setTimeout(() => {
                if (window.lucide) { lucide.createIcons(); }
            }, 50);
        });
    });
</script>
@endpush
@endsection
