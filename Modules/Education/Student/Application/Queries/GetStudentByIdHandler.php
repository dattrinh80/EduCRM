<?php

declare(strict_types=1);

namespace Modules\Education\Student\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\Education\Student\Infrastructure\ReadModels\StudentReadModel;

class GetStudentByIdHandler implements QueryHandler
{
    public function handle(Query $query): mixed
    {
        /** @var GetStudentByIdQuery $query */
        $dbQuery = StudentReadModel::query();
        
        if (!empty($query->with)) {
            $dbQuery->with($query->with);
        }

        return $dbQuery->find($query->id);
    }
}
