<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\Core\User\Infrastructure\ReadModels\UserReadModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetUsersPaginatedHandler implements QueryHandler
{
    public function handle(Query $query): LengthAwarePaginator
    {
        /** @var GetUsersPaginatedQuery $query */

        $builder = UserReadModel::query()->with('userRoles.role');

        // Search by name or email
        if ($query->search) {
            $search = $query->search;
            $builder->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($query->roleId) {
            $builder->whereHas('userRoles', function ($q) use ($query) {
                $q->where('role_id', $query->roleId);
            });
        }

        return $builder->latest()->paginate($query->perPage, ['*'], 'page', $query->page);
    }
}
