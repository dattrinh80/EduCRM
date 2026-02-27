<?php

declare(strict_types=1);

namespace Modules\Core\Role\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\Core\User\Infrastructure\ReadModels\RoleReadModel;
use Illuminate\Database\Eloquent\Collection;

class GetAllRolesHandler implements QueryHandler
{
    public function handle(Query $query): Collection
    {
        /** @var GetAllRolesQuery $query */

        return RoleReadModel::orderBy('name')->get();
    }
}
