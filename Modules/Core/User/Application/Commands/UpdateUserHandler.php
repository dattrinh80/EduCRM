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

class UpdateUserHandler implements CommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository
    ) {
    }

    public function handle(Command $command): User
    {
        /** @var UpdateUserCommand $command */

        $user = $this->repository->findById($command->id);

        if (!$user) {
            throw new \Exception('User not found');
        }

        $user->update(
            $command->name,
            $command->email,
            $command->password ? Hash::make($command->password) : null
        );

        $this->repository->update($user);

        // Sync roles: delete old assignments, create new ones
        UserRoleReadModel::where('user_id', $command->id)->delete();

        foreach ($command->roles as $role) {
            UserRoleReadModel::create([
                'id' => (string) Str::uuid(),
                'user_id' => $command->id,
                'role_id' => $role['role_id'],
                'scope_type' => $role['scope_type'],
                'scope_id' => $role['scope_id'] ?? null,
            ]);
        }

        return $user;
    }
}
