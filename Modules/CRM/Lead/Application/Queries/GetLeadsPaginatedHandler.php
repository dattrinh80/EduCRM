<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\CRM\Lead\Infrastructure\ReadModels\LeadReadModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetLeadsPaginatedHandler implements QueryHandler
{
    public function handle(Query $query): LengthAwarePaginator
    {
        /** @var GetLeadsPaginatedQuery $query */
        $builder = LeadReadModel::query();

        if (!empty($query->search)) {
            $builder->where('name', 'like', '%' . $query->search . '%');
        }

        if (!empty($query->phone)) {
            $builder->where('phone', 'like', '%' . $query->phone . '%');
        }

        if (!empty($query->centerId)) {
            $builder->where('center_id', $query->centerId);
        }

        if (!empty($query->status)) {
            $builder->where('status', $query->status);
        }

        return $builder
            ->latest()
            ->paginate($query->perPage, ['*'], 'page', $query->page);
    }
}
