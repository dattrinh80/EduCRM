<?php

declare(strict_types=1);

namespace Modules\CRM\LeadStatus\Application\Commands;

use Modules\CRM\LeadStatus\Domain\LeadStatusRepositoryInterface;

class UpdateLeadStatusHandler
{
    public function __construct(
        private LeadStatusRepositoryInterface $statusRepository
    ) {}

    public function handle(UpdateLeadStatusCommand $command): void
    {
        $status = $this->statusRepository->findById($command->id);
        if (!$status) {
            throw new \Exception("Lead status not found: {$command->id}");
        }

        $status->name = $command->name;
        $status->stage = $command->stage;
        $status->sortOrder = $command->sortOrder;
        $status->isActive = $command->isActive;
        $status->color = $command->color;

        $this->statusRepository->save($status);
    }
}
