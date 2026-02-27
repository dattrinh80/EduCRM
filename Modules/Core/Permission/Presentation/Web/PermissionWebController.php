<?php

declare(strict_types=1);

namespace Modules\Core\Permission\Presentation\Web;

use Illuminate\Routing\Controller;
use Modules\Core\Permission\Application\Queries\GetPermissionGroupsQuery;
use Modules\Core\Permission\Application\Queries\GetPermissionGroupsHandler;
use Modules\Core\Permission\Application\Queries\GetPermissionsCountQuery;
use Modules\Core\Permission\Application\Queries\GetPermissionsCountHandler;

class PermissionWebController extends Controller
{
    public function index(
        GetPermissionGroupsHandler $groupsHandler,
        GetPermissionsCountHandler $countHandler
    ) {
        $permissionGroups = $groupsHandler->handle(new GetPermissionGroupsQuery());
        $totalPermissions = $countHandler->handle(new GetPermissionsCountQuery());

        return view('permission::index', compact('permissionGroups', 'totalPermissions'));
    }
}
