<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Infrastructure\Persistence;

use Modules\CRM\Lead\Domain\LeadAssignment;
use Modules\CRM\Lead\Domain\LeadAssignmentRepositoryInterface;
use Modules\CRM\Lead\Infrastructure\ReadModels\LeadAssignmentReadModel;

class EloquentLeadAssignmentRepository implements LeadAssignmentRepositoryInterface
{
    public function save(LeadAssignment $assignment): void
    {
        LeadAssignmentReadModel::updateOrCreate(
            ['id' => $assignment->getId()],
            [
                'lead_id' => $assignment->getLeadId(),
                'assigned_to' => $assignment->getAssignedTo(),
                'assigned_by' => $assignment->getAssignedBy(),
                'notes' => $assignment->getNotes(),
                'created_at' => $assignment->getCreatedAt(),
            ]
        );
    }

    public function findByLeadId(string $leadId): array
    {
        return LeadAssignmentReadModel::where('lead_id', $leadId)
            ->latest()
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    private function toDomain(LeadAssignmentReadModel $model): LeadAssignment
    {
        return new LeadAssignment(
            $model->id,
            $model->lead_id,
            $model->assigned_to,
            $model->assigned_by,
            $model->notes,
            new \DateTimeImmutable($model->created_at->toString())
        );
    }
}
