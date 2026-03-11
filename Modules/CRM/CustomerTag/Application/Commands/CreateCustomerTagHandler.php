<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerTag\Application\Commands;

use Modules\CRM\CustomerTag\Domain\CustomerTag;
use Modules\CRM\CustomerTag\Domain\CustomerTagRepositoryInterface;
use Illuminate\Support\Str;

class CreateCustomerTagHandler
{
    public function __construct(
        private CustomerTagRepositoryInterface $tagRepository
    ) {}

    public function handle(CreateCustomerTagCommand $command): void
    {
        $tag = CustomerTag::create(
            (string) Str::uuid(),
            $command->name,
            $command->color
        );

        $this->tagRepository->save($tag);
    }
}
