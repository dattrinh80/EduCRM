@props([
    'title' => '',
    'icon' => null,
    'maxWidth' => 'xl'
])

@php
$maxWidthClass = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '3xl' => 'max-w-3xl',
    '4xl' => 'max-w-4xl',
    '5xl' => 'max-w-5xl',
    '6xl' => 'max-w-6xl',
    '7xl' => 'max-w-7xl',
][$maxWidth] ?? 'max-w-xl';
@endphp

<template x-teleport="body">
    <div 
        x-cloak
        {{ $attributes->merge(['class' => 'fixed inset-0 z-[100] flex items-center justify-center p-4']) }}
    >
        <!-- Backdrop -->
        <div 
            class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" 
            @click="$dispatch('close')" 
            x-transition.opacity
        ></div>
        
        <!-- Modal Content -->
        <div 
            class="relative bg-white rounded-2xl shadow-2xl w-full {{ $maxWidthClass }} mx-auto overflow-hidden text-left"
            x-show="{{ $attributes->get('x-show') }}"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                        <i data-lucide="{{ $icon ?? 'layers' }}" class="w-4 h-4"></i>
                    </div>
                    {{ $title }}
                </h3>
                <button type="button" @click="$dispatch('close')" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</template>
