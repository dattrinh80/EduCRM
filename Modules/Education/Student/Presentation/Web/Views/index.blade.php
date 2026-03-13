@extends('layouts.app')

@section('title', 'Quản lý Học viên')

@section('breadcrumb_items')
    <a href="{{ route('admin.students.index') }}" class="text-slate-400 hover:text-primary-500 transition-colors">Học vụ</a>
    <i data-lucide="chevron-right" class="w-2.5 h-2.5 opacity-50"></i>
    <span class="text-primary-500">Student Profiles</span>
@endsection

@section('content')
<div class="space-y-6" x-data="{ 
    showImportModal: false, 
    importFile: null, 
    importing: false, 
    importProgress: 0, 
    importTotal: 0, 
    importSuccess: 0, 
    importFailed: 0, 
    importErrors: [],
    importCenterId: '',
    
    async handleImport() {
        if (!this.$refs.fileInput.files[0] || !this.importCenterId) {
            alert('Vui lòng chọn file và cơ sở');
            return;
        }
        
        this.importing = true;
        this.importProgress = 0;
        this.importErrors = [];
        this.importSuccess = 0;
        this.importFailed = 0;
        
        let formData = new FormData();
        formData.append('file', this.$refs.fileInput.files[0]);
        formData.append('_token', '{{ csrf_token() }}');
        
        try {
            let res = await fetch('{{ route('admin.students.import') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            let data = await res.json();
            
            if (res.status === 200) {
                this.importTotal = data.total;
                await this.processImport(data.import_id, 0);
            } else {
                alert(data.message || 'Lỗi khởi tạo import');
                this.importing = false;
            }
        } catch (e) {
            console.error(e);
            alert('Lỗi kết nối');
            this.importing = false;
        }
    },
    
    async processImport(importId, offset) {
        try {
            let res = await fetch('{{ route('admin.students.import.process') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    import_id: importId,
                    offset: offset,
                    center_id: this.importCenterId
                })
            });
            let data = await res.json();
            
            if (res.status === 200) {
                this.importSuccess += data.success;
                this.importFailed += data.failed;
                this.importErrors = [...this.importErrors, ...data.errors];
                this.importProgress = Math.min(100, Math.round(((offset + 50) / this.importTotal) * 100));
                
                if (!data.is_finished) {
                    await this.processImport(importId, offset + 50);
                } else {
                    this.importing = false;
                    alert('Hoàn thành: ' + this.importSuccess + ' thành công, ' + this.importFailed + ' thất bại.');
                    if (this.importSuccess > 0) {
                        location.reload();
                    }
                }
            } else {
                alert(data.message || 'Lỗi xử lý file');
                this.importing = false;
            }
        } catch (e) {
            console.error(e);
            this.importing = false;
        }
    }
}">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight">Quản lý Học viên</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-1.5 text-sm">
                <i data-lucide="graduation-cap" class="w-3.5 h-3.5 opacity-60"></i>
                Quản lý hồ sơ học tập và thông tin cá nhân của học viên
            </p>
        </div>
        <div class="flex items-center gap-2">
            <x-ui.button variant="secondary" icon="file-down" @click="showImportModal = true">
                Nhập Excel
            </x-ui.button>
            <x-ui.button variant="primary" icon="plus-circle" :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.students.create'), 'tag' => 'a'])">
                Thêm Học viên
            </x-ui.button>
        </div>
    </div>

    <!-- Filter Bar -->
    <x-ui.card bodyClass="p-4">
        <form action="{{ route('admin.students.index') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4 items-end">
            <div class="flex flex-wrap items-end gap-4 w-full">
                <x-ui.input 
                    name="search" 
                    label="Tìm kiếm" 
                    placeholder="Họ tên, mã học viên…" 
                    value="{{ request('search') }}"
                    icon="search"
                    containerClass="w-full sm:w-80 shrink-0"
                />
                
                <div class="flex gap-2 shrink-0">
                    <x-ui.button type="submit" variant="secondary" icon="filter">
                        Lọc
                    </x-ui.button>
                    @if(request()->has('search'))
                        <x-ui.button variant="ghost" icon="x-circle" :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.students.index'), 'tag' => 'a'])">
                            Xoá lọc
                        </x-ui.button>
                    @endif

                    <div class="relative inline-block text-left" x-data="{ open: false }">
                        <x-ui.button type="button" variant="ghost" icon="download" @click="open = !open">
                            Xuất file
                        </x-ui.button>
                        <div x-show="open" @click.away="open = false" 
                            class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100">
                            <div class="py-1">
                                <a href="{{ route('admin.students.export', array_merge(request()->all(), ['format' => 'xlsx'])) }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 flex items-center gap-2">
                                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-green-600"></i> Excel (.xlsx)
                                </a>
                                <a href="{{ route('admin.students.export', array_merge(request()->all(), ['format' => 'pdf'])) }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 flex items-center gap-2">
                                    <i data-lucide="file-text" class="w-4 h-4 text-red-600"></i> PDF (.pdf)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </x-ui.card>

    @if(isset($students) && count($students) > 0)
        <!-- Data Table -->
        <x-ui.card bodyClass="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase text-slate-500 font-bold tracking-widest whitespace-nowrap">
                            <th class="px-6 py-4">Học viên</th>
                            <th class="px-6 py-4 text-center">Mã học viên</th>
                            <th class="px-6 py-4 text-center">Trung tâm</th>
                            <th class="px-6 py-4 text-center">Trạng thái</th>
                            <th class="px-6 py-4">Ngày tạo</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($students as $student)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center text-primary-600 font-bold text-sm border border-primary-200 shadow-sm">
                                        {{ strtoupper(substr($student->customer?->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 tracking-tight">{{ $student->customer?->name ?? 'N/A' }}</div>
                                        <div class="text-[10px] text-slate-400 font-medium">{{ $student->customer?->phone ?? 'No phone' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center tabular-nums">
                                <span class="px-2.5 py-1 bg-slate-50 text-slate-600 rounded-lg font-mono text-xs font-bold border border-slate-100 tracking-wider">{{ $student->student_code }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded uppercase tracking-wider">
                                    {{ $student->customer?->center?->code ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <x-ui.badge variant="success" :dot="true">
                                    {{ $student->status }}
                                </x-ui.badge>
                            </td>
                            <td class="px-6 py-4 text-slate-400 text-[11px] font-bold flex flex-col uppercase tracking-wider tabular-nums">
                                 {{ \Carbon\Carbon::parse($student->created_at)->translatedFormat('d/m/Y') }}
                                 <span class="text-[9px] opacity-60 font-medium normal-case tracking-normal">{{ \Carbon\Carbon::parse($student->created_at)->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1 opacity-10 sm:opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:translate-x-0 translate-x-4">
                                    <x-ui.button variant="ghost" size="xs" icon="eye" class="text-slate-400" title="Chi tiết" :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.students.show', $student->id), 'tag' => 'a'])" />
                                    <x-ui.button variant="ghost" size="xs" icon="edit" class="text-slate-400" title="Chỉnh sửa" :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.students.edit', $student->id), 'tag' => 'a'])" />
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($students instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $students->links() }}
                </div>
            @endif
        </x-ui.card>
    @else
        <x-ui.empty-state 
            title="Chưa có học viên nào"
            description="Hệ thống chưa ghi nhận thông tin học viên nào. Hãy thử chuyển đổi từ Lead hoặc nhập liệu mới."
            icon="graduation-cap"
            actionText="Thêm học viên đầu tiên"
            :actionUrl="route('admin.students.create')"
        />
    @endif
    <!-- Import Modal -->
    <template x-teleport="body">
        <div x-show="showImportModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="!importing && (showImportModal = false)" x-transition.opacity></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto overflow-hidden text-left"
                 x-show="showImportModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                 
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800">Nhập dữ liệu từ Excel</h3>
                    <button type="button" @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 p-2" x-show="!importing">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="p-6">
                    <div x-show="!importing">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Chọn cơ sở nhận học viên <span class="text-red-500">*</span></label>
                                <x-ui.select name="import_center_id" x-model="importCenterId" icon="building-2">
                                    <option value="">-- Chọn cơ sở --</option>
                                    @foreach($centers as $c)
                                        <option value="{{ $c->id }}">[{{ $c->code }}] {{ $c->name }}</option>
                                    @endforeach
                                </x-ui.select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">File Excel <span class="text-red-500">*</span></label>
                                <div class="relative border-2 border-dashed border-slate-200 rounded-xl p-8 text-center hover:border-primary-400 transition-colors bg-slate-50">
                                    <input type="file" x-ref="fileInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="importFile = $refs.fileInput.files[0]">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="upload-cloud" class="w-10 h-10 text-slate-400 mb-2"></i>
                                        <p class="text-sm text-slate-600" x-text="importFile ? importFile.name : 'Nhấn để chọn hoặc kéo thả file vào đây'"></p>
                                        <p class="text-[10px] text-slate-400 mt-1">Hỗ trợ .xlsx, .xls, .csv (Tối đa 10MB)</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                                <h4 class="text-xs font-bold text-blue-700 uppercase mb-2 flex items-center gap-1.5">
                                    <i data-lucide="help-circle" class="w-3.5 h-3.5"></i> Lưu ý cấu trúc file:
                                </h4>
                                <ul class="text-[11px] text-blue-600 space-y-1 list-disc pl-4">
                                    <li>Cột bắt buộc: <span class="font-bold">ho_va_ten</span> (hoặc <span class="font-bold">name</span>)</li>
                                    <li>Cột tùy chọn: <span class="font-bold">email, so_dien_thoai, ngay_sinh, gioi_tinh</span></li>
                                    <li>Cột người giám hộ: <span class="font-bold">ho_ten_nguoi_giam_ho, sdt_nguoi_giam_ho</span></li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3">
                            <x-ui.button variant="ghost" @click="showImportModal = false">Đóng</x-ui.button>
                            <x-ui.button variant="primary" @click="handleImport()">Bắt đầu xử lý</x-ui.button>
                        </div>
                    </div>

                    <div x-show="importing">
                        <div class="flex flex-col items-center py-8">
                            <div class="w-full bg-slate-100 rounded-full h-2.5 mb-4 shadow-inner">
                                <div class="bg-primary-500 h-2.5 rounded-full transition-all duration-300 shadow-[0_0_10px_rgba(20,184,166,0.3)]" :style="'width: ' + importProgress + '%'"></div>
                            </div>
                            <p class="text-sm font-bold text-slate-700 mb-1" x-text="'Đang xử lý: ' + importProgress + '%'"></p>
                            <p class="text-xs text-slate-500" x-text="'Thành công: ' + importSuccess + ' | Thất bại: ' + importFailed"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
