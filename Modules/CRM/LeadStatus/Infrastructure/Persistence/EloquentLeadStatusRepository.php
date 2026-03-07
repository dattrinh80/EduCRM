<?php

declare(strict_types=1);

namespace Modules\CRM\LeadStatus\Infrastructure\Persistence;

use Modules\CRM\LeadStatus\Domain\LeadStatus;
use Modules\CRM\LeadStatus\Domain\LeadStatusRepositoryInterface;
use Modules\CRM\LeadStatus\Infrastructure\ReadModels\LeadStatusReadModel;

class EloquentLeadStatusRepository implements LeadStatusRepositoryInterface
{
    public function save(LeadStatus $status): void
    {
        $model = LeadStatusReadModel::findOrNew($status->getId());
        $model->id = $status->getId();
        $model->name = $status->name;
        $model->stage = $status->stage;
        $model->sort_order = $status->sortOrder;
        $model->is_active = $status->isActive;
        $model->color = $status->color;
        $model->save();
    }

    public function findById(string $id): ?LeadStatus
    {
        $model = LeadStatusReadModel::find($id);
        if (!$model) {
            return null;
        }

        return $this->mapToDomain($model);
    }

    public function findByName(string $name): ?LeadStatus
    {
        $model = LeadStatusReadModel::where('name', $name)->first();
        if (!$model) {
            return null;
        }

        return $this->mapToDomain($model);
    }

    public function findByStage(string $stage): ?LeadStatus
    {
        $model = LeadStatusReadModel::where('stage', $stage)->first();
        if (!$model) {
            return null;
        }

        return $this->mapToDomain($model);
    }

    public function getAll(): array
    {
        return LeadStatusReadModel::orderBy('sort_order')
            ->get()
            ->map(fn($model) => $this->mapToDomain($model))
            ->toArray();
    }

    public function getAllActive(): array
    {
        return LeadStatusReadModel::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($model) => $this->mapToDomain($model))
            ->toArray();
    }

    public function delete(string $id): void
    {
        LeadStatusReadModel::destroy($id);
    }

    private function mapToDomain(LeadStatusReadModel $model): LeadStatus
    {
        return new LeadStatus(
            $model->id,
            $model->name,
            $model->stage,
            (int) $model->sort_order,
            (bool) $model->is_active,
            $model->color
        );
    }
}

