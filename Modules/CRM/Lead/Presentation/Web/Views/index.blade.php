@extends('layouts.app')

@section('title', 'Leads Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Leads Management</h1>
            <p class="text-slate-500 mt-1">Manage and track all leads</p>
        </div>
        <a href="{{ route('admin.leads.create') }}" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition flex items-center gap-2 shadow-lg shadow-primary-500/30 w-fit">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>New Lead</span>
        </a>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 text-emerald-600 border border-emerald-200 px-4 py-3 rounded-xl flex items-center gap-3 slide-down">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Data List -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($leads->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="users" class="w-8 h-8 text-slate-400"></i>
            </div>
            <p class="text-slate-500">No leads found</p>
            <a href="{{ route('admin.leads.create') }}" class="inline-flex items-center gap-2 mt-4 text-primary-500 hover:text-primary-600 font-medium">
                <i data-lucide="plus" class="w-4 h-4"></i> Create new lead
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                        <th class="p-4 px-6">Name</th>
                        <th class="p-4 px-6">Phone</th>
                        <th class="p-4 px-6">Status</th>
                        <th class="p-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($leads as $lead)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="p-4 px-6 whitespace-nowrap">
                                <div class="font-medium text-slate-800">{{ $lead->name }}</div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-slate-600">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="phone" class="w-4 h-4 text-slate-400"></i>
                                    {{ $lead->phone }}
                                </div>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap">
                                @php
                                    $statusColor = match(strtolower($lead->status)) {
                                        'new' => 'bg-blue-100 text-blue-700',
                                        'contacted' => 'bg-amber-100 text-amber-700',
                                        'qualified' => 'bg-emerald-100 text-emerald-700',
                                        'lost' => 'bg-red-100 text-red-700',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                    {{ ucfirst($lead->status) }}
                                </span>
                            </td>
                            <td class="p-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                    <a href="{{ route('admin.leads.edit', $lead->id) }}" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Edit">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.leads.destroy', $lead->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this lead?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($leads->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $leads->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
