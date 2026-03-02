<?php

declare(strict_types=1);

namespace Modules\CRM\InterestType\Application\Commands;

use Illuminate\Support\Str;
use Modules\CRM\InterestType\Domain\InterestType;
use Modules\CRM\InterestType\Domain\InterestTypeRepositoryInterface;

class CreateInterestTypeHandler
{
    public function __construct(
        private InterestTypeRepositoryInterface $repository
    ) {}

    public function handle(CreateInterestTypeCommand $command): string
    {
        $id = Str::uuid()->toString();

        $interestType = InterestType::create(
            $id,
            $command->name,
            $command->description
        );

        $this->repository->save($interestType);

        return $id;
    }
}
