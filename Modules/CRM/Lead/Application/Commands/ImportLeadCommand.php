<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use App\Core\CQRS\Command;

class ImportLeadCommand implements Command
{
    public function __construct(
        public readonly string $name,
        public readonly string $phone,
        public readonly ?string $email = null,
        public readonly ?string $centerCode = null,
        public readonly ?string $dob = null,
        public readonly ?string $leadSourceCode = null,
        public readonly ?string $campaignCode = null,
        public readonly ?string $interestTypeCode = null
    ) {
    }
}
