<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\LeadActivity\Infrastructure\Persistence;

use Modules\CRM\Lead\LeadActivity\Domain\LeadActivity;
use Modules\CRM\Lead\LeadActivity\Domain\LeadActivityRepositoryInterface;
use Modules\CRM\Lead\LeadActivity\Infrastructure\ReadModels\LeadActivityReadModel;

class EloquentLeadActivityRepository implements LeadActivityRepositoryInterface
{
    public function save(LeadActivity $activity): void
    {
        $model = new LeadActivityReadModel();
        $model->id = $activity->getId();
        $model->lead_id = $activity->leadId;
        $model->activity_type = $activity->activityType;
        $model->description = $activity->description;
        $model->created_by = $activity->createdBy;
        $model->created_at = $activity->createdAt?->format('Y-m-d H:i:s') ?? now();
        $model->save();
    }

    public function findById(string $id): ?LeadActivity
    {
        $model = LeadActivityReadModel::find($id);
        if (!$model) {
            return null;
        }

        return new LeadActivity(
            $model->id,
            $model->lead_id,
            $model->activity_type,
            $model->description,
            $model->created_by,
            $model->created_at ? new \DateTimeImmutable($model->created_at->toDateTimeString()) : null
        );
    }

    public function deleteByLeadId(string $leadId): void
    {
        LeadActivityReadModel::where('lead_id', $leadId)->delete();
    }
}
