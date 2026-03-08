@extends('layouts.app')

@section('title', 'Quản lý Khách hàng (Phụ huynh)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Khách hàng (Phụ huynh)</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-1.5 text-sm">
                <i data-lucide="info" class="w-3.5 h-3.5 opacity-60"></i>
                Quản lý thông tin phụ huynh và người giám hộ học viên
            </p>
        </div>
        <div class="flex gap-2">
            <button type="button" class="px-6 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-300 flex items-center gap-2 shadow-lg shadow-primary-500/25 whitespace-nowrap font-bold active:scale-95 group">
                <i data-lucide="plus-circle" class="w-5 h-5 group-hover:rotate-90 transition-transform"></i>
                <span>Thêm Khách hàng</span>
            </button>
        </div>
    </div>

    <!-- Filter/Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row gap-4 items-end">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label for="search" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Tìm kiếm</label>
                <div class="relative w-full group">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                    <input type="text" name="search" id="search" placeholder="Họ tên, số điện thoại, email…" value="{{ request('search') }}"
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none">
                </div>
            </div>
            
            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="px-5 py-2.5 bg-primary-50 text-primary-600 hover:bg-primary-100 rounded-xl transition font-bold text-sm flex items-center gap-2 whitespace-nowrap active:scale-95">
                    <i data-lucide="filter" class="w-4 h-4"></i> Lọc dữ liệu
                </button>
                @if(request()->has('search'))
                <a href="{{ route('admin.customers.index') }}" class="px-5 py-2.5 bg-slate-50 text-slate-600 hover:bg-slate-100 rounded-xl transition font-bold text-sm flex items-center gap-2 border border-slate-200 whitespace-nowrap active:scale-95">
                    <i data-lucide="x-circle" class="w-4 h-4"></i> Xoá lọc
                </a>
                @endif
            </div>
        </form>
    </div>

    @if(count($customers) > 0)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
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
                            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-xs border border-indigo-100">
                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 leading-tight">{{ $customer->name }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">
                                    {{ $customer->gender === 'MALE' ? 'Nam' : ($customer->gender === 'FEMALE' ? 'Nữ' : 'Khác') }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-600 tabular-nums">
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2 text-sm font-medium">
                                <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i> {{ $customer->phone }}
                            </div>
                            @if($customer->email)
                            <div class="flex items-center gap-2 text-[11px] text-slate-400">
                                <i data-lucide="mail" class="w-3.5 h-3.5 opacity-60"></i> {{ $customer->email }}
                            </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-500 truncate max-w-[250px] text-sm">
                        <div class="flex items-start gap-2">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-300 mt-0.5"></i>
                            <span class="truncate">{{ $customer->address ?: 'Chưa cập nhật' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-400 text-[11px] font-medium tabular-nums flex flex-col uppercase tracking-wider">
                         {{ \Carbon\Carbon::parse($customer->createdAt)->translatedFormat('d/m/Y') }}
                         <span class="text-[9px] opacity-60 capitalize">{{ \Carbon\Carbon::parse($customer->createdAt)->diffForHumans() }}</span>
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                            <button class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="Chi tiết">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            <button class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="Chỉnh sửa">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-3xl border border-dashed border-slate-200 p-20 text-center shadow-inner group">
        <div class="w-24 h-24 rounded-3xl bg-slate-50 flex items-center justify-center mx-auto mb-8 transform rotate-3 group-hover:rotate-0 transition-all duration-500 shadow-inner">
            <i data-lucide="users-2" class="w-12 h-12 text-slate-300"></i>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-2 tracking-tight">Chưa có khách hàng</h3>
        <p class="text-slate-500 max-w-sm mx-auto mb-10 font-medium italic">Hệ thống chưa ghi nhận thông tin phụ huynh / giám hộ nào. Chuyển đổi Lead hoặc thêm thủ công ngay.</p>
        <button type="button" class="inline-flex items-center gap-3 px-8 py-3.5 bg-primary-500 text-white rounded-2xl font-bold hover:bg-primary-600 transition shadow-xl shadow-primary-500/25 active:scale-95">
            <i data-lucide="user-plus" class="w-5 h-5"></i>
            Thêm khách hàng đầu tiên
        </button>
    </div>
    @endif
</div>
@endsection
