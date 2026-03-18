<?php

declare(strict_types=1);

namespace Modules\Marketing\Campaign;

use Modules\ServiceProvider as BaseServiceProvider;
use Modules\Marketing\Campaign\Domain\CampaignRepositoryInterface;
use Modules\Marketing\Campaign\Infrastructure\Persistence\EloquentCampaignRepository;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CampaignRepositoryInterface::class, EloquentCampaignRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/Presentation/Web/Views', 'campaign');
    }
}