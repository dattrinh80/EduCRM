<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Presentation\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Task\Application\Commands\CreateTaskCommand;
use Modules\CRM\Task\Application\Commands\CreateTaskHandler;
use Modules\CRM\Task\Application\Commands\UpdateTaskCommand;
use Modules\CRM\Task\Application\Commands\UpdateTaskHandler;
use Modules\CRM\Task\Application\Queries\GetTasksPaginatedQuery;
use Modules\CRM\Task\Application\Queries\GetTasksPaginatedHandler;
use Modules\CRM\Task\Infrastructure\ReadModels\TaskReadModel;
use Modules\Core\Center\Application\Queries\GetActiveCentersQuery;
use Modules\Core\Center\Application\Queries\GetActiveCentersHandler;
use Modules\Core\User\Application\Queries\GetAllUsersQuery;
use Modules\Core\User\Application\Queries\GetAllUsersHandler;

class TaskWebController extends Controller
{
    public function index(
        Request $request,
        GetTasksPaginatedHandler $handler,
        GetActiveCentersHandler $centersHandler,
        GetAllUsersHandler $usersHandler
    ) {
        $perPage = (int) $request->query('per_page', 20);
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
        ]);

        try {
            $handler->handle(new UpdateTaskCommand(
                $id,
                $request->title,
                $request->description,
                $request->due_date,
                $request->status,
                $request->priority,
                $request->assigned_to
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

    public function toggleStatus(string $id, TaskReadModel $readModel)
    {
        $taskModel = $readModel->find($id);
        if (!$taskModel) {
            return response()->json(['success' => false, 'message' => 'Task not found'], 404);
        }

        $newStatus = ($taskModel->status === 'DONE') ? 'TODO' : 'DONE';
        $taskModel->update(['status' => $newStatus]);

        return response()->json([
            'success' => true, 
            'message' => 'Trạng thái đã được cập nhật.',
            'new_status' => $newStatus
        ]);
    }
}
