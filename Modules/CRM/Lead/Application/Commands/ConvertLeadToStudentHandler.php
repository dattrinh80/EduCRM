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

            // 1. Create/Update Guardian
            $guardianId = (string) Str::uuid();
            $guardian = Customer::create(
                id: $guardianId,
                name: $command->guardianData['name'],
                phone: $command->guardianData['phone'] ?? null,
                email: $command->guardianData['email'] ?? null,
                centerId: $lead->centerId,
                dob: $command->guardianData['dob'] ?? null,
                gender: $command->guardianData['gender'] ?? null,
                address: $command->guardianData['address'] ?? null
            );
            
            $this->customerRepository->save($guardian);

            // 2. Process Students
            foreach ($command->students as $studentData) {
                // 2a. Create Customer for student
                $studentCustomerId = (string) Str::uuid();
                $studentCustomer = Customer::create(
                    id: $studentCustomerId,
                    name: $studentData['name'],
                    centerId: $lead->centerId,
                    dob: $studentData['dob'] ?? null,
                    gender: $studentData['gender'] ?? null
                );
                
                $this->customerRepository->save($studentCustomer);

                // 2b. Create Student record
                $studentId = (string) Str::uuid();
                $studentCode = $this->studentRepository->getNextStudentCode();
                $student = Student::create(
                    id: $studentId,
                    customerId: $studentCustomerId,
                    studentCode: $studentCode
                );
                
                $this->studentRepository->save($student);

                // 2c. Link Student to Guardian
                $this->studentRepository->saveGuardianLink(
                    studentId: $studentId,
                    guardianId: $guardianId,
                    relationship: $studentData['relationship'] ?? 'Parent',
                    isPrimary: true
                );

                // 2d. Record Conversion
                $conversion = LeadConversion::create(
                    id: (string) Str::uuid(),
                    leadId: $command->leadId,
                    studentId: $studentId,
                    convertedBy: $command->convertedBy
                );
                
                $this->conversionRepository->save($conversion);
            }

            // 3. Update Lead Status
            $convertedStatus = $this->statusRepository->findByStage(\Modules\CRM\LeadStatus\Domain\LeadStatus::STAGE_CONVERTED);
            if ($convertedStatus) {
                // Note: We might want to use PARTIALLY_CONVERTED if the logic grows more complex, 
                // but for now we follow the document's basic conversion flow.
                $lead->statusId = $convertedStatus->getId();
                $this->leadRepository->update($lead);
            }
        });
    }
}
