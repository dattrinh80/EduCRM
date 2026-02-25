<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\CRM\Lead\Domain\LeadRepositoryInterface;

class DeleteLeadHandler implements CommandHandler
{
    public function __construct(
        private readonly LeadRepositoryInterface $repository
    ) {
    }

    public function handle(Command $command): void
    {
        /** @var DeleteLeadCommand $command */
        
        $lead = $this->repository->findById($command->id);

        if (!$lead) {
            throw new \Exception('Lead not found');
        }

        $this->repository->delete($command->id);
    }
}
