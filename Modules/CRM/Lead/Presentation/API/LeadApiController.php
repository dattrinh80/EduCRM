<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
use Modules\CRM\Lead\LeadActivity\Application\Commands\AddLeadActivityCommand;
use Modules\CRM\Lead\LeadActivity\Application\Commands\AddLeadActivityHandler;
use Modules\CRM\Lead\LeadActivity\Application\Queries\GetLeadActivitiesQuery;
use Modules\CRM\Lead\LeadActivity\Application\Queries\GetLeadActivitiesHandler;
use Modules\CRM\Lead\LeadNote\Application\Commands\AddLeadNoteCommand;
use Modules\CRM\Lead\LeadNote\Application\Commands\AddLeadNoteHandler;
use Modules\CRM\Lead\LeadNote\Application\Queries\GetLeadNotesQuery;
use Modules\CRM\Lead\LeadNote\Application\Queries\GetLeadNotesHandler;
use Illuminate\Validation\ValidationException;

class LeadApiController extends Controller
{
    public function index(Request $request, GetLeadsPaginatedHandler $handler): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);

        $query = new GetLeadsPaginatedQuery($perPage, $page);
        $leads = $handler->handle($query);

        return response()->json([
            'success' => true,
            'data' => $leads->items(),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total()
            ]
        ]);
    }

    public function show(string $id, GetLeadByIdHandler $handler): JsonResponse
    {
        $query = new GetLeadByIdQuery($id);
        $lead = $handler->handle($query);

        if (!$lead) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'LEAD_NOT_FOUND',
                    'message' => 'Lead not found'
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $lead
        ]);
    }

    public function store(Request $request, CreateLeadHandler $handler): JsonResponse
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
            null, // statusId (will be default 'New' in handler)
            $validated['tag_ids'] ?? [],
            auth()->id()
        );

        $lead = $handler->handle($command);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $lead->getId()
            ]
        ], 201);
    }

    public function update(Request $request, string $id, UpdateLeadHandler $handler): JsonResponse
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
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'LEAD_NOT_FOUND',
                    'message' => $e->getMessage()
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }

    public function destroy(string $id, DeleteLeadHandler $handler): JsonResponse
    {
        try {
            $command = new DeleteLeadCommand($id);
            $handler->handle($command);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'LEAD_NOT_FOUND',
                    'message' => $e->getMessage()
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }

    // ─── Lead Notes ─────────────────────────────────────────

    public function getNotes(Request $request, string $id, GetLeadNotesHandler $handler): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 20);
        $page = (int) $request->query('page', 1);

        $notes = $handler->handle(new GetLeadNotesQuery($id, $perPage, $page));

        return response()->json([
            'success' => true,
            'data' => $notes->items(),
            'meta' => [
                'current_page' => $notes->currentPage(),
                'last_page' => $notes->lastPage(),
                'per_page' => $notes->perPage(),
                'total' => $notes->total()
            ]
        ]);
    }

    public function storeNote(Request $request, string $id, AddLeadNoteHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $note = $handler->handle(new AddLeadNoteCommand(
            $id,
            $validated['content'],
            auth()->id()
        ));

        return response()->json([
            'success' => true,
            'data' => ['id' => $note->getId()]
        ], 201);
    }

    // ─── Lead Activities ────────────────────────────────────

    public function getActivities(Request $request, string $id, GetLeadActivitiesHandler $handler): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 20);
        $page = (int) $request->query('page', 1);

        $activities = $handler->handle(new GetLeadActivitiesQuery($id, $perPage, $page));

        return response()->json([
            'success' => true,
            'data' => $activities->items(),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total()
            ]
        ]);
    }

    public function storeActivity(Request $request, string $id, AddLeadActivityHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'activity_type' => 'required|string|in:call,meeting,sms,email',
            'description' => 'nullable|string|max:5000',
        ]);

        $activity = $handler->handle(new AddLeadActivityCommand(
            $id,
            $validated['activity_type'],
            $validated['description'] ?? null,
            auth()->id()
        ));

        return response()->json([
            'success' => true,
            'data' => ['id' => $activity->getId()]
        ], 201);
    }
}
