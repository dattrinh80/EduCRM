<?php

declare(strict_types=1);

namespace Modules\Core\Permission\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Permission\Infrastructure\ReadModels\PermissionGroupReadModel;

class PermissionApiController extends Controller
{
    public function index(): JsonResponse
    {
        $permissionGroups = PermissionGroupReadModel::with('permissions')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $permissionGroups
        ]);
    }
}
