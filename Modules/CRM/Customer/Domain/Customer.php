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
        ?string $centerId = null,
        ?string $dob = null,
        ?string $gender = null,
        ?string $address = null
    ): self {
        return new self(
            id: $id,
            name: $name,
            phone: $phone,
            email: $email,
            dob: $dob,
            gender: $gender,
            address: $address,
            centerId: $centerId,
            createdAt: now()->toDateTimeString(),
            updatedAt: now()->toDateTimeString()
        );
    }

    public function update(
        string $name,
        ?string $phone = null,
        ?string $email = null,
        ?string $centerId = null,
        ?string $dob = null,
        ?string $gender = null,
        ?string $address = null
    ): void {
        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
        $this->centerId = $centerId;
        $this->dob = $dob;
        $this->gender = $gender;
        $this->address = $address;
        $this->updatedAt = now()->toDateTimeString();
    }
}
