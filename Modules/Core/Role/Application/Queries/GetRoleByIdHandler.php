<?php

declare(strict_types=1);

namespace Modules\Core\Role\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\Core\Role\Infrastructure\ReadModels\RoleReadModel;

class GetRoleByIdHandler implements QueryHandler
{
    public function handle(Query $query): ?RoleReadModel
    {
        /** @var GetRoleByIdQuery $query */

        return RoleReadModel::with('permissions')->find($query->id);
    }
}
