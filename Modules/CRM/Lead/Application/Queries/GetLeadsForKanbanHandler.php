<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\CRM\Lead\Infrastructure\ReadModels\LeadReadModel;
use Modules\CRM\LeadStatus\Infrastructure\ReadModels\LeadStatusReadModel;
use Illuminate\Support\Collection;

class GetLeadsForKanbanHandler implements QueryHandler
{
    public function handle(Query $query): Collection
    {
        /** @var GetLeadsForKanbanQuery $query */
        
        $statuses = LeadStatusReadModel::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        foreach ($statuses as $status) {
            $builder = LeadReadModel::query()
                ->where('status_id', $status->id);

            if (!empty($query->search)) {
                $builder->where('name', 'like', '%' . $query->search . '%');
            }

            if (!empty($query->phone)) {
                $builder->where('phone', 'like', '%' . $query->phone . '%');
            }

            if (!empty($query->centerId)) {
                $builder->where('center_id', $query->centerId);
            }

            if (!empty($query->statusId)) {
                $builder->where('status_id', $query->statusId);
            }

            $status->leads = $builder
                ->with(['leadSource', 'interestType', 'assignTo', 'center', 'tags'])
                ->latest()
                ->limit(50) // LIMIT each column for performance
                ->get();
        }

        return $statuses;
    }
}
