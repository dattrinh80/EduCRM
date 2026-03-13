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
use Modules\Core\User\Application\Queries\GetAllUsersQuery;
use Modules\Core\User\Application\Queries\GetAllUsersHandler;
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
        \Modules\CRM\Task\Application\Queries\GetTasksPaginatedHandler $tasksHandler,
        GetAllUsersHandler $usersHandler
    ) {
        $customer = $customerHandler->handle(new \Modules\CRM\Customer\Application\Queries\GetCustomerByIdQuery($id, [
            'tags', 'studentGuardians.student.customer', 'studentProfile'
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

        $usersQueryCenterId = $isGlobalScope ? null : (app()->has('center_id') ? app('center_id') : null);
        $users = $usersHandler->handle(new GetAllUsersQuery($usersQueryCenterId));

        return view('customer::detail', compact(
            'customer', 'activities', 'notes', 'tasks',
            'centers', 'allTags', 'isGlobalScope', 'users'
        ));
    }

    public function export(
        Request $request, 
        \Modules\CRM\Customer\Application\Queries\ExportCustomersHandler $handler
    ) {
        $query = new \Modules\CRM\Customer\Application\Queries\ExportCustomersQuery(
            search: $request->query('search'),
            phone: $request->query('phone'),
            centerId: $request->query('center_id'),
            format: $request->query('format', 'excel')
        );

        $result = $handler->handle($query);

        if ($query->format === 'pdf') {
            return $result->download('customers.pdf');
        }

        return Excel::download($result, 'customers.xlsx');
    }

    public function import(Request $request, \Modules\CRM\Customer\Application\Commands\InitiateCustomerImportHandler $handler)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            $command = new \Modules\CRM\Customer\Application\Commands\InitiateCustomerImportCommand($request->file('file'));
            $result = $handler->handle($command);
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function importProcess(Request $request, \Modules\CRM\Customer\Application\Commands\ProcessCustomerImportChunkHandler $handler)
    {
        try {
            $command = new \Modules\CRM\Customer\Application\Commands\ProcessCustomerImportChunkCommand(
                $request->input('import_id'),
                (int) $request->input('offset', 0),
                (int) $request->input('limit', 10)
            );
            
            $result = $handler->handle($command);
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy(string $id, \Modules\CRM\Customer\Application\Commands\DeleteCustomerHandler $handler)
    {
        $handler->handle(new \Modules\CRM\Customer\Application\Commands\DeleteCustomerCommand($id));
        return redirect()->route('admin.customers.index')->with('success', 'Đã xoá khách hàng thành công.');
    }

    public function storeNote(Request $request, string $id, \Modules\CRM\CustomerNote\Application\Commands\AddCustomerNoteHandler $handler)
    {
        $request->validate(['content' => 'required|string|max:5000']);
        $handler->handle(new \Modules\CRM\CustomerNote\Application\Commands\AddCustomerNoteCommand($id, $request->input('content'), auth()->id()));
        return redirect()->route('admin.customers.show', $id)->with('success', 'Ghi chú đã được thêm.');
    }

    public function storeActivity(Request $request, string $id, \Modules\CRM\CustomerActivity\Application\Commands\AddCustomerActivityHandler $handler)
    {
        $request->validate(['activity_type' => 'required|string|in:call,meeting,sms,email', 'description' => 'nullable|string|max:5000']);
        $handler->handle(new \Modules\CRM\CustomerActivity\Application\Commands\AddCustomerActivityCommand($id, $request->input('activity_type'), $request->input('description'), auth()->id()));
        return redirect()->route('admin.customers.show', $id)->with('success', 'Hoạt động đã được ghi nhận.');
    }

    public function downloadTemplate(\Modules\CRM\Customer\Application\Queries\DownloadCustomerTemplateHandler $handler)
    {
        return $handler->handle(new \Modules\CRM\Customer\Application\Queries\DownloadCustomerTemplateQuery());
    }
}
