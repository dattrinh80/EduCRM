<?php

declare(strict_types=1);

namespace Modules\CRM\Source\Application\Queries;

use Illuminate\Support\Collection;
use Modules\CRM\Source\Infrastructure\ReadModels\SourceReadModel;

class GetSourcesHandler
{
    public function handle(GetSourcesQuery $query): Collection
    {
        $sql = SourceReadModel::query();

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
