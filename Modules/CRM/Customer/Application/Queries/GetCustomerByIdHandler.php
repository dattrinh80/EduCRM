<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\CRM\Customer\Infrastructure\Persistence\CustomerModel;

class GetCustomerByIdHandler implements QueryHandler
{
    public function handle(Query $query): ?CustomerModel
    {
        /** @var GetCustomerByIdQuery $query */
        $builder = CustomerModel::query();
        
        if (!empty($query->relations)) {
            $builder->with($query->relations);
        }

        return $builder->find($query->id);
    }
}
