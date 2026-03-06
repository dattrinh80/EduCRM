<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Domain;

class LeadAssignment
{
    public function __construct(
        private readonly string $id,
        private readonly string $leadId,
        private readonly string $assignedTo,
        private readonly ?string $assignedBy = null,
        private readonly ?string $notes = null,
        private readonly ?\DateTimeImmutable $createdAt = null
    ) {
    }

    public static function create(
        string $id,
        string $leadId,
        string $assignedTo,
        ?string $assignedBy = null,
        ?string $notes = null
    ): self {
        return new self($id, $leadId, $assignedTo, $assignedBy, $notes, new \DateTimeImmutable());
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLeadId(): string
    {
        return $this->leadId;
    }

    public function getAssignedTo(): string
    {
        return $this->assignedTo;
    }

    public function getAssignedBy(): ?string
    {
        return $this->assignedBy;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}
