<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\CRM\Task\Domain\Task;
use Modules\CRM\Task\Domain\TaskRepositoryInterface;

class ToggleTaskStatusHandler implements CommandHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository
    ) {
    }

    public function handle(Command $command): Task
    {
        /** @var ToggleTaskStatusCommand $command */
        
        $task = $this->repository->findById($command->id);
        if (!$task) {
            throw new \Exception('Task not found');
        }

        $task->toggleStatus();

        $this->repository->update($task);

        return $task;
    }
}
