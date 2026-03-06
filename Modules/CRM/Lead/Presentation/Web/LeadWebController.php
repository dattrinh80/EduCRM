<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Presentation\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Lead\Application\Commands\CreateLeadCommand;
use Modules\CRM\Lead\Application\Commands\CreateLeadHandler;
use Modules\CRM\Lead\Application\Commands\UpdateLeadCommand;
use Modules\CRM\Lead\Application\Commands\UpdateLeadHandler;
use Modules\CRM\Lead\Application\Commands\DeleteLeadCommand;
use Modules\CRM\Lead\Application\Commands\DeleteLeadHandler;
use Modules\CRM\Lead\Application\Queries\GetLeadByIdQuery;
use Modules\CRM\Lead\Application\Queries\GetLeadByIdHandler;
use Modules\CRM\Lead\Application\Queries\GetLeadsPaginatedQuery;
use Modules\CRM\Lead\Application\Queries\GetLeadsPaginatedHandler;
use Modules\Core\Center\Application\Queries\GetActiveCentersQuery;
use Modules\Core\Center\Application\Queries\GetActiveCentersHandler;
use Modules\CRM\LeadSource\Application\Queries\GetLeadSourcesQuery;
use Modules\CRM\LeadSource\Application\Queries\GetLeadSourcesHandler;
use Modules\CRM\InterestType\Application\Queries\GetInterestTypesQuery;
use Modules\CRM\InterestType\Application\Queries\GetInterestTypesHandler;
use Modules\CRM\Campaign\Application\Queries\GetCampaignsQuery;
use Modules\CRM\Campaign\Application\Queries\GetCampaignsHandler;
use Modules\Core\User\Application\Queries\GetAllUsersQuery;
use Modules\Core\User\Application\Queries\GetAllUsersHandler;
use Modules\CRM\Lead\Application\Queries\DownloadLeadTemplateQuery;
use Modules\CRM\Lead\Application\Queries\DownloadLeadTemplateHandler;
use Modules\CRM\Lead\Application\Imports\LeadsImport;
use Maatwebsite\Excel\Facades\Excel;
use Modules\CRM\Lead\Application\Exports\LeadsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Core\Helpers\PaginationHelper;
use Modules\CRM\Lead\Application\Commands\BulkUpdateLeadsCommand;
use Modules\CRM\Lead\Application\Commands\BulkUpdateLeadsHandler;
use Modules\CRM\LeadActivity\Application\Commands\AddLeadActivityCommand;
use Modules\CRM\LeadActivity\Application\Commands\AddLeadActivityHandler;
use Modules\CRM\LeadActivity\Application\Queries\GetLeadActivitiesQuery;
use Modules\CRM\LeadActivity\Application\Queries\GetLeadActivitiesHandler;
use Modules\CRM\LeadNote\Application\Commands\AddLeadNoteCommand;
use Modules\CRM\LeadNote\Application\Commands\AddLeadNoteHandler;
use Modules\CRM\LeadNote\Application\Queries\GetLeadNotesQuery;
use Modules\CRM\LeadNote\Application\Queries\GetLeadNotesHandler;
use Modules\CRM\Lead\Infrastructure\ReadModels\LeadReadModel;

class LeadWebController extends Controller
{
    public function __construct(
        private readonly \Modules\CRM\LeadStatus\Domain\LeadStatusRepositoryInterface $statusRepository,
        private readonly \Modules\CRM\LeadTag\Domain\LeadTagRepositoryInterface $tagRepository
    ) {}

    /**
     * Lead Detail Page
     */
    public function show(
        string $id,
        GetActiveCentersHandler $centersHandler,
        GetLeadSourcesHandler $leadSourcesHandler,
        GetInterestTypesHandler $interestTypesHandler,
        GetCampaignsHandler $campaignsHandler,
        GetAllUsersHandler $usersHandler,
        GetLeadActivitiesHandler $activitiesHandler,
        GetLeadNotesHandler $notesHandler
    ) {
        $lead = LeadReadModel::with([
            'leadSource', 'interestType', 'assignTo', 'center', 'leadStatus', 'tags',
            'assignments.assignedToUser', 'assignments.assignedByUser'
        ])->find($id);

        if (!$lead) {
            return redirect()->route('admin.leads.index')->with('error', 'Lead not found.');
        }

        $activities = $activitiesHandler->handle(new GetLeadActivitiesQuery($id, 50));
        $notes = $notesHandler->handle(new GetLeadNotesQuery($id, 50));

        $centers = $centersHandler->handle(new GetActiveCentersQuery());
        $leadSources = $leadSourcesHandler->handle(new GetLeadSourcesQuery(null, true));
        $interestTypes = $interestTypesHandler->handle(new GetInterestTypesQuery(null, true));
        $campaigns = $campaignsHandler->handle(new GetCampaignsQuery(null, true));
        $users = $usersHandler->handle(new GetAllUsersQuery());
        $statuses = $this->statusRepository->getAllActive();
        $allTags = $this->tagRepository->getAll();

        $isGlobalScope = false;
        try { $isGlobalScope = app('is_global_scope'); } catch (\Exception $e) {}

        return view('lead::detail', compact(
            'lead', 'activities', 'notes',
            'centers', 'leadSources', 'interestTypes', 'campaigns', 'users', 'statuses', 'allTags',
            'isGlobalScope'
        ));
    }

