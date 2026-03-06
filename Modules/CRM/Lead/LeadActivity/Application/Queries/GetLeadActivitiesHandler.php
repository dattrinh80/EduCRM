<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\LeadActivity\Application\Queries;

use Modules\CRM\Lead\LeadActivity\Infrastructure\ReadModels\LeadActivityReadModel;

class GetLeadActivitiesHandler
{
    public function handle(GetLeadActivitiesQuery $query)
    {
        return LeadActivityReadModel::where('lead_id', $query->leadId)
            ->with('creator')
            ->orderByDesc('created_at')
            ->paginate($query->perPage, ['*'], 'page', $query->page);
    }
}
