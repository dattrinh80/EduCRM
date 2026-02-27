<?php

declare(strict_types=1);

namespace Modules\Core\Center\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\Core\Center\Domain\CenterRepositoryInterface;

class DeleteCenterHandler implements CommandHandler
{
    public function __construct(
        private readonly CenterRepositoryInterface $repository
    ) {
    }

    public function handle(Command $command): mixed
    {
        /** @var DeleteCenterCommand $command */

        $center = $this->repository->findById($command->id);

        if (!$center) {
            throw new \Exception('Center not found');
        }

        $this->repository->delete($command->id);

        return null;
    }
}
