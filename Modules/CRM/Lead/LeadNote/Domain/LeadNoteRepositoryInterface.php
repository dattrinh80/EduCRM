<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\LeadNote\Domain;

interface LeadNoteRepositoryInterface
{
    public function save(LeadNote $note): void;

    public function findById(string $id): ?LeadNote;

    public function delete(string $id): void;

    public function deleteByLeadId(string $leadId): void;
}
