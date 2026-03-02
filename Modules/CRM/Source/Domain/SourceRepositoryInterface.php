<?php

declare(strict_types=1);

namespace Modules\CRM\Source\Domain;

interface SourceRepositoryInterface
{
    public function save(Source $source): void;
    
    public function findById(string $id): ?Source;
    
    public function findByCode(string $code): ?Source;
    
    public function delete(Source $source): void;
}
