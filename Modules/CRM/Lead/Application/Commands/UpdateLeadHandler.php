<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\CRM\Lead\Domain\Lead;
use Modules\CRM\Lead\Domain\LeadRepositoryInterface;

class UpdateLeadHandler implements CommandHandler
{
    public function __construct(
        private readonly LeadRepositoryInterface $repository,
        private readonly \Modules\CRM\LeadStatus\Domain\LeadStatusRepositoryInterface $statusRepository,
        private readonly \Modules\CRM\LeadTag\Domain\LeadTagRepositoryInterface $tagRepository,
        private readonly \Modules\CRM\Lead\Domain\LeadAssignmentRepositoryInterface $assignmentRepository,
        private readonly \Modules\CRM\LeadActivity\Domain\LeadActivityRepositoryInterface $activityRepository
    ) {
    }

    public function handle(Command $command): Lead
    {
        /** @var UpdateLeadCommand $command */
        
        $lead = $this->repository->findById($command->id);

        if (!$lead) {
            throw new \Exception('Lead not found');
        }

        $newStatus = $this->statusRepository->findById($command->statusId);
        if (!$newStatus) {
            throw new \Exception("Status not found: {$command->statusId}");
        }

        $currentStatus = $this->statusRepository->findById($lead->statusId);

        $oldAssignedTo = $lead->assignedTo;

        $lead->update(
            $command->name,
            $command->phone,
            $command->email,
            $command->statusId,
            $newStatus->stage,
            $currentStatus?->stage,
            $command->centerId,
            $command->dob,
            $command->leadSourceId,
            $command->campaignId,
            $command->interestTypeId,
            $command->assignedTo
        );

        $this->repository->update($lead);

        // Log status change
        if ($command->statusId !== $currentStatus?->getId()) {
            $activity = \Modules\CRM\LeadActivity\Domain\LeadActivity::create(
                (string) \Illuminate\Support\Str::uuid(),
                $lead->getId(),
                \Modules\CRM\LeadActivity\Domain\LeadActivity::TYPE_STATUS_CHANGE,
                "Trạng thái thay đổi từ [" . ($currentStatus?->name ?? 'N/A') . "] sang [" . $newStatus->name . "]",
                $command->assignedBy
            );
            $this->activityRepository->save($activity);
        }

        if ($command->assignedTo !== $oldAssignedTo) {
            $assignment = \Modules\CRM\Lead\Domain\LeadAssignment::create(
                (string) \Illuminate\Support\Str::uuid(),
                $lead->getId(),
                $command->assignedTo ?? '',
                $command->assignedBy,
                'Assignment updated via lead update'
            );
            $this->assignmentRepository->save($assignment);

            // Also log as activity
            $assignedUserName = 'Chưa giao';
            if ($command->assignedTo) {
                $assignedUser = \Modules\Core\User\Infrastructure\ReadModels\UserReadModel::find($command->assignedTo);
                $assignedUserName = $assignedUser ? $assignedUser->name : 'User ID: ' . $command->assignedTo;
            }

            $activity = \Modules\CRM\LeadActivity\Domain\LeadActivity::create(
                (string) \Illuminate\Support\Str::uuid(),
                $lead->getId(),
                \Modules\CRM\LeadActivity\Domain\LeadActivity::TYPE_ASSIGNMENT,
                "Giao Lead cho: " . $assignedUserName,
                $command->assignedBy
            );
            $this->activityRepository->save($activity);
        }

        if (isset($command->tagIds)) {
            $this->tagRepository->syncTagsForLead($lead->getId(), $command->tagIds);
        }

        return $lead;
    }
}

