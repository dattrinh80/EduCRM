<?php

declare(strict_types=1);

namespace Modules\CRM\LeadTag\Application\Commands;

class SyncLeadTagsCommand
{
    public function __construct(
        public string $leadId,
        public array $tagIds
    ) {}
}

