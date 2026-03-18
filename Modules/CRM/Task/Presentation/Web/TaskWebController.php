<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Presentation\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Core\Helpers\PaginationHelper;
use Modules\CRM\Task\Application\Commands\CreateTaskCommand;
use Modules\CRM\Task\Application\Commands\CreateTaskHandler;
use Modules\CRM\Task\Application\Commands\UpdateTaskCommand;
use Modules\CRM\Task\Application\Commands\UpdateTaskHandler;
use Modules\CRM\Task\Application\Queries\GetTasksPaginatedQuery;
use Modules\CRM\Task\Application\Queries\GetTasksPaginatedHandler;
use Modules\Core\Center\Application\Queries\GetActiveCentersQuery;
use Modules\Core\Center\Application\Queries\GetActiveCentersHandler;
use Modules\Core\User\Application\Queries\GetAllUsersQuery;
use Modules\Core\User\Application\Queries\GetAllUsersHandler;
use Modules\CRM\Task\Application\Queries\GetTaskByIdQuery;
use Modules\CRM\Task\Application\Queries\GetTaskByIdHandler;
use Modules\CRM\Task\Application\Commands\ToggleTaskStatusCommand;
use Modules\CRM\Task\Application\Commands\ToggleTaskStatusHandler;
use Modules\CRM\Task\Application\Commands\DeleteTaskCommand;
use Modules\CRM\Task\Application\Commands\DeleteTaskHandler;
use Modules\CRM\Task\Application\Queries\SearchTaskRelationsQuery;
use Modules\CRM\Task\Application\Queries\SearchTaskRelationsHandler;

class TaskWebController extends Controller
{
    public function index(
        Request $request,
        GetTasksPaginatedHandler $handler,
        GetActiveCentersHandler $centersHandler,
        GetAllUsersHandler $usersHandler
    ) {
        $perPage = PaginationHelper::resolvePerPage((int) $request->query('per_page'));
        $page = (int) $request->query('page', 1);
        $search = $request->query('search');
        $status = $request->query('status');
        $priority = $request->query('priority');
        $assignedTo = $request->query('assigned_to');
        $centerId = $request->query('center_id');

        $query = new GetTasksPaginatedQuery(
            $perPage, $page, $search, $status, $priority, $assignedTo, $centerId
        );
        $tasks = $handler->handle($query);

        $centers = $centersHandler->handle(new GetActiveCentersQuery());
        $users = $usersHandler->handle(new GetAllUsersQuery());

        $isGlobalScope = false;
        try { $isGlobalScope = app('is_global_scope'); } catch (\Exception $e) {}

        return view('task::index', compact(
            'tasks', 'centers', 'users', 'isGlobalScope',
            'search', 'status', 'priority', 'assignedTo', 'centerId'
        ));
    }

    public function store(Request $request, CreateTaskHandler $handler)
    {
        $isGlobalScope = app('is_global_scope');
        
        $rules = [
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'priority' => 'required|string',
            'assigned_to' => 'nullable|uuid',
        ];

        if ($isGlobalScope) {
            $rules['center_id'] = 'required|uuid';
        }

        $request->validate($rules);

        $centerId = $isGlobalScope 
            ? $request->center_id 
            : (session('current_center_id') ?? app('center_id'));

        try {
            $handler->handle(new CreateTaskCommand(
                $request->title,
                $request->description,
                $request->due_date,
                $request->priority,
                $request->assigned_to,
                (string) auth()->id(),
                $centerId,
                $request->start_date,
                $request->relation_id,
                $request->relation_type
            ));

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Nhiệm vụ đã được tạo thành công.']);
            }

            return redirect()->back()->with('success', 'Nhiệm vụ đã được tạo thành công.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getStaffByCenter(string $centerId, GetAllUsersHandler $usersHandler)
    {
        $users = $usersHandler->handle(new GetAllUsersQuery($centerId));
        return response()->json($users);
    }

    public function update(Request $request, string $id, UpdateTaskHandler $handler)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|string',
            'priority' => 'required|string',
            'start_date' => 'nullable|date',
        ]);

        try {
            $handler->handle(new UpdateTaskCommand(
                $id,
                $request->title,
                $request->description,
                $request->due_date,
                $request->status,
                $request->priority,
                $request->assigned_to,
                $request->start_date
            ));

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Cập nhật nhiệm vụ thành công.']);
            }

            return redirect()->back()->with('success', 'Cập nhật nhiệm vụ thành công.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function toggleStatus(string $id, ToggleTaskStatusHandler $handler)
    {
        try {
            $task = $handler->handle(new ToggleTaskStatusCommand($id));
            
            return response()->json([
                'success' => true, 
                'message' => 'Trạng thái đã được cập nhật.',
                'new_status' => $task->status
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function show(string $id, GetTaskByIdHandler $handler, GetAllUsersHandler $usersHandler)
    {
        $task = $handler->handle(new GetTaskByIdQuery($id));
        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Nhiệm vụ không tồn tại.'], 404);
        }

        // Include staff members for the task's center to optimize frontend loading
        $staff = $usersHandler->handle(new GetAllUsersQuery($task->center_id));

        return response()->json([
            'task' => $task,
            'available_staff' => $staff
        ]);
    }

    public function destroy(string $id, DeleteTaskHandler $handler)
    {
        try {
            $handler->handle(new DeleteTaskCommand($id));
            return response()->json(['success' => true, 'message' => 'Nhiệm vụ đã được xóa.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function searchRelations(Request $request, SearchTaskRelationsHandler $handler)
    {
        $q = $request->query('q');
        $type = $request->query('type'); // Lead or Customer

        if (empty($q) || strlen($q) < 2) {
            return response()->json([]);
        }

        $results = $handler->handle(new SearchTaskRelationsQuery($q, $type));

        return response()->json($results);
    }
}
