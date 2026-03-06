<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Infrastructure\Persistence;

use Modules\CRM\Lead\Domain\Lead;
use Modules\CRM\Lead\Domain\LeadRepositoryInterface;
use Modules\CRM\Lead\Infrastructure\ReadModels\LeadReadModel;

class EloquentLeadRepository implements LeadRepositoryInterface
{
    public function save(Lead $lead): void
    {
        $model = new LeadReadModel();
        $this->mapDomainToModel($lead, $model);
        $model->save();
    }

    public function update(Lead $lead): void
    {
        $model = LeadReadModel::find($lead->getId());
        if ($model) {
            $this->mapDomainToModel($lead, $model);
            $model->save();
        }
    }

    public function delete(string $id): void
    {
        LeadReadModel::destroy($id);
    }

    public function findById(string $id): ?Lead
    {
        $model = LeadReadModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->mapModelToDomain($model);
    }

    private function mapDomainToModel(Lead $lead, LeadReadModel $model): void
    {
        $model->id = $lead->getId();
        $model->name = $lead->name;
        $model->phone = $lead->phone;
        $model->email = $lead->email;
        $model->status_id = $lead->statusId;
        $model->center_id = $lead->centerId;
        $model->dob = $lead->dob;
        $model->lead_source_id = $lead->leadSourceId;
        $model->campaign_id = $lead->campaignId;
        $model->interest_type_id = $lead->interestTypeId;
        $model->assigned_to = $lead->assignedTo;
        
        // Eloquent will handle created_at / updated_at internally
    }

    private function mapModelToDomain(LeadReadModel $model): Lead
    {
        return new Lead(
            $model->id,
            $model->name,
            $model->phone,
            $model->email,
            $model->status_id ?? '',
            $model->center_id,
            $model->dob,
            $model->lead_source_id,
            $model->campaign_id,
            $model->interest_type_id,
            $model->assigned_to,
            $model->created_at ? new \DateTimeImmutable($model->created_at->toDateTimeString()) : null,
            $model->updated_at ? new \DateTimeImmutable($model->updated_at->toDateTimeString()) : null
        );
    }
}
