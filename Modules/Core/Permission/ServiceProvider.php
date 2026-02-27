<?php

declare(strict_types=1);

namespace Modules\Core\Permission;

use Modules\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (file_exists(__DIR__ . '/routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        }

        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        if (is_dir(__DIR__ . '/Presentation/Web/Views')) {
            $this->loadViewsFrom(__DIR__ . '/Presentation/Web/Views', 'permission');
        }
    }
}
