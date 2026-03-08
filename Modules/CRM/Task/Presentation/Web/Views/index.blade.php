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
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Trung tâm Nhiệm vụ</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-1.5 text-sm">
                <i data-lucide="info" class="w-3.5 h-3.5 opacity-60"></i>
                Theo dõi và xử lý các đầu việc cần thực hiện
            </p>
        </div>
        <div class="flex gap-2">
            <button type="button" @click="showCreateModal = true" class="px-6 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-300 flex items-center gap-2 shadow-lg shadow-primary-500/25 whitespace-nowrap font-bold active:scale-95 group">
                <i data-lucide="plus-circle" class="w-5 h-5 group-hover:rotate-90 transition-transform"></i>
                <span>Giao việc mới</span>
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row gap-4 items-end">
        <form action="{{ route('admin.tasks.index') }}" method="GET" class="w-full grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="md:col-span-2">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Tiêu đề</label>
                <div class="relative group">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                    <input type="text" name="search" placeholder="Tìm kiếm nhiệm vụ…" value="{{ $search }}"
                           class="w-full pl-10 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none">
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Cơ sở</label>
                <select name="center_id" class="w-full px-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none appearance-none">
                    <option value="">Tất cả cơ sở</option>
                    @foreach($centers as $c)
                        <option value="{{ $c->id }}" {{ $centerId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Trạng thái</label>
                <select name="status" class="w-full px-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none">
                    <option value="">Tất cả</option>
                    <option value="TODO" {{ $status == 'TODO' ? 'selected' : '' }}>Chưa làm (Todo)</option>
                    <option value="DOING" {{ $status == 'DOING' ? 'selected' : '' }}>Đang làm</option>
                    <option value="DONE" {{ $status == 'DONE' ? 'selected' : '' }}>Hoàn thành</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2.5 bg-primary-50 text-primary-600 hover:bg-primary-100 rounded-xl transition font-bold text-sm flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i> Lọc dữ liệu
                </button>
            </div>
        </form>
    </div>

    <!-- Task List -->
    <div class="grid grid-cols-1 gap-4">
        @if($tasks->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-100 shadow-sm">
            <div class="w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 shadow-inner">
                <i data-lucide="clipboard-list" class="w-10 h-10 text-slate-300"></i>
            </div>
            <h3 class="font-bold text-slate-800 text-lg">Danh sách trống</h3>
            <p class="text-slate-500 mt-1 max-w-xs mx-auto">Chưa có nhiệm vụ nào được phân bổ hoặc không tìm thấy kết quả phù hợp.</p>
        </div>
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
            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click="editTask('{{ $task->id }}')" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </button>
                <button @click="deleteTask('{{ $task->id }}')" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
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
                        <div class="space-y-1">
                            <label class="text-sm font-bold text-slate-700">Giao cho nhân sự</label>
                            <select name="assigned_to" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                                <option value="">Tự làm / Chưa giao</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-bold text-slate-700">Cơ sở</label>
                            <select name="center_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                                @foreach($centers as $c)
                                    <option value="{{ $c->id }}" {{ $centerId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
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
            init() {
                // Initialize icons
                $dispatch('refresh-icons');
            },
            toggleStatus(id) {
                fetch(`/admin/tasks/${id}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => window.location.reload(), 500);
                    } else {
                        showToast(data.message, 'error');
                    }
                });
            },
            editTask(id) {
                showToast('Tính năng chỉnh sửa đang phát triển...', 'info');
            },
            deleteTask(id) {
                showConfirm({
                    title: 'Xoá nhiệm vụ',
                    message: 'Bạn có chắc chắn muốn xoá nhiệm vụ này?',
                    confirmText: 'Xoá ngay',
                    type: 'danger'
                }).then(ok => {
                    if (ok) showToast('Nhiệm vụ đã được xoá.', 'success');
                });
            }
        }
    }
</script>
@endpush
