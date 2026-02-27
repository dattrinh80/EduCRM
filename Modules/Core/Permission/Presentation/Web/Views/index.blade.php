@extends('layouts.app')

@section('title', 'Permissions Overview')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Permissions Overview</h1>
        <p class="text-slate-500 mt-1">All system permissions grouped by module ({{ $totalPermissions }} total)</p>
    </div>

    <!-- Permission Groups -->
    <div class="space-y-4">
        @forelse ($permissionGroups as $group)
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-4 flex items-center justify-between border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">
                        <i data-lucide="folder" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-800">{{ $group->name }}</h3>
                        @if($group->description)
                            <p class="text-xs text-slate-500">{{ $group->description }}</p>
                        @endif
                    </div>
                </div>
                <span class="px-3 py-1 bg-violet-50 text-violet-600 rounded-full text-xs font-semibold">
                    {{ $group->permissions->count() }} permissions
                </span>
            </div>
            <div class="p-4">
                @if($group->permissions->isEmpty())
                    <p class="text-sm text-slate-400 text-center py-4 italic">No permissions in this group</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                        @foreach ($group->permissions as $perm)
                        <div class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="key" class="w-3.5 h-3.5"></i>
                            </div>
                            <div class="min-w-0">
                                <code class="text-sm font-medium text-slate-800 block truncate">{{ $perm->name }}</code>
                                @if($perm->description)
                                    <span class="text-xs text-slate-400 block truncate">{{ $perm->description }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="key" class="w-8 h-8 text-slate-400"></i>
            </div>
            <p class="text-slate-500">No permissions registered yet</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
