@extends('layouts.app')

@section('title', 'Quản lý Cơ sở')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Quản lý Cơ sở</h1>
            <p class="text-slate-500 mt-1">Quản lý danh sách các cơ sở / chi nhánh</p>
        </div>
        @can('centers.create')
        <a href="{{ route('admin.centers.create') }}" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition flex items-center gap-2 shadow-lg shadow-primary-500/30 w-fit">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Thêm Cơ sở</span>
        </a>
        @endcan
    </div>

    <!-- Data List -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($centers->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="building-2" class="w-8 h-8 text-slate-400"></i>
            </div>
            <p class="text-slate-500">Chưa có cơ sở nào</p>
            <a href="{{ route('admin.centers.create') }}" class="inline-flex items-center gap-2 mt-4 text-primary-500 hover:text-primary-600 font-medium">
                <i data-lucide="plus" class="w-4 h-4"></i> Thêm cơ sở mới
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="p-4 px-6">Mã</th>
                        <th class="p-4 px-6">Tên cơ sở</th>
                        <th class="p-4 px-6">Điện thoại</th>
                        <th class="p-4 px-6">Email</th>
                        <th class="p-4 px-6">Trạng thái</th>
                        <th class="p-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($centers as $center)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="p-4 px-6 whitespace-nowrap">
                                <span class="px-2 py-1 bg-slate-100 rounded text-xs font-mono font-medium text-slate-600">{{ $center->code }}</span>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center">
                                        <i data-lucide="building-2" class="w-4 h-4 text-primary-600"></i>
                                    </div>
                                    <div class="font-medium text-slate-800">{{ $center->name }}</div>
                                </div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-slate-600">
                                @if($center->phone)
                                <div class="flex items-center gap-2">
                                    <i data-lucide="phone" class="w-4 h-4 text-slate-400"></i>
                                    {{ $center->phone }}
                                </div>
                                @else
                                <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-slate-600">
                                @if($center->email)
                                <div class="flex items-center gap-2">
                                    <i data-lucide="mail" class="w-4 h-4 text-slate-400"></i>
                                    {{ $center->email }}
                                </div>
                                @else
                                <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                @php
                                    $statusColor = match(strtolower($center->status)) {
                                        'active' => 'bg-emerald-100 text-emerald-700',
                                        'inactive' => 'bg-red-100 text-red-700',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                    $statusLabel = match(strtolower($center->status)) {
                                        'active' => 'Hoạt động',
                                        'inactive' => 'Ngừng hoạt động',
                                        default => ucfirst($center->status)
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                    @can('centers.update')
                                    <a href="{{ route('admin.centers.edit', $center->id) }}" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Sửa">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    @endcan
                                    @can('centers.delete')
                                    <form action="{{ route('admin.centers.destroy', $center->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(this, '{{ $center->name }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Xoá">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($centers->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $centers->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
