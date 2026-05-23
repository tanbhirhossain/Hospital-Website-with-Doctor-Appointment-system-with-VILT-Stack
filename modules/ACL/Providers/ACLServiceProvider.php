<?php

namespace Modules\ACL\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\ACL\Interfaces\RoleRepositoryInterface;
use Modules\ACL\Interfaces\UserRepositoryInterface;
use Modules\ACL\Repositories\RoleRepository;
use Modules\ACL\Repositories\UserRepository;

class ACLServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'hrm');
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');

        if (file_exists(__DIR__.'/../Routes/api.php')) {
            $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        }

        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->publishes([
            __DIR__.'/../Resources/js'  => public_path('modules/acl/js'),
            __DIR__.'/../Resources/css' => public_path('modules/acl/css'),
        ], 'acl-assets');
    }
}
