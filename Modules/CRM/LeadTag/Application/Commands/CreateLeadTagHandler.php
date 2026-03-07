<?php

declare(strict_types=1);

namespace Modules\CRM\LeadTag\Application\Commands;

use Modules\CRM\LeadTag\Domain\LeadTag;
use Modules\CRM\LeadTag\Domain\LeadTagRepositoryInterface;
use Illuminate\Support\Str;

class CreateLeadTagHandler
{
    public function __construct(
        private LeadTagRepositoryInterface $tagRepository
    ) {}

    public function handle(CreateLeadTagCommand $command): void
    {
        $tag = LeadTag::create(
            (string) Str::uuid(),
            $command->name,
            $command->color
        );

        $this->tagRepository->save($tag);
    }
}
