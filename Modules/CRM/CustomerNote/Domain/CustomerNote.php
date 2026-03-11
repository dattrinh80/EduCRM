<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerNote\Domain;

use App\Core\Domain\Entity;

class CustomerNote extends Entity
{
    public function __construct(
        string $id,
        public string $customerId,
        public string $content,
        public ?string $createdBy,
        public ?\DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
    }

    public static function create(
        string $id,
        string $customerId,
        string $content,
        ?string $createdBy = null
    ): self {
        if (empty(trim($content))) {
            throw new \InvalidArgumentException('Note content cannot be empty.');
        }

        return new self(
            $id,
            $customerId,
            $content,
            $createdBy,
            new \DateTimeImmutable()
        );
    }
}

