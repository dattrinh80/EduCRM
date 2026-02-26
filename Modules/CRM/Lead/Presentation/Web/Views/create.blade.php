@extends('layouts.app')

@section('title', 'Create New Lead')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Create New Lead</h1>
            <p class="text-slate-500 mt-1">Add a new lead to the CRM</p>
        </div>
        <a href="{{ route('admin.leads.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition flex items-center gap-2 text-sm font-medium w-fit">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to List</span>
        </a>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden max-w-2xl mx-auto p-6 lg:p-8">
        <form action="{{ route('admin.leads.store') }}" method="POST" class="space-y-5">
            @csrf
            
            <div class="space-y-1">
                <label for="name" class="text-sm font-medium text-slate-700 block">Name <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i data-lucide="user" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="name" id="name" required class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('name') }}" placeholder="Enter lead name">
                </div>
                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label for="phone" class="text-sm font-medium text-slate-700 block">Phone <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i data-lucide="phone" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="phone" id="phone" required class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('phone') }}" placeholder="Enter phone number">
                </div>
                @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label for="email" class="text-sm font-medium text-slate-700 block">Email <span class="text-slate-400 font-normal">(Optional)</span></label>
                <div class="relative">
                    <i data-lucide="mail" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="email" name="email" id="email" class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" value="{{ old('email') }}" placeholder="Enter email address">
                </div>
                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label for="center_id" class="text-sm font-medium text-slate-700 block">Center ID <span class="text-slate-400 font-normal">(Optional)</span></label>
                <div class="relative">
                    <i data-lucide="building" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="center_id" id="center_id" class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" placeholder="UUID format" value="{{ old('center_id') }}">
                </div>
                @error('center_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3 mt-8">
                <a href="{{ route('admin.leads.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition flex items-center gap-2 font-medium shadow-lg shadow-primary-500/30">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Save Lead</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
