<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerActivity\Infrastructure\Persistence;

use Modules\CRM\CustomerActivity\Domain\CustomerActivity;
use Modules\CRM\CustomerActivity\Domain\CustomerActivityRepositoryInterface;
use Modules\CRM\CustomerActivity\Infrastructure\ReadModels\CustomerActivityReadModel;

class EloquentCustomerActivityRepository implements CustomerActivityRepositoryInterface
{
    public function save(CustomerActivity $activity): void
    {
        $model = new CustomerActivityReadModel();
        $model->id = $activity->getId();
        $model->customer_id = $activity->customerId;
        $model->activity_type = $activity->activityType;
        $model->description = $activity->description;
        $model->created_by = $activity->createdBy;
        $model->created_at = $activity->createdAt?->format('Y-m-d H:i:s') ?? now();
        $model->save();
    }

    public function findById(string $id): ?CustomerActivity
    {
        $model = CustomerActivityReadModel::find($id);
        if (!$model) {
            return null;
        }

        return new CustomerActivity(
            $model->id,
            $model->customer_id,
            $model->activity_type,
            $model->description,
            $model->created_by,
            $model->created_at ? new \DateTimeImmutable($model->created_at->toDateTimeString()) : null
        );
    }

    public function deleteByCustomerId(string $customerId): void
    {
        CustomerActivityReadModel::where('customer_id', $customerId)->delete();
    }
}

