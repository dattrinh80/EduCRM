<?php

declare(strict_types=1);

namespace Modules\Core\User;

use Modules\ServiceProvider as BaseServiceProvider;
use Modules\Core\User\Application\Services\AuthorizationServiceInterface;
use Modules\Core\User\Infrastructure\Services\DatabaseAuthorizationService;
use Modules\Core\User\Domain\UserRepositoryInterface;
use Modules\Core\User\Infrastructure\Persistence\EloquentUserRepository;
use Modules\Core\User\Infrastructure\Middleware\CheckPermission;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthorizationServiceInterface::class, DatabaseAuthorizationService::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
    }

    public function boot(): void
    {
        // Register middleware alias
        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('permission', CheckPermission::class);

        // Register Gate that checks permissions via AuthorizationService
        // This enables Blade @can('leads.delete') directives
        Gate::before(function ($user, string $ability) {
            /** @var AuthorizationServiceInterface $authService */
            $authService = app(AuthorizationServiceInterface::class);
            return $authService->can($user->id, $ability) ?: null;
        });

        // Load Web and API routes
        if (file_exists(__DIR__ . '/routes/api.php')) {
            $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        }
        if (file_exists(__DIR__ . '/routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        }

        // Load Migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        // Load Views
        if (is_dir(__DIR__ . '/Presentation/Web/Views')) {
            $this->loadViewsFrom(__DIR__ . '/Presentation/Web/Views', 'user');
        }
    }
}
