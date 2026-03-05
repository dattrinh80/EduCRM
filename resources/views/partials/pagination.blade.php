@php
    $perPageOptions = \App\Core\Helpers\PaginationHelper::getPerPageOptions();
    $currentPerPage = request('per_page', \App\Core\Helpers\PaginationHelper::getDefaultPerPage());
    $pages = \App\Core\Helpers\PaginationHelper::calculatePageNumbers($paginator);
@endphp

<div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="flex items-center gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            <span class="text-sm text-slate-500 whitespace-nowrap">Hiển thị</span>
            <select onchange="window.location.href=this.value" class="pl-3 pr-8 py-1.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 transition outline-none appearance-none cursor-pointer font-medium text-slate-700">
                @foreach($perPageOptions as $option)
                    <option value="{{ $paginator->appends(array_merge(request()->query(), ['per_page' => $option, 'page' => 1]))->url(1) }}" {{ $currentPerPage == $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
            </select>
            <span class="text-sm text-slate-500 whitespace-nowrap">bản ghi / trang</span>
            <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 pointer-events-none text-slate-400" style="display: none; /* Usually need a wrapper for absolute icon in select, skipping for simplicity if existing design didn't use it here */"></i>
        </div>
        <span class="text-slate-300 hidden sm:inline">|</span>
        <span class="text-sm text-slate-500">
            Hiển thị <span class="font-semibold text-slate-700">{{ $paginator->firstItem() ?? 0 }}</span> - <span class="font-semibold text-slate-700">{{ $paginator->lastItem() ?? 0 }}</span> trên tổng <span class="font-semibold text-slate-700">{{ $paginator->total() }}</span> bản ghi
        </span>
    </div>

    @if($paginator->hasPages())
    <nav class="flex items-center gap-1">
        {{-- Previous --}}
        @if($paginator->onFirstPage())
            <span class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-300 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            </span>
        @else
            <a href="{{ $paginator->appends(request()->query())->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-primary-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach($pages as $p)
            @if($p === '...')
                <span class="w-9 h-9 flex items-center justify-center text-slate-400 text-sm">…</span>
            @elseif($p == $paginator->currentPage())
                <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-primary-500 text-white text-sm font-semibold shadow-sm">{{ $p }}</span>
            @else
                <a href="{{ $paginator->appends(request()->query())->url($p) }}" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-600 hover:bg-primary-50 hover:text-primary-600 text-sm font-medium transition">{{ $p }}</a>
            @endif
        @endforeach

        {{-- Next --}}
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->appends(request()->query())->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-primary-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-300 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif
    </nav>
    @endif
</div>
