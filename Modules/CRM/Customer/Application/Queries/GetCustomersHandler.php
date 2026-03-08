<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\CRM\Customer\Domain\CustomerRepositoryInterface;

class GetCustomersHandler implements QueryHandler
{
    public function __construct(
        private readonly CustomerRepositoryInterface $repository
    ) {
    }

    public function handle(Query $query): array|\Illuminate\Support\Collection
    {
        /** @var GetCustomersQuery $query */
        return $this->repository->search($query->search);
    }
}
