<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerTag\Application\Queries;

class GetCustomerTagsHandler
{
    public function __construct(
        private \Modules\CRM\CustomerTag\Domain\CustomerTagRepositoryInterface $tagRepository
    ) {}

    public function handle(GetCustomerTagsQuery $query): array
    {
        // Simple implementation, similar to CustomerStatus
        return $this->tagRepository->getAll();
    }
}
