<?php

declare(strict_types=1);

namespace Modules\Core\User\Infrastructure\Persistence;

use Modules\Core\User\Domain\User;
use Modules\Core\User\Domain\UserRepositoryInterface;
use Modules\Core\User\Infrastructure\ReadModels\UserReadModel;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function save(User $user): void
    {
        $model = new UserReadModel();
        $this->mapDomainToModel($user, $model);
        $model->save();
    }

    public function update(User $user): void
    {
        $model = UserReadModel::find($user->getId());
        if ($model) {
            $this->mapDomainToModel($user, $model);
            $model->save();
        }
    }

    public function delete(string $id): void
    {
        UserReadModel::destroy($id);
    }

    public function findById(string $id): ?User
    {
        $model = UserReadModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->mapModelToDomain($model);
    }

    private function mapDomainToModel(User $user, UserReadModel $model): void
    {
        $model->id = $user->getId();
        $model->name = $user->name;
        $model->email = $user->email;
        $model->password = $user->password;
    }

    private function mapModelToDomain(UserReadModel $model): User
    {
        return new User(
            $model->id,
            $model->name,
            $model->email,
            $model->password,
            $model->created_at ? new \DateTimeImmutable($model->created_at->toDateTimeString()) : null,
            $model->updated_at ? new \DateTimeImmutable($model->updated_at->toDateTimeString()) : null
        );
    }
}
