@extends('layouts.app')

@section('title', 'Quản lý Nhiệm vụ')

@section('breadcrumb_items')
    <a href="{{ route('admin.tasks.index') }}" class="text-slate-400 hover:text-primary-500 transition-colors">CRM</a>
    <i data-lucide="chevron-right" class="w-2.5 h-2.5 opacity-50"></i>
    <span class="text-primary-500">Tasks Desk</span>
@endsection

@section('content')
<div class="space-y-6" x-data="taskManagement()">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight">Trung tâm Nhiệm vụ</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-1.5 text-sm">
                <i data-lucide="info" class="w-3.5 h-3.5 opacity-60"></i>
                Theo dõi và xử lý các đầu việc cần thực hiện
            </p>
        </div>
        <div class="flex gap-2">
            <x-ui.button variant="primary" icon="plus-circle" @click="showCreateModal = true; $nextTick(() => { if (window.lucide) { lucide.createIcons(); } })">
                Giao việc mới
            </x-ui.button>
        </div>
    </div>


    <!-- Filters -->
    <x-ui.card bodyClass="p-4">
        <form action="{{ route('admin.tasks.index') }}" method="GET" class="flex flex-wrap items-end gap-x-6 gap-y-4">
            <x-ui.input name="search" label="Tiêu đề" placeholder="Tìm kiếm nhiệm vụ…" value="{{ $search }}" icon="search" containerClass="w-full sm:w-80 shrink-0" />
            
            @if($isGlobalScope)
            <x-ui.select name="center_id" label="Cơ sở" containerClass="w-full sm:w-72 shrink-0">
                <option value="">Tất cả cơ sở</option>
                @foreach($centers as $c)
                    <option value="{{ $c->id }}" {{ $centerId == $c->id ? 'selected' : '' }}>[{{ $c->code }}] {{ $c->name }}</option>
                @endforeach
            </x-ui.select>
            @endif

            <x-ui.select name="status" label="Trạng thái" containerClass="w-full sm:w-56 shrink-0">
                <option value="">Tất cả</option>
                <option value="TODO" {{ $status == 'TODO' ? 'selected' : '' }}>Chưa làm (Todo)</option>
                <option value="DOING" {{ $status == 'DOING' ? 'selected' : '' }}>Đang làm</option>
                <option value="DONE" {{ $status == 'DONE' ? 'selected' : '' }}>Hoàn thành</option>
            </x-ui.select>

            <div class="flex gap-2 pb-0.5 shrink-0">
                <x-ui.button type="submit" variant="secondary" icon="filter">
                    Lọc
                </x-ui.button>
                @if(!empty($search) || !empty($status) || !empty($centerId))
                    <x-ui.button variant="ghost" icon="x-circle" :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.tasks.index'), 'tag' => 'a'])">
                        Xoá lọc
                    </x-ui.button>
                @endif
            </div>
        </form>
    </x-ui.card>


    <!-- Task List -->
    <div class="grid grid-cols-1 gap-4">
        @if($tasks->isEmpty())
            <x-ui.empty-state 
                title="Danh sách trống"
                description="Chưa có nhiệm vụ nào được phân bổ hoặc không tìm thấy kết quả phù hợp."
                icon="clipboard-list"
            />
        @else

        @foreach($tasks as $task)
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all p-4 flex flex-col md:flex-row gap-4 items-center group">
            <div class="flex-shrink-0">
                <button @click="toggleStatus('{{ $task->id }}')" 
                        class="w-10 h-10 rounded-xl border-2 flex items-center justify-center transition-all {{ $task->status === 'DONE' ? 'bg-emerald-50 border-emerald-500 text-emerald-600' : 'border-slate-200 text-slate-300 hover:border-primary-400' }}">
                    <i data-lucide="{{ $task->status === 'DONE' ? 'check' : 'circle' }}" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="font-bold text-slate-800 truncate {{ $task->status === 'DONE' ? 'line-through opacity-50' : '' }}">{{ $task->title }}</h3>
                    @php
                        $priorityColors = ['LOW' => 'bg-slate-100 text-slate-500', 'MEDIUM' => 'bg-blue-50 text-blue-600', 'HIGH' => 'bg-orange-50 text-orange-600', 'URGENT' => 'bg-red-50 text-red-600'];
                    @endphp
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $priorityColors[$task->priority] ?? 'bg-slate-100' }}">
                        {{ $task->priority }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        {{ $task->start_date ? $task->start_date->format('d/m/Y') : '' }}
                        {!! $task->start_date && $task->due_date ? '<i data-lucide="arrow-right" class="w-3 h-3 mx-0.5 opacity-50"></i>' : '' !!}
                        {{ $task->due_date ? $task->due_date->format('d/m/Y') : ($task->start_date ? 'Hạn: Không' : 'Hạn: Không') }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                        Giao cho: {{ $task->assignedTo->name ?? 'Người khác' }}
                    </span>
                    @if($task->relation)
                    @php
                        $relType = str_contains($task->relation_type, 'Lead') ? 'Lead' : (str_contains($task->relation_type, 'Customer') ? 'Khách hàng' : class_basename($task->relation_type));
                    @endphp
                    <span class="flex items-center gap-1.5 px-2 py-0.5 bg-primary-50 text-primary-600 rounded-md font-bold">
                        <i data-lucide="link" class="w-3 h-3"></i>
                        {{ $relType }}: {{ $task->relation->name ?? '...' }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity" x-data="{ showEditModal: false, taskCenterId: '{{ $task->center_id }}' }">
                <button @click="showEditModal = true; $nextTick(() => { if (window.lucide) { lucide.createIcons(); } })" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </button>
                <button @click="deleteTask('{{ $task->id }}')" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>

                <!-- Edit Task Modal (Per Row) -->
                <template x-teleport="body">
                    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="showEditModal = false" x-transition.opacity></div>
                        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden text-left"
                             x-show="showEditModal" 
                             x-init="$watch('showEditModal', value => { if(value && window.lucide) { setTimeout(() => lucide.createIcons(), 50) } })"
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                            
                            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                                <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </div>
                                    Cập nhật nhiệm vụ
                                </h3>
                                <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                                    <i data-lucide="x" class="w-5 h-5"></i>
                                </button>
                            </div>
                            
                            <form action="{{ route('admin.tasks.update', $task->id) }}" method="POST" class="p-6">
                                @csrf
                                @method('PUT')
                                
                                <div class="space-y-4">
                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Tiêu đề nhiệm vụ <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <i data-lucide="type" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            <input type="text" name="title" value="{{ $task->title }}" required placeholder="Nhập tên nhiệm vụ..." 
                                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all text-sm font-medium text-slate-700">
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Mô tả chi tiết</label>
                                        <textarea name="description" rows="3" placeholder="Ghi chú thêm về nội dung công việc..." 
                                                  class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all text-sm font-medium text-slate-600 leading-relaxed">{{ $task->description }}</textarea>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Ngày bắt đầu</label>
                                            <div class="relative">
                                                <i data-lucide="calendar" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                <input type="date" name="start_date" value="{{ $task->start_date ? $task->start_date->format('Y-m-d') : '' }}" 
                                                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all text-sm font-medium text-slate-700">
                                            </div>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Hạn chót</label>
                                            <div class="relative">
                                                <i data-lucide="calendar-clock" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                <input type="date" name="due_date" value="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}" 
                                                       class="w-full pl-10 pr-4 py-2.5 bg-primary-50/30 border border-primary-100 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all text-sm font-semibold text-primary-700">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Ưu tiên</label>
                                            <div class="relative">
                                                <i data-lucide="flag" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                <select name="priority" class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm font-medium text-slate-700 appearance-none bg-white">
                                                    <option value="LOW" {{ $task->priority === 'LOW' ? 'selected' : '' }}>Thấp</option>
                                                    <option value="MEDIUM" {{ $task->priority === 'MEDIUM' ? 'selected' : '' }}>Trung bình</option>
                                                    <option value="HIGH" {{ $task->priority === 'HIGH' ? 'selected' : '' }}>Cao</option>
                                                    <option value="URGENT" {{ $task->priority === 'URGENT' ? 'selected' : '' }}>Khẩn cấp</option>
                                                </select>
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                            </div>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Trạng thái</label>
                                            <div class="relative">
                                                <i data-lucide="check-circle" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                <select name="status" class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm font-medium text-slate-700 appearance-none bg-white">
                                                    <option value="TODO" {{ $task->status === 'TODO' ? 'selected' : '' }}>Mới (Todo)</option>
                                                    <option value="DOING" {{ $task->status === 'DOING' ? 'selected' : '' }}>Đang làm</option>
                                                    <option value="DONE" {{ $task->status === 'DONE' ? 'selected' : '' }}>Hoàn thành</option>
                                                    <option value="CANCELLED" {{ $task->status === 'CANCELLED' ? 'selected' : '' }}>Đã huỷ</option>
                                                </select>
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Người phụ trách</label>
                                        <div class="relative">
                                            <i data-lucide="user-check" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            <select name="assigned_to" class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm font-medium text-slate-700 appearance-none bg-white">
                                                <option value="">Tự làm / Chưa giao</option>
                                                @foreach($users as $u)
                                                    @if($u->default_center_id == $task->center_id)
                                                        <option value="{{ $u->id }}" {{ $task->assigned_to == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-6 mt-6 border-t border-slate-100 flex gap-3 justify-end relative">
                                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
                                    <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
                                        <i data-lucide="check" class="w-4 h-4"></i> Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        @endforeach
        @endif
    </div>

    @if($tasks->hasPages())
    <div class="mt-4">
        {{ $tasks->appends(request()->query())->links() }}
    </div>
    @endif

    <!-- Create Modal -->
    <template x-teleport="body">
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="showCreateModal = false" x-transition.opacity></div>
                        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden text-left"
                             x-show="showCreateModal" 
                             x-init="$watch('showCreateModal', value => { if(value && window.lucide) { setTimeout(() => lucide.createIcons(), 50) } })"
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                            
                            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                                <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                    </div>
                                    Tạo nhiệm vụ mới
                                </h3>
                                <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                                    <i data-lucide="x" class="w-5 h-5"></i>
                                </button>
                            </div>
                            
                            <form action="{{ route('admin.tasks.store') }}" method="POST" class="p-6">
                    @csrf
                    
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

                                    <div class="space-y-1 relative" x-data="{ q: '', results: [], show: false }">
                                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Liên kết tới (Lead / Khách hàng)</label>
                                        <div class="relative group">
                                            <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            <input type="text" x-model="q" 
                                                   @input.debounce.300ms="searchRelation(q).then(r => { results = r; show = true })" 
                                                   placeholder="Tìm theo tên hoặc số điện thoại..." 
                                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all text-sm font-medium text-slate-700">
                                        </div>
                                <input type="hidden" name="relation_id" :value="selectedRelation?.id">
                                <input type="hidden" name="relation_type" :value="selectedRelation?.type">
                                
                                <div x-show="show && results.length > 0" @click.away="show = false" 
                                     class="absolute z-50 left-0 right-0 mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl max-h-48 overflow-y-auto p-2">
                                    <template x-for="res in results" :key="res.id + res.type">
                                        <div @click="selectedRelation = res; q = ''; show = false" 
                                             class="px-4 py-3 hover:bg-primary-50 rounded-xl cursor-pointer transition-all flex items-center justify-between group">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-white border border-slate-100 flex items-center justify-center font-black text-[10px] text-primary-500 group-hover:border-primary-200" x-text="res.type.includes('Lead') ? 'LD' : 'KH'"></div>
                                                <div class="text-sm font-bold text-slate-700" x-text="res.name"></div>
                                            </div>
                                            <i data-lucide="plus" class="w-4 h-4 text-slate-300 group-hover:text-primary-500"></i>
                                        </div>
                                    </template>
                                </div>

                                        <div x-show="selectedRelation" class="mt-2" x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 translate-y-2">
                                             <div class="px-4 py-2 bg-primary-50/50 border border-primary-100 rounded-xl flex items-center justify-between group">
                                                <div class="flex items-center gap-2 text-sm font-semibold text-primary-700">
                                                    <i data-lucide="link" class="w-4 h-4"></i>
                                                    <span x-text="selectedRelation?.name"></span>
                                                </div>
                                                <button type="button" @click="selectedRelation = null; q = ''" class="text-primary-400 hover:text-red-500 transition-colors">
                                                    <i data-lucide="x" class="w-4 h-4"></i>
                                                </button>
                                             </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
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

                                        @if($isGlobalScope)
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Cơ sở <span class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="building-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                <select name="center_id" id="task_center_id" required @change="loadStaff($event.target.value)" 
                                                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm font-medium text-slate-700 appearance-none bg-white">
                                                    <option value="">-- Chọn cơ sở --</option>
                                                    @foreach($centers as $c)
                                                        <option value="{{ $c->id }}" {{ $centerId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Giao cho nhân sự</label>
                                        <div class="relative">
                                            <i data-lucide="user-check" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            <select name="assigned_to" class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm font-medium text-slate-700 appearance-none bg-white">
                                                <option value="">Tự làm / Chưa giao</option>
                                                <template x-for="user in staffList" :key="user.id">
                                                    <option :value="user.id" x-text="user.name"></option>
                                                </template>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-6 mt-6 border-t border-slate-100 flex gap-3 justify-end relative">
                                    <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
                                    <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 flex items-center gap-2 font-medium">
                                        <i data-lucide="plus" class="w-4 h-4"></i> Tạo nhiệm vụ
                                    </button>
                                </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
    function taskManagement() {
        return {
            showCreateModal: false,
            staffList: [],
            allUsers: @json($users),
            selectedRelation: null,
            init() {
                // Initialize icons
                this.$nextTick(() => {
                    if (window.lucide) { lucide.createIcons(); }
                });
                
                // Load initial staff list for Create Modal
                @if(!$isGlobalScope)
                    this.loadStaff('{{ session('current_center_id') ?? app('center_id') }}');
                @elseif($centerId)
                    this.loadStaff('{{ $centerId }}');
                @endif
            },
            loadStaff(centerId) {
                if (!centerId) {
                    this.staffList = [];
                    return;
                }
                this.staffList = this.allUsers.filter(u => u.default_center_id == centerId);
            },
            async searchRelation(q) {
                if (q.length < 2) return [];
                const res = await fetch(`{{ route('admin.tasks.search-relations') }}?q=${q}`);
                return await res.json();
            },
            async toggleStatus(id) {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.content 
                                || document.querySelector('input[name="_token"]')?.value;

                    const res = await fetch(`/admin/tasks/${id}/toggle-status`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => window.location.reload(), 500);
                    } else {
                        showToast(data.message || 'Cập nhật thất bại', 'error');
                    }
                } catch (err) {
                    console.error('Toggle status error:', err);
                    showToast('Lỗi khi cập nhật trạng thái.', 'error');
                }
            },
            async deleteTask(id) {
                const ok = await showConfirm({
                    title: 'Xoá nhiệm vụ?',
                    message: 'Bạn có chắc chắn muốn xoá nhiệm vụ này? Hành động này không thể hoàn tác.',
                    confirmText: 'Xoá ngay',
                    type: 'danger'
                });

                if (!ok) return;

                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.content 
                                || document.querySelector('input[name="_token"]')?.value;

                    const res = await fetch(`/admin/tasks/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (res.status === 419) {
                        showToast('Phiên làm việc hết hạn, vui lòng reload trang.', 'error');
                        return;
                    }

                    const data = await res.json();
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => window.location.reload(), 500);
                    } else {
                        showToast(data.message || 'Xoá thất bại', 'error');
                    }
                } catch (err) {
                    console.error('Delete error:', err);
                    showToast('Lỗi kết nối hoặc hệ thống khi xoá: ' + err.message, 'error');
                }
            }
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) { lucide.createIcons(); }
    });

    window.addEventListener('refresh-icons', () => {
        if (window.lucide) {
            setTimeout(() => { lucide.createIcons(); }, 50);
        }
    });
</script>
@endpush
