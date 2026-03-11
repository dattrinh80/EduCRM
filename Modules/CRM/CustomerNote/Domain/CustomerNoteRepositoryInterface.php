<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerNote\Domain;

interface CustomerNoteRepositoryInterface
{
    public function save(CustomerNote $note): void;

    public function findById(string $id): ?CustomerNote;

    public function delete(string $id): void;

    public function deleteByCustomerId(string $customerId): void;
}

