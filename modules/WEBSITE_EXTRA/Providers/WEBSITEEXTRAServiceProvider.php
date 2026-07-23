<?php
namespace Modules\WEBSITE_EXTRA\Providers;
use Illuminate\Support\ServiceProvider;
use Modules\WEBSITE_EXTRA\Interfaces\ClientReviewRepositoryInterface;
use Modules\WEBSITE_EXTRA\Interfaces\ContactMessageNotifierInterface;
use Modules\WEBSITE_EXTRA\Interfaces\ContactMessageRepositoryInterface;
use Modules\WEBSITE_EXTRA\Interfaces\HealthPackageRepositoryInterface;
use Modules\WEBSITE_EXTRA\Interfaces\HeroSliderRepositoryInterface;
use Modules\WEBSITE_EXTRA\Interfaces\PatientReviewRepositoryInterface;
use Modules\WEBSITE_EXTRA\Interfaces\ServiceRepositoryInterface;
use Modules\WEBSITE_EXTRA\Repositories\ClientReviewRepository;
use Modules\WEBSITE_EXTRA\Repositories\ContactMessageRepository;
use Modules\WEBSITE_EXTRA\Repositories\HealthPackageRepository;
use Modules\WEBSITE_EXTRA\Repositories\HeroSliderRepository;
use Modules\WEBSITE_EXTRA\Repositories\PatientReviewRepository;
use Modules\WEBSITE_EXTRA\Repositories\ServiceRepository;
use Modules\WEBSITE_EXTRA\Services\MailContactMessageNotifier;
class WEBSITEEXTRAServiceProvider extends ServiceProvider
{
    public function register(): void {
        $this->app->bind(HeroSliderRepositoryInterface::class, HeroSliderRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(ClientReviewRepositoryInterface::class, ClientReviewRepository::class);
        $this->app->bind(PatientReviewRepositoryInterface::class, PatientReviewRepository::class);
        $this->app->bind(HealthPackageRepositoryInterface::class, HealthPackageRepository::class);
        $this->app->bind(ContactMessageRepositoryInterface::class, ContactMessageRepository::class);
        $this->app->bind(ContactMessageNotifierInterface::class, MailContactMessageNotifier::class);
    }
    public function boot(): void {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
    }
}
