<?php

declare(strict_types=1);

namespace Modules\Marketing\LeadSource\Application\Commands;

use Modules\Marketing\LeadSource\Domain\LeadSourceRepositoryInterface;
use InvalidArgumentException;

class DeleteLeadSourceHandler
{
    public function __construct(
        private LeadSourceRepositoryInterface $repository
    ) {}

    public function handle(DeleteLeadSourceCommand $command): void
    {
        $leadSource = $this->repository->findById($command->id);

        if (!$leadSource) {
            throw new InvalidArgumentException("Lead source not found.");
        }

        $this->repository->delete($leadSource);
    }
}
