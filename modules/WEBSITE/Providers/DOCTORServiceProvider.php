<?php

namespace Modules\WEBSITE\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\WEBSITE\Interfaces\COERepositoryInterface;
use Modules\WEBSITE\Interfaces\DepartmentRepositoryInterface;
use Modules\WEBSITE\Interfaces\DoctorRepositoryInterface;
use Modules\WEBSITE\Interfaces\DoctorTimetableRepositoryInterface;
use Modules\WEBSITE\Repositories\COERepository;
use Modules\WEBSITE\Repositories\DepartmentRepository;
use Modules\WEBSITE\Repositories\DoctorRepository;
use Modules\WEBSITE\Repositories\DoctorTimetableRepository;


class DOCTORServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->bind(DoctorRepositoryInterface::class, DoctorRepository::class);
        $this->app->bind(DoctorTimetableRepositoryInterface::class, DoctorTimetableRepository::class);
        $this->app->bind(COERepositoryInterface::class, COERepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'doctor');
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');

        if (file_exists(__DIR__.'/../Routes/api.php')) {
            $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        }

        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

    }
}
