<?php

declare(strict_types=1);

namespace Modules\CRM\LeadStatus\Application\Commands;

use Modules\CRM\Lead\Domain\LeadRepositoryInterface;
use Modules\CRM\LeadStatus\Domain\LeadStatusRepositoryInterface;
use Modules\CRM\LeadActivity\Application\Commands\AddLeadActivityCommand;
use Modules\CRM\LeadActivity\Application\Commands\AddLeadActivityHandler;
use Modules\CRM\LeadActivity\Domain\LeadActivity;

class ChangeLeadStatusHandler
{
    public function __construct(
        private LeadRepositoryInterface $leadRepository,
        private LeadStatusRepositoryInterface $statusRepository,
        private AddLeadActivityHandler $activityHandler
    ) {}

    public function handle(ChangeLeadStatusCommand $command): void
    {
        $lead = $this->leadRepository->findById($command->leadId);
        if (!$lead) {
            throw new \Exception("Lead not found: {$command->leadId}");
        }

        $newStatus = $this->statusRepository->findById($command->statusId);
        if (!$newStatus) {
            throw new \Exception("Status not found: {$command->statusId}");
        }

        $currentStatus = $this->statusRepository->findById($lead->statusId);
        
        // Domain logic for status transitions
        $lead->changeStatus($newStatus->id, $newStatus->stage, $currentStatus?->stage);
        $this->leadRepository->save($lead);

        // Auto-log activity
        $this->activityHandler->handle(new AddLeadActivityCommand(
            $command->leadId,
            LeadActivity::TYPE_STATUS_CHANGE,
            "Chuyển trạng thái sang: {$newStatus->name}",
            $command->changedBy
        ));
    }
}

