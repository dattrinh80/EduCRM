@extends('layouts.app')

@section('title', 'Quản lý Học viên')

@section('breadcrumb_items')
    <a href="{{ route('admin.students.index') }}" class="text-slate-400 hover:text-primary-500 transition-colors">Học vụ</a>
    <i data-lucide="chevron-right" class="w-2.5 h-2.5 opacity-50"></i>
    <span class="text-primary-500">Student Profiles</span>
@endsection

@section('content')
<div class="space-y-6" x-data="studentManagement()">

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
            <x-ui.button variant="primary" icon="plus-circle" @click="loadModal('{{ route('admin.students.create') }}')">
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
                            @php
                                $statusMap = [
                                    'NEW' => ['label' => 'Mới', 'variant' => 'primary'],
                                    'ACTIVE' => ['label' => 'Đang học', 'variant' => 'success'],
                                    'DROPPED' => ['label' => 'Thôi học', 'variant' => 'danger'],
                                    'GRADUATED' => ['label' => 'Tốt nghiệp', 'variant' => 'info'],
                                ];
                                $statusInfo = $statusMap[$student->status] ?? ['label' => $student->status, 'variant' => 'success'];
                            @endphp
                            <td class="px-6 py-4 text-center">
                                <x-ui.badge :variant="$statusInfo['variant']" :dot="true">
                                    {{ $statusInfo['label'] }}
                                </x-ui.badge>
                            </td>
                            <td class="px-6 py-4 text-slate-400 text-[11px] font-bold flex flex-col uppercase tracking-wider tabular-nums">
                                 {{ \Carbon\Carbon::parse($student->created_at)->translatedFormat('d/m/Y') }}
                                 <span class="text-[9px] opacity-60 font-medium normal-case tracking-normal">{{ \Carbon\Carbon::parse($student->created_at)->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1 opacity-10 sm:opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:translate-x-0 translate-x-4">
                                    <x-ui.button variant="ghost" size="xs" icon="eye" class="text-slate-400" title="Chi tiết" :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.students.show', $student->id), 'tag' => 'a'])" />
                                    <x-ui.button variant="ghost" size="xs" icon="edit" class="text-slate-400" title="Chỉnh sửa" @click="loadModal('{{ route('admin.students.edit', $student->id) }}')" />
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
            actionClick="loadModal('{{ route('admin.students.create') }}')"
        />
    @endif
    <!-- Import Modal -->
    <template x-teleport="body">
        <div x-show="showImportModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="!importing && (showImportModal = false)" x-transition.opacity></div>
            
            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg mx-auto max-h-[90vh] overflow-hidden flex flex-col text-left border border-slate-100"
                 x-show="showImportModal"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                 
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <div class="p-1.5 bg-primary-100 text-primary-600 rounded-lg">
                            <i data-lucide="file-down" class="w-5 h-5"></i>
                        </div>
                        Nhập dữ liệu từ Excel
                    </h3>
                    <button type="button" @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition" x-show="!importing">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="p-6 flex-1 overflow-y-auto">
                    <div x-show="!importing">
                        <div class="space-y-6">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Chọn cơ sở nhận học viên <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i data-lucide="building-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <select x-model="importCenterId" class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm font-medium text-slate-700 appearance-none bg-white">
                                        <option value="">-- Chọn cơ sở --</option>
                                        @foreach($centers as $c)
                                            <option value="{{ $c->id }}">[{{ $c->code }}] {{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                </div>
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">File Excel <span class="text-red-500">*</span></label>
                                <div class="relative border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center hover:border-primary-400 transition-colors bg-slate-50/50 group">
                                    <input type="file" x-ref="fileInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="importFile = $refs.fileInput.files[0]">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-slate-100 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                            <i data-lucide="upload-cloud" class="w-6 h-6 text-primary-500"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-700" x-text="importFile ? importFile.name : 'Nhấn để chọn hoặc kéo thả file vào đây'"></p>
                                        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-wider">Hỗ trợ .xlsx, .xls, .csv (Tối đa 10MB)</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100 flex gap-3">
                                <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-blue-500 shrink-0 border border-blue-100 shadow-sm">
                                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                                </div>
                                <div class="space-y-2">
                                    <h4 class="text-xs font-bold text-blue-700 uppercase tracking-wider">Lưu ý cấu trúc file:</h4>
                                    <ul class="text-[11px] text-blue-600/80 space-y-1 list-disc pl-4 leading-relaxed">
                                        <li>Cột bắt buộc: <span class="font-bold text-blue-700">ho_va_ten</span> (hoặc <span class="font-bold text-blue-700">name</span>)</li>
                                        <li>Cột tùy chọn: <span class="font-bold text-blue-700">email, so_dien_thoai, ngay_sinh, gioi_tinh</span></li>
                                        <li>Cột người giám hộ: <span class="font-bold text-blue-700">ho_ten_nguoi_giam_ho, sdt_nguoi_giam_ho</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="importing" class="py-12">
                        <div class="flex flex-col items-center">
                            <div class="relative w-24 h-24 mb-6">
                                <svg class="w-full h-full transform -rotate-90">
                                    <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent" class="text-slate-100"/>
                                    <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent" class="text-primary-500 transition-all duration-300" :style="{ strokeDasharray: '251.2', strokeDashoffset: 251.2 * (1 - importProgress / 100) }"/>
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl font-display font-bold text-slate-800" x-text="importProgress + '%'"></span>
                                </div>
                            </div>
                            <p class="text-sm font-bold text-slate-700 mb-1">Đang xử lý dữ liệu học viên...</p>
                            <p class="text-[11px] text-slate-400 font-medium uppercase tracking-widest" x-text="'Thành công: ' + importSuccess + ' | Thất bại: ' + importFailed"></p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-3 shrink-0 bg-slate-50/50" x-show="!importing">
                    <button type="button" @click="showImportModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy bỏ</button>
                    <button type="button" @click="handleImport()" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition shadow-lg shadow-primary-500/30 font-bold flex items-center gap-2">
                         <i data-lucide="play" class="w-4 h-4"></i> Nhập dữ liệu
                    </button>
                </div>
            </div>
        </div>
    </template>
    <!-- Dynamic Modal -->
    <template x-teleport="body">
        <div x-show="showDynamicModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="!isLoadingModal && (showDynamicModal = false)" x-transition.opacity></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-auto max-h-[90vh] overflow-hidden flex flex-col text-left"
                 x-show="showDynamicModal"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                 
                <div x-show="isLoadingModal" class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-10 flex items-center justify-center">
                    <div class="w-10 h-10 border-4 border-primary-100 border-t-primary-500 rounded-full animate-spin"></div>
                </div>

                <div x-html="modalContent" class="flex-1 overflow-hidden flex flex-col"></div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
    function studentManagement() {
        return {
            showImportModal: false, 
            importFile: null, 
            importing: false, 
            importProgress: 0, 
            importTotal: 0, 
            importSuccess: 0, 
            importFailed: 0, 
            importErrors: [],
            importCenterId: '',
            
            showDynamicModal: false,
            modalContent: '',
            isLoadingModal: false,

            async loadModal(url) {
                this.isLoadingModal = true;
                this.showDynamicModal = true;
                this.modalContent = '';
                
                try {
                    const response = await fetch(url + (url.includes('?') ? '&' : '?') + '_ajax=1', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    this.modalContent = await response.text();
                    
                    this.$nextTick(() => {
                        if (window.lucide) {
                            lucide.createIcons();
                        }
                    });
                } catch (error) {
                    console.error('Error loading modal:', error);
                    this.modalContent = '<div class="p-8 text-center text-red-500">Có lỗi xảy ra khi tải nội dung.</div>';
                } finally {
                    this.isLoadingModal = false;
                }
            },
            
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
        }
    }
</script>
@endpush
@endsection
