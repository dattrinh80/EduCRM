<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Queries;

use Modules\Core\User\Infrastructure\ReadModels\UserReadModel;

class GetAllUsersHandler
{
    public function handle(GetAllUsersQuery $query)
    {
        $builder = UserReadModel::query();

        if ($query->centerId) {
            $builder->where(function ($q) use ($query) {
                $q->where('default_center_id', $query->centerId)
                  ->orWhereHas('userRoles', function ($sq) use ($query) {
                      $sq->where(function ($ssq) use ($query) {
                          $ssq->where('scope_type', 'CENTER')
                              ->where('scope_id', $query->centerId);
                      })->orWhere('scope_type', 'ALL');
                  });
            });
        }

        return $builder->get();
    }
}
