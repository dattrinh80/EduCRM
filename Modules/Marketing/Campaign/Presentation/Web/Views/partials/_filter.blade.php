<!-- Filter/Search -->
<x-ui.card bodyClass="p-4">
    <form action="{{ route('admin.campaigns.index') }}" method="GET" class="space-y-4">
        <!-- Keep existing sort query params hidden -->
        @if(request('sort_by')) <input type="hidden" name="sort_by" value="{{ request('sort_by') }}"> @endif
        @if(request('sort_dir')) <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}"> @endif
        @if(request('per_page')) <input type="hidden" name="per_page" value="{{ request('per_page') }}"> @endif

        <div class="flex flex-wrap items-end gap-x-6 gap-y-4">
            <x-ui.input name="search" label="Chiến dịch" placeholder="Tên hoặc mã..." value="{{ request('search') }}" icon="search" containerClass="w-full sm:w-80 shrink-0" />

            @if($isGlobalScope)
            <x-ui.select name="center_id" label="Cơ sở" icon="building-2" containerClass="w-full sm:w-72 shrink-0">
                <option value="">Tất cả cơ sở</option>
                @foreach($centers as $c)
                    <option value="{{ $c->id }}" {{ request('center_id') == $c->id ? 'selected' : '' }}>[{{ $c->code }}] {{ $c->name }}</option>
                @endforeach
            </x-ui.select>
            @endif

            <x-ui.select name="is_active" label="Trạng thái" icon="tag" containerClass="w-full sm:w-56 shrink-0">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Đã khóa</option>
            </x-ui.select>

            <div class="flex gap-2 pb-0.5 shrink-0">
                <x-ui.button type="submit" variant="secondary" icon="filter">
                    Lọc
                </x-ui.button>
                @if(request()->hasAny(['search', 'center_id', 'budget_from', 'budget_to', 'date_from', 'date_to', 'is_active']))
                <x-ui.button variant="ghost" icon="x-circle" :attributes="new \Illuminate\View\ComponentAttributeBag(['href' => route('admin.campaigns.index'), 'tag' => 'a'])">
                    Xoá lọc
                </x-ui.button>
                @endif
            </div>
        </div>

        <!-- Second row for ranges -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Ngân sách (Từ - Đến)</label>
                <div class="flex items-center gap-2">
                    <x-ui.input type="number" name="budget_from" value="{{ request('budget_from') }}" placeholder="Min" containerClass="flex-1" />
                    <span class="text-slate-300">-</span>
                    <x-ui.input type="number" name="budget_to" value="{{ request('budget_to') }}" placeholder="Max" containerClass="flex-1" />
                </div>
            </div>
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Thời gian (Từ - Đến)</label>
                <div class="flex items-center gap-2">
                    <x-ui.input type="date" name="date_from" value="{{ request('date_from') }}" containerClass="flex-1" />
                    <span class="text-slate-300">-</span>
                    <x-ui.input type="date" name="date_to" value="{{ request('date_to') }}" containerClass="flex-1" />
                </div>
            </div>
        </div>
    </form>
</x-ui.card>
