<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerActivity\Application\Commands;

use Illuminate\Support\Str;
use Modules\CRM\CustomerActivity\Domain\CustomerActivity;
use Modules\CRM\CustomerActivity\Domain\CustomerActivityRepositoryInterface;

class AddCustomerActivityHandler
{
    public function __construct(
        private CustomerActivityRepositoryInterface $repository
    ) {}

    public function handle(AddCustomerActivityCommand $command): CustomerActivity
    {
        $activity = CustomerActivity::create(
            (string) Str::uuid(),
            $command->customerId,
            $command->activityType,
            $command->description,
            $command->createdBy
        );

        $this->repository->save($activity);

        return $activity;
    }
}

