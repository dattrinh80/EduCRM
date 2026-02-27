<?php

declare(strict_types=1);

namespace Modules\Core\User\Domain;

use App\Core\Domain\Entity;

class User extends Entity
{
    public function __construct(
        string $id,
        public string $name,
        public string $email,
        public string $password,
        public ?string $centerId = null,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
    }

    public static function create(
        string $id,
        string $name,
        string $email,
        string $password,
        ?string $centerId = null
    ): self {
        return new self(
            $id,
            $name,
            $email,
            $password,
            $centerId,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
    }

    public function update(
        string $name,
        string $email,
        ?string $password = null,
        ?string $centerId = null
    ): void {
        $this->name = $name;
        $this->email = $email;
        $this->centerId = $centerId;
        if ($password !== null) {
            $this->password = $password;
        }
        $this->updatedAt = new \DateTimeImmutable();
    }
}
