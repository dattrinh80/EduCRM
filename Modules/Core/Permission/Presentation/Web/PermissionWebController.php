<?php

declare(strict_types=1);

namespace Modules\Core\Permission\Presentation\Web;

use Illuminate\Routing\Controller;
use Modules\Core\Permission\Infrastructure\ReadModels\PermissionGroupReadModel;
use Modules\Core\Permission\Infrastructure\ReadModels\PermissionReadModel;

class PermissionWebController extends Controller
{
    public function index()
    {
        $permissionGroups = PermissionGroupReadModel::with('permissions')
            ->orderBy('sort_order')
            ->get();

        $totalPermissions = PermissionReadModel::count();

        return view('permission::index', compact('permissionGroups', 'totalPermissions'));
    }
}
