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

        if (!empty($query->statusId)) {
            $builder->where('status_id', $query->statusId);
        }

        // Apply sorting: validate column against whitelist, fallback to latest()
        $sortableColumns = config('crm.lead.sortable_columns', ['name', 'phone', 'email', 'status_id', 'created_at', 'updated_at']);
        $validSortColumn = \App\Core\Helpers\PaginationHelper::resolveSortColumn($query->sortBy, $sortableColumns);
        
        if ($validSortColumn) {
            $direction = \App\Core\Helpers\PaginationHelper::resolveSortDirection($query->sortDirection);
            $builder->orderBy($validSortColumn, $direction);
        } else {
            $builder->latest();
        }

        return $builder
            ->with(['leadSource', 'interestType', 'assignTo', 'center', 'leadStatus', 'tags', 'campaign'])
            ->paginate($query->perPage, ['*'], 'page', $query->page);
    }
}
