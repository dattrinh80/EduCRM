<?php

declare(strict_types=1);

namespace Modules\Core\User\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Illuminate\Support\Facades\Hash;
use Modules\Core\User\Infrastructure\ReadModels\UserReadModel;

class AuthenticateUserHandler implements QueryHandler
{
    /**
     * @return array{token: string, user: array}|null
     */
    public function handle(Query $query): ?array
    {
        /** @var AuthenticateUserQuery $query */

        $user = UserReadModel::where('email', $query->email)->first();

        if (!$user || !Hash::check($query->password, $user->password)) {
            return null;
        }

        $deviceName = $query->deviceName ?? 'api_token';
        $token = $user->createToken($deviceName)->plainTextToken;

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ];
    }
}
