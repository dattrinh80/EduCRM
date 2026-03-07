<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Domain;

interface LeadConversionRepositoryInterface
{
    public function save(LeadConversion $conversion): void;
    public function findByLeadId(string $leadId): array;
}
