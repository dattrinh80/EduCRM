<?php

declare(strict_types=1);

namespace Modules\CRM\LeadStatus\Application\Commands;

use Modules\CRM\LeadStatus\Domain\LeadStatus;
use Modules\CRM\LeadStatus\Domain\LeadStatusRepositoryInterface;
use Illuminate\Support\Str;

class CreateLeadStatusHandler
{
    public function __construct(
        private LeadStatusRepositoryInterface $statusRepository
    ) {}

    public function handle(CreateLeadStatusCommand $command): void
    {
        $status = LeadStatus::create(
            (string) Str::uuid(),
            $command->name,
            $command->stage,
            $command->sortOrder,
            $command->isActive,
            $command->color
        );

        $this->statusRepository->save($status);
    }
}
