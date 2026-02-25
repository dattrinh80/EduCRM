<?php

declare(strict_types=1);

namespace Bootstrap;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $modulesPath = base_path('Modules');

        if (!File::exists($modulesPath)) {
            return;
        }

        $directories = File::directories($modulesPath);

        foreach ($directories as $directory) {
            $providerClass = 'Modules\\' . basename($directory) . '\\ServiceProvider';

            if (class_exists($providerClass)) {
                $this->app->register($providerClass);
            }
        }
    }

    public function boot(): void
    {
    }
}
