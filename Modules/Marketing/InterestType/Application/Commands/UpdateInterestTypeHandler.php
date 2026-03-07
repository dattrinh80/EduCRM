<?php

declare(strict_types=1);

namespace Modules\Marketing\InterestType\Application\Commands;

use Modules\Marketing\InterestType\Domain\InterestTypeRepositoryInterface;
use InvalidArgumentException;

class UpdateInterestTypeHandler
{
    public function __construct(
        private InterestTypeRepositoryInterface $repository
    ) {}

    public function handle(UpdateInterestTypeCommand $command): void
    {
        $interestType = $this->repository->findById($command->id);

        if (!$interestType) {
            throw new InvalidArgumentException("InterestType not found.");
        }

        $interestType->update(
            $command->name,
            $command->description,
            $command->isActive
        );

        $this->repository->save($interestType);
    }
}
