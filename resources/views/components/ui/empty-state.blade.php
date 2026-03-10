@props([
    'title' => 'Chưa có dữ liệu',
    'description' => 'Hệ thống hiện chưa ghi nhận dữ liệu này.',
    'icon' => 'database',
    'actionText' => null,
    'actionUrl' => null,
    'actionClick' => null
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-3xl border border-dashed border-slate-200 p-20 text-center shadow-inner group']) }}>
    <div class="w-24 h-24 rounded-3xl bg-slate-50 flex items-center justify-center mx-auto mb-8 transform rotate-3 group-hover:rotate-0 transition-all duration-500 shadow-inner">
        <i data-lucide="{{ $icon }}" class="w-12 h-12 text-slate-300"></i>
    </div>
    <h3 class="text-2xl font-display font-bold text-slate-800 mb-2 tracking-tight">{{ $title }}</h3>
    <p class="text-slate-500 max-w-sm mx-auto mb-10 font-medium italic">{{ $description }}</p>
    
    @if($actionText)
        @if($actionUrl)
            <a href="{{ $actionUrl }}" class="inline-flex items-center gap-3 px-8 py-3.5 bg-primary-500 text-white rounded-2xl font-bold hover:bg-primary-600 transition shadow-xl shadow-primary-500/25 active:scale-95">
                <i data-lucide="plus" class="w-5 h-5"></i>
                {{ $actionText }}
            </a>
        @else
            <button type="button" @click="{{ $actionClick }}" class="inline-flex items-center gap-3 px-8 py-3.5 bg-primary-500 text-white rounded-2xl font-bold hover:bg-primary-600 transition shadow-xl shadow-primary-500/25 active:scale-95">
                <i data-lucide="plus" class="w-5 h-5"></i>
                {{ $actionText }}
            </button>
        @endif
    @endif
</div>
