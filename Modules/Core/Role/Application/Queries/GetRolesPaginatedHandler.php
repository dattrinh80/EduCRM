<?php

declare(strict_types=1);

namespace Modules\Core\Role\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\Core\Role\Infrastructure\ReadModels\RoleReadModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetRolesPaginatedHandler implements QueryHandler
{
    public function handle(Query $query): LengthAwarePaginator
    {
        /** @var GetRolesPaginatedQuery $query */

        $builder = RoleReadModel::query()->withCount('permissions');

        if ($query->search) {
            $builder->where('name', 'like', "%{$query->search}%");
        }

        return $builder->latest()->paginate($query->perPage, ['*'], 'page', $query->page);
    }
}
