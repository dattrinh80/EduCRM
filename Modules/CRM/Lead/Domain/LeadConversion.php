<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Domain;

class LeadConversion
{
    public function __construct(
        public string $id,
        public string $leadId,
        public string $studentId,
        public ?string $convertedBy = null,
        public ?string $convertedAt = null
    ) {}

    public static function create(
        string $id,
        string $leadId,
        string $studentId,
        ?string $convertedBy = null
    ): self {
        return new self(
            id: $id,
            leadId: $leadId,
            studentId: $studentId,
            convertedBy: $convertedBy
        );
    }
}
