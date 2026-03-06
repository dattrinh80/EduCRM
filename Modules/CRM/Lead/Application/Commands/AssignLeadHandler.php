<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use Modules\CRM\Lead\Domain\LeadRepositoryInterface;

class AssignLeadHandler
{
    public function __construct(
        private readonly LeadRepositoryInterface $repository,
        private readonly \Modules\CRM\Lead\Domain\LeadAssignmentRepositoryInterface $assignmentRepository,
        private readonly \Modules\CRM\LeadActivity\Domain\LeadActivityRepositoryInterface $activityRepository
    ) {
    }

    public function handle(AssignLeadCommand $command): void
    {
        if (empty($command->leadIds)) {
            return;
        }

        foreach ($command->leadIds as $leadId) {
            $lead = $this->repository->findById($leadId);
            if ($lead) {
                // Only log if it's a change or first assignment
                $oldAssignedTo = $lead->assignedTo;
                
                $lead->assignTo($command->assignedTo);
                $this->repository->update($lead);

                if ($command->assignedTo !== $oldAssignedTo) {
                    $assignment = \Modules\CRM\Lead\Domain\LeadAssignment::create(
                        (string) \Illuminate\Support\Str::uuid(),
                        $leadId,
                        $command->assignedTo ?? '', // Empty string if unassigned
                        $command->assignedBy,
                        $command->assignedTo ? 'Lead assigned' : 'Lead unassigned'
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
                        $leadId,
                        \Modules\CRM\LeadActivity\Domain\LeadActivity::TYPE_ASSIGNMENT,
                        "Bàn giao Lead cho: " . $assignedUserName,
                        $command->assignedBy
                    );
                    $this->activityRepository->save($activity);
                }
            }
        }
    }
}

