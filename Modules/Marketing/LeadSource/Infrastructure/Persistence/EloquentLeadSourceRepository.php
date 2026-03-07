<?php

declare(strict_types=1);

namespace Modules\Marketing\LeadSource\Infrastructure\Persistence;

use Modules\Marketing\LeadSource\Domain\LeadSource;
use Modules\Marketing\LeadSource\Domain\LeadSourceRepositoryInterface;
use Modules\Marketing\LeadSource\Infrastructure\ReadModels\LeadSourceReadModel;
use Carbon\Carbon;

class EloquentLeadSourceRepository implements LeadSourceRepositoryInterface
{
    public function save(LeadSource $leadSource): void
    {
        LeadSourceReadModel::updateOrCreate(
            ['id' => $leadSource->getId()],
            [
                'name' => $leadSource->name,
                'code' => $leadSource->code,
                'is_active' => $leadSource->isActive,
                'created_at' => $leadSource->createdAt ? Carbon::instance($leadSource->createdAt) : now(),
                'updated_at' => $leadSource->updatedAt ? Carbon::instance($leadSource->updatedAt) : now(),
            ]
        );
    }

    public function findById(string $id): ?LeadSource
    {
        $model = LeadSourceReadModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function findByCode(string $code): ?LeadSource
    {
        $model = LeadSourceReadModel::where('code', $code)->first();

        if (!$model) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function delete(LeadSource $leadSource): void
    {
        LeadSourceReadModel::destroy($leadSource->getId());
    }

    private function toDomain(LeadSourceReadModel $model): LeadSource
    {
        return new LeadSource(
            $model->id,
            $model->name,
            $model->code,
            $model->is_active,
            $model->created_at ? new \DateTimeImmutable($model->created_at->toDateTimeString()) : null,
            $model->updated_at ? new \DateTimeImmutable($model->updated_at->toDateTimeString()) : null
        );
    }
}
