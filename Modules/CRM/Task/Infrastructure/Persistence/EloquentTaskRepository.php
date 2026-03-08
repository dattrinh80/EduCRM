<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Infrastructure\Persistence;

use Modules\CRM\Task\Domain\Task;
use Modules\CRM\Task\Domain\TaskRepositoryInterface;
use Modules\CRM\Task\Infrastructure\ReadModels\TaskReadModel;

class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function save(Task $task): void
    {
        $model = new TaskReadModel();
        $this->mapDomainToModel($task, $model);
        $model->save();
    }

    public function update(Task $task): void
    {
        $model = TaskReadModel::find($task->getId());
        if ($model) {
            $this->mapDomainToModel($task, $model);
            $model->save();
        }
    }

    public function delete(string $id): void
    {
        TaskReadModel::destroy($id);
    }

    public function findById(string $id): ?Task
    {
        $model = TaskReadModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->mapModelToDomain($model);
    }

    private function mapDomainToModel(Task $task, TaskReadModel $model): void
    {
        $model->id = $task->getId();
        $model->title = $task->title;
        $model->description = $task->description;
        $model->due_date = $task->dueDate;
        $model->status = $task->status;
        $model->priority = $task->priority;
        $model->assigned_to = $task->assignedTo;
        $model->assigned_by = $task->assignedBy;
        $model->center_id = $task->centerId;
        $model->relation_id = $task->relationId;
        $model->relation_type = $task->relationType;
    }

    private function mapModelToDomain(TaskReadModel $model): Task
    {
        return new Task(
            $model->id,
            $model->title,
            $model->description,
            (string) $model->due_date,
            $model->status,
            $model->priority,
            $model->assigned_to,
            $model->assigned_by,
            $model->center_id,
            $model->relation_id,
            $model->relation_type,
            $model->created_at ? new \DateTimeImmutable($model->created_at->toDateTimeString()) : null,
            $model->updated_at ? new \DateTimeImmutable($model->updated_at->toDateTimeString()) : null
        );
    }
}
