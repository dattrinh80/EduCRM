<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\CRM\Lead\Domain\LeadRepositoryInterface;
use Modules\CRM\Customer\Domain\Customer;
use Modules\CRM\Customer\Domain\CustomerRepositoryInterface;
use Modules\Education\Student\Domain\Student;
use Modules\Education\Student\Domain\StudentRepositoryInterface;
use Modules\CRM\Lead\Domain\LeadConversion;
use Modules\CRM\Lead\Domain\LeadConversionRepositoryInterface;
use Modules\CRM\LeadStatus\Domain\LeadStatusRepositoryInterface;

class ConvertLeadToStudentHandler
{
    public function __construct(
        private LeadRepositoryInterface $leadRepository,
        private CustomerRepositoryInterface $customerRepository,
        private StudentRepositoryInterface $studentRepository,
        private LeadConversionRepositoryInterface $conversionRepository,
        private LeadStatusRepositoryInterface $statusRepository
    ) {}

    public function handle(ConvertLeadToStudentCommand $command): void
    {
        DB::transaction(function () use ($command) {
            $lead = $this->leadRepository->findById($command->leadId);
            if (!$lead) {
                throw new \Exception("Lead not found: {$command->leadId}");
            }

            // Track created guardians by phone to enable reuse across students
            $guardianCache = []; // phone => customerId

            foreach ($command->students as $studentData) {
                // 1. Create Customer record for the student
                $studentCustomerId = (string) Str::uuid();
                $studentCustomer = Customer::create(
                    id: $studentCustomerId,
                    name: $studentData['name'],
                    phone: $studentData['phone'] ?? null,
                    email: $studentData['email'] ?? null,
                    centerId: $lead->centerId,
                    dob: $studentData['dob'] ?? null,
                    gender: $studentData['gender'] ?? null,
                    address: $studentData['address'] ?? null
                );
                $this->customerRepository->save($studentCustomer);

                // 2. Create Student record
                $studentId = (string) Str::uuid();
                $studentCode = $this->studentRepository->getNextStudentCode();
                $student = Student::create(
                    id: $studentId,
                    customerId: $studentCustomerId,
                    studentCode: $studentCode
                );
                $this->studentRepository->save($student);

                // 3. Process guardians for this student
                $guardians = $studentData['guardians'] ?? [];
                foreach ($guardians as $guardianData) {
                    $guardianCustomerId = null;

                    // If an existing customer was selected from the picker
                    if (!empty($guardianData['customer_id'])) {
                        $guardianCustomerId = $guardianData['customer_id'];
                    } else {
                        $guardianPhone = $guardianData['phone'] ?? null;

                        // Resolve guardian: reuse by phone or create new
                        if ($guardianPhone && isset($guardianCache[$guardianPhone])) {
                            $guardianCustomerId = $guardianCache[$guardianPhone];
                        } else {
                            // Check if a customer with this phone already exists
                            $existingCustomer = $guardianPhone
                                ? $this->customerRepository->findByPhone($guardianPhone)
                                : null;

                            if ($existingCustomer) {
                                $guardianCustomerId = $existingCustomer->id;
                            } else {
                                $guardianCustomerId = (string) Str::uuid();
                                $guardian = Customer::create(
                                    id: $guardianCustomerId,
                                    name: $guardianData['name'],
                                    phone: $guardianPhone,
                                    email: $guardianData['email'] ?? null,
                                    centerId: $lead->centerId,
                                    dob: $guardianData['dob'] ?? null,
                                    gender: $guardianData['gender'] ?? null,
                                    address: $guardianData['address'] ?? null
                                );
                                $this->customerRepository->save($guardian);
                            }

                            if ($guardianPhone) {
                                $guardianCache[$guardianPhone] = $guardianCustomerId;
                            }
                        }
                    }

                    // 4. Link guardian to student
                    $this->studentRepository->saveGuardianLink(
                        studentId: $studentId,
                        guardianId: $guardianCustomerId,
                        relationship: $guardianData['relationship'] ?? 'Parent',
                        isPrimary: (bool) ($guardianData['is_primary'] ?? false)
                    );
                }

                // 5. Record conversion
                $conversion = LeadConversion::create(
                    id: (string) Str::uuid(),
                    leadId: $command->leadId,
                    studentId: $studentId,
                    convertedBy: $command->convertedBy
                );
                $this->conversionRepository->save($conversion);
            }

            // 6. Update Lead Status to Converted
            $convertedStatus = $this->statusRepository->findByStage(
                \Modules\CRM\LeadStatus\Domain\LeadStatus::STAGE_CONVERTED
            );
            if ($convertedStatus) {
                $lead->statusId = $convertedStatus->getId();
                $this->leadRepository->update($lead);
            }
        });
    }
}
