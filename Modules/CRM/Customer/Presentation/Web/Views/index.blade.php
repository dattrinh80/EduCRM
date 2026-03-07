@extends('layouts.app')

@section('title', 'Quản lý Khách hàng (Phụ huynh)')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Danh sách Khách hàng</h1>
            <p class="text-slate-500 mt-1">Quản lý thông phụ huynh và người giám hộ</p>
        </div>
    </div>

    @if(count($customers) > 0)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Khách hàng</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Liên hệ</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Địa chỉ</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Ngày tạo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @foreach($customers as $customer)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-slate-800">{{ $customer->name }}</div>
                        <div class="text-xs text-slate-400 capitalize">{{ $customer->gender === 'MALE' ? 'Nam' : ($customer->gender === 'FEMALE' ? 'Nữ' : 'Khác') }}</div>
                    </td>
                    <td class="px-6 py-4 text-slate-600">
                        <div class="flex items-center gap-2"><i data-lucide="phone" class="w-3.5 h-3.5"></i> {{ $customer->phone }}</div>
                        @if($customer->email)
                        <div class="flex items-center gap-2 text-xs text-slate-400"><i data-lucide="mail" class="w-3.5 h-3.5"></i> {{ $customer->email }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-500 truncate max-w-[200px]">{{ $customer->address }}</td>
                    <td class="px-6 py-4 text-slate-400 text-xs">{{ $customer->createdAt }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="users" class="w-8 h-8 text-slate-400"></i>
        </div>
        <p class="text-slate-500">Chưa có khách hàng nào trong hệ thống</p>
    </div>
    @endif
</div>
@endsection
