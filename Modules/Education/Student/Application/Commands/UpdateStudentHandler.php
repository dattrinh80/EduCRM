<?php

declare(strict_types=1);

namespace Modules\Education\Student\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Illuminate\Support\Facades\DB;
use Modules\Education\Student\Domain\StudentRepositoryInterface;
use Modules\CRM\Customer\Domain\CustomerRepositoryInterface;

class UpdateStudentHandler implements CommandHandler
{
    public function __construct(
        private readonly StudentRepositoryInterface $studentRepository,
        private readonly CustomerRepositoryInterface $customerRepository
    ) {}

    public function handle(Command $command): mixed
    {
        /** @var UpdateStudentCommand $command */
        return DB::transaction(function () use ($command) {
            $student = $this->studentRepository->findById($command->id);
            if (!$student) {
                throw new \Exception('Student not found');
            }

            // Update student status if provided
            if ($command->status !== null) {
                $student->status = $command->status;
            }
            $this->studentRepository->save($student);

            // Update linked customer data
            $customer = $this->customerRepository->findById($student->customerId);
            if ($customer) {
                $customer->update(
                    name: $command->name ?? $customer->name,
                    phone: $command->phone ?? $customer->phone,
                    email: $command->email ?? $customer->email,
                    centerId: $customer->centerId,
                    dob: $command->dob ?? $customer->dob,
                    gender: $command->gender ?? $customer->gender,
                    address: $command->address ?? $customer->address
                );
                $this->customerRepository->update($customer);
            }

            return $student;
        });
    }
}
