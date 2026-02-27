<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\Core\User\Infrastructure\ReadModels\UserReadModel;

class GetUserByIdHandler implements QueryHandler
{
    public function handle(Query $query): ?UserReadModel
    {
        /** @var GetUserByIdQuery $query */

        return UserReadModel::with('userRoles.role')->find($query->id);
    }
}
