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
use Modules\CRM\Source\Application\Queries\GetSourcesQuery;
use Modules\CRM\Source\Application\Queries\GetSourcesHandler;
use Modules\CRM\InterestType\Application\Queries\GetInterestTypesQuery;
use Modules\CRM\InterestType\Application\Queries\GetInterestTypesHandler;
use Modules\CRM\Campaign\Application\Queries\GetCampaignsQuery;
use Modules\CRM\Campaign\Application\Queries\GetCampaignsHandler;
use Modules\Core\User\Application\Queries\GetAllUsersQuery;
use Modules\Core\User\Application\Queries\GetAllUsersHandler;
use Modules\CRM\Lead\Application\Queries\DownloadLeadTemplateQuery;
use Modules\CRM\Lead\Application\Queries\DownloadLeadTemplateHandler;
use Modules\CRM\Lead\Application\Imports\LeadsImport;

class LeadWebController extends Controller
{
    public function index(
        Request $request, 
        GetLeadsPaginatedHandler $handler, 
        GetActiveCentersHandler $centersHandler,
        GetSourcesHandler $sourcesHandler,
        GetInterestTypesHandler $interestTypesHandler,
        GetCampaignsHandler $campaignsHandler,
        GetAllUsersHandler $usersHandler
    ) {
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);

        $query = new GetLeadsPaginatedQuery($perPage, $page);
        $leads = $handler->handle($query);

        $centers = $centersHandler->handle(new GetActiveCentersQuery());
        $sources = $sourcesHandler->handle(new GetSourcesQuery(null, true));
        $interestTypes = $interestTypesHandler->handle(new GetInterestTypesQuery(null, true));
        $campaigns = $campaignsHandler->handle(new GetCampaignsQuery(null, true));
        $users = $usersHandler->handle(new GetAllUsersQuery());

        $isSuperAdmin = false;
        try { $isSuperAdmin = app('is_super_admin'); } catch (\Exception $e) {}

        return view('lead::index', compact('leads', 'centers', 'sources', 'interestTypes', 'campaigns', 'users', 'isSuperAdmin'));
    }

    public function store(Request $request, CreateLeadHandler $handler)
    {
        $isSuperAdmin = false;
        try { $isSuperAdmin = app('is_super_admin'); } catch (\Exception $e) {}

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date',
            'source_id' => 'nullable|uuid|exists:sources,id',
            'campaign_id' => 'nullable|uuid|exists:campaigns,id',
            'interest_type_id' => 'nullable|uuid|exists:interest_types,id',
            'assigned_to' => 'nullable|uuid|exists:users,id',
        ];

        // Only Super Admin can manually choose center
        if ($isSuperAdmin) {
            $rules['center_id'] = 'required|uuid|exists:centers,id';
        }

        $validated = $request->validate($rules);

        // Auto-fill center_id from session context for normal users
        $centerId = $isSuperAdmin
            ? ($validated['center_id'] ?? null)
            : (session('current_center_id') ?? app('center_id'));

        $command = new CreateLeadCommand(
            $validated['name'],
            $validated['phone'],
            $validated['email'] ?? null,
            $centerId,
            $validated['dob'] ?? null,
            $validated['source_id'] ?? null,
            $validated['campaign_id'] ?? null,
            $validated['interest_type_id'] ?? null,
            $validated['assigned_to'] ?? null
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
                    empty($normalizedRow['source_code']) ? null : (string)$normalizedRow['source_code'],
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
        $isSuperAdmin = false;
        try { $isSuperAdmin = app('is_super_admin'); } catch (\Exception $e) {}

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date',
            'source_id' => 'nullable|uuid|exists:sources,id',
            'campaign_id' => 'nullable|uuid|exists:campaigns,id',
            'interest_type_id' => 'nullable|uuid|exists:interest_types,id',
            'assigned_to' => 'nullable|uuid|exists:users,id',
        ];

        if ($isSuperAdmin) {
            $rules['center_id'] = 'required|uuid|exists:centers,id';
        }

        $validated = $request->validate($rules);

        $centerId = $isSuperAdmin
            ? ($validated['center_id'] ?? null)
            : (session('current_center_id') ?? app('center_id'));

        try {
            $command = new UpdateLeadCommand(
                $id,
                $validated['name'],
                $validated['phone'],
                $validated['status'],
                $validated['email'] ?? null,
                $centerId,
                $validated['dob'] ?? null,
                $validated['source_id'] ?? null,
                $validated['campaign_id'] ?? null,
                $validated['interest_type_id'] ?? null,
                $validated['assigned_to'] ?? null
            );

            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.leads.index')->with('error', 'Lead not found.');
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
            $request->input('assigned_to')
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
}
