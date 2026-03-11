<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Presentation\Web;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\CRM\Customer\Application\Queries\GetCustomersPaginatedQuery;
use Modules\CRM\Customer\Application\Queries\GetCustomersPaginatedHandler;
use Modules\CRM\Customer\Application\Commands\CreateCustomerCommand;
use Modules\CRM\Customer\Application\Commands\CreateCustomerHandler;
use Modules\CRM\Customer\Application\Commands\UpdateCustomerCommand;
use Modules\CRM\Customer\Application\Commands\UpdateCustomerHandler;
use Modules\Core\Center\Application\Queries\GetActiveCentersQuery;
use Modules\Core\Center\Application\Queries\GetActiveCentersHandler;
use Modules\CRM\CustomerTag\Application\Queries\GetCustomerTagsQuery;
use Modules\CRM\CustomerTag\Application\Queries\GetCustomerTagsHandler;
use App\Core\Helpers\PaginationHelper;
use Modules\CRM\Customer\Application\Exports\CustomersExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class CustomerWebController extends Controller
{
    public function index(
        Request $request, 
        GetCustomersPaginatedHandler $handler,
        GetActiveCentersHandler $centersHandler,
        GetCustomerTagsHandler $tagsHandler
    ) {
        $perPage = PaginationHelper::resolvePerPage((int) $request->query('per_page'));
        $page = (int) $request->query('page', 1);

        $search = $request->query('search');
        $phone = $request->query('phone');
        $centerId = $request->query('center_id');
        
        $sortBy = $request->query('sort_by');
        $sortDir = PaginationHelper::resolveSortDirection($request->query('sort_dir'));

        $query = new GetCustomersPaginatedQuery($perPage, $page, $search, $phone, $centerId, $sortBy, $sortDir);
        $customers = $handler->handle($query);

        $isGlobalScope = false;
        try { $isGlobalScope = app('is_global_scope'); } catch (\Exception $e) {}

        $centers = $centersHandler->handle(new GetActiveCentersQuery());
        $allTags = $tagsHandler->handle(new GetCustomerTagsQuery());

        return view('customer::index', compact(
            'customers', 'centers', 'allTags', 'isGlobalScope',
            'search', 'phone', 'centerId', 'sortBy', 'sortDir', 'perPage'
        ));
    }

    public function store(Request $request, CreateCustomerHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:MALE,FEMALE,OTHER',
            'address' => 'nullable|string',
            'center_id' => 'nullable|uuid|exists:centers,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'uuid|exists:customer_tags,id',
        ]);

        $command = new CreateCustomerCommand(
            $validated['name'],
            $validated['phone'] ?? null,
            $validated['email'] ?? null,
            $validated['dob'] ?? null,
            $validated['gender'] ?? null,
            $validated['address'] ?? null,
            $validated['center_id'] ?? null,
            $validated['tag_ids'] ?? []
        );

        $handler->handle($command);

        return redirect()->route('admin.customers.index')->with('success', 'Khách hàng đã được tạo.');
    }

    public function update(Request $request, string $id, UpdateCustomerHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:MALE,FEMALE,OTHER',
            'address' => 'nullable|string',
            'center_id' => 'nullable|uuid|exists:centers,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'uuid|exists:customer_tags,id',
        ]);

        $command = new UpdateCustomerCommand(
            $id,
            $validated['name'],
            $validated['phone'] ?? null,
            $validated['email'] ?? null,
            $validated['dob'] ?? null,
            $validated['gender'] ?? null,
            $validated['address'] ?? null,
            $validated['center_id'] ?? null,
            $validated['tag_ids'] ?? []
        );

        $handler->handle($command);

        return redirect()->route('admin.customers.index')->with('success', 'Khách hàng đã được cập nhật.');
    }

    public function show(
        string $id,
        \Modules\CRM\Customer\Application\Queries\GetCustomerByIdHandler $customerHandler,
        GetActiveCentersHandler $centersHandler,
        GetCustomerTagsHandler $tagsHandler,
        \Modules\CRM\CustomerActivity\Application\Queries\GetCustomerActivitiesHandler $activitiesHandler,
        \Modules\CRM\CustomerNote\Application\Queries\GetCustomerNotesHandler $notesHandler,
        \Modules\CRM\Task\Application\Queries\GetTasksPaginatedHandler $tasksHandler
    ) {
        $customer = $customerHandler->handle(new \Modules\CRM\Customer\Application\Queries\GetCustomerByIdQuery($id, [
            'tags', 'studentGuardians.student'
        ]));

        if (!$customer) {
            return redirect()->route('admin.customers.index')->with('error', 'Khách hàng không tồn tại.');
        }

        $activities = $activitiesHandler->handle(new \Modules\CRM\CustomerActivity\Application\Queries\GetCustomerActivitiesQuery($id, 50));
        $notes = $notesHandler->handle(new \Modules\CRM\CustomerNote\Application\Queries\GetCustomerNotesQuery($id, 50));

        $isGlobalScope = false;
        try { $isGlobalScope = app('is_global_scope'); } catch (\Exception $e) {}

        $centers = $centersHandler->handle(new GetActiveCentersQuery());
        $allTags = $tagsHandler->handle(new GetCustomerTagsQuery());
        
        $tasks = $tasksHandler->handle(new \Modules\CRM\Task\Application\Queries\GetTasksPaginatedQuery(
            perPage: 100,
            relationId: $id,
            relationType: 'Customer'
        ));

        return view('customer::detail', compact(
            'customer', 'activities', 'notes', 'tasks',
            'centers', 'allTags', 'isGlobalScope'
        ));
    }

    public function export(
        Request $request, 
        GetCustomersPaginatedHandler $handler,
        GetActiveCentersHandler $centersHandler
    ) {
        $search = $request->query('search');
        $phone = $request->query('phone');
        $centerId = $request->query('center_id');

        $allCustomers = collect();
        $page = 1;
        $perPage = 1000;

        do {
            $query = new GetCustomersPaginatedQuery($perPage, $page, $search, $phone, $centerId);
            $paginator = $handler->handle($query);
            
            $items = \Illuminate\Database\Eloquent\Collection::make($paginator->items());
            if ($items->isNotEmpty()) {
                $allCustomers = $allCustomers->merge($items);
            }
            
            $page++;
        } while ($paginator->hasMorePages());

        $format = $request->query('format', 'excel');
        $centers = $centersHandler->handle(new GetActiveCentersQuery())->pluck('name', 'id')->toArray();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('customer::exports.pdf', [
                'customers' => $allCustomers,
                'centers' => $centers
            ])->setPaper('a4', 'landscape');
            
            return $pdf->download('customers.pdf');
        }

        return Excel::download(new CustomersExport($allCustomers, $centers), 'customers.xlsx');
    }

    public function destroy(string $id, \Modules\CRM\Customer\Application\Commands\DeleteCustomerHandler $handler)
    {
        try {
            $command = new \Modules\CRM\Customer\Application\Commands\DeleteCustomerCommand($id);
            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.customers.index')->with('error', 'Khách hàng không tồn tại.');
        }

        return redirect()->route('admin.customers.index')->with('success', 'Đã xoá khách hàng thành công.');
    }

    public function storeNote(Request $request, string $id, \Modules\CRM\CustomerNote\Application\Commands\AddCustomerNoteHandler $handler)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $handler->handle(new \Modules\CRM\CustomerNote\Application\Commands\AddCustomerNoteCommand(
            $id,
            $request->input('content'),
            auth()->id()
        ));

        return redirect()->route('admin.customers.show', $id)->with('success', 'Ghi chú đã được thêm.');
    }

    public function storeActivity(Request $request, string $id, \Modules\CRM\CustomerActivity\Application\Commands\AddCustomerActivityHandler $handler)
    {
        $request->validate([
            'activity_type' => 'required|string|in:call,meeting,sms,email',
            'description' => 'nullable|string|max:5000',
        ]);

        $handler->handle(new \Modules\CRM\CustomerActivity\Application\Commands\AddCustomerActivityCommand(
            $id,
            $request->input('activity_type'),
            $request->input('description'),
            auth()->id()
        ));

        return redirect()->route('admin.customers.show', $id)->with('success', 'Hoạt động đã được ghi nhận.');
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
            \Illuminate\Support\Facades\Cache::put('customer_import_' . $importId, $rows, now()->addHours(1));
            
            return response()->json([
                'import_id' => $importId,
                'total' => count($rows)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi đọc file Excel: ' . $e->getMessage()], 400);
        }
    }

    public function importProcess(Request $request, \Modules\CRM\Customer\Application\Commands\ImportCustomerHandler $handler)
    {
        $importId = $request->input('import_id');
        $offset = (int) $request->input('offset', 0);
        $limit = (int) $request->input('limit', 10);
        
        $rows = \Illuminate\Support\Facades\Cache::get('customer_import_' . $importId);
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
                
                $command = new \Modules\CRM\Customer\Application\Commands\ImportCustomerCommand(
                    (string)($normalizedRow['name'] ?? ''),
                    (string)($normalizedRow['phone'] ?? ''),
                    empty($normalizedRow['email']) ? null : (string)$normalizedRow['email'],
                    empty($normalizedRow['center_code']) ? null : (string)$normalizedRow['center_code'],
                    empty($normalizedRow['dob']) ? null : (string)$normalizedRow['dob'],
                    empty($normalizedRow['gender']) ? null : (string)$normalizedRow['gender'],
                    empty($normalizedRow['address']) ? null : (string)$normalizedRow['address']
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

    public function downloadTemplate(\Modules\CRM\Customer\Application\Queries\DownloadCustomerTemplateHandler $handler)
    {
        return $handler->handle(new \Modules\CRM\Customer\Application\Queries\DownloadCustomerTemplateQuery());
    }
}
