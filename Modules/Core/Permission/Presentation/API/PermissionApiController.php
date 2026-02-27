<?php

declare(strict_types=1);

namespace Modules\Core\Permission\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Permission\Application\Queries\GetPermissionGroupsQuery;
use Modules\Core\Permission\Application\Queries\GetPermissionGroupsHandler;

class PermissionApiController extends Controller
{
    public function index(GetPermissionGroupsHandler $handler): JsonResponse
    {
        $permissionGroups = $handler->handle(new GetPermissionGroupsQuery());

        return response()->json([
            'success' => true,
            'data' => $permissionGroups
        ]);
    }
}
