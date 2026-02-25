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
        private readonly LeadRepositoryInterface $repository
    ) {
    }

    public function handle(Command $command): Lead
    {
        /** @var CreateLeadCommand $command */
        
        $lead = Lead::create(
            (string) Str::uuid(),
            $command->name,
            $command->phone,
            $command->email,
            $command->centerId
        );

        $this->repository->save($lead);

        return $lead;
    }
}
