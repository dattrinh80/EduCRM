<?php

declare(strict_types=1);

namespace Modules\CRM\LeadSource\Application\Commands;

use Illuminate\Support\Str;
use Modules\CRM\LeadSource\Domain\LeadSource;
use Modules\CRM\LeadSource\Domain\LeadSourceRepositoryInterface;

class CreateLeadSourceHandler
{
    public function __construct(
        private LeadSourceRepositoryInterface $repository
    ) {}

    public function handle(CreateLeadSourceCommand $command): string
    {
        $id = Str::uuid()->toString();

        $leadSource = LeadSource::create(
            $id,
            $command->name,
            $command->code
        );

        $this->repository->save($leadSource);

        return $id;
    }
}
