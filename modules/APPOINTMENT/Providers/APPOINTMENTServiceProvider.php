<?php

namespace Modules\APPOINTMENT\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\APPOINTMENT\Interfaces\AppointmentRepositoryInterface;
use Modules\APPOINTMENT\Interfaces\AppointmentServiceInterface;
use Modules\APPOINTMENT\Interfaces\TimeSlotServiceInterface;
use Modules\APPOINTMENT\Repositories\AppointmentRepository;
use Modules\APPOINTMENT\Services\AppointmentService;
use Modules\APPOINTMENT\Services\TimeSlotService;

class APPOINTMENTServiceProvider extends ServiceProvider
{



    public function register(): void
    {
        $this->app->bind(AppointmentRepositoryInterface::class, AppointmentRepository::class);
        $this->app->bind(AppointmentServiceInterface::class, AppointmentService::class);
        $this->app->bind(TimeSlotServiceInterface::class, TimeSlotService::class);
    }


    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'appointment');
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');

        if (file_exists(__DIR__.'/../Routes/api.php')) {
            $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        }

        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }
}