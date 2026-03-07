@extends('layouts.app')

@section('title', 'Quản lý Học viên')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Danh sách Học viên</h1>
            <p class="text-slate-500 mt-1">Quản lý hồ sơ học tập và thông tin cá nhân của học viên</p>
        </div>
    </div>

    @if(count($students) > 0)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Học viên</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Mã học viên</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Ngày tạo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @foreach($students as $student)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 font-semibold text-slate-800">
                        {{ $student->studentName ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded-lg font-mono text-xs">{{ $student->studentCode }}</span>
                    </td>
                    <td class="px-6 py-4 text-slate-600">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                            {{ $student->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-400 text-xs">{{ $student->createdAt }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="graduation-cap" class="w-8 h-8 text-slate-400"></i>
        </div>
        <p class="text-slate-500">Chưa có học viên nào trong hệ thống</p>
    </div>
    @endif
</div>
@endsection
