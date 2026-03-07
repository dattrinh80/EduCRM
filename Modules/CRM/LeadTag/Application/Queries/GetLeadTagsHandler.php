<?php

declare(strict_types=1);

namespace Modules\CRM\LeadTag\Application\Queries;

class GetLeadTagsHandler
{
    public function __construct(
        private \Modules\CRM\LeadTag\Domain\LeadTagRepositoryInterface $tagRepository
    ) {}

    public function handle(GetLeadTagsQuery $query): array
    {
        // Simple implementation, similar to LeadStatus
        return $this->tagRepository->getAll();
    }
}
