<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\LeadNote\Application\Commands;

class AddLeadNoteCommand
{
    public function __construct(
        public readonly string $leadId,
        public readonly string $content,
        public readonly ?string $createdBy = null
    ) {}
}
