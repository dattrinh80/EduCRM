<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Modules\Core\User\Domain\User;
use Modules\Core\User\Domain\UserRepositoryInterface;
use Modules\Core\User\Infrastructure\ReadModels\UserRoleReadModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CreateUserHandler implements CommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository
    ) {
    }

    public function handle(Command $command): User
    {
        /** @var CreateUserCommand $command */

        $user = User::create(
            (string) Str::uuid(),
            $command->name,
            $command->email,
            Hash::make($command->password),
            $command->defaultCenterId
        );

        $this->repository->save($user);

        // Assign roles with scopes
        foreach ($command->roles as $role) {
            UserRoleReadModel::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->getId(),
                'role_id' => $role['role_id'],
                'scope_type' => $role['scope_type'],
                'scope_id' => $role['scope_id'] ?? null,
            ]);
        }

        return $user;
    }
}
