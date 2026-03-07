<?php
declare(strict_types=1);

namespace Modules\Marketing\Campaign;

use Illuminate\Support\Facades\Route;
use Modules\Marketing\Campaign\Domain\CampaignRepositoryInterface;
use Modules\Marketing\Campaign\Infrastructure\Persistence\EloquentCampaignRepository;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CampaignRepositoryInterface::class, EloquentCampaignRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/Presentation/Web/Views', 'campaign');

        Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('/campaigns', [\Modules\Marketing\Campaign\Presentation\Web\CampaignWebController::class, 'index'])->name('campaigns.index')->middleware('permission:campaigns.view');
            Route::post('/campaigns', [\Modules\Marketing\Campaign\Presentation\Web\CampaignWebController::class, 'store'])->name('campaigns.store')->middleware('permission:campaigns.create');
            Route::put('/campaigns/{id}', [\Modules\Marketing\Campaign\Presentation\Web\CampaignWebController::class, 'update'])->name('campaigns.update')->middleware('permission:campaigns.update');
            Route::delete('/campaigns/{id}', [\Modules\Marketing\Campaign\Presentation\Web\CampaignWebController::class, 'destroy'])->name('campaigns.destroy')->middleware('permission:campaigns.delete');
        });

        Route::middleware(['api', 'auth:sanctum'])->prefix('api/v1')->name('api.')->group(function () {
            Route::get('/campaigns', [\Modules\Marketing\Campaign\Presentation\API\CampaignApiController::class, 'index'])->name('campaigns.index')->middleware('permission:campaigns.view');
            Route::post('/campaigns', [\Modules\Marketing\Campaign\Presentation\API\CampaignApiController::class, 'store'])->name('campaigns.store')->middleware('permission:campaigns.create');
            Route::put('/campaigns/{id}', [\Modules\Marketing\Campaign\Presentation\API\CampaignApiController::class, 'update'])->name('campaigns.update')->middleware('permission:campaigns.update');
            Route::delete('/campaigns/{id}', [\Modules\Marketing\Campaign\Presentation\API\CampaignApiController::class, 'destroy'])->name('campaigns.destroy')->middleware('permission:campaigns.delete');
        });
    }
}