<?php

declare(strict_types=1);

namespace Modules\CRM\Source\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Modules\CRM\Source\Application\Commands\CreateSourceCommand;
use Modules\CRM\Source\Application\Commands\CreateSourceHandler;
use Modules\CRM\Source\Application\Commands\UpdateSourceCommand;
use Modules\CRM\Source\Application\Commands\UpdateSourceHandler;
use Modules\CRM\Source\Application\Commands\DeleteSourceCommand;
use Modules\CRM\Source\Application\Commands\DeleteSourceHandler;
use Modules\CRM\Source\Application\Queries\GetSourcesQuery;
use Modules\CRM\Source\Application\Queries\GetSourcesHandler;

class SourceApiController extends Controller
{
    public function index(Request $request, GetSourcesHandler $handler): JsonResponse
    {
        $search = $request->query('search');
        $isActive = $request->has('is_active') ? filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN) : null;

        $query = new GetSourcesQuery($search, $isActive);
        $sources = $handler->handle($query);

        return response()->json([
            'success' => true,
            'data' => $sources
        ]);
    }

    public function store(Request $request, CreateSourceHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:sources,code',
        ]);

        $command = new CreateSourceCommand(
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

    public function update(Request $request, string $id, UpdateSourceHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:sources,code,' . $id,
            'is_active' => 'required|boolean'
        ]);

        try {
            $command = new UpdateSourceCommand(
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

    public function destroy(string $id, DeleteSourceHandler $handler): JsonResponse
    {
        try {
            $command = new DeleteSourceCommand($id);
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
