<?php

declare(strict_types=1);

namespace Modules\Core\Permission\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\Core\Permission\Infrastructure\ReadModels\PermissionGroupReadModel;
use Illuminate\Database\Eloquent\Collection;

class GetPermissionGroupsHandler implements QueryHandler
{
    public function handle(Query $query): Collection
    {
        /** @var GetPermissionGroupsQuery $query */

        return PermissionGroupReadModel::with('permissions')
            ->orderBy('sort_order')
            ->get();
    }
}
