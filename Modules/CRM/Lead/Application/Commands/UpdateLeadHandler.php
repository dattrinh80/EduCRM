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
        private readonly LeadRepositoryInterface $repository
    ) {
    }

    public function handle(Command $command): Lead
    {
        /** @var UpdateLeadCommand $command */
        
        $lead = $this->repository->findById($command->id);

        if (!$lead) {
            throw new \Exception('Lead not found');
        }

        $lead->update(
            $command->name,
            $command->phone,
            $command->email,
            $command->status,
            $command->centerId,
            $command->dob,
            $command->leadSourceId,
            $command->campaignId,
            $command->interestTypeId,
            $command->assignedTo
        );

        $this->repository->update($lead);

        return $lead;
    }
}
