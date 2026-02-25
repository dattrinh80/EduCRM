<?php

declare(strict_types=1);

namespace Modules;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

abstract class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        // Base module registration overrides
    }

    public function boot(): void
    {
        // Base module booting overrides
    }
}
