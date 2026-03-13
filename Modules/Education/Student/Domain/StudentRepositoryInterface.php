<?php

declare(strict_types=1);

namespace Modules\Education\Student\Domain;

interface StudentRepositoryInterface
{
    public function findById(string $id): ?Student;
    public function save(Student $student): void;
    public function getNextStudentCode(): string;
    public function saveGuardianLink(string $studentId, string $guardianId, ?string $relationship = null, bool $isPrimary = false): void;
    public function getAll(): array;
    public function delete(string $id): void;
}
