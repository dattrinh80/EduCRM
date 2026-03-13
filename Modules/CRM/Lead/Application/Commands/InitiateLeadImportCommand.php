<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use App\Core\CQRS\Command;
use Illuminate\Http\UploadedFile;

class InitiateLeadImportCommand implements Command
{
    public function __construct(
        public readonly UploadedFile $file
    ) {}
}
