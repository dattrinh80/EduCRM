<?php

declare(strict_types=1);

namespace Modules\Core\Center\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Center\Application\Commands\CreateCenterCommand;
use Modules\Core\Center\Application\Commands\CreateCenterHandler;
use Modules\Core\Center\Application\Commands\UpdateCenterCommand;
use Modules\Core\Center\Application\Commands\UpdateCenterHandler;
use Modules\Core\Center\Application\Commands\DeleteCenterCommand;
use Modules\Core\Center\Application\Commands\DeleteCenterHandler;
use Modules\Core\Center\Application\Queries\GetCenterByIdQuery;
use Modules\Core\Center\Application\Queries\GetCenterByIdHandler;
use Modules\Core\Center\Application\Queries\GetCentersPaginatedQuery;
use Modules\Core\Center\Application\Queries\GetCentersPaginatedHandler;

class CenterApiController extends Controller
{
    public function index(Request $request, GetCentersPaginatedHandler $handler): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);

        $query = new GetCentersPaginatedQuery($perPage, $page);
        $centers = $handler->handle($query);

        return response()->json([
            'success' => true,
            'data' => $centers->items(),
            'meta' => [
                'current_page' => $centers->currentPage(),
                'last_page' => $centers->lastPage(),
                'per_page' => $centers->perPage(),
                'total' => $centers->total()
            ]
        ]);
    }

    public function show(string $id, GetCenterByIdHandler $handler): JsonResponse
    {
        $query = new GetCenterByIdQuery($id);
        $center = $handler->handle($query);

        if (!$center) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'CENTER_NOT_FOUND',
                    'message' => 'Center not found'
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $center
        ]);
    }

    public function store(Request $request, CreateCenterHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:centers,code',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500'
        ]);

        $command = new CreateCenterCommand(
            $validated['name'],
            $validated['code'],
            $validated['phone'] ?? null,
            $validated['email'] ?? null,
            $validated['address'] ?? null
        );

        $center = $handler->handle($command);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $center->getId()
            ]
        ], 201);
    }

    public function update(Request $request, string $id, UpdateCenterHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:centers,code,' . $id,
            'status' => 'required|string|in:active,inactive',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500'
        ]);

        try {
            $command = new UpdateCenterCommand(
                $id,
                $validated['name'],
                $validated['code'],
                $validated['status'],
                $validated['phone'] ?? null,
                $validated['email'] ?? null,
                $validated['address'] ?? null
            );

            $handler->handle($command);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'CENTER_NOT_FOUND',
                    'message' => $e->getMessage()
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }

    public function destroy(string $id, DeleteCenterHandler $handler): JsonResponse
    {
        try {
            $command = new DeleteCenterCommand($id);
            $handler->handle($command);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'CENTER_NOT_FOUND',
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
