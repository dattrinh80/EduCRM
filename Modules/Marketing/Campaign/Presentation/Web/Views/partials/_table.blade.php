<!-- Data List -->
<x-ui.card bodyClass="p-0">
    @if($campaigns->isEmpty() && !$search && !request()->hasAny(['center_id', 'budget_from', 'budget_to', 'date_from', 'date_to', 'is_active']))
        <x-ui.empty-state 
            title="Chưa có chiến dịch nào"
            description="Hệ thống chưa có dữ liệu chiến dịch. Hãy bắt đầu bằng cách thêm chiến dịch đầu tiên."
            icon="megaphone"
            actionText="Thêm chiến dịch mới"
            actionClick="showCreateModal = true; $dispatch('refresh-icons')"
        />
    @else

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                    <th class="p-4 px-6">
                        <a href="{{ route('admin.campaigns.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_dir' => ($sortBy === 'name' && $sortDir === 'asc') ? 'desc' : 'asc', 'page' => 1])) }}"
                           class="inline-flex items-center gap-1.5 hover:text-primary-600 transition group/sort cursor-pointer select-none">
                            Chiến dịch
                            @if($sortBy === 'name')
                                @if($sortDir === 'asc')
                                    <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 19V5m0 0l-5 5m5-5 5 5"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14m0 0l5-5m-5 5-5-5"/></svg>
                                @endif
                            @else
                                <svg class="w-3.5 h-3.5 text-slate-300 opacity-0 group-hover/sort:opacity-100 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 16l5 5 5-5M7 8l5-5 5 5"/></svg>
                            @endif
                        </a>
                    </th>
                    <th class="p-4 px-6 text-slate-500">Cơ sở</th>
                    <th class="p-4 px-6">
                        <a href="{{ route('admin.campaigns.index', array_merge(request()->query(), ['sort_by' => 'channel', 'sort_dir' => ($sortBy === 'channel' && $sortDir === 'asc') ? 'desc' : 'asc', 'page' => 1])) }}"
                           class="inline-flex items-center gap-1.5 hover:text-primary-600 transition group/sort cursor-pointer select-none">
                            Kênh (Channel)
                            @if($sortBy === 'channel')
                                @if($sortDir === 'asc')
                                    <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 19V5m0 0l-5 5m5-5 5 5"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14m0 0l5-5m-5 5-5-5"/></svg>
                                @endif
                            @else
                                <svg class="w-3.5 h-3.5 text-slate-300 opacity-0 group-hover/sort:opacity-100 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 16l5 5 5-5M7 8l5-5 5 5"/></svg>
                            @endif
                        </a>
                    </th>
                    <th class="p-4 px-6">
                        <a href="{{ route('admin.campaigns.index', array_merge(request()->query(), ['sort_by' => 'budget', 'sort_dir' => ($sortBy === 'budget' && $sortDir === 'asc') ? 'desc' : 'asc', 'page' => 1])) }}"
                           class="inline-flex items-center gap-1.5 hover:text-primary-600 transition group/sort cursor-pointer select-none">
                            Ngân sách
                            @if($sortBy === 'budget')
                                @if($sortDir === 'asc')
                                    <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 19V5m0 0l-5 5m5-5 5 5"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14m0 0l5-5m-5 5-5-5"/></svg>
                                @endif
                            @else
                                <svg class="w-3.5 h-3.5 text-slate-300 opacity-0 group-hover/sort:opacity-100 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 16l5 5 5-5M7 8l5-5 5 5"/></svg>
                            @endif
                        </a>
                    </th>
                    <th class="p-4 px-6">
                        <a href="{{ route('admin.campaigns.index', array_merge(request()->query(), ['sort_by' => 'start_date', 'sort_dir' => ($sortBy === 'start_date' && $sortDir === 'asc') ? 'desc' : 'asc', 'page' => 1])) }}"
                           class="inline-flex items-center gap-1.5 hover:text-primary-600 transition group/sort cursor-pointer select-none">
                            Thời gian
                            @if($sortBy === 'start_date')
                                @if($sortDir === 'asc')
                                    <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 19V5m0 0l-5 5m5-5 5 5"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14m0 0l5-5m-5 5-5-5"/></svg>
                                @endif
                            @else
                                <svg class="w-3.5 h-3.5 text-slate-300 opacity-0 group-hover/sort:opacity-100 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 16l5 5 5-5M7 8l5-5 5 5"/></svg>
                            @endif
                        </a>
                    </th>
                    <th class="p-4 px-6">
                        <a href="{{ route('admin.campaigns.index', array_merge(request()->query(), ['sort_by' => 'is_active', 'sort_dir' => ($sortBy === 'is_active' && $sortDir === 'asc') ? 'desc' : 'asc', 'page' => 1])) }}"
                           class="inline-flex items-center gap-1.5 hover:text-primary-600 transition group/sort cursor-pointer select-none">
                            Trạng thái
                            @if($sortBy === 'is_active')
                                @if($sortDir === 'asc')
                                    <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 19V5m0 0l-5 5m5-5 5 5"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14m0 0l5-5m-5 5-5-5"/></svg>
                                @endif
                            @else
                                <svg class="w-3.5 h-3.5 text-slate-300 opacity-0 group-hover/sort:opacity-100 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 16l5 5 5-5M7 8l5-5 5 5"/></svg>
                            @endif
                        </a>
                    </th>
                    <th class="p-4 px-6 text-right text-slate-500">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($campaigns as $campaign)
                    <tr class="hover:bg-slate-50 transition group" x-data="{ showEditModal: {{ $errors->any() && old('_method') == 'PUT' && old('campaign_id') == $campaign->id ? 'true' : 'false' }} }">
                        <td class="p-4 px-6 whitespace-nowrap">
                            <div class="font-medium text-slate-800">{{ $campaign->name }}</div>
                            @if($campaign->code)
                            <div class="text-xs font-mono text-slate-500 mt-0.5">{{ $campaign->code }}</div>
                            @endif
                        </td>
                        <td class="p-4 px-6 whitespace-nowrap text-slate-600">
                            @php
                                $campCenter = isset($centers) ? $centers->firstWhere('id', $campaign->center_id) : null;
                            @endphp
                            @if($campCenter)
                                <div class="flex items-center gap-1.5 text-sm">
                                    <i data-lucide="building-2" class="w-4 h-4 text-slate-400"></i>
                                    <span>[{{ $campCenter->code }}] {{ $campCenter->name }}</span>
                                </div>
                            @else
                                <span class="text-slate-400 text-sm italic">N/A</span>
                            @endif
                        </td>
                        <td class="p-4 px-6 whitespace-nowrap">
                            <span class="text-sm text-slate-600">{{ $campaign->channel ?: '—' }}</span>
                        </td>
                        <td class="p-4 px-6 whitespace-nowrap">
                            <span class="text-sm text-slate-600">{{ $campaign->budget ? number_format($campaign->budget) . ' đ' : '—' }}</span>
                        </td>
                        <td class="p-4 px-6 whitespace-nowrap text-slate-500 text-sm">
                            <div>{{ $campaign->start_date ? \Carbon\Carbon::parse($campaign->start_date)->format('d/m/Y') : '—' }} đến</div>
                            <div>{{ $campaign->end_date ? \Carbon\Carbon::parse($campaign->end_date)->format('d/m/Y') : '—' }}</div>
                        </td>
                        <td class="p-4 px-6 whitespace-nowrap">
                            <x-ui.badge :variant="$campaign->is_active ? 'success' : 'danger'" dot>
                                {{ $campaign->is_active ? 'Hoạt động' : 'Đã khóa' }}
                            </x-ui.badge>
                        </td>

                        <td class="p-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                                @can('campaigns.update')
                                <button type="button" @click="showEditModal = true; $dispatch('refresh-icons')" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition cursor-pointer" title="Sửa">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </button>
                                @endcan
                                @can('campaigns.delete')
                                <form action="{{ route('admin.campaigns.destroy', $campaign->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(this, '{{ addslashes($campaign->name) }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Xoá">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>

                            @include('campaign::partials._edit_modal', ['campaign' => $campaign])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-slate-500">
                            Không tìm thấy kết quả phù hợp với "{{ $search }}"
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
</x-ui.card>

<div class="mt-6">
    {{ $campaigns->links() }}
</div>
