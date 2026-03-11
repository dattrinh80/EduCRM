<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerActivity\Domain;

interface CustomerActivityRepositoryInterface
{
    public function save(CustomerActivity $activity): void;

    public function findById(string $id): ?CustomerActivity;

    public function deleteByCustomerId(string $customerId): void;
}

