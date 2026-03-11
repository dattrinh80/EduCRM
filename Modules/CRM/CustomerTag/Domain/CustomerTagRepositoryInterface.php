<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerTag\Domain;

interface CustomerTagRepositoryInterface
{
    public function save(CustomerTag $tag): void;

    public function findById(string $id): ?CustomerTag;

    public function findByName(string $name): ?CustomerTag;

    public function getAll(): array;

    public function delete(string $id): void;
    
    public function syncTagsForCustomer(string $customerId, array $tagIds): void;
}

