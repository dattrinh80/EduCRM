<?php

declare(strict_types=1);

namespace Modules\Education\Student\Infrastructure\Persistence;

use Modules\Education\Student\Domain\Student;
use Modules\Education\Student\Domain\StudentRepositoryInterface;
use Illuminate\Support\Str;

class EloquentStudentRepository implements StudentRepositoryInterface
{
    public function findById(string $id): ?Student
    {
        $model = StudentModel::find($id);
        if (!$model) {
            return null;
        }

        return $this->mapToDomain($model);
    }

    public function save(Student $student): void
    {
        StudentModel::updateOrCreate(
            ['id' => $student->id],
            [
                'customer_id' => $student->customerId,
                'student_code' => $student->studentCode,
                'status' => $student->status,
            ]
        );
    }

    public function getNextStudentCode(): string
    {
        $lastStudent = StudentModel::orderBy('created_at', 'desc')->first();
        if (!$lastStudent) {
            return 'STU00001';
        }

        $lastCode = $lastStudent->student_code;
        $number = (int) substr($lastCode, 3);
        $nextNumber = $number + 1;

        return 'STU' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public function saveGuardianLink(string $studentId, string $guardianId, ?string $relationship = null, bool $isPrimary = false): void
    {
        StudentGuardianModel::updateOrCreate(
            [
                'student_id' => $studentId,
                'guardian_id' => $guardianId,
            ],
            [
                'relationship' => $relationship,
                'is_primary' => $isPrimary,
            ]
        );
    }

    private function mapToDomain(StudentModel $model): Student
    {
        return new Student(
            id: $model->id,
            customerId: $model->customer_id,
            studentCode: $model->student_code,
            status: $model->status,
            createdAt: $model->created_at?->toDateTimeString(),
            updatedAt: $model->updated_at?->toDateTimeString()
        );
    }
}
