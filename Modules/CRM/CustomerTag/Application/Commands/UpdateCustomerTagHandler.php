<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerTag\Application\Commands;

use Modules\CRM\CustomerTag\Domain\CustomerTagRepositoryInterface;

class UpdateCustomerTagHandler
{
    public function __construct(
        private CustomerTagRepositoryInterface $tagRepository
    ) {}

    public function handle(UpdateCustomerTagCommand $command): void
    {
        $tag = $this->tagRepository->findById($command->id);
        if (!$tag) {
            throw new \Exception("Customer tag not found: {$command->id}");
        }

        $tag->name = $command->name;
        $tag->color = $command->color;

        $this->tagRepository->save($tag);
    }
}
