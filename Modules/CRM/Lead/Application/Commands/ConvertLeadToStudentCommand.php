<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

class ConvertLeadToStudentCommand
{
    /**
     * @param string $leadId
     * @param array $students Array of student data, each containing:
     *   - name (string, required)
     *   - dob (string|null)
     *   - gender (string|null) MALE/FEMALE/OTHER
     *   - school (string|null)
     *   - grade (string|null)
     *   - guardians (array) each containing:
     *       - name (string, required)
     *       - phone (string, required)
     *       - email (string|null)
     *       - relationship (string, required) e.g. Father/Mother/Grandparent/Guardian/Sibling/Other
     *       - is_primary (bool)
     * @param string|null $convertedBy
     */
    public function __construct(
        public string $leadId,
        public array $students,
        public ?string $convertedBy = null
    ) {}
}
