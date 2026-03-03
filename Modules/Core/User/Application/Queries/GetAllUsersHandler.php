<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Queries;

use Modules\Core\User\Infrastructure\ReadModels\UserReadModel;

class GetAllUsersHandler
{
    public function handle(GetAllUsersQuery $query)
    {
        return UserReadModel::all();
    }
}
