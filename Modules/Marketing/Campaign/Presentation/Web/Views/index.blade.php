@extends('layouts.app')

@section('title', 'Chiến dịch (Campaigns)')

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }} }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight">Chiến dịch Marketing</h1>
            <p class="text-slate-500 mt-1 flex items-center gap-1.5 text-sm">
                <i data-lucide="info" class="w-3.5 h-3.5 opacity-60"></i>
                Quản lý và theo dõi hiệu quả các chiến dịch Marketing
            </p>
        </div>
        @can('campaigns.create')
        <x-ui.button variant="primary" icon="plus-circle" @click="showCreateModal = true; $dispatch('refresh-icons')">
            Thêm Chiến Dịch
        </x-ui.button>
        @endcan
    </div>

    <!-- Filter/Search -->
    @include('campaign::partials._filter')

    <!-- Data List -->
    @include('campaign::partials._table')

    <!-- Create Modal -->
    @include('campaign::partials._create_modal')
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        window.addEventListener('refresh-icons', () => {
            setTimeout(() => {
                if (window.lucide) { lucide.createIcons(); }
            }, 50);
        });
    });

    function confirmDelete(form, name) {
        if (confirm(`Bạn có chắc chắn muốn xoá chiến dịch "${name}"?`)) {
            return true;
        }
        return false;
    }
</script>
@endpush
@endsection
