<?php

declare(strict_types=1);

namespace Modules\CRM\LeadSource\Application\Commands;

use Modules\CRM\LeadSource\Domain\LeadSourceRepositoryInterface;
use InvalidArgumentException;

class UpdateLeadSourceHandler
{
    public function __construct(
        private LeadSourceRepositoryInterface $repository
    ) {}

    public function handle(UpdateLeadSourceCommand $command): void
    {
        $leadSource = $this->repository->findById($command->id);

        if (!$leadSource) {
            throw new InvalidArgumentException("Lead source not found.");
        }

        $leadSource->update(
            $command->name,
            $command->code,
            $command->isActive
        );

        $this->repository->save($leadSource);
    }
}
