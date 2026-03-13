<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\CRM\Customer\Infrastructure\ReadModels\CustomerReadModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetCustomersPaginatedHandler implements QueryHandler
{
    public function handle(Query $query): LengthAwarePaginator
    {
        /** @var GetCustomersPaginatedQuery $query */
        $builder = CustomerReadModel::query();

        if (!empty($query->search)) {
            $builder->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query->search . '%')
                  ->orWhere('email', 'like', '%' . $query->search . '%')
                  ->orWhere('phone', 'like', '%' . $query->search . '%');
            });
        }

        if (!empty($query->phone)) {
            $builder->where('phone', 'like', '%' . $query->phone . '%');
        }

        if (!empty($query->centerId)) {
            $builder->where('center_id', $query->centerId);
        }

        // Apply sorting
        $sortableColumns = ['name', 'phone', 'email', 'created_at', 'updated_at'];
        $validSortColumn = \App\Core\Helpers\PaginationHelper::resolveSortColumn($query->sortBy, $sortableColumns);
        
        if ($validSortColumn) {
            $direction = \App\Core\Helpers\PaginationHelper::resolveSortDirection($query->sortDirection);
            $builder->orderBy($validSortColumn, $direction);
        } else {
            $builder->latest();
        }

        $results = $builder
            ->with(['tags', 'studentGuardians'])
            ->paginate($query->perPage, ['*'], 'page', $query->page);
        
        return $results;
    }
}
