<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\CRM\Task\Domain\TaskRepositoryInterface;

class DeleteTaskHandler implements CommandHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository
    ) {
    }

    public function handle(Command $command): mixed
    {
        /** @var DeleteTaskCommand $command */
        
        $this->repository->delete($command->id);
        
        return true;
    }
}
