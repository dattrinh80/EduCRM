<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use Modules\CRM\Lead\Domain\LeadRepositoryInterface;

class BulkUpdateLeadsHandler
{
    private \Modules\CRM\Lead\LeadStatus\Domain\LeadStatusRepositoryInterface $statusRepository;

    public function __construct(
        LeadRepositoryInterface $repository,
        \Modules\CRM\Lead\LeadStatus\Domain\LeadStatusRepositoryInterface $statusRepository
    )
    {
        $this->repository = $repository;
        $this->statusRepository = $statusRepository;
    }

    public function handle(BulkUpdateLeadsCommand $command): void
    {
        if (empty($command->leadIds)) {
            return;
        }

        $newStatus = null;
        if ($command->status !== null) {
            $newStatus = $this->statusRepository->findById($command->status);
            if (!$newStatus) {
                throw new \Exception("Status not found: {$command->status}");
            }
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

            if ($newStatus !== null && $lead->statusId !== $newStatus->id) {
                $currentStatus = $this->statusRepository->findById($lead->statusId);
                $lead->setStatus($newStatus->id, $newStatus->stage, $currentStatus?->stage);
            }

            $this->repository->update($lead);
        }
    }
}
