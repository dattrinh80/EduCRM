<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\Core\User\Infrastructure\Services\SystemAuditLogger;

class LogSystemOwnerLoginHandler implements CommandHandler
{
    public function handle(Command $command): mixed
    {
        /** @var LogSystemOwnerLoginCommand $command */
        SystemAuditLogger::log('LOGIN_SYSTEM_OWNER', $command->userId, $command->targetId);
        return null;
    }
}
