<?php

declare(strict_types=1);

namespace Modules\Marketing\InterestType\Application\Queries;

use Illuminate\Support\Collection;
use Modules\Marketing\InterestType\Infrastructure\ReadModels\InterestTypeReadModel;

class GetInterestTypesHandler
{
    public function handle(GetInterestTypesQuery $query): Collection
    {
        $sql = InterestTypeReadModel::query();

        if ($query->search) {
            $sql->where(function($q) use ($query) {
                $q->where('name', 'like', '%' . $query->search . '%')
                  ->orWhere('description', 'like', '%' . $query->search . '%');
            });
        }

        if ($query->isActive !== null) {
            $sql->where('is_active', $query->isActive);
        }

        return $sql->orderBy('created_at', 'desc')->get();
    }
}
