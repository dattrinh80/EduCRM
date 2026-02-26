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

        if (!\Illuminate\Support\Facades\File::exists($modulesPath)) {
            return;
        }

        $files = \Illuminate\Support\Facades\File::allFiles($modulesPath);

        foreach ($files as $file) {
            if ($file->getFilename() === 'ServiceProvider.php') {
                $relativePath = $file->getRelativePath();
                
                if (empty($relativePath)) {
                    continue;
                }

                $namespacePath = str_replace(['/', '\\'], '\\', $relativePath);
                $providerClass = 'Modules\\' . $namespacePath . '\\ServiceProvider';
                
                \Illuminate\Support\Facades\Log::info("Found Provider: " . $providerClass);

                if (class_exists($providerClass)) {
                    $this->app->register($providerClass);
                    \Illuminate\Support\Facades\Log::info("Registered Provider: " . $providerClass);
                } else {
                    \Illuminate\Support\Facades\Log::info("Class does not exist: " . $providerClass);
                }
            }
        }
    }

    public function boot(): void
    {
    }
}
