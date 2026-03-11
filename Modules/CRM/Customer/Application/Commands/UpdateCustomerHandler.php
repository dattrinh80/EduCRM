<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\CRM\Customer\Domain\Customer;
use Modules\CRM\Customer\Domain\CustomerRepositoryInterface;
use Modules\CRM\CustomerTag\Domain\CustomerTagRepositoryInterface;

class UpdateCustomerHandler implements CommandHandler
{
    public function __construct(
        private readonly CustomerRepositoryInterface $repository,
        private readonly CustomerTagRepositoryInterface $tagRepository
    ) {
    }

    public function handle(Command $command): Customer
    {
        /** @var UpdateCustomerCommand $command */
        
        $customer = $this->repository->findById($command->id);

        if (!$customer) {
            throw new \Exception('Customer not found');
        }

        $customer->update(
            name: $command->name,
            phone: $command->phone,
            email: $command->email,
            centerId: $command->centerId,
            dob: $command->dob,
            gender: $command->gender,
            address: $command->address
        );

        $this->repository->save($customer);
        $this->tagRepository->syncTagsForCustomer($customer->id, $command->tagIds);

        return $customer;
    }
}
