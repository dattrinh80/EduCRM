<?php

declare(strict_types=1);

namespace Modules\Marketing\LeadSource\Domain;

use App\Core\Domain\Entity;

class LeadSource extends Entity
{
    public function __construct(
        string $id,
        public string $name,
        public string $code,
        public bool $isActive = true,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
    }

    public static function create(
        string $id,
        string $name,
        string $code
    ): self {
        return new self(
            $id,
            $name,
            $code,
            true,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
    }

    public function update(
        string $name,
        string $code,
        bool $isActive
    ): void {
        $this->name = $name;
        $this->code = $code;
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
