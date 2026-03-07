<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Domain;

interface CustomerRepositoryInterface
{
    public function findById(string $id): ?Customer;
    public function save(Customer $customer): void;
    public function findByPhone(string $phone): ?Customer;
    public function findByEmail(string $email): ?Customer;
}
