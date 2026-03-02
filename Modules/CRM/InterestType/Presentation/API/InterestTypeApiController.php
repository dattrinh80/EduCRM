<?php

declare(strict_types=1);

namespace Modules\CRM\InterestType\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Modules\CRM\InterestType\Application\Commands\CreateInterestTypeCommand;
use Modules\CRM\InterestType\Application\Commands\CreateInterestTypeHandler;
use Modules\CRM\InterestType\Application\Commands\UpdateInterestTypeCommand;
use Modules\CRM\InterestType\Application\Commands\UpdateInterestTypeHandler;
use Modules\CRM\InterestType\Application\Commands\DeleteInterestTypeCommand;
use Modules\CRM\InterestType\Application\Commands\DeleteInterestTypeHandler;
use Modules\CRM\InterestType\Application\Queries\GetInterestTypesQuery;
use Modules\CRM\InterestType\Application\Queries\GetInterestTypesHandler;

class InterestTypeApiController extends Controller
{
    public function index(Request $request, GetInterestTypesHandler $handler): JsonResponse
    {
        $search = $request->query('search');
        $isActive = $request->has('is_active') ? filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN) : null;

        $query = new GetInterestTypesQuery($search, $isActive);
        $interestTypes = $handler->handle($query);

        return response()->json([
            'success' => true,
            'data' => $interestTypes
        ]);
    }

    public function store(Request $request, CreateInterestTypeHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $command = new CreateInterestTypeCommand(
            $validated['name'],
            $validated['description'] ?? null
        );

        $id = $handler->handle($command);

        return response()->json([
            'success' => true,
            'message' => 'Nhu cầu/Loại dịch vụ được tạo thành công.',
            'data' => ['id' => $id]
        ], 201);
    }

    public function update(Request $request, string $id, UpdateInterestTypeHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean'
        ]);

        try {
            $command = new UpdateInterestTypeCommand(
                $id,
                $validated['name'],
                $validated['description'] ?? null,
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

    public function destroy(string $id, DeleteInterestTypeHandler $handler): JsonResponse
    {
        try {
            $command = new DeleteInterestTypeCommand($id);
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
