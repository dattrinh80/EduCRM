<?php

declare(strict_types=1);

namespace Modules\CRM\Source;

use Modules\ServiceProvider as BaseServiceProvider;
use Modules\CRM\Source\Domain\SourceRepositoryInterface;
use Modules\CRM\Source\Infrastructure\Persistence\EloquentSourceRepository;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            SourceRepositoryInterface::class,
            EloquentSourceRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/Presentation/Web/Views', 'source');
    }
}
