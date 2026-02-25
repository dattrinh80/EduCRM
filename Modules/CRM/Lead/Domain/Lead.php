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
        ?string $centerId
    ): self {
        return new self(
            $id,
            $name,
            $phone,
            $email,
            'new',
            $centerId,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
    }

    public function update(
        string $name,
        string $phone,
        ?string $email,
        string $status,
        ?string $centerId
    ): void {
        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
        $this->status = $status;
        $this->centerId = $centerId;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
