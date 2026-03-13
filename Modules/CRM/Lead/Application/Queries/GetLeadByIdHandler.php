<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\CRM\Lead\Infrastructure\ReadModels\LeadReadModel;

class GetLeadByIdHandler implements QueryHandler
{
    public function handle(Query $query): mixed
    {
        /** @var GetLeadByIdQuery $query */
        
        $dbQuery = LeadReadModel::query();
        
        if (!empty($query->with)) {
            $dbQuery->with($query->with);
        }

        return $dbQuery->find($query->id);
    }
}
