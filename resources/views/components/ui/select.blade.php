@props([
    'label' => null,
    'icon' => null,
    'id' => null,
    'name' => null,
    'containerClass' => 'w-full'
])

<div class="{{ $containerClass }}">
    @if($label)
        <label for="{{ $id }}" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">{{ $label }}</label>
    @endif
    <div class="relative w-full group">
        @if($icon)
            <i data-lucide="{{ $icon }}" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
        @endif
        <select name="{{ $name }}" id="{{ $id }}" 
                {{ $attributes->merge(['class' => ($icon ? 'pl-10 ' : 'px-4 ') . 'pr-10 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none cursor-pointer']) }}>
            {{ $slot }}
        </select>
        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 opacity-60">
            <i data-lucide="chevron-down" class="w-4 h-4"></i>
        </div>
    </div>
</div>
