<?php

declare(strict_types=1);

namespace Modules\CRM\LeadSource\Domain;

interface LeadSourceRepositoryInterface
{
    public function save(LeadSource $leadSource): void;
    
    public function findById(string $id): ?LeadSource;
    
    public function findByCode(string $code): ?LeadSource;
    
    public function delete(LeadSource $leadSource): void;
}
