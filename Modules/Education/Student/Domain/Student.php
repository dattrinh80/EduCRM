<?php

declare(strict_types=1);

namespace Modules\Education\Student\Domain;

class Student
{
    public function __construct(
        public string $id,
        public string $customerId,
        public string $studentCode,
        public string $status = 'NEW',
        public ?string $studentName = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

    public static function create(
        string $id,
        string $customerId,
        string $studentCode,
        string $status = 'NEW'
    ): self {
        return new self(
            id: $id,
            customerId: $customerId,
            studentCode: $studentCode,
            status: $status
        );
    }
}
