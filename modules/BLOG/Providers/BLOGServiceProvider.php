<?php

namespace Modules\BLOG\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\BLOG\Interfaces\BlogRepositoryInterface;
use Modules\BLOG\Repositories\BlogRepository;

class BLOGServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BlogRepositoryInterface::class, BlogRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }
}
