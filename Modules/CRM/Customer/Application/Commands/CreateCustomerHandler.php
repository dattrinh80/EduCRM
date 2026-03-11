<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\CRM\Customer\Domain\Customer;
use Modules\CRM\Customer\Domain\CustomerRepositoryInterface;
use Modules\CRM\CustomerTag\Domain\CustomerTagRepositoryInterface;
use Illuminate\Support\Str;

class CreateCustomerHandler implements CommandHandler
{
    public function __construct(
        private readonly CustomerRepositoryInterface $repository,
        private readonly CustomerTagRepositoryInterface $tagRepository
    ) {
    }

    public function handle(Command $command): Customer
    {
        /** @var CreateCustomerCommand $command */
        
        $customer = Customer::create(
            id: (string) Str::uuid(),
            name: $command->name,
            phone: $command->phone,
            email: $command->email,
            centerId: $command->centerId,
            dob: $command->dob,
            gender: $command->gender,
            address: $command->address
        );

        $this->repository->save($customer);

        if (!empty($command->tagIds)) {
            $this->tagRepository->syncTagsForCustomer($customer->id, $command->tagIds);
        }

        return $customer;
    }
}
