@props([
    'variant' => 'neutral',
    'color' => null,
    'dot' => false
])

@php
    $baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase tracking-wider';
    
    $variants = [
        'neutral' => 'bg-slate-50 text-slate-500 border-slate-200',
        'primary' => 'bg-primary-50 text-primary-700 border-primary-100',
        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
        'danger' => 'bg-red-50 text-red-700 border-red-100',
        'warning' => 'bg-amber-50 text-amber-700 border-amber-100',
        'info' => 'bg-blue-50 text-blue-700 border-blue-100',
    ];

    $style = $color ? "background-color: {$color}10; color: {$color}; border-color: {$color}30;" : "";
    $class = $baseClasses . ' ' . ($color ? '' : ($variants[$variant] ?? $variants['neutral']));
@endphp

<span {{ $attributes->merge(['class' => $class, 'style' => $style]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full mr-1.5" style="background-color: {{ $color ?? 'currentColor' }}"></span>
    @endif
    {{ $slot }}
</span>
