<?php

declare(strict_types=1);

namespace Modules\Education\Student\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\CRM\Customer\Domain\Customer;
use Modules\CRM\Customer\Domain\CustomerRepositoryInterface;
use Modules\Education\Student\Domain\Student;
use Modules\Education\Student\Domain\StudentRepositoryInterface;

class CreateStudentHandler implements CommandHandler
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly StudentRepositoryInterface $studentRepository
    ) {}

    public function handle(Command $command): mixed
    {
        /** @var CreateStudentCommand $command */
        return DB::transaction(function () use ($command) {
            // 1. Create/Find Customer for the student
            // For now, we always create a new customer record for the student identity
            $customerId = (string) Str::uuid();
            $customer = Customer::create(
                id: $customerId,
                name: $command->name,
                phone: $command->phone,
                email: $command->email,
                centerId: $command->centerId,
                dob: $command->dob,
                gender: $command->gender,
                address: $command->address
            );
            $this->customerRepository->save($customer);

            // 2. Create Student record
            $studentId = (string) Str::uuid();
            $studentCode = $command->studentCode ?? $this->studentRepository->getNextStudentCode();
            $student = Student::create(
                id: $studentId,
                customerId: $customerId,
                studentCode: $studentCode,
                status: $command->status ?? 'NEW'
            );
            $this->studentRepository->save($student);

            // 3. Link Guardians
            foreach ($command->guardians as $guardianData) {
                $guardianCustomerId = $guardianData['customer_id'] ?? null;
                
                if (!$guardianCustomerId) {
                    // Create new customer for guardian if not provided
                    $guardianCustomerId = (string) Str::uuid();
                    $guardian = Customer::create(
                        id: $guardianCustomerId,
                        name: $guardianData['name'],
                        phone: $guardianData['phone'] ?? null,
                        email: $guardianData['email'] ?? null,
                        centerId: $command->centerId,
                        dob: $guardianData['dob'] ?? null,
                        gender: $guardianData['gender'] ?? null,
                        address: $guardianData['address'] ?? null
                    );
                    $this->customerRepository->save($guardian);
                }

                $this->studentRepository->saveGuardianLink(
                    studentId: $studentId,
                    guardianId: $guardianCustomerId,
                    relationship: $guardianData['relationship'] ?? 'Guardian',
                    isPrimary: (bool)($guardianData['is_primary'] ?? false)
                );
            }

            return $student;
        });
    }
}
