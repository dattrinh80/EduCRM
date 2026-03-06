<?php

declare(strict_types=1);

namespace Modules\CRM\LeadNote\Infrastructure\Persistence;

use Modules\CRM\LeadNote\Domain\LeadNote;
use Modules\CRM\LeadNote\Domain\LeadNoteRepositoryInterface;
use Modules\CRM\LeadNote\Infrastructure\ReadModels\LeadNoteReadModel;

class EloquentLeadNoteRepository implements LeadNoteRepositoryInterface
{
    public function save(LeadNote $note): void
    {
        $model = new LeadNoteReadModel();
        $model->id = $note->getId();
        $model->lead_id = $note->leadId;
        $model->content = $note->content;
        $model->created_by = $note->createdBy;
        $model->created_at = $note->createdAt?->format('Y-m-d H:i:s') ?? now();
        $model->save();
    }

    public function findById(string $id): ?LeadNote
    {
        $model = LeadNoteReadModel::find($id);
        if (!$model) {
            return null;
        }

        return new LeadNote(
            $model->id,
            $model->lead_id,
            $model->content,
            $model->created_by,
            $model->created_at ? new \DateTimeImmutable($model->created_at->toDateTimeString()) : null
        );
    }

    public function delete(string $id): void
    {
        LeadNoteReadModel::destroy($id);
    }

    public function deleteByLeadId(string $leadId): void
    {
        LeadNoteReadModel::where('lead_id', $leadId)->delete();
    }
}

