<?php

declare(strict_types=1);

namespace Modules\CRM\LeadNote\Domain;

use App\Core\Domain\Entity;

class LeadNote extends Entity
{
    public function __construct(
        string $id,
        public string $leadId,
        public string $content,
        public ?string $createdBy,
        public ?\DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
    }

    public static function create(
        string $id,
        string $leadId,
        string $content,
        ?string $createdBy = null
    ): self {
        if (empty(trim($content))) {
            throw new \InvalidArgumentException('Note content cannot be empty.');
        }

        return new self(
            $id,
            $leadId,
            $content,
            $createdBy,
            new \DateTimeImmutable()
        );
    }
}

