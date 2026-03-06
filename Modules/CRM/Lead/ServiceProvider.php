<?php

declare(strict_types=1);

namespace Modules\CRM\Lead;

use Modules\ServiceProvider as BaseServiceProvider;
use Modules\CRM\Lead\Domain\LeadRepositoryInterface;
use Modules\CRM\Lead\Infrastructure\Persistence\EloquentLeadRepository;
use Modules\CRM\Lead\LeadActivity\Domain\LeadActivityRepositoryInterface;
use Modules\CRM\Lead\LeadActivity\Infrastructure\Persistence\EloquentLeadActivityRepository;
use Modules\CRM\Lead\LeadNote\Domain\LeadNoteRepositoryInterface;
use Modules\CRM\Lead\LeadNote\Infrastructure\Persistence\EloquentLeadNoteRepository;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        // Lead core
        $this->app->bind(
            LeadRepositoryInterface::class,
            EloquentLeadRepository::class
        );

        // Lead Activity sub-module
        $this->app->bind(
            LeadActivityRepositoryInterface::class,
            EloquentLeadActivityRepository::class
        );

        // Lead Note sub-module
        $this->app->bind(
            LeadNoteRepositoryInterface::class,
            EloquentLeadNoteRepository::class
        );

        // Lead Status sub-module
        $this->app->bind(
            \Modules\CRM\Lead\LeadStatus\Domain\LeadStatusRepositoryInterface::class,
            \Modules\CRM\Lead\LeadStatus\Infrastructure\Persistence\EloquentLeadStatusRepository::class
        );

        // Lead Tag sub-module
        $this->app->bind(
            \Modules\CRM\Lead\LeadTag\Domain\LeadTagRepositoryInterface::class,
            \Modules\CRM\Lead\LeadTag\Infrastructure\Persistence\EloquentLeadTagRepository::class
        );

        // Lead Assignment History
        $this->app->bind(
            \Modules\CRM\Lead\Domain\LeadAssignmentRepositoryInterface::class,
            \Modules\CRM\Lead\Infrastructure\Persistence\EloquentLeadAssignmentRepository::class
        );
    }

    public function boot(): void
    {
        // Load Web and API routes
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // Load Migrations (core + sub-modules)
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/LeadActivity/Database/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/LeadNote/Database/Migrations');

        // Load Views
        $this->loadViewsFrom(__DIR__ . '/Presentation/Web/Views', 'lead');
    }
}
