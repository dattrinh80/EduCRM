<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use App\Core\CQRS\Command;

class CreateLeadCommand implements Command
{
    public function __construct(
        public readonly string $name,
        public readonly string $phone,
        public readonly ?string $email = null,
        public readonly ?string $centerId = null,
        public readonly ?string $dob = null,
        public readonly ?string $leadSourceId = null,
        public readonly ?string $campaignId = null,
        public readonly ?string $interestTypeId = null,
        public readonly ?string $assignedTo = null,
        public readonly ?string $statusId = null,
        public readonly array $tagIds = [],
        public readonly ?string $assignedBy = null
    ) {
    }
}
