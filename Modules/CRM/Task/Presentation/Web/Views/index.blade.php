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
            <x-ui.button variant="primary" icon="plus-circle" @click="showCreateModal = true; $dispatch('refresh-icons')">
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
                        Hạn: {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') : 'Không' }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                        Giao cho: {{ $task->assignedTo->name ?? 'Người khác' }}
                    </span>
                    @if($task->relation)
                    <span class="flex items-center gap-1.5 px-2 py-0.5 bg-primary-50 text-primary-600 rounded-md font-bold">
                        <i data-lucide="link" class="w-3 h-3"></i>
                        {{ $task->relation_type }}: {{ $task->relation->name ?? '...' }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity" x-data="{ showEditModal: false, taskCenterId: '{{ $task->center_id }}' }">
                <button @click="showEditModal = true; $dispatch('refresh-icons')" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </button>
                <button @click="deleteTask('{{ $task->id }}')" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>

                <!-- Edit Task Modal (Per Row) -->
                <template x-teleport="body">
                    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showEditModal = false" x-transition.opacity></div>
                        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl mx-auto overflow-hidden text-left"
                             x-show="showEditModal" 
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                            
                            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                                <h3 class="text-lg font-bold text-slate-800">Cập nhật nhiệm vụ</h3>
                                <button @click="showEditModal = false" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl transition-colors"><i data-lucide="x" class="w-5 h-5"></i></button>
                            </div>
                            
                            <form action="{{ route('admin.tasks.update', $task->id) }}" method="POST" class="p-6 space-y-4">
                                @csrf
                                @method('PUT')
                                <div class="space-y-1">
                                    <label class="text-sm font-bold text-slate-700">Tiêu đề nhiệm vụ *</label>
                                    <input type="text" name="title" value="{{ $task->title }}" required placeholder="Nhập tên nhiệm vụ..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                                </div>
                                
                                <div class="space-y-1">
                                    <label class="text-sm font-bold text-slate-700">Mô tả chi tiết</label>
                                    <textarea name="description" rows="3" placeholder="Ghi chú thêm về công việc..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">{{ $task->description }}</textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-sm font-bold text-slate-700">Ngày hết hạn</label>
                                        <input type="date" name="due_date" value="{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '' }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-sm font-bold text-slate-700">Độ ưu tiên</label>
                                        <select name="priority" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                                            <option value="LOW" {{ $task->priority === 'LOW' ? 'selected' : '' }}>Thấp</option>
                                            <option value="MEDIUM" {{ $task->priority === 'MEDIUM' ? 'selected' : '' }}>Trung bình</option>
                                            <option value="HIGH" {{ $task->priority === 'HIGH' ? 'selected' : '' }}>Cao</option>
                                            <option value="URGENT" {{ $task->priority === 'URGENT' ? 'selected' : '' }}>Khẩn cấp</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-sm font-bold text-slate-700">Trạng thái</label>
                                        <select name="status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                                            <option value="TODO" {{ $task->status === 'TODO' ? 'selected' : '' }}>Mới (Todo)</option>
                                            <option value="DOING" {{ $task->status === 'DOING' ? 'selected' : '' }}>Đang làm</option>
                                            <option value="DONE" {{ $task->status === 'DONE' ? 'selected' : '' }}>Hoàn thành</option>
                                            <option value="CANCELLED" {{ $task->status === 'CANCELLED' ? 'selected' : '' }}>Đã huỷ</option>
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-sm font-bold text-slate-700">Giao cho nhân sự</label>
                                        <select name="assigned_to" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                                            <option value="">Tự làm / Chưa giao</option>
                                            @foreach($users as $u)
                                                @if($u->default_center_id == $task->center_id)
                                                    <option value="{{ $u->id }}" {{ $task->assigned_to == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                @if($isGlobalScope)
                                <div class="space-y-1">
                                    <label class="text-sm font-bold text-slate-700">Cơ sở (Chỉ đọc)</label>
                                    <div class="px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-slate-500 text-sm font-medium">
                                        {{ $task->center->name ?? 'N/A' }}
                                    </div>
                                </div>
                                @endif

                                <div class="pt-4 flex gap-3">
                                    <button type="button" @click="showEditModal = false" class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 transition">Huỷ</button>
                                    <button type="submit" class="flex-1 px-4 py-2.5 bg-primary-600 text-white rounded-xl font-bold shadow-lg shadow-primary-500/25 hover:bg-primary-700 transition">
                                        Cập nhật
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
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showCreateModal = false" x-transition.opacity></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl mx-auto overflow-hidden text-left"
                 x-show="showCreateModal" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800">Tạo nhiệm vụ mới</h3>
                    <button @click="showCreateModal = false" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl transition-colors"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                
                <form action="{{ route('admin.tasks.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div class="space-y-1">
                        <label class="text-sm font-bold text-slate-700">Tiêu đề nhiệm vụ *</label>
                        <input type="text" name="title" required placeholder="Nhập tên nhiệm vụ..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                    </div>
                    
                    <div class="space-y-1">
                        <label class="text-sm font-bold text-slate-700">Mô tả chi tiết</label>
                        <textarea name="description" rows="3" placeholder="Ghi chú thêm về công việc..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-bold text-slate-700">Ngày hết hạn</label>
                            <input type="date" name="due_date" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-bold text-slate-700">Độ ưu tiên</label>
                            <select name="priority" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                                <option value="LOW">Thấp</option>
                                <option value="MEDIUM" selected>Trung bình</option>
                                <option value="HIGH">Cao</option>
                                <option value="URGENT">Khẩn cấp</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        @if($isGlobalScope)
                        <div class="space-y-1">
                            <label class="text-sm font-bold text-slate-700">Cơ sở</label>
                            <select name="center_id" id="task_center_id" required @change="loadStaff($event.target.value)" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                                <option value="">-- Chọn cơ sở --</option>
                                @foreach($centers as $c)
                                    <option value="{{ $c->id }}" {{ $centerId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="space-y-1 {{ !$isGlobalScope ? 'col-span-2' : '' }}">
                            <label class="text-sm font-bold text-slate-700">Giao cho nhân sự</label>
                            <select name="assigned_to" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                                <option value="">Tự làm / Chưa giao</option>
                                <template x-for="user in staffList" :key="user.id">
                                    <option :value="user.id" x-text="user.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" @click="showCreateModal = false" class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 transition">Huỷ</button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-primary-600 text-white rounded-xl font-bold shadow-lg shadow-primary-500/25 hover:bg-primary-700 transition">Tạo nhiệm vụ</button>
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
            init() {
                // Initialize icons
                $dispatch('refresh-icons');
                
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
</script>
@endpush
