<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\CRM\Customer\Infrastructure\Persistence\CustomerModel;

class DeleteCustomerHandler implements CommandHandler
{
    public function handle(Command $command): void
    {
        /** @var DeleteCustomerCommand $command */
        $model = CustomerModel::find($command->id);
        
        if (!$model) {
            throw new \Exception('Customer not found');
        }

        $model->delete();
    }
}
