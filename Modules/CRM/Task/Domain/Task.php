<?php

declare(strict_types=1);

namespace Modules\CRM\Task\Domain;

use App\Core\Domain\Entity;
use DateTimeImmutable;

class Task extends Entity
{
    public const STATUS_TODO = 'TODO';
    public const STATUS_DOING = 'DOING';
    public const STATUS_DONE = 'DONE';
    public const STATUS_CANCELLED = 'CANCELLED';

    public const PRIORITY_LOW = 'LOW';
    public const PRIORITY_MEDIUM = 'MEDIUM';
    public const PRIORITY_HIGH = 'HIGH';
    public const PRIORITY_URGENT = 'URGENT';

    public function __construct(
        string $id,
        public string $title,
        public ?string $description,
        public ?string $dueDate,
        public string $status,
        public string $priority,
        public ?string $assignedTo,
        public string $assignedBy,
        public string $centerId,
        public ?string $relationId = null,
        public ?string $relationType = null,
        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
    }

    public static function create(
        string $id,
        string $title,
        ?string $description,
        ?string $dueDate,
        string $priority,
        ?string $assignedTo,
        string $assignedBy,
        string $centerId,
        ?string $relationId = null,
        ?string $relationType = null
    ): self {
        return new self(
            $id,
            $title,
            $description,
            $dueDate,
            self::STATUS_TODO,
            $priority,
            $assignedTo,
            $assignedBy,
            $centerId,
            $relationId,
            $relationType,
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );
    }

    public function update(
        string $title,
        ?string $description,
        ?string $dueDate,
        string $status,
        string $priority,
        ?string $assignedTo
    ): void {
        $this->title = $title;
        $this->description = $description;
        $this->dueDate = $dueDate;
        $this->status = $status;
        $this->priority = $priority;
        $this->assignedTo = $assignedTo;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function markAsDone(): void
    {
        $this->status = self::STATUS_DONE;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function toggleStatus(): void
    {
        $this->status = ($this->status === self::STATUS_DONE) ? self::STATUS_TODO : self::STATUS_DONE;
        $this->updatedAt = new DateTimeImmutable();
    }
}
