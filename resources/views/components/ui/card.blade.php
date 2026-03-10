@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'iconColor' => 'bg-primary-100 text-primary-600',
    'headerClass' => 'px-6 py-4 border-b border-slate-100 bg-slate-50/50',
    'bodyClass' => 'p-6',
    'footerClass' => 'px-6 py-4 border-t border-slate-100 bg-slate-50/20',
    'footer' => null,
    'glass' => false
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden ' . ($glass ? 'glass' : '')]) }}>
    @if($title || $icon || isset($header))
        <div class="{{ $headerClass }} flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($icon)
                    <div class="w-10 h-10 rounded-xl {{ $iconColor }} flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                    </div>
                @endif
                <div>
                    @if($title)
                        <h3 class="font-display font-bold text-slate-800 tracking-tight">{{ $title }}</h3>
                    @endif
                    @if($subtitle)
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mt-0.5">{{ $subtitle }}</p>
                    @endif
                </div>
                @if(isset($header))
                    {{ $header }}
                @endif
            </div>
            @if(isset($headerActions))
                <div class="flex items-center gap-2">
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif

    <div class="{{ $bodyClass }}">
        {{ $slot }}
    </div>

    @if($footer || isset($footerActions) || isset($footerSlot))
        <div class="{{ $footerClass }}">
            @if($footer)
                {{ $footer }}
            @endif
            @if(isset($footerSlot))
                {{ $footerSlot }}
            @endif
            @if(isset($footerActions))
                <div class="flex items-center justify-end gap-3">
                    {{ $footerActions }}
                </div>
            @endif
        </div>
    @endif
</div>
