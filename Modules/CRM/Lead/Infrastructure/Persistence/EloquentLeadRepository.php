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
        $model->status = $lead->status;
        $model->center_id = $lead->centerId;
        
        // Eloquent will handle created_at / updated_at internally
    }

    private function mapModelToDomain(LeadReadModel $model): Lead
    {
        return new Lead(
            $model->id,
            $model->name,
            $model->phone,
            $model->email,
            $model->status,
            $model->center_id,
            $model->created_at ? new \DateTimeImmutable($model->created_at->toDateTimeString()) : null,
            $model->updated_at ? new \DateTimeImmutable($model->updated_at->toDateTimeString()) : null
        );
    }
}
