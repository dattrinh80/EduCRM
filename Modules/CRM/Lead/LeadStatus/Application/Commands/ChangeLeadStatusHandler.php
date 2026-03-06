<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\LeadStatus\Application\Commands;

use Modules\CRM\Lead\Domain\LeadRepositoryInterface;
use Modules\CRM\Lead\LeadStatus\Domain\LeadStatusRepositoryInterface;
use Modules\CRM\Lead\LeadActivity\Application\Commands\AddLeadActivityCommand;
use Modules\CRM\Lead\LeadActivity\Application\Commands\AddLeadActivityHandler;
use Modules\CRM\Lead\LeadActivity\Domain\LeadActivity;

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
