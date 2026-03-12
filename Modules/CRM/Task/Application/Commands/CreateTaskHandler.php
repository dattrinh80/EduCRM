<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\CRM\Task\Domain\Task;
use Modules\CRM\Task\Domain\TaskRepositoryInterface;
use Illuminate\Support\Str;

class CreateTaskHandler implements CommandHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository
    ) {
    }

    public function handle(Command $command): Task
    {
        /** @var CreateTaskCommand $command */
        
        $task = Task::create(
            (string) Str::uuid(),
            $command->title,
            $command->description,
            $command->dueDate,
            $command->priority,
            $command->assignedTo,
            $command->assignedBy,
            $command->centerId,
            $command->relationId,
            $command->relationType,
            $command->startDate
        );

        $this->repository->save($task);

        return $task;
    }
}
