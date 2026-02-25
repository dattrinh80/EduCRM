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
        
        return LeadReadModel::query()
            ->latest()
            ->paginate($query->perPage, ['*'], 'page', $query->page);
    }
}
