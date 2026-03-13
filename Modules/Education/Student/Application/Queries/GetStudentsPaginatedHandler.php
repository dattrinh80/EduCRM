<?php

declare(strict_types=1);

namespace Modules\Education\Student\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\Education\Student\Infrastructure\ReadModels\StudentReadModel;
use App\Core\Helpers\PaginationHelper;

class GetStudentsPaginatedHandler implements QueryHandler
{
    public function handle(Query $query): mixed
    {
        /** @var GetStudentsPaginatedQuery $query */
        $builder = StudentReadModel::query()
            ->with(['customer', 'customer.center']);

        if (!empty($query->search)) {
            $builder->where(function ($q) use ($query) {
                $q->where('student_code', 'like', '%' . $query->search . '%')
                  ->orWhereHas('customer', function ($cq) use ($query) {
                      $cq->where('name', 'like', '%' . $query->search . '%')
                         ->orWhere('phone', 'like', '%' . $query->search . '%')
                         ->orWhere('email', 'like', '%' . $query->search . '%');
                  });
            });
        }

        if (!empty($query->status)) {
            $builder->where('status', $query->status);
        }

        // Apply sorting
        $sortableColumns = ['student_code', 'status', 'created_at', 'updated_at'];
        $validSortColumn = PaginationHelper::resolveSortColumn($query->sortBy, $sortableColumns);
        
        if ($validSortColumn) {
            $direction = PaginationHelper::resolveSortDirection($query->sortDirection);
            $builder->orderBy($validSortColumn, $direction);
        } else {
            $builder->latest();
        }

        return $builder->paginate($query->perPage, ['*'], 'page', $query->page);
    }
}
