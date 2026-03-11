<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerNote\Infrastructure\Persistence;

use Modules\CRM\CustomerNote\Domain\CustomerNote;
use Modules\CRM\CustomerNote\Domain\CustomerNoteRepositoryInterface;
use Modules\CRM\CustomerNote\Infrastructure\ReadModels\CustomerNoteReadModel;

class EloquentCustomerNoteRepository implements CustomerNoteRepositoryInterface
{
    public function save(CustomerNote $note): void
    {
        $model = new CustomerNoteReadModel();
        $model->id = $note->getId();
        $model->customer_id = $note->customerId;
        $model->content = $note->content;
        $model->created_by = $note->createdBy;
        $model->created_at = $note->createdAt?->format('Y-m-d H:i:s') ?? now();
        $model->save();
    }

    public function findById(string $id): ?CustomerNote
    {
        $model = CustomerNoteReadModel::find($id);
        if (!$model) {
            return null;
        }

        return new CustomerNote(
            $model->id,
            $model->customer_id,
            $model->content,
            $model->created_by,
            $model->created_at ? new \DateTimeImmutable($model->created_at->toDateTimeString()) : null
        );
    }

    public function delete(string $id): void
    {
        CustomerNoteReadModel::destroy($id);
    }

    public function deleteByCustomerId(string $customerId): void
    {
        CustomerNoteReadModel::where('customer_id', $customerId)->delete();
    }
}

