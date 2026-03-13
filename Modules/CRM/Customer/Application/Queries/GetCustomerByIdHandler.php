<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\CRM\Customer\Infrastructure\ReadModels\CustomerReadModel;

class GetCustomerByIdHandler implements QueryHandler
{
    public function handle(Query $query): ?CustomerReadModel
    {
        /** @var GetCustomerByIdQuery $query */
        $builder = CustomerReadModel::query();
        
        if (!empty($query->relations)) {
            $builder->with($query->relations);
        }

        return $builder->find($query->id);
    }
}
