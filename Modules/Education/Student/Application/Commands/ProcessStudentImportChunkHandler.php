<?php

declare(strict_types=1);

namespace Modules\Education\Student\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\CRM\Customer\Domain\Customer;
use Modules\CRM\Customer\Domain\CustomerRepositoryInterface;
use Modules\Education\Student\Domain\Student;
use Modules\Education\Student\Domain\StudentRepositoryInterface;

class ProcessStudentImportChunkHandler implements CommandHandler
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly StudentRepositoryInterface $studentRepository
    ) {}

    public function handle(Command $command): mixed
    {
        /** @var ProcessStudentImportChunkCommand $command */
        $rows = Cache::get('student_import_' . $command->importId);
        if (!$rows) {
            throw new \Exception('Import session expired.');
        }

        $chunk = array_slice($rows, $command->offset, $command->limit);
        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($chunk as $index => $row) {
            try {
                DB::transaction(function () use ($row, $command) {
                    $name = $row['ho_va_ten'] ?? $row['name'] ?? null;
                    $phone = $row['so_dien_thoai'] ?? $row['phone'] ?? null;
                    if (!$name) throw new \Exception('Thiếu cột Họ và tên.');

                    // 1. Create/Find Customer
                    $customerId = (string) Str::uuid();
                    $customer = Customer::create(
                        id: $customerId,
                        name: $name,
                        phone: $phone ? (string)$phone : null,
                        email: $row['email'] ?? null,
                        centerId: $command->centerId,
                        dob: $row['ngay_sinh'] ?? null,
                        gender: isset($row['gioi_tinh']) ? (str_contains(strtolower($row['gioi_tinh']), 'nam') ? 'MALE' : 'FEMALE') : 'OTHER'
                    );
                    $this->customerRepository->save($customer);

                    // 2. Create Student
                    $studentId = (string) Str::uuid();
                    $studentCode = $row['ma_hoc_vien'] ?? $row['student_code'] ?? $this->studentRepository->getNextStudentCode();
                    $student = Student::create(
                        id: $studentId,
                        customerId: $customerId,
                        studentCode: (string)$studentCode,
                        status: 'NEW'
                    );
                    $this->studentRepository->save($student);

                    // 3. Guardian (optional)
                    $guardianName = $row['ho_ten_nguoi_giam_ho'] ?? $row['guardian_name'] ?? null;
                    if ($guardianName) {
                        $guardianId = (string) Str::uuid();
                        $guardian = Customer::create(
                            id: $guardianId,
                            name: $guardianName,
                            phone: isset($row['sdt_nguoi_giam_ho']) ? (string)$row['sdt_nguoi_giam_ho'] : null,
                            centerId: $command->centerId
                        );
                        $this->customerRepository->save($guardian);
                        $this->studentRepository->saveGuardianLink($studentId, $guardianId, 'Parent', true);
                    }
                });
                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Dòng " . ($command->offset + $index + 2) . ": " . $e->getMessage();
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors,
            'is_finished' => ($command->offset + $command->limit) >= count($rows)
        ];
    }
}
