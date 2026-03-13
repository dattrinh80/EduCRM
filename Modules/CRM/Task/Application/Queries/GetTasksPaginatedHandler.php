<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\CRM\Task\Infrastructure\ReadModels\TaskReadModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetTasksPaginatedHandler implements QueryHandler
{
    public function handle(Query $query): LengthAwarePaginator
    {
        /** @var GetTasksPaginatedQuery $query */
        $builder = TaskReadModel::query();

        if (!empty($query->search)) {
            $builder->where('title', 'like', '%' . $query->search . '%');
        }

        if (!empty($query->status)) {
            $builder->where('status', $query->status);
        }

        if (!empty($query->priority)) {
            $builder->where('priority', $query->priority);
        }

        if (!empty($query->assignedTo)) {
            $builder->where('assigned_to', $query->assignedTo);
        }

        if (!empty($query->centerId)) {
            $builder->where('center_id', $query->centerId);
        }

        if (!empty($query->relationId)) {
            $builder->where('relation_id', $query->relationId);
        }

        if (!empty($query->relationType)) {
            $builder->where('relation_type', $query->relationType);
        }

        return $builder
            ->with(['assignedTo', 'assignedBy', 'center', 'relation'])
            ->latest('due_date')
            ->latest('created_at')
            ->latest('id')
            ->paginate($query->perPage, ['*'], 'page', $query->page);
    }
}
