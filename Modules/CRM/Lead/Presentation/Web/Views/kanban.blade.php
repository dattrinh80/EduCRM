<div class="flex-1 min-h-0 overflow-x-auto overflow-y-hidden pb-4 custom-scrollbar" x-data="kanbanBoard()">
    <div class="inline-flex h-full gap-6 px-1 min-w-full">
        @foreach($kanbanData as $status)
        <!-- Column -->
        <div class="flex flex-col w-[350px] bg-slate-100/50 rounded-3xl border border-slate-200/60 p-3 h-full max-h-[calc(100vh-250px)]">
            <!-- Column Header -->
            <div class="flex items-center justify-between px-3 py-2 mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full shadow-sm" style="background-color: {{ $status->color ?? '#94a3b8' }}"></div>
                    <h3 class="font-bold text-slate-700 tracking-tight">{{ $status->name }}</h3>
                    <span class="px-2 py-0.5 bg-white text-[10px] font-bold text-slate-400 rounded-lg shadow-sm">{{ $status->leads->count() }}</span>
                </div>
                <button class="p-1 hover:bg-white rounded-lg transition-colors text-slate-400">
                    <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Cards Container -->
            <div class="flex-1 overflow-y-auto space-y-3 px-1 custom-scrollbar kanban-column" 
                 data-status-id="{{ $status->id }}">
                @foreach($status->leads as $lead)
                <!-- Card -->
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200/50 hover:border-primary-500/30 hover:shadow-md transition-all cursor-grab active:cursor-grabbing group kanban-card"
                     data-lead-id="{{ $lead->id }}">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-1.5">
                            @if($lead->center)
                            <span class="px-2 py-0.5 bg-slate-100 text-[9px] font-bold text-slate-500 rounded uppercase tracking-wider">{{ $lead->center->code }}</span>
                            @endif
                        </div>
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('admin.leads.show', $lead->id) }}" class="p-1 text-slate-400 hover:text-primary-600 transition-colors" title="Xem chi tiết">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                    
                    <a href="{{ route('admin.leads.show', $lead->id) }}"><h4 class="font-bold text-slate-800 text-sm mb-1 group-hover:text-primary-600 transition-colors cursor-pointer">{{ $lead->name }}</h4></a>
                    <p class="text-[11px] text-slate-500 font-medium tabular-nums mb-3 flex items-center gap-1.5">
                        <i data-lucide="phone" class="w-3 h-3 opacity-60"></i>
                        {{ $lead->phone }}
                    </p>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-50">
                        <div class="flex -space-x-2">
                            @if($lead->assignTo)
                            <div class="w-6 h-6 rounded-lg bg-primary-100 border-2 border-white flex items-center justify-center text-[9px] font-bold text-primary-600" title="{{ $lead->assignTo->name }}">
                                {{ strtoupper(substr($lead->assignTo->name, 0, 1)) }}
                            </div>
                            @else
                            <div class="w-6 h-6 rounded-lg bg-slate-100 border-2 border-white flex items-center justify-center text-slate-400" title="Chưa giao">
                                <i data-lucide="user" class="w-3 h-3"></i>
                            </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @if($lead->interestType)
                            <span class="text-[10px] font-bold text-slate-400">{{ $lead->interestType->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    function kanbanBoard() {
        return {
            init() {
                this.initSortable();
            },
            initSortable() {
                const columns = document.querySelectorAll('.kanban-column');
                columns.forEach(column => {
                    new Sortable(column, {
                        group: 'kanban',
                        animation: 250,
                        ghostClass: 'opacity-50',
                        dragClass: 'shadow-2xl',
                        onEnd: (evt) => {
                            const leadId = evt.item.dataset.leadId;
                            const newStatusId = evt.to.dataset.statusId;
                            const oldStatusId = evt.from.dataset.statusId;

                            if (newStatusId !== oldStatusId) {
                                this.updateLeadStatus(leadId, newStatusId);
                            }
                        }
                    });
                });
            },
            async updateLeadStatus(leadId, statusId) {
                try {
                    const response = await fetch('{{ route('admin.leads.update-status') }}', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            lead_id: leadId,
                            status_id: statusId
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message, 'error');
                        // Optional: Refresh page or move card back on failure
                        window.location.reload();
                    }
                } catch (error) {
                    showToast('Đã xảy ra lỗi khi cập nhật trạng thái', 'error');
                    window.location.reload();
                }
            }
        }
    }
</script>
