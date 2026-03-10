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
        public string $statusId,
        public ?string $centerId,
        public ?string $dob = null,
        public ?string $gender = null,
        public ?string $leadSourceId = null,
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
        string $statusId,
        ?string $centerId,
        ?string $dob = null,
        ?string $gender = null,
        ?string $leadSourceId = null,
        ?string $campaignId = null,
        ?string $interestTypeId = null,
        ?string $assignedTo = null
    ): self {
        return new self(
            $id,
            $name,
            $phone,
            $email,
            $statusId,
            $centerId,
            $dob,
            $gender,
            $leadSourceId,
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
        string $newStatusId,
        string $newStage,
        ?string $currentStage = null,
        ?string $centerId = null,
        ?string $dob = null,
        ?string $gender = null,
        ?string $leadSourceId = null,
        ?string $campaignId = null,
        ?string $interestTypeId = null,
        ?string $assignedTo = null
    ): void {
        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
        
        if ($this->statusId !== $newStatusId) {
            $this->changeStatus($newStatusId, $newStage, $currentStage);
        }

        $this->centerId = $centerId;
        $this->dob = $dob;
        $this->gender = $gender;
        $this->leadSourceId = $leadSourceId;
        $this->campaignId = $campaignId;
        $this->interestTypeId = $interestTypeId;
        $this->assignedTo = $assignedTo;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function changeStatus(string $newStatusId, string $newStage, ?string $currentStage = null): void
    {
        // Validation: Cannot move out of LOST or CONVERTED once reaching them (business rule example)
        if ($currentStage === \Modules\CRM\LeadStatus\Domain\LeadStatus::STAGE_LOST && $newStage !== \Modules\CRM\LeadStatus\Domain\LeadStatus::STAGE_LOST) {
            throw new \DomainException("Cannot change status from a 'Lost' state.");
        }

        if ($currentStage === \Modules\CRM\LeadStatus\Domain\LeadStatus::STAGE_CONVERTED && $newStage !== \Modules\CRM\LeadStatus\Domain\LeadStatus::STAGE_CONVERTED) {
            throw new \DomainException("Cannot change status of a 'Converted' lead.");
        }

        $this->statusId = $newStatusId;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function assignTo(?string $userId): void {
        $this->assignedTo = $userId;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function setLeadSource(?string $leadSourceId): void {
        $this->leadSourceId = $leadSourceId;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function setInterestType(?string $interestTypeId): void {
        $this->interestTypeId = $interestTypeId;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function setCenter(?string $centerId): void {
        $this->centerId = $centerId;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function setCampaign(?string $campaignId): void {
        $this->campaignId = $campaignId;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function setStatus(string $statusId, string $newStage, ?string $currentStage = null): void {
        $this->changeStatus($statusId, $newStage, $currentStage);
    }
}

