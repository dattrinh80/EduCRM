<?php

declare(strict_types=1);

namespace Modules\CRM\InterestType\Application\Commands;

use Modules\CRM\InterestType\Domain\InterestTypeRepositoryInterface;
use InvalidArgumentException;

class DeleteInterestTypeHandler
{
    public function __construct(
        private InterestTypeRepositoryInterface $repository
    ) {}

    public function handle(DeleteInterestTypeCommand $command): void
    {
        $interestType = $this->repository->findById($command->id);

        if (!$interestType) {
            throw new InvalidArgumentException("InterestType not found.");
        }

        $this->repository->delete($interestType);
    }
}
