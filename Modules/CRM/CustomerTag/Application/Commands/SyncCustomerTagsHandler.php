<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerTag\Application\Commands;

use Modules\CRM\CustomerTag\Domain\CustomerTagRepositoryInterface;

class SyncCustomerTagsHandler
{
    public function __construct(
        private CustomerTagRepositoryInterface $tagRepository
    ) {}

    public function handle(SyncCustomerTagsCommand $command): void
    {
        $this->tagRepository->syncTagsForCustomer($command->customerId, $command->tagIds);
    }
}

