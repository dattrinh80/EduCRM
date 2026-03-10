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
    showEditModal: {{ $errors->any() && old('_method') == 'PUT' ? 'true' : 'false' }},
    currentCustomer: {
        id: '',
        name: '',
        phone: '',
        email: '',
        dob: '',
        gender: '',
        address: '',
        center_id: ''
    },
    editCustomer(customer) {
        this.currentCustomer = customer;
        this.showEditModal = true;
        this.$dispatch('refresh-icons');
    }
}">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight">Khách hàng (Phụ huynh)</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-1.5 text-sm">
                <i data-lucide="info" class="w-3.5 h-3.5 opacity-60"></i>
                Quản lý thông tin phụ huynh và người giám hộ học viên
            </p>
        </div>
        <div>
            <x-ui.button variant="primary" icon="plus-circle" @click="showCreateModal = true; $dispatch('refresh-icons')">
                Thêm Khách hàng
            </x-ui.button>
        </div>
    </div>

    <!-- Filter/Search Bar -->
    <x-ui.card bodyClass="p-4">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4 items-end">
            <div class="flex flex-wrap items-end gap-4 w-full">
                <x-ui.input 
                    name="search" 
                    label="Tìm kiếm" 
                    placeholder="Họ tên, số điện thoại, email…" 
                    value="{{ request('search') }}"
                    icon="search"
                    containerClass="w-full sm:w-80 shrink-0"
                />
                
                <div class="flex gap-2 shrink-0">
                    <x-ui.button type="submit" variant="secondary" icon="filter">
                        Lọc
                    </x-ui.button>
                    @if(request()->has('search'))
                        <x-ui.button variant="ghost" icon="x-circle" :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.customers.index'), 'tag' => 'a'])">
                            Xoá lọc
                        </x-ui.button>
                    @endif
                </div>
            </div>

        </form>
    </x-ui.card>

    @if(count($customers) > 0)
        <!-- Data Table -->
        <x-ui.card bodyClass="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase text-slate-500 font-bold tracking-widest whitespace-nowrap">
                            <th class="px-6 py-4">Khách hàng</th>
                            <th class="px-6 py-4">Liên hệ</th>
                            <th class="px-6 py-4">Ngày sinh</th>
                            <th class="px-6 py-4">Địa chỉ / Ghi chú</th>
                            <th class="px-6 py-4">Thời gian tạo</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($customers as $customer)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm border border-indigo-200 shadow-sm">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 leading-tight">{{ $customer->name }}</div>
                                        @if($customer->gender)
                                        <x-ui.badge variant="{{ $customer->gender === 'MALE' ? 'info' : ($customer->gender === 'FEMALE' ? 'danger' : 'secondary') }}" class="mt-0.5 text-[10px] px-1.5 py-0">
                                            {{ $customer->gender === 'MALE' ? 'Nam' : ($customer->gender === 'FEMALE' ? 'Nữ' : 'Khác') }}
                                        </x-ui.badge>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 tabular-nums whitespace-nowrap">
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($customer->dob)
                                <div class="flex items-center gap-2 text-sm text-slate-600 font-medium">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                                    {{ \Carbon\Carbon::parse($customer->dob)->format('d/m/Y') }}
                                </div>
                                @else
                                <span class="text-slate-300 italic text-xs">Chưa cập nhật</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 truncate max-w-[250px] text-sm font-medium">
                                <div class="flex items-start gap-2">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-300 mt-0.5"></i>
                                    <span class="truncate">{{ $customer->address ?: 'Chưa cập nhật' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-400 text-[11px] font-bold whitespace-nowrap flex flex-col uppercase tracking-wider tabular-nums">
                                 {{ \Carbon\Carbon::parse($customer->createdAt)->translatedFormat('d/m/Y') }}
                                 <span class="text-[9px] opacity-60 font-medium normal-case tracking-normal">{{ \Carbon\Carbon::parse($customer->createdAt)->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:translate-x-0 translate-x-4">
                                    <x-ui.button variant="ghost" size="xs" icon="eye" class="text-slate-400" />
                                    <x-ui.button variant="ghost" size="xs" icon="edit-3" class="text-slate-400" @click="editCustomer({
                                        id: '{{ $customer->id }}',
                                        name: '{{ addslashes($customer->name) }}',
                                        phone: '{{ $customer->phone }}',
                                        email: '{{ $customer->email }}',
                                        dob: '{{ $customer->dob }}',
                                        gender: '{{ $customer->gender }}',
                                        address: '{{ addslashes($customer->address) }}',
                                        center_id: '{{ $customer->centerId }}'
                                    })" />
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    @else
        <!-- Empty State -->
        <x-ui.empty-state 
            title="Chưa có khách hàng"
            description="Hệ thống chưa ghi nhận thông tin phụ huynh / giám hộ nào. Chuyển đổi Lead hoặc thêm thủ công ngay."
            icon="users-2"
            actionText="Thêm khách hàng đầu tiên"
            actionClick="showCreateModal = true; $dispatch('refresh-icons')"
        />
    @endif

    <!-- Create Modal -->
    <x-ui.modal x-show="showCreateModal" title="Thêm Khách hàng mới" @close.window="showCreateModal = false">
        <form action="{{ route('admin.customers.store') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input name="name" label="Họ và tên" placeholder="Nhập họ tên phụ huynh/giám hộ" required value="{{ old('name') }}" />
                <x-ui.input name="phone" label="Số điện thoại" placeholder="Nhập số điện thoại" required value="{{ old('phone') }}" />
                <x-ui.input name="email" label="Email" type="email" placeholder="Nhập địa chỉ email" value="{{ old('email') }}" />
                <x-ui.input name="dob" label="Ngày sinh" type="date" value="{{ old('dob') }}" />
                
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 block">Giới tính</label>
                    <select name="gender" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                        <option value="">Chọn giới tính</option>
                        <option value="MALE" {{ old('gender') === 'MALE' ? 'selected' : '' }}>Nam</option>
                        <option value="FEMALE" {{ old('gender') === 'FEMALE' ? 'selected' : '' }}>Nữ</option>
                        <option value="OTHER" {{ old('gender') === 'OTHER' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 block">Cơ sở (Trung tâm)</label>
                    <select name="center_id" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                        <option value="">Chọn cơ sở</option>
                        @foreach($centers as $center)
                            <option value="{{ $center->id }}" {{ old('center_id') == $center->id ? 'selected' : '' }}>[{{ $center->code }}] {{ $center->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <x-ui.input name="address" label="Địa chỉ" placeholder="Nhập địa chỉ" value="{{ old('address') }}" />

            <div class="flex justify-end gap-3 pt-4">
                <x-ui.button type="button" variant="ghost" @click="showCreateModal = false">Hủy</x-ui.button>
                <x-ui.button type="submit" variant="primary">Lưu thông tin</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    <!-- Edit Modal -->
    <x-ui.modal x-show="showEditModal" title="Cập nhật Khách hàng" @close.window="showEditModal = false">
        <form :action="'{{ route('admin.customers.index') }}/' + currentCustomer.id" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input name="name" label="Họ và tên" placeholder="Nhập họ tên" required x-model="currentCustomer.name" />
                <x-ui.input name="phone" label="Số điện thoại" placeholder="Nhập số điện thoại" required x-model="currentCustomer.phone" />
                <x-ui.input name="email" label="Email" type="email" placeholder="Nhập email" x-model="currentCustomer.email" />
                <x-ui.input name="dob" label="Ngày sinh" type="date" x-model="currentCustomer.dob" />
                
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 block">Giới tính</label>
                    <select name="gender" x-model="currentCustomer.gender" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                        <option value="">Chọn giới tính</option>
                        <option value="MALE">Nam</option>
                        <option value="FEMALE">Nữ</option>
                        <option value="OTHER">Khác</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 block">Cơ sở (Trung tâm)</label>
                    <select name="center_id" x-model="currentCustomer.center_id" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                        <option value="">Chọn cơ sở</option>
                        @foreach($centers as $center)
                            <option value="{{ $center->id }}">[{{ $center->code }}] {{ $center->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <x-ui.input name="address" label="Địa chỉ" placeholder="Nhập địa chỉ" x-model="currentCustomer.address" />

            <div class="flex justify-end gap-3 pt-4">
                <x-ui.button type="button" variant="ghost" @click="showEditModal = false">Hủy</x-ui.button>
                <x-ui.button type="submit" variant="primary">Cập nhật</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>
@endsection
