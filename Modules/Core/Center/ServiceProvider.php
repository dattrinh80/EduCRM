<?php

declare(strict_types=1);

namespace Modules\Core\Center;

use Modules\ServiceProvider as BaseServiceProvider;
use Modules\Core\Center\Domain\CenterRepositoryInterface;
use Modules\Core\Center\Infrastructure\Persistence\EloquentCenterRepository;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        // Bind Domain Repository Interface to Eloquent Implementation
        $this->app->bind(
            CenterRepositoryInterface::class,
            EloquentCenterRepository::class
        );
    }

    public function boot(): void
    {
        // Load Web and API routes
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // Load Migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        // Load Views
        $this->loadViewsFrom(__DIR__ . '/Presentation/Web/Views', 'center');
    }
}
