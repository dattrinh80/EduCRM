<?php

declare(strict_types=1);

namespace Modules\CRM\LeadTag\Application\Commands;

use Modules\CRM\LeadTag\Domain\LeadTagRepositoryInterface;

class SyncLeadTagsHandler
{
    public function __construct(
        private LeadTagRepositoryInterface $tagRepository
    ) {}

    public function handle(SyncLeadTagsCommand $command): void
    {
        $this->tagRepository->syncTagsForLead($command->leadId, $command->tagIds);
    }
}

