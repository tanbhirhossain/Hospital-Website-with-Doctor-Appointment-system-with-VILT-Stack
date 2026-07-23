<?php

namespace Modules\EMAILMARKETING\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\EMAILMARKETING\Console\Commands\SendScheduledEmailCampaignsCommand;
use Modules\EMAILMARKETING\Interfaces\NewsletterRepositoryInterface;
use Modules\EMAILMARKETING\Repositories\NewsletterRepository;

class EMAILMARKETINGServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NewsletterRepositoryInterface::class, NewsletterRepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'emailmarketing');
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');

        if (file_exists(__DIR__.'/../Routes/api.php')) {
            $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        }

        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                SendScheduledEmailCampaignsCommand::class,
            ]);
        }
    }
}
