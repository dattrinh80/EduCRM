<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\CRM\Task\Domain\Task;
use Modules\CRM\Task\Domain\TaskRepositoryInterface;

class UpdateTaskHandler implements CommandHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository
    ) {
    }

    public function handle(Command $command): Task
    {
        /** @var UpdateTaskCommand $command */
        
        $task = $this->repository->findById($command->id);
        if (!$task) {
            throw new \Exception('Task not found');
        }

        $task->update(
            $command->title,
            $command->description,
            $command->dueDate,
            $command->status,
            $command->priority,
            $command->assignedTo,
            $command->startDate
        );

        $this->repository->update($task);

        return $task;
    }
}
