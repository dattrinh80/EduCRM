<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerActivity\Application\Queries;

use Modules\CRM\CustomerActivity\Infrastructure\ReadModels\CustomerActivityReadModel;

class GetCustomerActivitiesHandler
{
    public function handle(GetCustomerActivitiesQuery $query)
    {
        return CustomerActivityReadModel::where('customer_id', $query->customerId)
            ->with('creator')
            ->orderByDesc('created_at')
            ->paginate($query->perPage, ['*'], 'page', $query->page);
    }
}

