<?php

declare(strict_types=1);

namespace Modules\Core\Permission\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\Core\Permission\Infrastructure\ReadModels\PermissionReadModel;

class GetPermissionsCountHandler implements QueryHandler
{
    public function handle(Query $query): int
    {
        /** @var GetPermissionsCountQuery $query */

        return PermissionReadModel::count();
    }
}
