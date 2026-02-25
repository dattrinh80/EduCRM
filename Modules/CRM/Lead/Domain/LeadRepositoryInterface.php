<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Domain;

interface LeadRepositoryInterface
{
    public function save(Lead $lead): void;

    public function update(Lead $lead): void;

    public function delete(string $id): void;

    public function findById(string $id): ?Lead;
}
