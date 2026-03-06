<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\LeadStatus\Domain;

interface LeadStatusRepositoryInterface
{
    public function save(LeadStatus $status): void;

    public function findById(string $id): ?LeadStatus;

    public function findByName(string $name): ?LeadStatus;

    public function getAllActive(): array;
}
