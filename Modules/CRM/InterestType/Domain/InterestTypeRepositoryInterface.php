<?php

declare(strict_types=1);

namespace Modules\CRM\InterestType\Domain;

interface InterestTypeRepositoryInterface
{
    public function save(InterestType $interestType): void;
    
    public function findById(string $id): ?InterestType;
    
    public function delete(InterestType $interestType): void;
}
