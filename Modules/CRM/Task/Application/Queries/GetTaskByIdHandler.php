<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\CRM\Task\Infrastructure\ReadModels\TaskReadModel;

class GetTaskByIdHandler implements QueryHandler
{
    public function handle(Query $query): ?TaskReadModel
    {
        /** @var GetTaskByIdQuery $query */
        return TaskReadModel::with(['assignedTo', 'assignedBy', 'center', 'relation'])->find($query->id);
    }
}
