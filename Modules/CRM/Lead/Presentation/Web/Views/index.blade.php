@extends('layouts.app')

@section('title', 'Leads Management')

@section('content')
<div class="space-y-6" x-data="leadManagementStore()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Leads Management</h1>
            <p class="text-slate-500 mt-1">Manage and track all leads</p>
        </div>
        <div class="flex gap-2">
            @can('leads.export')
            <div x-data="{ open: false }" class="relative">
                <button type="button" @click="open = !open" @click.away="open = false" class="px-4 py-2 bg-emerald-50 text-emerald-700 hover:text-emerald-800 rounded-lg hover:bg-emerald-100 transition flex items-center gap-2 border border-emerald-200 font-medium whitespace-nowrap">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Export Leads</span>
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </button>
                <div x-show="open" x-cloak 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50">
                    <a href="{{ route('admin.leads.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition">
                        <i data-lucide="sheet" class="w-4 h-4"></i>
                        Export to Excel
                    </a>
                    <a href="{{ route('admin.leads.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-red-600 transition">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        Export to PDF
                    </a>
                </div>
            </div>
            @endcan
            @can('leads.create')
            <button type="button" @click="showImportModal = true; $dispatch('refresh-icons')" class="px-4 py-2 bg-slate-100 text-slate-700 hover:text-slate-900 rounded-lg hover:bg-slate-200 transition flex items-center gap-2 border border-slate-200 font-medium whitespace-nowrap">
                <i data-lucide="file-down" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Import Excel</span>
            </button>
            <button type="button" @click="showCreateModal = true; $dispatch('refresh-icons')" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition flex items-center gap-2 shadow-lg shadow-primary-500/30 whitespace-nowrap font-medium">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>New Lead</span>
            </button>
            @endcan
        </div>
    </div>

    <!-- Filter/Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row gap-4 items-end">
        <form action="{{ route('admin.leads.index') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/4">
                <label for="search" class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                <div class="relative w-full">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" id="search" placeholder="Search by name..." value="{{ request('search') }}"
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 transition outline-none">
                </div>
            </div>
            <div class="w-full md:w-1/4">
                <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                <div class="relative w-full">
                    <i data-lucide="phone" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="phone" id="phone" placeholder="Search by phone..." value="{{ request('phone') }}"
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 transition outline-none">
                </div>
            </div>
            @if($isGlobalScope)
            <div class="w-full md:w-1/4">
                <label for="center_id" class="block text-sm font-medium text-slate-700 mb-1">Center</label>
                <div class="relative w-full">
                    <i data-lucide="building-2" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <select name="center_id" id="center_id" class="w-full pl-9 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 transition outline-none appearance-none">
                        <option value="">All Centers</option>
                        @foreach($centers as $c)
                            <option value="{{ $c->id }}" {{ request('center_id') == $c->id ? 'selected' : '' }}>[{{ $c->code }}] {{ $c->name }}</option>
                        @endforeach
                    </select>
                    <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>
            </div>
            @endif
            <div class="w-full md:w-1/4">
                <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <div class="relative w-full">
                    <i data-lucide="tag" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <select name="status" id="status" class="w-full pl-9 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 transition outline-none appearance-none">
                        <option value="">All Statuses</option>
                        <option value="New" {{ request('status') == 'New' ? 'selected' : '' }}>New</option>
                        <option value="Contacted" {{ request('status') == 'Contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="Qualified" {{ request('status') == 'Qualified' ? 'selected' : '' }}>Qualified</option>
                        <option value="Lost" {{ request('status') == 'Lost' ? 'selected' : '' }}>Lost</option>
                    </select>
                    <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="px-4 py-2 bg-primary-50 text-primary-600 hover:bg-primary-100 rounded-lg transition font-medium text-sm flex items-center gap-2 whitespace-nowrap">
                    <i data-lucide="filter" class="w-4 h-4"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'phone', 'center_id', 'status']))
                <a href="{{ route('admin.leads.index') }}" class="px-4 py-2 bg-slate-50 text-slate-600 hover:bg-slate-100 rounded-lg transition font-medium text-sm flex items-center gap-2 border border-slate-200 whitespace-nowrap">
                    <i data-lucide="x" class="w-4 h-4"></i> Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data List -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($leads->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="users" class="w-8 h-8 text-slate-400"></i>
            </div>
            <p class="text-slate-500 mb-4">No leads found</p>
            @can('leads.create')
            <button type="button" @click="showCreateModal = true; $dispatch('refresh-icons')" class="inline-flex items-center gap-2 text-primary-500 hover:text-primary-600 font-medium cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i> Create new lead
            </button>
            @endcan
        </div>
        @else
        <div class="overflow-x-auto">
            <!-- Bulk Action Header -->
            <div x-show="selectedItems.length > 0" x-cloak class="bg-primary-50 px-6 py-3 border-b border-primary-100 flex items-center justify-between transition-all">
                <div class="text-sm font-medium text-primary-800">
                    <span x-text="selectedItems.length"></span> lead(s) selected
                </div>
                <div class="flex items-center gap-2">
                    @can('leads.update')
                    <button type="button" x-show="selectedItems.length > 1" @click="showMergeModal = true" class="px-3 py-1.5 bg-white border border-primary-200 text-primary-600 rounded-lg text-sm font-medium hover:bg-primary-100 transition flex items-center gap-1 shadow-sm">
                        <i data-lucide="merge" class="w-4 h-4"></i> Merge
                    </button>
                    <button type="button" @click="showAssignModal = true" class="px-3 py-1.5 bg-white border border-primary-200 text-primary-600 rounded-lg text-sm font-medium hover:bg-primary-100 transition flex items-center gap-1 shadow-sm">
                        <i data-lucide="user-check" class="w-4 h-4"></i> Assign Selected
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
                        <th class="p-4 px-6">Name</th>
                        <th class="p-4 px-6">Phone</th>
                        <th class="p-4 px-6">Center</th>
                        <th class="p-4 px-6">Assigned To</th>
                        <th class="p-4 px-6">Status</th>
                        <th class="p-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($leads as $lead)
                        <tr class="hover:bg-slate-50 transition group" :class="{ 'bg-primary-50/30': selectedItems.includes('{{ $lead->id }}') }" x-data="{ showEditModal: {{ $errors->any() && old('_method') == 'PUT' && old('lead_id') == $lead->id ? 'true' : 'false' }} }">
                            <td class="p-4 px-6 whitespace-nowrap">
                                <input type="checkbox" value="{{ $lead->id }}" x-model="selectedItems" class="rounded border-slate-300 text-primary-500 focus:ring-primary-500 w-4 h-4 cursor-pointer">
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                <div class="font-medium text-slate-800">{{ $lead->name }}</div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-slate-600">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="phone" class="w-4 h-4 text-slate-400"></i>
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
                                @php
                                    $assignedUser = $lead->assigned_to ? $users->firstWhere('id', $lead->assigned_to) : null;
                                @endphp
                                @if($assignedUser)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-xs font-semibold text-slate-600 uppercase border border-slate-200">
                                            {{ substr($assignedUser->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm font-medium text-slate-700">{{ $assignedUser->name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-sm italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                @php
                                    $statusColor = match(strtolower($lead->status)) {
                                        'new' => 'bg-blue-100 text-blue-700',
                                        'contacted' => 'bg-amber-100 text-amber-700',
                                        'qualified' => 'bg-emerald-100 text-emerald-700',
                                        'lost' => 'bg-red-100 text-red-700',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                    {{ ucfirst($lead->status) }}
                                </span>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                    @can('leads.update')
                                    <button type="button" @click="showEditModal = true; $dispatch('refresh-icons')" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition cursor-pointer" title="Edit">
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
                                                            <label class="text-sm font-medium text-slate-700 block">Name <span class="text-red-500">*</span></label>
                                                            <div class="relative">
                                                                <i data-lucide="user" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('lead_id') == $lead->id ? old('name') : $lead->name }}">
                                                            </div>
                                                            @if(old('lead_id') == $lead->id)
                                                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                            @endif
                                                        </div>

                                                        <div class="space-y-1">
                                                            <label class="text-sm font-medium text-slate-700 block">Phone <span class="text-red-500">*</span></label>
                                                            <div class="relative">
                                                                <i data-lucide="phone" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <input type="text" name="phone" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('lead_id') == $lead->id ? old('phone') : $lead->phone }}">
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
                                                                <select name="source_id" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                                                    <option value="">-- Chọn Nguồn --</option>
                                                                    @foreach($sources as $source)
                                                                        <option value="{{ $source->id }}" {{ (old('lead_id') == $lead->id ? old('source_id') : $lead->source_id) === $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                                            </div>
                                                            @if(old('lead_id') == $lead->id)
                                                                @error('source_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
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
                                                            <label class="text-sm font-medium text-slate-700 block">Status <span class="text-red-500">*</span></label>
                                                            <div class="relative">
                                                                <i data-lucide="tag" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                <select name="status" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                                                    <option value="New" {{ (old('lead_id') == $lead->id ? old('status') : strtolower($lead->status)) === 'new' ? 'selected' : '' }}>New</option>
                                                                    <option value="Contacted" {{ (old('lead_id') == $lead->id ? old('status') : strtolower($lead->status)) === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                                                    <option value="Qualified" {{ (old('lead_id') == $lead->id ? old('status') : strtolower($lead->status)) === 'qualified' ? 'selected' : '' }}>Qualified</option>
                                                                    <option value="Lost" {{ (old('lead_id') == $lead->id ? old('status') : strtolower($lead->status)) === 'lost' ? 'selected' : '' }}>Lost</option>
                                                                </select>
                                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                                            </div>
                                                            @if(old('lead_id') == $lead->id)
                                                                @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                            @endif
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
        
        @if($leads->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $leads->appends(request()->query())->links() }}
        </div>
        @endif
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
                                <label class="text-sm font-medium text-slate-700 block">Name <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i data-lucide="user" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <input type="text" name="name" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ !old('_method') ? old('name') : '' }}">
                                </div>
                                @if(!old('_method'))
                                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @endif
                            </div>

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 block">Phone <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i data-lucide="phone" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <input type="text" name="phone" required class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ !old('_method') ? old('phone') : '' }}">
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
                                    <select name="source_id" class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                                        <option value="">-- Chọn Nguồn --</option>
                                        @foreach($sources as $source)
                                            <option value="{{ $source->id }}" {{ (!old('_method') && old('source_id') === $source->id) ? 'selected' : '' }}>{{ $source->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                </div>
                                @if(!old('_method'))
                                    @error('source_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
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
                            
                            <div></div>
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
                                    <li>Cột tùy chọn: <code class="bg-white/60 px-1 rounded font-mono">email</code>, <code class="bg-white/60 px-1 rounded font-mono">dob</code>, <code class="bg-white/60 px-1 rounded font-mono">source_code</code>, <code class="bg-white/60 px-1 rounded font-mono">campaign_code</code>, <code class="bg-white/60 px-1 rounded font-mono">interest_type_code</code>.</li>
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
</div>

@push('scripts')
<script>
    window.allLeads = {!! json_encode($leads->map(fn($l) => ['id' => $l->id, 'name' => $l->name, 'phone' => $l->phone])) !!};
    document.addEventListener('alpine:init', () => {
        Alpine.data('leadManagementStore', () => ({
            showCreateModal: {{ $errors->any() && !old('_method') && !old('import') && !old('assign') && !old('merge') ? 'true' : 'false' }}, 
            showImportModal: {{ $errors->any() && old('import') ? 'true' : 'false' }},
            showAssignModal: {{ $errors->any() && old('assign') ? 'true' : 'false' }},
            showMergeModal: {{ $errors->any() && old('merge') ? 'true' : 'false' }},
            
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
