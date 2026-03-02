<?php

declare(strict_types=1);

namespace Modules\CRM\Source\Application\Commands;

use Modules\CRM\Source\Domain\SourceRepositoryInterface;
use InvalidArgumentException;

class UpdateSourceHandler
{
    public function __construct(
        private SourceRepositoryInterface $repository
    ) {}

    public function handle(UpdateSourceCommand $command): void
    {
        $source = $this->repository->findById($command->id);

        if (!$source) {
            throw new InvalidArgumentException("Source not found.");
        }

        $source->update(
            $command->name,
            $command->code,
            $command->isActive
        );

        $this->repository->save($source);
    }
}
