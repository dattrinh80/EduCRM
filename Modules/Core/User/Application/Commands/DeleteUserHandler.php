<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\Core\User\Domain\UserRepositoryInterface;

class DeleteUserHandler implements CommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository
    ) {
    }

    public function handle(Command $command): void
    {
        /** @var DeleteUserCommand $command */

        $user = $this->repository->findById($command->id);

        if (!$user) {
            throw new \Exception('User not found');
        }

        $this->repository->delete($command->id);
    }
}
