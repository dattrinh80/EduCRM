<?php

declare(strict_types=1);

namespace Modules\Education\Student\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\Education\Student\Domain\StudentRepositoryInterface;

class DeleteStudentHandler implements CommandHandler
{
    public function __construct(
        private readonly StudentRepositoryInterface $repository
    ) {}

    public function handle(Command $command): mixed
    {
        /** @var DeleteStudentCommand $command */
        $this->repository->delete($command->id);
        return null;
    }
}
