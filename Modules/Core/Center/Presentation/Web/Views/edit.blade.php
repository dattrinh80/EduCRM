@extends('layouts.app')

@section('title', 'Chỉnh sửa Cơ sở')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Chỉnh sửa: {{ $center->name }}</h1>
            <p class="text-slate-500 mt-1">Cập nhật thông tin cơ sở</p>
        </div>
        <a href="{{ route('admin.centers.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition flex items-center gap-2 text-sm font-medium w-fit">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Quay lại</span>
        </a>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden max-w-2xl mx-auto p-6 lg:p-8">
        <form action="{{ route('admin.centers.update', $center->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1">
                    <label for="name" class="text-sm font-medium text-slate-700 block">Tên cơ sở <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="building-2" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="name" id="name" required class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('name', $center->name) }}">
                    </div>
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label for="code" class="text-sm font-medium text-slate-700 block">Mã cơ sở <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="hash" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="code" id="code" required class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('code', $center->code) }}">
                    </div>
                    @error('code') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1">
                    <label for="phone" class="text-sm font-medium text-slate-700 block">Điện thoại</label>
                    <div class="relative">
                        <i data-lucide="phone" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="phone" id="phone" class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('phone', $center->phone) }}">
                    </div>
                    @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label for="email" class="text-sm font-medium text-slate-700 block">Email</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="email" name="email" id="email" class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('email', $center->email) }}">
                    </div>
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="space-y-1">
                <label for="address" class="text-sm font-medium text-slate-700 block">Địa chỉ</label>
                <div class="relative">
                    <i data-lucide="map-pin" class="w-5 h-5 absolute left-3 top-3 text-slate-400"></i>
                    <textarea name="address" id="address" rows="3" class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition resize-none">{{ old('address', $center->address) }}</textarea>
                </div>
                @error('address') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label for="status" class="text-sm font-medium text-slate-700 block">Trạng thái</label>
                <div class="relative">
                    <i data-lucide="activity" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <select name="status" id="status" class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition appearance-none bg-white">
                        <option value="active" {{ old('status', $center->status) === 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="inactive" {{ old('status', $center->status) === 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </div>
                </div>
                @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3 mt-8">
                <a href="{{ route('admin.centers.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition">
                    Huỷ
                </a>
                <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition flex items-center gap-2 font-medium shadow-lg shadow-primary-500/30">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    <span>Cập nhật</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
