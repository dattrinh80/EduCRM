<?php

declare(strict_types=1);

namespace Modules\Core\User\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\User\Application\Queries\AuthenticateUserQuery;
use Modules\Core\User\Application\Queries\AuthenticateUserHandler;

class AuthApiController extends Controller
{
    public function login(Request $request, AuthenticateUserHandler $handler): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string'
        ]);

        $query = new AuthenticateUserQuery(
            $request->email,
            $request->password,
            $request->device_name
        );

        $result = $handler->handle($query);

        if (!$result) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Credentials do not match.'
                ]
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('userRoles.role.permissions');

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
}
