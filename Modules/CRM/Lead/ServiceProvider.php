<?php

declare(strict_types=1);

namespace Modules\CRM\Lead;

use Modules\ServiceProvider as BaseServiceProvider;
use Modules\CRM\Lead\Domain\LeadRepositoryInterface;
use Modules\CRM\Lead\Infrastructure\Persistence\EloquentLeadRepository;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        // Bind Domain Repository Interface to Eloquent Implementation
        $this->app->bind(
            LeadRepositoryInterface::class,
            EloquentLeadRepository::class
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
        $this->loadViewsFrom(__DIR__ . '/Presentation/Web/Views', 'lead');
    }
}
