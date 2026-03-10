@extends('layouts.app')

@section('title', 'Quản lý Khách hàng (Phụ huynh)')

@section('breadcrumb_items')
    <a href="{{ route('admin.customers.index') }}" class="text-slate-400 hover:text-primary-500 transition-colors">CRM</a>
    <i data-lucide="chevron-right" class="w-2.5 h-2.5 opacity-50"></i>
    <span class="text-primary-500">Khách hàng</span>
@endsection

@section('content')
<div class="space-y-6">
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
            <x-ui.button variant="primary" icon="plus-circle">
                Thêm Khách hàng
            </x-ui.button>
        </div>
    </div>

    <!-- Filter/Search Bar -->
    <x-ui.card bodyClass="p-4">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4 items-end">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
                <x-ui.input 
                    name="search" 
                    label="Tìm kiếm" 
                    placeholder="Họ tên, số điện thoại, email…" 
                    value="{{ request('search') }}"
                    icon="search"
                    containerClass="md:col-span-2"
                />
                
                <div class="flex gap-2 items-end">
                    <x-ui.button type="submit" variant="secondary" icon="filter">
                        Lọc dữ liệu
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
                            <th class="px-6 py-4">Địa chỉ / Ghi chú</th>
                            <th class="px-6 py-4">Thời gian tạo</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($customers as $customer)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm border border-indigo-200 shadow-sm">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 leading-tight">{{ $customer->name }}</div>
                                        <x-ui.badge variant="info" class="mt-0.5">
                                            {{ $customer->gender === 'MALE' ? 'Nam' : ($customer->gender === 'FEMALE' ? 'Nữ' : 'Khác') }}
                                        </x-ui.badge>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 tabular-nums">
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
                            <td class="px-6 py-4 text-slate-500 truncate max-w-[250px] text-sm font-medium">
                                <div class="flex items-start gap-2">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-300 mt-0.5"></i>
                                    <span class="truncate">{{ $customer->address ?: 'Chưa cập nhật' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-400 text-[11px] font-bold flex flex-col uppercase tracking-wider tabular-nums">
                                 {{ \Carbon\Carbon::parse($customer->createdAt)->translatedFormat('d/m/Y') }}
                                 <span class="text-[9px] opacity-60 font-medium normal-case tracking-normal">{{ \Carbon\Carbon::parse($customer->createdAt)->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1 opacity-10 sm:opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:translate-x-0 translate-x-4">
                                    <x-ui.button variant="ghost" size="xs" icon="eye" class="text-slate-400" />
                                    <x-ui.button variant="ghost" size="xs" icon="edit-3" class="text-slate-400" />
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
        />
    @endif
</div>
@endsection
