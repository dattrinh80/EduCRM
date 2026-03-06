<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\LeadActivity\Application\Commands;

use Illuminate\Support\Str;
use Modules\CRM\Lead\LeadActivity\Domain\LeadActivity;
use Modules\CRM\Lead\LeadActivity\Domain\LeadActivityRepositoryInterface;

class AddLeadActivityHandler
{
    public function __construct(
        private LeadActivityRepositoryInterface $repository
    ) {}

    public function handle(AddLeadActivityCommand $command): LeadActivity
    {
        $activity = LeadActivity::create(
            (string) Str::uuid(),
            $command->leadId,
            $command->activityType,
            $command->description,
            $command->createdBy
        );

        $this->repository->save($activity);

        return $activity;
    }
}
