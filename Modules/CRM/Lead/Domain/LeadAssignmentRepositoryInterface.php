<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Domain;

interface LeadAssignmentRepositoryInterface
{
    public function save(LeadAssignment $assignment): void;
    
    /**
     * @return LeadAssignment[]
     */
    public function findByLeadId(string $leadId): array;
}
