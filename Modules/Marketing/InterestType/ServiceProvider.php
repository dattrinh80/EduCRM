<?php

declare(strict_types=1);

namespace Modules\Marketing\InterestType;

use Modules\ServiceProvider as BaseServiceProvider;
use Modules\Marketing\InterestType\Domain\InterestTypeRepositoryInterface;
use Modules\Marketing\InterestType\Infrastructure\Persistence\EloquentInterestTypeRepository;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            InterestTypeRepositoryInterface::class,
            EloquentInterestTypeRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/Presentation/Web/Views', 'interest-type');
    }
}
