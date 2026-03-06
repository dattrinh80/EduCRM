<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\LeadActivity\Domain;

interface LeadActivityRepositoryInterface
{
    public function save(LeadActivity $activity): void;

    public function findById(string $id): ?LeadActivity;

    public function deleteByLeadId(string $leadId): void;
}
