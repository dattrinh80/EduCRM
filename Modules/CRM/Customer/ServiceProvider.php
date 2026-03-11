<?php

declare(strict_types=1);

namespace Modules\CRM\Customer;

use Modules\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Modules\CRM\Customer\Domain\CustomerRepositoryInterface::class,
            \Modules\CRM\Customer\Infrastructure\Persistence\EloquentCustomerRepository::class
        );

        $this->app->bind(
            \Modules\CRM\CustomerTag\Domain\CustomerTagRepositoryInterface::class,
            \Modules\CRM\CustomerTag\Infrastructure\Persistence\EloquentCustomerTagRepository::class
        );

        $this->app->bind(
            \Modules\CRM\CustomerNote\Domain\CustomerNoteRepositoryInterface::class,
            \Modules\CRM\CustomerNote\Infrastructure\Persistence\EloquentCustomerNoteRepository::class
        );

        $this->app->bind(
            \Modules\CRM\CustomerActivity\Domain\CustomerActivityRepositoryInterface::class,
            \Modules\CRM\CustomerActivity\Infrastructure\Persistence\EloquentCustomerActivityRepository::class
        );
    }

    public function boot(): void
    {
        if (file_exists(__DIR__ . '/routes/api.php')) {
            $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        }
        if (file_exists(__DIR__ . '/routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        }

        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadMigrationsFrom(dirname(__DIR__) . '/CustomerTag/Database/Migrations');
        $this->loadMigrationsFrom(dirname(__DIR__) . '/CustomerNote/Database/Migrations');
        $this->loadMigrationsFrom(dirname(__DIR__) . '/CustomerActivity/Database/Migrations');

        if (is_dir(__DIR__ . '/Presentation/Web/Views')) {
            $this->loadViewsFrom(__DIR__ . '/Presentation/Web/Views', 'customer');
        }
        
        $this->loadViewsFrom(dirname(__DIR__) . '/CustomerTag/Presentation/Web/Views', 'customer_tag');
    }
}
