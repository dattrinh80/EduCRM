<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerTag\Application\Commands;

use Modules\CRM\CustomerTag\Domain\CustomerTagRepositoryInterface;

class DeleteCustomerTagHandler
{
    public function __construct(
        private CustomerTagRepositoryInterface $tagRepository
    ) {}

    public function handle(DeleteCustomerTagCommand $command): void
    {
        $this->tagRepository->delete($command->id);
    }
}
