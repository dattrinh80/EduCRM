<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\LeadNote\Application\Queries;

use Modules\CRM\Lead\LeadNote\Infrastructure\ReadModels\LeadNoteReadModel;

class GetLeadNotesHandler
{
    public function handle(GetLeadNotesQuery $query)
    {
        return LeadNoteReadModel::where('lead_id', $query->leadId)
            ->with('creator')
            ->orderByDesc('created_at')
            ->paginate($query->perPage, ['*'], 'page', $query->page);
    }
}
