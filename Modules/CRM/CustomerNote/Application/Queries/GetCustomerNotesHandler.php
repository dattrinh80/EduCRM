<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerNote\Application\Queries;

use Modules\CRM\CustomerNote\Infrastructure\ReadModels\CustomerNoteReadModel;

class GetCustomerNotesHandler
{
    public function handle(GetCustomerNotesQuery $query)
    {
        return CustomerNoteReadModel::where('customer_id', $query->customerId)
            ->with('creator')
            ->orderByDesc('created_at')
            ->paginate($query->perPage, ['*'], 'page', $query->page);
    }
}

