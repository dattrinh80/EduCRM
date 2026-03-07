<?php

declare(strict_types=1);

namespace Modules\Marketing\Campaign\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Marketing\Campaign\Application\Commands\CreateCampaignCommand;
use Modules\Marketing\Campaign\Application\Commands\CreateCampaignHandler;
use Modules\Marketing\Campaign\Application\Commands\UpdateCampaignCommand;
use Modules\Marketing\Campaign\Application\Commands\UpdateCampaignHandler;
use Modules\Marketing\Campaign\Application\Commands\DeleteCampaignCommand;
use Modules\Marketing\Campaign\Application\Commands\DeleteCampaignHandler;
use Modules\Marketing\Campaign\Application\Queries\GetCampaignsQuery;
use Modules\Marketing\Campaign\Application\Queries\GetCampaignsHandler;

class CampaignApiController extends Controller
{
    public function index(Request $request, GetCampaignsHandler $handler): JsonResponse
    {
        $search = $request->query('search');
        $isActive = $request->has('is_active') ? filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN) : null;

        $query = new GetCampaignsQuery($search, $isActive);
        $campaigns = $handler->handle($query);

        return response()->json([
            'success' => true,
            'data' => $campaigns
        ]);
    }

    public function store(Request $request, CreateCampaignHandler $handler): JsonResponse
    {
        $isGlobalScope = false;
        try { $isGlobalScope = app('is_global_scope'); } catch (\Exception $e) {}

        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:campaigns,code',
            'channel' => 'nullable|string|max:100',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];

        if ($isGlobalScope) {
            $rules['center_id'] = 'required|uuid|exists:centers,id';
        }

        $validated = $request->validate($rules);

        $centerId = $isGlobalScope
            ? ($validated['center_id'] ?? null)
            : (session('current_center_id') ?? app('center_id'));

        try {
            $command = new CreateCampaignCommand(
                $validated['name'],
                $validated['code'] ?? null,
                $validated['channel'] ?? null,
                $validated['budget'] ? (float)$validated['budget'] : null,
                $centerId,
                $validated['start_date'] ? new \DateTimeImmutable($validated['start_date']) : null,
                $validated['end_date'] ? new \DateTimeImmutable($validated['end_date']) : null
            );

            $id = $handler->handle($command);

            return response()->json([
                'success' => true,
                'message' => 'Chiến dịch được tạo thành công.',
                'data' => ['id' => $id]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, string $id, UpdateCampaignHandler $handler): JsonResponse
    {
        $isGlobalScope = false;
        try { $isGlobalScope = app('is_global_scope'); } catch (\Exception $e) {}

        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:campaigns,code,' . $id,
            'channel' => 'nullable|string|max:100',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'required|boolean'
        ];

        if ($isGlobalScope) {
            $rules['center_id'] = 'required|uuid|exists:centers,id';
        }

        $validated = $request->validate($rules);

        $centerId = $isGlobalScope
            ? ($validated['center_id'] ?? null)
            : (session('current_center_id') ?? app('center_id'));

        try {
            $command = new UpdateCampaignCommand(
                $id,
                $validated['name'],
                $validated['code'] ?? null,
                $validated['channel'] ?? null,
                $validated['budget'] ? (float)$validated['budget'] : null,
                $centerId,
                $validated['start_date'] ? new \DateTimeImmutable($validated['start_date']) : null,
                $validated['end_date'] ? new \DateTimeImmutable($validated['end_date']) : null,
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

    public function destroy(string $id, DeleteCampaignHandler $handler): JsonResponse
    {
        try {
            $command = new DeleteCampaignCommand($id);
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
