<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

class ConvertLeadToStudentCommand
{
    public function __construct(
        public string $leadId,
        public array $guardianData, // [name, phone, email, address, dob, gender]
        public array $students, // [[name, dob, gender], [name, dob, gender], ...]
        public ?string $convertedBy = null
    ) {}
}
