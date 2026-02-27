<?php

declare(strict_types=1);

namespace Modules\Core\Center\Domain;

use App\Core\Domain\Entity;

class Center extends Entity
{
    public function __construct(
        string $id,
        public string $name,
        public string $code,
        public ?string $phone,
        public ?string $email,
        public ?string $address,
        public string $status,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
    }

    public static function create(
        string $id,
        string $name,
        string $code,
        ?string $phone,
        ?string $email,
        ?string $address
    ): self {
        return new self(
            $id,
            $name,
            $code,
            $phone,
            $email,
            $address,
            'active',
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
    }

    public function update(
        string $name,
        string $code,
        ?string $phone,
        ?string $email,
        ?string $address,
        string $status
    ): void {
        $this->name = $name;
        $this->code = $code;
        $this->phone = $phone;
        $this->email = $email;
        $this->address = $address;
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
