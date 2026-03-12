<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\CRM\Lead\Infrastructure\ReadModels\LeadReadModel;
use Modules\CRM\Customer\Infrastructure\Persistence\CustomerModel;

class SearchTaskRelationsHandler implements QueryHandler
{
    public function handle(Query $query): array
    {
        /** @var SearchTaskRelationsQuery $query */
        $q = $query->query;
        $type = $query->type;

        $results = [];

        if (!$type || $type === 'Lead') {
            $leads = LeadReadModel::query()
                ->where('name', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%")
                ->limit(5)
                ->get(['id', 'name', 'phone']);
            
            foreach ($leads as $lead) {
                $results[] = [
                    'id' => $lead->id,
                    'name' => "[Lead] {$lead->name} - {$lead->phone}",
                    'type' => 'Modules\CRM\Lead\Infrastructure\ReadModels\LeadReadModel'
                ];
            }
        }

        if (!$type || $type === 'Customer') {
            $customers = CustomerModel::query()
                ->where('name', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%")
                ->limit(5)
                ->get(['id', 'name', 'phone']);

            foreach ($customers as $customer) {
                $results[] = [
                    'id' => $customer->id,
                    'name' => "[KH] {$customer->name} - {$customer->phone}",
                    'type' => 'Modules\CRM\Customer\Infrastructure\Persistence\CustomerModel'
                ];
            }
        }

        return $results;
    }
}
