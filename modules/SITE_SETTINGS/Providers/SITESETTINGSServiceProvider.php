<?php

namespace Modules\SITE_SETTINGS\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\SITE_SETTINGS\Services\FrontendSiteSettingsService;

class SITESETTINGSServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FrontendSiteSettingsService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
    }
}
