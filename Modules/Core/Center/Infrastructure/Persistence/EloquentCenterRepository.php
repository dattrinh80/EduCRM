<?php

declare(strict_types=1);

namespace Modules\Core\Center\Infrastructure\Persistence;

use Modules\Core\Center\Domain\Center;
use Modules\Core\Center\Domain\CenterRepositoryInterface;
use Modules\Core\Center\Infrastructure\ReadModels\CenterReadModel;

class EloquentCenterRepository implements CenterRepositoryInterface
{
    public function save(Center $center): void
    {
        $model = new CenterReadModel();
        $this->mapDomainToModel($center, $model);
        $model->save();
    }

    public function update(Center $center): void
    {
        $model = CenterReadModel::find($center->getId());
        if ($model) {
            $this->mapDomainToModel($center, $model);
            $model->save();
        }
    }

    public function delete(string $id): void
    {
        CenterReadModel::destroy($id);
    }

    public function findById(string $id): ?Center
    {
        $model = CenterReadModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->mapModelToDomain($model);
    }

    private function mapDomainToModel(Center $center, CenterReadModel $model): void
    {
        $model->id = $center->getId();
        $model->name = $center->name;
        $model->code = $center->code;
        $model->phone = $center->phone;
        $model->email = $center->email;
        $model->address = $center->address;
        $model->status = $center->status;
    }

    private function mapModelToDomain(CenterReadModel $model): Center
    {
        return new Center(
            $model->id,
            $model->name,
            $model->code,
            $model->phone,
            $model->email,
            $model->address,
            $model->status,
            $model->created_at ? new \DateTimeImmutable($model->created_at->toDateTimeString()) : null,
            $model->updated_at ? new \DateTimeImmutable($model->updated_at->toDateTimeString()) : null
        );
    }
}
