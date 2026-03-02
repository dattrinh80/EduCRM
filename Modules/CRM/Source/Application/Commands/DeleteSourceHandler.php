<?php

declare(strict_types=1);

namespace Modules\CRM\Source\Application\Commands;

use Modules\CRM\Source\Domain\SourceRepositoryInterface;
use InvalidArgumentException;

class DeleteSourceHandler
{
    public function __construct(
        private SourceRepositoryInterface $repository
    ) {}

    public function handle(DeleteSourceCommand $command): void
    {
        $source = $this->repository->findById($command->id);

        if (!$source) {
            throw new InvalidArgumentException("Source not found.");
        }

        $this->repository->delete($source);
    }
}
