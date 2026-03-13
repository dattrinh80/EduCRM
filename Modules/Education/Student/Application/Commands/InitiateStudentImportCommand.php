<?php

declare(strict_types=1);

namespace Modules\Education\Student\Application\Commands;

use App\Core\CQRS\Command;
use Illuminate\Http\UploadedFile;

class InitiateStudentImportCommand implements Command
{
    public function __construct(
        public readonly UploadedFile $file
    ) {}
}
