<?php

declare(strict_types=1);

namespace Modules\CRM\Task;

use Modules\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Modules\CRM\Task\Domain\TaskRepositoryInterface::class,
            \Modules\CRM\Task\Infrastructure\Persistence\EloquentTaskRepository::class
        );
    }

    public function boot(): void
    {
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'Lead' => \Modules\CRM\Lead\Infrastructure\ReadModels\LeadReadModel::class,
            'Customer' => \Modules\CRM\Customer\Infrastructure\Persistence\CustomerModel::class,
        ]);

        if (file_exists(__DIR__ . '/routes/api.php')) {
            $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        }
        if (file_exists(__DIR__ . '/routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        }

        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        if (is_dir(__DIR__ . '/Presentation/Web/Views')) {
            $this->loadViewsFrom(__DIR__ . '/Presentation/Web/Views', 'task');
        }
    }
}
