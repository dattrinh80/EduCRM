<?php

declare(strict_types=1);

namespace Modules\CRM\InterestType\Infrastructure\Persistence;

use Modules\CRM\InterestType\Domain\InterestType;
use Modules\CRM\InterestType\Domain\InterestTypeRepositoryInterface;
use Modules\CRM\InterestType\Infrastructure\ReadModels\InterestTypeReadModel;
use Carbon\Carbon;

class EloquentInterestTypeRepository implements InterestTypeRepositoryInterface
{
    public function save(InterestType $interestType): void
    {
        InterestTypeReadModel::updateOrCreate(
            ['id' => $interestType->id],
            [
                'name' => $interestType->name,
                'description' => $interestType->description,
                'is_active' => $interestType->isActive,
                'created_at' => $interestType->createdAt ? Carbon::instance($interestType->createdAt) : now(),
                'updated_at' => $interestType->updatedAt ? Carbon::instance($interestType->updatedAt) : now(),
            ]
        );
    }

    public function findById(string $id): ?InterestType
    {
        $model = InterestTypeReadModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function delete(InterestType $interestType): void
    {
        InterestTypeReadModel::destroy($interestType->id);
    }

    private function toDomain(InterestTypeReadModel $model): InterestType
    {
        return new InterestType(
            $model->id,
            $model->name,
            $model->description,
            $model->is_active,
            $model->created_at ? new \DateTimeImmutable($model->created_at->toDateTimeString()) : null,
            $model->updated_at ? new \DateTimeImmutable($model->updated_at->toDateTimeString()) : null
        );
    }
}
