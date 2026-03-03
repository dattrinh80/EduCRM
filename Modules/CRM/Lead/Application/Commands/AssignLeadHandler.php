<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use Modules\CRM\Lead\Domain\LeadRepositoryInterface;

class AssignLeadHandler
{
    private LeadRepositoryInterface $repository;

    public function __construct(LeadRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function handle(AssignLeadCommand $command): void
    {
        if (empty($command->leadIds)) {
            return;
        }

        foreach ($command->leadIds as $leadId) {
            $lead = $this->repository->findById($leadId);
            if ($lead) {
                $lead->assignTo($command->assignedTo);
                $this->repository->update($lead);
            }
        }
    }
}