    /**
     * Store a note for a lead
     */
    public function storeNote(Request $request, string $id, AddLeadNoteHandler $handler)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $handler->handle(new AddLeadNoteCommand(
            $id,
            $request->input('content'),
            auth()->id()
        ));

        return redirect()->route('admin.leads.show', $id)->with('success', 'Ghi chú đã được thêm.');
    }

    /**
     * Store an activity for a lead
     */
    public function storeActivity(Request $request, string $id, AddLeadActivityHandler $handler)
    {
        $request->validate([
            'activity_type' => 'required|string|in:call,meeting,sms,email',
            'description' => 'nullable|string|max:5000',
        ]);

        $handler->handle(new AddLeadActivityCommand(
            $id,
            $request->input('activity_type'),
            $request->input('description'),
            auth()->id()
        ));

        return redirect()->route('admin.leads.show', $id)->with('success', 'Hoạt động đã được ghi nhận.');
    }

    public function index(
        Request $request, 
        GetLeadsPaginatedHandler $handler, 
        GetActiveCentersHandler $centersHandler,
        GetLeadSourcesHandler $leadSourcesHandler,
        GetInterestTypesHandler $interestTypesHandler,
        GetCampaignsHandler $campaignsHandler,
        GetAllUsersHandler $usersHandler
    ) {
        $perPage = PaginationHelper::resolvePerPage((int) $request->query('per_page'));
        $page = (int) $request->query('page', 1);

        $search = $request->query('search');
        $phone = $request->query('phone');
        $centerId = $request->query('center_id');
        $statusId = $request->query('status_id');
        
        $sortBy = $request->query('sort_by');
        $sortDir = PaginationHelper::resolveSortDirection($request->query('sort_dir'));

        $query = new GetLeadsPaginatedQuery($perPage, $page, $search, $phone, $centerId, $statusId, $sortBy, $sortDir);
        $leads = $handler->handle($query);

        $centers = $centersHandler->handle(new GetActiveCentersQuery());
        $leadSources = $leadSourcesHandler->handle(new GetLeadSourcesQuery(null, true));
        $interestTypes = $interestTypesHandler->handle(new GetInterestTypesQuery(null, true));
        $campaigns = $campaignsHandler->handle(new GetCampaignsQuery(null, true));
        $users = $usersHandler->handle(new GetAllUsersQuery());
        $statuses = $this->statusRepository->getAllActive();
        $allTags = $this->tagRepository->getAll();

        $isGlobalScope = false;
        try { $isGlobalScope = app('is_global_scope'); } catch (\Exception $e) {}

        return view('lead::index', compact(
            'leads', 'centers', 'leadSources', 'interestTypes', 'campaigns', 'users', 'statuses', 'allTags',
            'isGlobalScope', 'search', 'phone', 'centerId', 'statusId',
            'sortBy', 'sortDir', 'perPage'
        ));
    }

    public function export(
        Request $request, 
        GetLeadsPaginatedHandler $handler,
        GetActiveCentersHandler $centersHandler,
        GetAllUsersHandler $usersHandler
    ) {
        $search = $request->query('search');
        $phone = $request->query('phone');
        $centerId = $request->query('center_id');
        $statusId = $request->query('status_id');

        $allLeads = collect();
        $page = 1;
        $perPage = 1000;

        do {
            $query = new GetLeadsPaginatedQuery($perPage, $page, $search, $phone, $centerId, $statusId);
            $paginator = $handler->handle($query);
            
            $items = \Illuminate\Database\Eloquent\Collection::make($paginator->items());
            if ($items->isNotEmpty()) {
                $items->loadMissing(['leadSource', 'interestType', 'leadStatus']);
                $allLeads = $allLeads->merge($items);
            }
            
            $page++;
        } while ($paginator->hasMorePages());

        $format = $request->query('format', 'excel');

        $centers = $centersHandler->handle(new GetActiveCentersQuery());
        $users = $usersHandler->handle(new GetAllUsersQuery());

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('lead::exports.pdf', [
                'leads' => $allLeads,
                'centers' => $centers,
                'users' => $users
            ])->setPaper('a4', 'landscape');
            
            return $pdf->download('leads.pdf');
        }

        return Excel::download(new LeadsExport($allLeads, $centers, $users), 'leads.xlsx');
    }

    public function store(Request $request, CreateLeadHandler $handler)
    {
        $isGlobalScope = false;
        try { $isGlobalScope = app('is_global_scope'); } catch (\Exception $e) {}

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date',
            'lead_source_id' => 'nullable|uuid|exists:lead_sources,id',
            'campaign_id' => 'nullable|uuid|exists:campaigns,id',
            'interest_type_id' => 'nullable|uuid|exists:interest_types,id',
            'assigned_to' => 'nullable|uuid|exists:users,id',
            'status_id' => 'nullable|uuid|exists:lead_statuses,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'uuid|exists:lead_tags,id',
        ];

        // Only users with Global Scope can manually choose center
        if ($isGlobalScope) {
            $rules['center_id'] = 'required|uuid|exists:centers,id';
        }

        $validated = $request->validate($rules);

        // Auto-fill center_id from session context for normal users
        $centerId = $isGlobalScope
            ? ($validated['center_id'] ?? null)
            : (session('current_center_id') ?? app('center_id'));

        $command = new CreateLeadCommand(
            $validated['name'],
            $validated['phone'],
            $validated['email'] ?? null,
            $centerId,
            $validated['dob'] ?? null,
            $validated['lead_source_id'] ?? null,
            $validated['campaign_id'] ?? null,
            $validated['interest_type_id'] ?? null,
            $validated['assigned_to'] ?? null,
            $validated['status_id'] ?? null,
            $validated['tag_ids'] ?? [],
            auth()->id()
        );

        $handler->handle($command);

        return redirect()->route('admin.leads.index')->with('success', 'Lead created successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            $array = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray, \Maatwebsite\Excel\Concerns\WithHeadingRow {
                public function array(array $array) {}
            }, $request->file('file'));
            
            $rows = $array[0] ?? []; 
            if (empty($rows)) {
                return response()->json(['error' => 'File rỗng hoặc không có dữ liệu (Kiểm tra lại Sheet 1).'], 400);
            }
            
            $importId = (string) \Illuminate\Support\Str::uuid();
            \Illuminate\Support\Facades\Cache::put('lead_import_' . $importId, $rows, now()->addHours(1));
            
            return response()->json([
                'import_id' => $importId,
                'total' => count($rows)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi đọc file Excel: ' . $e->getMessage()], 400);
        }
    }

    public function importProcess(Request $request, \Modules\CRM\Lead\Application\Commands\ImportLeadHandler $handler)
    {
        $importId = $request->input('import_id');
        $offset = (int) $request->input('offset', 0);
        $limit = (int) $request->input('limit', 10);
        
        $rows = \Illuminate\Support\Facades\Cache::get('lead_import_' . $importId);
        if (!$rows) {
            return response()->json(['error' => 'Tiến trình đã hết hạn hoặc không tìm thấy, vui lòng upload lại file.'], 400);
        }
        
        $total = count($rows);
        $chunk = array_slice($rows, $offset, $limit);
        
        $successCount = 0;
        $errorCount = 0;
        $logs = [];
        
        foreach ($chunk as $index => $row) {
            $currentRow = $offset + $index + 2; // +1 header, +1 for 0-based to 1-based indexing
            
            try {
                $normalizedRow = [];
                foreach ($row as $k => $v) {
                    $normalizedRow[strtolower(trim((string)$k))] = is_string($v) ? trim($v) : $v;
                }
                
                if (empty($normalizedRow['center_code'])) {
                    throw new \Exception("Thiếu cột bắt buộc (center_code)");
                }
                
                $command = new \Modules\CRM\Lead\Application\Commands\ImportLeadCommand(
                    (string)($normalizedRow['name'] ?? ''),
                    (string)($normalizedRow['phone'] ?? ''),
                    empty($normalizedRow['email']) ? null : (string)$normalizedRow['email'],
                    empty($normalizedRow['center_code']) ? null : (string)$normalizedRow['center_code'],
                    empty($normalizedRow['dob']) ? null : (string)$normalizedRow['dob'],
                    empty($normalizedRow['lead_source_code']) ? null : (string)$normalizedRow['lead_source_code'],
                    empty($normalizedRow['campaign_code']) ? null : (string)$normalizedRow['campaign_code'],
                    empty($normalizedRow['interest_type_code']) ? null : (string)$normalizedRow['interest_type_code']
                );
                
                $handler->handle($command);
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $logs[] = "Dòng [{$currentRow}]: " . $e->getMessage();
            }
        }
        
        return response()->json([
            'success_count' => $successCount,
            'error_count'   => $errorCount,
            'logs'          => $logs,
            'next_offset'   => $offset + $limit,
            'is_finished'   => ($offset + $limit >= $total)
        ]);
    }

    public function downloadTemplate(DownloadLeadTemplateHandler $handler)
    {
        return $handler->handle(new DownloadLeadTemplateQuery());
    }

    public function update(Request $request, string $id, UpdateLeadHandler $handler)
    {
        $isGlobalScope = false;
        try { $isGlobalScope = app('is_global_scope'); } catch (\Exception $e) {}

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'status_id' => 'required|uuid|exists:lead_statuses,id',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date',
            'lead_source_id' => 'nullable|uuid|exists:lead_sources,id',
            'campaign_id' => 'nullable|uuid|exists:campaigns,id',
            'interest_type_id' => 'nullable|uuid|exists:interest_types,id',
            'assigned_to' => 'nullable|uuid|exists:users,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'uuid|exists:lead_tags,id',
        ];

        if ($isGlobalScope) {
            $rules['center_id'] = 'required|uuid|exists:centers,id';
        }

        $validated = $request->validate($rules);

        $centerId = $isGlobalScope
            ? ($validated['center_id'] ?? null)
            : (session('current_center_id') ?? app('center_id'));

        try {
            $command = new UpdateLeadCommand(
                $id,
                $validated['name'],
                $validated['phone'],
                $validated['status_id'],
                $validated['email'] ?? null,
                $centerId,
                $validated['dob'] ?? null,
                $validated['lead_source_id'] ?? null,
                $validated['campaign_id'] ?? null,
                $validated['interest_type_id'] ?? null,
                $validated['assigned_to'] ?? null,
                $validated['tag_ids'] ?? [],
                auth()->id()
            );

            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.leads.index')->with('error', 'Lead not found or update failed.');
        }

        return redirect()->route('admin.leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(string $id, DeleteLeadHandler $handler)
    {
        try {
            $command = new DeleteLeadCommand($id);
            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.leads.index')->with('error', 'Lead not found.');
        }

        return redirect()->route('admin.leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function assign(Request $request, \Modules\CRM\Lead\Application\Commands\AssignLeadHandler $handler)
    {
        $request->validate([
            'lead_ids' => 'required|array',
            'lead_ids.*' => 'required|uuid',
            'assigned_to' => 'nullable|uuid'
        ]);

        $command = new \Modules\CRM\Lead\Application\Commands\AssignLeadCommand(
            $request->input('lead_ids'),
            $request->input('assigned_to'),
            auth()->id()
        );

        $handler->handle($command);

        return redirect()->back()->with('success', count($request->input('lead_ids')) . ' leads assigned successfully.');
    }

    public function merge(Request $request, \Modules\CRM\Lead\Application\Commands\MergeLeadsHandler $handler)
    {
        $request->validate([
            'master_lead_id' => 'required|uuid',
            'slave_lead_ids' => 'required|array',
            'slave_lead_ids.*' => 'required|uuid'
        ]);

        $command = new \Modules\CRM\Lead\Application\Commands\MergeLeadsCommand(
            $request->input('master_lead_id'),
            $request->input('slave_lead_ids')
        );

        try {
            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', count($request->input('slave_lead_ids')) . ' leads merged successfully.');
    }

    public function bulkUpdate(Request $request, BulkUpdateLeadsHandler $handler)
    {
        $request->validate([
            'lead_ids' => 'required|array',
            'lead_ids.*' => 'required|uuid',
            'lead_source_id' => 'nullable',
            'interest_type_id' => 'nullable',
            'center_id' => 'nullable',
            'assigned_to' => 'nullable',
            'campaign_id' => 'nullable',
            'status_id' => 'nullable|uuid|exists:lead_statuses,id',
        ]);

        $leadIds = $request->input('lead_ids');
        
        // We only update fields that are provided in the request as non-empty strings
        // Empty string means "Keep original"
        $command = new BulkUpdateLeadsCommand(
            $leadIds,
            $request->input('lead_source_id') !== '' ? $request->input('lead_source_id') : null,
            $request->input('interest_type_id') !== '' ? $request->input('interest_type_id') : null,
            $request->input('center_id') !== '' ? $request->input('center_id') : null,
            $request->input('assigned_to') !== '' ? $request->input('assigned_to') : null,
            $request->input('campaign_id') !== '' ? $request->input('campaign_id') : null,
            $request->input('status_id') !== '' ? $request->input('status_id') : null,
        );

        $handler->handle($command);

        return redirect()->back()->with('success', count($leadIds) . ' leads updated successfully.');
    }
}

