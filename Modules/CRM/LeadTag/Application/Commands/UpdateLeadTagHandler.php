<?php

declare(strict_types=1);

namespace Modules\CRM\LeadTag\Application\Commands;

use Modules\CRM\LeadTag\Domain\LeadTagRepositoryInterface;

class UpdateLeadTagHandler
{
    public function __construct(
        private LeadTagRepositoryInterface $tagRepository
    ) {}

    public function handle(UpdateLeadTagCommand $command): void
    {
        $tag = $this->tagRepository->findById($command->id);
        if (!$tag) {
            throw new \Exception("Lead tag not found: {$command->id}");
        }

        $tag->name = $command->name;
        $tag->color = $command->color;

        $this->tagRepository->save($tag);
    }
}
