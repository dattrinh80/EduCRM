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

use Modules\Marketing\Campaign\Presentation\Web\Requests\StoreCampaignRequest;
use Modules\Marketing\Campaign\Presentation\Web\Requests\UpdateCampaignRequest;

class CampaignApiController extends Controller
{
    public function index(Request $request, GetCampaignsHandler $handler): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);
        $search = $request->query('search');
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir', 'desc');
        
        $budgetFrom = $request->query('budget_from') !== null ? (float) $request->query('budget_from') : null;
        $budgetTo = $request->query('budget_to') !== null ? (float) $request->query('budget_to') : null;
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $isActive = $request->query('is_active') !== null ? filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN) : null;
        $centerId = $request->query('center_id');

        $query = new GetCampaignsQuery(
            $search, 
            $isActive, 
            $perPage, 
            $page, 
            $sortBy, 
            $sortDir, 
            $budgetFrom, 
            $budgetTo, 
            $dateFrom, 
            $dateTo, 
            $centerId
        );
        $campaigns = $handler->handle($query);

        return response()->json([
            'success' => true,
            'data' => $campaigns
        ]);
    }

    public function store(StoreCampaignRequest $request, CreateCampaignHandler $handler): JsonResponse
    {
        $validated = $request->getValidatedData();

        try {
            $command = new CreateCampaignCommand(
                $validated['name'],
                $validated['code'] ?? null,
                $validated['channel'] ?? null,
                $validated['budget'] ? (float)$validated['budget'] : null,
                $validated['center_id'],
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

    public function update(UpdateCampaignRequest $request, string $id, UpdateCampaignHandler $handler): JsonResponse
    {
        $validated = $request->getValidatedData();

        try {
            $command = new UpdateCampaignCommand(
                $id,
                $validated['name'],
                $validated['code'] ?? null,
                $validated['channel'] ?? null,
                $validated['budget'] ? (float)$validated['budget'] : null,
                $validated['center_id'],
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

