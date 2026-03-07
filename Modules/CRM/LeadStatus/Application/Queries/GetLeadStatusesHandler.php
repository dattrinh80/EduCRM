<?php

declare(strict_types=1);

namespace Modules\CRM\LeadStatus\Application\Queries;

class GetLeadStatusesHandler
{
    public function __construct(
        private \Modules\CRM\LeadStatus\Domain\LeadStatusRepositoryInterface $statusRepository
    ) {}

    public function handle(GetLeadStatusesQuery $query): array
    {
        if ($query->onlyActive) {
            return $this->statusRepository->getAllActive();
        }

        // Note: For now we just return all, in a real scenario we might filter by $query->search in the repository
        return $this->statusRepository->getAll();
    }
}
