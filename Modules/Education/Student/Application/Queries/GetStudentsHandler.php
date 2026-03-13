<?php

declare(strict_types=1);

namespace Modules\Education\Student\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\Education\Student\Domain\StudentRepositoryInterface;

class GetStudentsHandler implements QueryHandler
{
    public function __construct(
        private readonly StudentRepositoryInterface $repository
    ) {
    }

    public function handle(Query $query): mixed
    {
        return $this->repository->getAll();
    }
}
