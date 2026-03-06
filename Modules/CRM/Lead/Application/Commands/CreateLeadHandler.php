<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\CRM\Lead\Domain\Lead;
use Modules\CRM\Lead\Domain\LeadRepositoryInterface;
use Illuminate\Support\Str;

class CreateLeadHandler implements CommandHandler
{
    public function __construct(
        private readonly LeadRepositoryInterface $repository,
        private readonly \Modules\CRM\Lead\LeadStatus\Domain\LeadStatusRepositoryInterface $statusRepository,
        private readonly \Modules\CRM\Lead\LeadTag\Domain\LeadTagRepositoryInterface $tagRepository,
        private readonly \Modules\CRM\Lead\Domain\LeadAssignmentRepositoryInterface $assignmentRepository
    ) {
    }

    public function handle(Command $command): Lead
    {
        /** @var CreateLeadCommand $command */
        
        $statusId = $command->statusId;
        if (!$statusId) {
            $status = $this->statusRepository->findByName('New');
            $statusId = $status ? $status->getId() : '';
        }

        $lead = Lead::create(
            (string) Str::uuid(),
            $command->name,
            $command->phone,
            $command->email,
            $statusId,
            $command->centerId,
            $command->dob,
            $command->leadSourceId,
            $command->campaignId,
            $command->interestTypeId,
            $command->assignedTo
        );

        $this->repository->save($lead);

        if ($command->assignedTo) {
            $assignment = \Modules\CRM\Lead\Domain\LeadAssignment::create(
                (string) \Illuminate\Support\Str::uuid(),
                $lead->getId(),
                $command->assignedTo,
                $command->assignedBy,
                'Initial assignment'
            );
            $this->assignmentRepository->save($assignment);
        }

        if (!empty($command->tagIds)) {
            $this->tagRepository->syncTagsForLead($lead->getId(), $command->tagIds);
        }

        return $lead;
    }
}
