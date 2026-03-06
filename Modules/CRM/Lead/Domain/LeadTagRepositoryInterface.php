<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Domain;

interface LeadTagRepositoryInterface
{
    public function save(LeadTag $tag): void;

    public function findById(string $id): ?LeadTag;

    public function findByName(string $name): ?LeadTag;

    public function getAll(): array;

    public function delete(string $id): void;
    
    public function syncTagsForLead(string $leadId, array $tagIds): void;
}
