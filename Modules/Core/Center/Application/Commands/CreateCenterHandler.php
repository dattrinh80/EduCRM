<?php

declare(strict_types=1);

namespace Modules\Core\Center\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\Core\Center\Domain\Center;
use Modules\Core\Center\Domain\CenterRepositoryInterface;
use Illuminate\Support\Str;

class CreateCenterHandler implements CommandHandler
{
    public function __construct(
        private readonly CenterRepositoryInterface $repository
    ) {
    }

    public function handle(Command $command): Center
    {
        /** @var CreateCenterCommand $command */

        $center = Center::create(
            (string) Str::uuid(),
            $command->name,
            $command->code,
            $command->phone,
            $command->email,
            $command->address
        );

        $this->repository->save($center);

        return $center;
    }
}
