<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Domain;

use App\Core\Domain\Entity;

class Lead extends Entity
{
    public function __construct(
        string $id,
        public string $name,
        public string $phone,
        public ?string $email,
        public string $status,
        public ?string $centerId,
        public ?string $dob = null,
        public ?string $sourceId = null,
        public ?string $campaignId = null,
        public ?string $interestTypeId = null,
        public ?string $assignedTo = null,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
    }

    public static function create(
        string $id,
        string $name,
        string $phone,
        ?string $email,
        ?string $centerId,
        ?string $dob = null,
        ?string $sourceId = null,
        ?string $campaignId = null,
        ?string $interestTypeId = null,
        ?string $assignedTo = null
    ): self {
        return new self(
            $id,
            $name,
            $phone,
            $email,
            'new',
            $centerId,
            $dob,
            $sourceId,
            $campaignId,
            $interestTypeId,
            $assignedTo,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
    }

    public function update(
        string $name,
        string $phone,
        ?string $email,
        string $status,
        ?string $centerId,
        ?string $dob = null,
        ?string $sourceId = null,
        ?string $campaignId = null,
        ?string $interestTypeId = null,
        ?string $assignedTo = null
    ): void {
        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
        $this->status = $status;
        $this->centerId = $centerId;
        $this->dob = $dob;
        $this->sourceId = $sourceId;
        $this->campaignId = $campaignId;
        $this->interestTypeId = $interestTypeId;
        $this->assignedTo = $assignedTo;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function assignTo(?string $userId): void {
        $this->assignedTo = $userId;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
