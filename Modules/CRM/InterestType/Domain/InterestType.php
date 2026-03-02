<?php

declare(strict_types=1);

namespace Modules\CRM\InterestType\Domain;

use App\Core\Domain\Entity;

class InterestType extends Entity
{
    public function __construct(
        string $id,
        public string $name,
        public ?string $description,
        public bool $isActive = true,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
    }

    public static function create(
        string $id,
        string $name,
        ?string $description
    ): self {
        return new self(
            $id,
            $name,
            $description,
            true,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
    }

    public function update(
        string $name,
        ?string $description,
        bool $isActive
    ): void {
        $this->name = $name;
        $this->description = $description;
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
