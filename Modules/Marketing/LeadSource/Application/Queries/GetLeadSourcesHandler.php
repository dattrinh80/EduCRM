<?php

declare(strict_types=1);

namespace Modules\Marketing\LeadSource\Application\Queries;

use Illuminate\Support\Collection;
use Modules\Marketing\LeadSource\Infrastructure\ReadModels\LeadSourceReadModel;

class GetLeadSourcesHandler
{
    public function handle(GetLeadSourcesQuery $query): Collection
    {
        $sql = LeadSourceReadModel::query();

        if ($query->search) {
            $sql->where(function($q) use ($query) {
                $q->where('name', 'like', '%' . $query->search . '%')
                  ->orWhere('code', 'like', '%' . $query->search . '%');
            });
        }

        if ($query->isActive !== null) {
            $sql->where('is_active', $query->isActive);
        }

        return $sql->orderBy('created_at', 'desc')->get();
    }
}
