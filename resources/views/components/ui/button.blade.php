@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconRight' => null,
    'loading' => false,
    'type' => 'button'
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-bold transition-all duration-300 active:scale-95 disabled:opacity-50 disabled:pointer-events-none rounded-xl';
    
    $variants = [
        'primary' => 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-500/25 hover:from-primary-600 hover:to-primary-700 hover:shadow-primary-500/40',
        'secondary' => 'bg-white text-slate-700 border border-slate-200 shadow-sm hover:bg-slate-50 hover:border-slate-300',
        'outline' => 'bg-transparent border border-primary-500 text-primary-600 hover:bg-primary-50',
        'danger' => 'bg-red-50 text-red-600 border border-red-100 hover:bg-red-600 hover:text-white hover:shadow-lg hover:shadow-red-500/20',
        'success' => 'bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-600 hover:text-white hover:shadow-lg hover:shadow-emerald-500/20',
        'ghost' => 'bg-transparent text-slate-500 hover:bg-slate-100 hover:text-slate-800',
    ];

    $sizes = [
        'xs' => 'px-2.5 py-1.5 text-[10px] uppercase tracking-wider',
        'sm' => 'px-4 py-2 text-xs',
        'md' => 'px-6 py-2.5 text-sm',
        'lg' => 'px-8 py-3.5 text-base',
    ];

    $class = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button {{ $attributes->merge(['class' => $class, 'type' => $type]) }}>
    @if($icon && !$loading)
        <i data-lucide="{{ $icon }}" class="{{ $size === 'xs' ? 'w-3 h-3' : 'w-4 h-4' }}"></i>
    @endif
    
    @if($loading)
        <svg class="animate-spin h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @endif

    <span>{{ $slot }}</span>

    @if($iconRight && !$loading)
        <i data-lucide="{{ $iconRight }}" class="{{ $size === 'xs' ? 'w-3 h-3' : 'w-4 h-4' }}"></i>
    @endif
</button>
