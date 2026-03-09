<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\CRM\Lead\Domain\LeadRepositoryInterface;
use Modules\CRM\LeadStatus\Domain\LeadStatusRepositoryInterface;
use Modules\CRM\LeadActivity\Domain\LeadActivityRepositoryInterface;
use Modules\CRM\LeadActivity\Domain\LeadActivity;
use Illuminate\Support\Str;

class UpdateLeadStatusHandler implements CommandHandler
{
    public function __construct(
        private readonly LeadRepositoryInterface $repository,
        private readonly LeadStatusRepositoryInterface $statusRepository,
        private readonly LeadActivityRepositoryInterface $activityRepository
    ) {
    }

    public function handle(Command $command): mixed
    {
        /** @var UpdateLeadStatusCommand $command */
        
        $lead = $this->repository->findById($command->leadId);
        if (!$lead) {
            throw new \Exception('Lead not found');
        }

        $oldStatusId = $lead->statusId;
        if ($oldStatusId === $command->statusId) {
            return null;
        }

        $newStatus = $this->statusRepository->findById($command->statusId);
        if (!$newStatus) {
            throw new \Exception('New status not found');
        }

        $oldStatus = $this->statusRepository->findById($oldStatusId);

        // Update the domain object
        $lead->setStatus($command->statusId, $newStatus->stage, $oldStatus?->stage);
        
        // Persist
        $this->repository->update($lead);

        // Log the change
        $activity = LeadActivity::create(
            (string) Str::uuid(),
            $lead->getId(),
            LeadActivity::TYPE_STATUS_CHANGE,
            "Trạng thái thay đổi từ [" . ($oldStatus?->name ?? 'N/A') . "] sang [" . $newStatus->name . "] qua Kanban",
            $command->updatedBy
        );
        $this->activityRepository->save($activity);
        return null;
    }
}
