<?php

declare(strict_types=1);

namespace Modules\Marketing\LeadSource;

use Modules\ServiceProvider as BaseServiceProvider;
use Modules\Marketing\LeadSource\Domain\LeadSourceRepositoryInterface;
use Modules\Marketing\LeadSource\Infrastructure\Persistence\EloquentLeadSourceRepository;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            LeadSourceRepositoryInterface::class,
            EloquentLeadSourceRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/Presentation/Web/Views', 'lead_source');
    }
}
