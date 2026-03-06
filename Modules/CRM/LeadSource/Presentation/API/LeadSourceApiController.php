<?php

declare(strict_types=1);

namespace Modules\CRM\LeadSource\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Modules\CRM\LeadSource\Application\Commands\CreateLeadSourceCommand;
use Modules\CRM\LeadSource\Application\Commands\CreateLeadSourceHandler;
use Modules\CRM\LeadSource\Application\Commands\UpdateLeadSourceCommand;
use Modules\CRM\LeadSource\Application\Commands\UpdateLeadSourceHandler;
use Modules\CRM\LeadSource\Application\Commands\DeleteLeadSourceCommand;
use Modules\CRM\LeadSource\Application\Commands\DeleteLeadSourceHandler;
use Modules\CRM\LeadSource\Application\Queries\GetLeadSourcesQuery;
use Modules\CRM\LeadSource\Application\Queries\GetLeadSourcesHandler;

class LeadSourceApiController extends Controller
{
    public function index(Request $request, GetLeadSourcesHandler $handler): JsonResponse
    {
        $search = $request->query('search');
        $isActive = $request->has('is_active') ? filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN) : null;

        $query = new GetLeadSourcesQuery($search, $isActive);
        $leadSources = $handler->handle($query);

        return response()->json([
            'success' => true,
            'data' => $leadSources
        ]);
    }

    public function store(Request $request, CreateLeadSourceHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:lead_sources,code',
        ]);

        $command = new CreateLeadSourceCommand(
            $validated['name'],
            $validated['code']
        );

        $id = $handler->handle($command);

        return response()->json([
            'success' => true,
            'message' => 'Nguồn được tạo thành công.',
            'data' => ['id' => $id]
        ], 201);
    }

    public function update(Request $request, string $id, UpdateLeadSourceHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:lead_sources,code,' . $id,
            'is_active' => 'required|boolean'
        ]);

        try {
            $command = new UpdateLeadSourceCommand(
                $id,
                $validated['name'],
                $validated['code'],
                (bool) $validated['is_active']
            );

            $handler->handle($command);
            
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 404);
        }
    }

    public function destroy(string $id, DeleteLeadSourceHandler $handler): JsonResponse
    {
        try {
            $command = new DeleteLeadSourceCommand($id);
            $handler->handle($command);
            
            return response()->json([
                'success' => true,
                'message' => 'Xoá thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 404);
        }
    }
}
