<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Domain;

class Customer
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $dob = null,
        public ?string $gender = null,
        public ?string $address = null,
        public ?string $centerId = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

    public static function create(
        string $id,
        string $name,
        ?string $phone = null,
        ?string $email = null,
        ?string $centerId = null
    ): self {
        return new self(
            id: $id,
            name: $name,
            phone: $phone,
            email: $email,
            centerId: $centerId
        );
    }
}
