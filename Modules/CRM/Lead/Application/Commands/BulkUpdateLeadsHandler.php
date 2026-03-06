<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use Modules\CRM\Lead\Domain\LeadRepositoryInterface;

class BulkUpdateLeadsHandler
{
    private LeadRepositoryInterface $repository;

    public function __construct(LeadRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function handle(BulkUpdateLeadsCommand $command): void
    {
        if (empty($command->leadIds)) {
            return;
        }

        foreach ($command->leadIds as $leadId) {
            $lead = $this->repository->findById($leadId);
            if (!$lead) {
                continue;
            }

            if ($command->leadSourceId !== null) {
                $lead->setLeadSource($command->leadSourceId === 'null' ? null : $command->leadSourceId);
            }

            if ($command->interestTypeId !== null) {
                $lead->setInterestType($command->interestTypeId === 'null' ? null : $command->interestTypeId);
            }

            if ($command->centerId !== null) {
                $lead->setCenter($command->centerId === 'null' ? null : $command->centerId);
            }

            if ($command->assignedTo !== null) {
                $lead->assignTo($command->assignedTo === 'null' ? null : $command->assignedTo);
            }

            if ($command->campaignId !== null) {
                $lead->setCampaign($command->campaignId === 'null' ? null : $command->campaignId);
            }

            if ($command->status !== null) {
                $lead->setStatus($command->status);
            }

            $this->repository->update($lead);
        }
    }
}
