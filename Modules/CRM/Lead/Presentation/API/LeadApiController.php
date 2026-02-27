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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'center_id' => 'required|uuid|exists:centers,id'
        ]);

        $command = new CreateLeadCommand(
            $validated['name'],
            $validated['phone'],
            $validated['email'] ?? null,
            $validated['center_id'] ?? null
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'center_id' => 'required|uuid|exists:centers,id'
        ]);

        try {
            $command = new UpdateLeadCommand(
                $id,
                $validated['name'],
                $validated['phone'],
                $validated['status'],
                $validated['email'] ?? null,
                $validated['center_id'] ?? null
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
}
