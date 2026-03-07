<?php

declare(strict_types=1);

namespace Modules\CRM\LeadTag\Application\Commands;

use Modules\CRM\LeadTag\Domain\LeadTagRepositoryInterface;

class DeleteLeadTagHandler
{
    public function __construct(
        private LeadTagRepositoryInterface $tagRepository
    ) {}

    public function handle(DeleteLeadTagCommand $command): void
    {
        $this->tagRepository->delete($command->id);
    }
}
