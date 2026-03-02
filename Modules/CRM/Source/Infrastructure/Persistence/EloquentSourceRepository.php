<?php

declare(strict_types=1);

namespace Modules\CRM\Source\Infrastructure\Persistence;

use Modules\CRM\Source\Domain\Source;
use Modules\CRM\Source\Domain\SourceRepositoryInterface;
use Modules\CRM\Source\Infrastructure\ReadModels\SourceReadModel;
use Carbon\Carbon;

class EloquentSourceRepository implements SourceRepositoryInterface
{
    public function save(Source $source): void
    {
        SourceReadModel::updateOrCreate(
            ['id' => $source->getId()],
            [
                'name' => $source->name,
                'code' => $source->code,
                'is_active' => $source->isActive,
                'created_at' => $source->createdAt ? Carbon::instance($source->createdAt) : now(),
                'updated_at' => $source->updatedAt ? Carbon::instance($source->updatedAt) : now(),
            ]
        );
    }

    public function findById(string $id): ?Source
    {
        $model = SourceReadModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function findByCode(string $code): ?Source
    {
        $model = SourceReadModel::where('code', $code)->first();

        if (!$model) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function delete(Source $source): void
    {
        SourceReadModel::destroy($source->getId());
    }

    private function toDomain(SourceReadModel $model): Source
    {
        return new Source(
            $model->id,
            $model->name,
            $model->code,
            $model->is_active,
            $model->created_at ? new \DateTimeImmutable($model->created_at->toDateTimeString()) : null,
            $model->updated_at ? new \DateTimeImmutable($model->updated_at->toDateTimeString()) : null
        );
    }
}
