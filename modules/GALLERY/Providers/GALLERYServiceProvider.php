<?php

namespace Modules\GALLERY\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\GALLERY\Interfaces\GalleryCategoryRepositoryInterface;
use Modules\GALLERY\Interfaces\GalleryItemRepositoryInterface;
use Modules\GALLERY\Repositories\GalleryCategoryRepository;
use Modules\GALLERY\Repositories\GalleryItemRepository;

class GALLERYServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GalleryCategoryRepositoryInterface::class, GalleryCategoryRepository::class);
        $this->app->bind(GalleryItemRepositoryInterface::class, GalleryItemRepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'gallery');
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }
}
