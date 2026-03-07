<?php

declare(strict_types=1);

namespace Modules\Marketing\LeadSource\Domain;

interface LeadSourceRepositoryInterface
{
    public function save(LeadSource $leadSource): void;
    
    public function findById(string $id): ?LeadSource;
    
    public function findByCode(string $code): ?LeadSource;
    
    public function delete(LeadSource $leadSource): void;
}
