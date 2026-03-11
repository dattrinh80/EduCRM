@extends('layouts.app')

@section('title', 'Quản lý Khách hàng (Phụ huynh)')

@section('breadcrumb_items')
    <a href="{{ route('admin.customers.index') }}" class="text-slate-400 hover:text-primary-500 transition-colors">CRM</a>
    <i data-lucide="chevron-right" class="w-2.5 h-2.5 opacity-50"></i>
    <span class="text-primary-500">Khách hàng</span>
@endsection

@section('content')
<div class="space-y-6" x-data="{ 
    showCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }},
    showEditModal: false,
    selectedItems: [],
    get isAllSelected() {
        return this.selectedItems.length > 0 && this.selectedItems.length === document.querySelectorAll('tbody input[type=checkbox]').length;
    },
    toggleAll(e) {
        if (e.target.checked) {
            this.selectedItems = Array.from(document.querySelectorAll('tbody input[type=checkbox]')).map(el => el.value);
        } else {
            this.selectedItems = [];
        }
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight">Khách hàng (Phụ huynh)</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-1.5 text-sm">
                <i data-lucide="info" class="w-3.5 h-3.5 opacity-60"></i>
                Quản lý và theo dõi danh sách khách hàng toàn diện
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @can('customers.create')
            <x-ui.button variant="secondary" icon="file-down" @click="showImportModal = true; $dispatch('refresh-icons')">
                <span class="hidden sm:inline">Nhập Excel</span>
            </x-ui.button>
            <x-ui.button variant="primary" icon="plus-circle" @click="showCreateModal = true; $dispatch('refresh-icons')">
                <span>Thêm Khách hàng</span>
            </x-ui.button>
            @endcan

            @can('customers.export')
            <div x-data="{ open: false }" class="relative">
                <x-ui.button variant="secondary" icon="download" @click="open = !open" @click.away="open = false" iconRight="chevron-down" ::class="open ? 'ring-2 ring-primary-500/20' : ''">
                    <span class="hidden sm:inline">Xuất dữ liệu</span>
                </x-ui.button>
                <div x-show="open" x-cloak 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-1 z-50">
                    <a href="{{ route('admin.customers.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-500"></i>
                        Excel (.xlsx)
                    </a>
                    <a href="{{ route('admin.customers.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-rose-50 hover:text-rose-700 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4 text-rose-500"></i>
                        PDF (.pdf)
                    </a>
                </div>
            </div>
            @endcan
        </div>
    </div>


    <!-- Filter/Search -->
    <x-ui.card bodyClass="p-4">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="space-y-4">
            
            <div class="flex flex-wrap items-end gap-4">
                <x-ui.input 
                    label="Họ tên / Email" 
                    name="search" 
                    id="search" 
                    placeholder="Tên, email…" 
                    value="{{ request('search') }}" 
                    icon="search" 
                    containerClass="w-full sm:w-64 shrink-0"
                />
                
                <x-ui.input 
                    label="Số điện thoại" 
                    name="phone" 
                    id="phone" 
                    placeholder="Số điện thoại…" 
                    value="{{ request('phone') }}" 
                    icon="phone" 
                    :uppercase="true" 
                    containerClass="w-full sm:w-48 shrink-0"
                />

                @if($isGlobalScope)
                    <x-ui.select label="Cơ sở" name="center_id" id="center_id" icon="building-2" containerClass="w-full sm:w-64 shrink-0">
                        <option value="">Tất cả cơ sở</option>
                        @foreach($centers as $c)
                            <option value="{{ $c->id }}" {{ request('center_id') == $c->id ? 'selected' : '' }}>[{{ $c->code }}] {{ $c->name }}</option>
                        @endforeach
                    </x-ui.select>
                @endif

                <div class="flex gap-2 pb-0.5 shrink-0">
                    <x-ui.button type="submit" variant="secondary" icon="filter">
                        Lọc
                    </x-ui.button>
                    @if(request()->hasAny(['search', 'phone', 'center_id']))
                        <x-ui.button variant="ghost" icon="x-circle" :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.customers.index'), 'tag' => 'a'])">
                            Xoá lọc
                        </x-ui.button>
                    @endif
                </div>
            </div>
        </form>
    </x-ui.card>


    <!-- Data List -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($customers->isEmpty())
            <x-ui.empty-state 
                title="Chưa có khách hàng"
                description="Hệ thống chưa ghi nhận dữ liệu nào khớp với bộ lọc của bạn."
                icon="users-2"
                actionText="Tạo Khách hàng mới ngay"
                actionClick="showCreateModal = true; $dispatch('refresh-icons')"
            />
        @else
        <div class="overflow-x-auto">
            <!-- Bulk Action Header -->
            <div x-show="selectedItems.length > 0" x-cloak class="bg-primary-50 px-6 py-3 border-b border-primary-100 flex items-center justify-between transition-all">
                <div class="text-sm font-bold text-primary-800 flex items-center gap-2">
                    <span class="bg-primary-200 text-primary-700 px-2 py-0.5 rounded-md text-xs" x-text="selectedItems.length"></span>
                    <span>Khách hàng đã chọn</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="showMassEditModal = true; $dispatch('refresh-icons')" class="px-3 py-1.5 bg-white border border-primary-200 text-primary-600 rounded-xl text-xs font-bold hover:bg-primary-50 transition flex items-center gap-1.5 shadow-sm active:scale-95">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Sửa hàng loạt
                    </button>
                    <button type="button" @click="showMergeModal = true; $dispatch('refresh-icons')" class="px-3 py-1.5 bg-white border border-primary-200 text-primary-600 rounded-xl text-xs font-bold hover:bg-primary-50 transition flex items-center gap-1.5 shadow-sm active:scale-95">
                        <i data-lucide="merge" class="w-3.5 h-3.5"></i> Gộp Khách hàng
                    </button>
                </div>
            </div>

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase text-slate-500 font-bold tracking-widest whitespace-nowrap">
                        <th class="p-4 w-10 px-6">
                            <input type="checkbox" :checked="isAllSelected" @change="toggleAll" class="rounded border-slate-300 text-primary-500 focus:ring-primary-500 w-4 h-4 cursor-pointer">
                        </th>
                        @php
                            $sortableHeaders = [
                                'name' => 'Khách hàng',
                                'phone' => 'Liên hệ',
                                'created_at' => 'Thời gian tạo'
                            ];
                        @endphp
                        @foreach($sortableHeaders as $col => $label)
                        <th class="p-4 px-6">
                            <a href="{{ route('admin.customers.index', array_merge(request()->query(), ['sort_by' => $col, 'sort_dir' => ($sortBy === $col && $sortDir === 'asc') ? 'desc' : 'asc', 'page' => 1])) }}"
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
                        <th class="p-4 px-6">Học viên / Cơ sở</th>
                        <th class="p-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach ($customers as $customer)
                        <tr class="hover:bg-slate-50 transition group" :class="{ 'bg-primary-50/30': selectedItems.includes('{{ $customer->id }}') }" x-data="{ showEditModal: {{ $errors->any() && old('_method') == 'PUT' && old('customer_id') == $customer->id ? 'true' : 'false' }}, selectedCenterId: '{{ $customer->center_id }}' }">
                            <td class="p-4 px-6 whitespace-nowrap">
                                <input type="checkbox" value="{{ $customer->id }}" x-model="selectedItems" class="rounded border-slate-300 text-primary-500 focus:ring-primary-500 w-4 h-4 cursor-pointer">
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm border border-indigo-200 shadow-sm">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="font-bold text-slate-800 hover:text-primary-600 transition">{{ $customer->name }}</a>
                                            @if($customer->gender)
                                            <x-ui.badge variant="{{ $customer->gender === 'MALE' ? 'info' : ($customer->gender === 'FEMALE' ? 'danger' : 'secondary') }}" class="text-[9px] px-1.5 py-0 mt-0">
                                                {{ $customer->gender === 'MALE' ? 'Nam' : ($customer->gender === 'FEMALE' ? 'Nữ' : 'Khác') }}
                                            </x-ui.badge>
                                            @endif
                                        </div>
                                        @if($customer->tags->isNotEmpty())
                                        <div class="flex flex-wrap gap-1 mt-1.5">
                                            @foreach($customer->tags as $tag)
                                                <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $tag->color }}" title="{{ $tag->name }}"></div>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-slate-600 tabular-nums">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                        <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i> {{ $customer->phone }}
                                    </div>
                                    @if($customer->email)
                                    <div class="flex items-center gap-2 text-[11px] text-slate-400 font-medium">
                                        <i data-lucide="mail" class="w-3.5 h-3.5 opacity-60"></i> {{ $customer->email }}
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-400 text-[11px] font-bold whitespace-nowrap flex flex-col uppercase tracking-wider tabular-nums">
                                 {{ $customer->created_at->translatedFormat('d/m/Y') }}
                                 <span class="text-[9px] opacity-60 font-medium normal-case tracking-normal">{{ $customer->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                @php
                                    $studentCount = $customer->studentGuardians->count();
                                    $center = $centers->firstWhere('id', $customer->center_id);
                                @endphp
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5 text-xs font-bold {{ $studentCount > 0 ? 'text-primary-600' : 'text-slate-400' }}">
                                        <i data-lucide="users" class="w-3.5 h-3.5"></i> 
                                        {{ $studentCount }} Học viên
                                    </div>
                                    @if($center)
                                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        <i data-lucide="building-2" class="w-3 h-3 text-slate-400"></i>
                                        <span>[{{ $center->code }}] {{ $center->name }}</span>
                                    </div>
                                    @else
                                    <span class="text-slate-400 text-[11px] italic">N/A</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-right font-medium">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:translate-x-0 translate-x-4">
                                    <button @click="showToast('Tính năng gọi điện đang được kết nối...', 'info')" class="p-2 text-emerald-500 hover:bg-emerald-50 rounded-xl transition-all hover:scale-110 active:scale-95" title="Gọi điện">
                                        <i data-lucide="phone-forwarded" class="w-4 h-4"></i>
                                    </button>
                                    <button @click="showToast('Tính năng gửi tin nhắn nhanh...', 'info')" class="p-2 text-blue-500 hover:bg-blue-50 rounded-xl transition-all hover:scale-110 active:scale-95" title="Gửi Zalo/SMS">
                                        <i data-lucide="message-square" class="w-4 h-4"></i>
                                    </button>
                                    <div class="w-px h-4 bg-slate-200 mx-1"></div>
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all hover:scale-110 active:scale-95" title="Xem chi tiết">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <button type="button" @click="showEditModal = true; $dispatch('refresh-icons')" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all hover:scale-110 active:scale-95 cursor-pointer" title="Sửa">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </button>
                                    <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(this, '{{ addslashes($customer->name) }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                                
                                <!-- Edit Modal -->
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
                                                    Sửa Khách hàng: {{ $customer->name }}
                                                </h3>
                                                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                                                    <i data-lucide="x" class="w-5 h-5"></i>
                                                </button>
                                            </div>

                                            <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" class="p-6">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                                
                                                <div class="space-y-4">
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Họ tên <span class="text-red-500">*</span></label>
                                                            <div class="relative">
                                                                <i data-lucide="user" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('customer_id') == $customer->id ? old('name') : $customer->name }}">
                                                            </div>
                                                            @if(old('customer_id') == $customer->id)
                                                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                            @endif
                                                        </div>

                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Số điện thoại <span class="text-red-500">*</span></label>
                                                            <div class="relative">
                                                                <i data-lucide="phone" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <input type="text" name="phone" required class="w-full pl-10 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition tabular-nums" value="{{ old('customer_id') == $customer->id ? old('phone') : $customer->phone }}">
                                                            </div>
                                                            @if(old('customer_id') == $customer->id)
                                                                @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Email</label>
                                                            <div class="relative">
                                                                <i data-lucide="mail" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <input type="email" name="email" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('customer_id') == $customer->id ? old('email') : $customer->email }}">
                                                            </div>
                                                            @if(old('customer_id') == $customer->id)
                                                                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                            @endif
                                                        </div>

                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Ngày sinh</label>
                                                            <div class="relative">
                                                                <i data-lucide="calendar" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <input type="date" name="dob" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('customer_id') == $customer->id ? old('dob') : ($customer->dob ? \Carbon\Carbon::parse($customer->dob)->format('Y-m-d') : '') }}">
                                                            </div>
                                                        </div>

                                                        <div class="space-y-1 col-span-2 sm:col-span-1">
                                                            <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Giới tính</label>
                                                            <div class="grid grid-cols-3 gap-2">
                                                                <label class="cursor-pointer">
                                                                    <input type="radio" name="gender" value="MALE" class="peer hidden" {{ (old('customer_id') == $customer->id ? old('gender') : $customer->gender) === 'MALE' ? 'checked' : '' }}>
                                                                    <div class="flex items-center justify-center py-2 rounded-xl border border-slate-200 peer-checked:bg-primary-50 peer-checked:border-primary-500 peer-checked:text-primary-700 hover:bg-slate-50 transition text-sm">Nam</div>
                                                                </label>
                                                                <label class="cursor-pointer">
                                                                    <input type="radio" name="gender" value="FEMALE" class="peer hidden" {{ (old('customer_id') == $customer->id ? old('gender') : $customer->gender) === 'FEMALE' ? 'checked' : '' }}>
                                                                    <div class="flex items-center justify-center py-2 rounded-xl border border-slate-200 peer-checked:bg-primary-50 peer-checked:border-primary-500 peer-checked:text-primary-700 hover:bg-slate-50 transition text-sm">Nữ</div>
                                                                </label>
                                                                <label class="cursor-pointer">
                                                                    <input type="radio" name="gender" value="OTHER" class="peer hidden" {{ (old('customer_id') == $customer->id ? old('gender') : $customer->gender) === 'OTHER' ? 'checked' : '' }}>
                                                                    <div class="flex items-center justify-center py-2 rounded-xl border border-slate-200 peer-checked:bg-primary-50 peer-checked:border-primary-500 peer-checked:text-primary-700 hover:bg-slate-50 transition text-sm">Khác</div>
                                                                </label>
                                                            </div>
                                                        </div>

                                                        @if($isGlobalScope)
                                                        <div class="space-y-1 col-span-2 sm:col-span-1">
                                                            <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Cơ sở <span class="text-red-500">*</span></label>
                                                            <div class="relative">
                                                                <i data-lucide="building-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <select name="center_id" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                                                    <option value="">-- Chọn cơ sở --</option>
                                                                    @foreach($centers as $center)
                                                                        <option value="{{ $center->id }}" {{ (old('customer_id') == $customer->id ? old('center_id') : $customer->center_id) === $center->id ? 'selected' : '' }}>[{{ $center->code }}] {{ $center->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="space-y-1">
                                                        <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Địa chỉ</label>
                                                        <div class="relative">
                                                            <i data-lucide="map-pin" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                            <input type="text" name="address" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('customer_id') == $customer->id ? old('address') : $customer->address }}">
                                                        </div>
                                                    </div>

                                                    {{-- Tags Section --}}
                                                    <div class="space-y-1">
                                                        <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest mt-4">Phân loại (Tags)</label>
                                                        <div class="flex flex-wrap gap-2 p-3 border border-slate-100 rounded-xl bg-slate-50/50">
                                                            @php $customerTagIds = $customer->tags->pluck('id')->toArray(); @endphp
                                                            @foreach($allTags as $tag)
                                                            <label class="relative flex items-center group cursor-pointer">
                                                                <input type="checkbox" name="tag_ids[]" value="{{ $tag->getId() }}" {{ in_array($tag->getId(), old('customer_id') == $customer->id ? old('tag_ids', $customerTagIds) : $customerTagIds) ? 'checked' : '' }} class="peer appearance-none absolute">
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
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @include('partials.pagination', ['paginator' => $customers])
        @endif
    </div>

    <!-- Create Modal global -->
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
                        Tạo Khách hàng mới
                    </h3>
                    <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('admin.customers.store') }}" method="POST" class="p-6">
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
                                <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Email</label>
                                <div class="relative">
                                    <i data-lucide="mail" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <input type="email" name="email" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ !old('_method') ? old('email') : '' }}">
                                </div>
                                @if(!old('_method'))
                                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @endif
                            </div>

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Ngày sinh</label>
                                <div class="relative">
                                    <i data-lucide="calendar" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <input type="date" name="dob" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ !old('_method') ? old('dob') : '' }}">
                                </div>
                            </div>

                            <div class="space-y-1 col-span-2 sm:col-span-1">
                                <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Giới tính</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="gender" value="MALE" class="peer hidden" {{ (!old('_method') && old('gender') === 'MALE') ? 'checked' : '' }}>
                                        <div class="flex items-center justify-center py-2 rounded-xl border border-slate-200 peer-checked:bg-primary-50 peer-checked:border-primary-500 peer-checked:text-primary-700 hover:bg-slate-50 transition text-sm">Nam</div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="gender" value="FEMALE" class="peer hidden" {{ (!old('_method') && old('gender') === 'FEMALE') ? 'checked' : '' }}>
                                        <div class="flex items-center justify-center py-2 rounded-xl border border-slate-200 peer-checked:bg-primary-50 peer-checked:border-primary-500 peer-checked:text-primary-700 hover:bg-slate-50 transition text-sm">Nữ</div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="gender" value="OTHER" class="peer hidden" {{ (!old('_method') && old('gender') === 'OTHER') ? 'checked' : '' }}>
                                        <div class="flex items-center justify-center py-2 rounded-xl border border-slate-200 peer-checked:bg-primary-50 peer-checked:border-primary-500 peer-checked:text-primary-700 hover:bg-slate-50 transition text-sm">Khác</div>
                                    </label>
                                </div>
                            </div>

                            @if($isGlobalScope)
                            <div class="space-y-1 col-span-2 sm:col-span-1">
                                <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Cơ sở <span class="text-red-500">*</span></label>
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
                        </div>

                        <div class="space-y-1 mt-4">
                            <label class="text-sm font-medium text-slate-700 block text-xs uppercase tracking-widest">Địa chỉ</label>
                            <div class="relative">
                                <i data-lucide="map-pin" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" name="address" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ !old('_method') ? old('address') : '' }}">
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
                            <i data-lucide="save" class="w-4 h-4"></i> Tạo mới
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

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
                        Nhập Khách hàng từ Excel
                    </h3>
                    <button @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('admin.customers.import') }}" method="POST" class="p-6" enctype="multipart/form-data" @submit.prevent="submitImport">
                    @csrf
                    <input type="hidden" name="import" value="1">
                    
                    <div x-show="!isImporting && importProgress !== 100" class="space-y-4">
                        <div class="p-4 bg-primary-50 rounded-xl text-primary-800 text-sm flex gap-3 items-start border border-primary-100">
                            <i data-lucide="info" class="w-5 h-5 mt-0.5 shrink-0 text-primary-600"></i>
                            <div>
                                <p class="font-semibold mb-1">Hướng dẫn Import:</p>
                                <ul class="list-disc pl-4 space-y-1 text-primary-700/90 text-[13px]">
                                    <li>Cột bắt buộc: <code class="bg-white/60 px-1 rounded font-mono">name</code>, <code class="bg-white/60 px-1 rounded font-mono">phone</code>.</li>
                                    <li>Cột tùy chọn: <code class="bg-white/60 px-1 rounded font-mono">email</code>, <code class="bg-white/60 px-1 rounded font-mono">dob</code>, <code class="bg-white/60 px-1 rounded font-mono">gender</code> (MALE/FEMALE), <code class="bg-white/60 px-1 rounded font-mono">address</code>, <code class="bg-white/60 px-1 rounded font-mono">center_code</code>.</li>
                                </ul>
                                <a href="{{ route('admin.customers.download_template') }}" class="inline-flex items-center gap-1 mt-3 px-3 py-1.5 bg-white text-primary-600 hover:bg-primary-100 rounded-lg text-[13px] font-medium transition shadow-sm border border-primary-200">
                                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                    Tải file mẫu
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
                             <span>Tiến trình xử lý</span>
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
                            <i data-lucide="upload" class="w-4 h-4"></i> Bắt đầu Nhập
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('customerManagementStore', () => ({
            showCreateModal: {{ $errors->any() && !old('_method') && !old('import') ? 'true' : 'false' }}, 
            showImportModal: {{ $errors->any() && old('import') ? 'true' : 'false' }},
            
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
                fetch('{{ route("admin.customers.import_process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ import_id: importId, offset: offset, limit: 10 })
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
                    
                    let nextOffset = Math.min(offset + 10, total);
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
