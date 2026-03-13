<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Commands;

use Illuminate\Http\UploadedFile;

class InitiateCustomerImportCommand
{
    public function __construct(
        public readonly UploadedFile $file
    ) {}
}
