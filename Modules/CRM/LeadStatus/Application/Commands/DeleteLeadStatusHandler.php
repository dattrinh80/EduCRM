<?php

declare(strict_types=1);

namespace Modules\CRM\LeadStatus\Application\Commands;

use Modules\CRM\LeadStatus\Domain\LeadStatusRepositoryInterface;

class DeleteLeadStatusHandler
{
    public function __construct(
        private LeadStatusRepositoryInterface $statusRepository
    ) {}

    public function handle(DeleteLeadStatusCommand $command): void
    {
        $this->statusRepository->delete($command->id);
    }
}
