<?php

namespace Modules\APPOINTMENT\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\APPOINTMENT\Events\AppointmentCreated;
use Modules\APPOINTMENT\Events\BookingCancel;
use Modules\APPOINTMENT\Events\BookingConfirm;
use Modules\APPOINTMENT\Listeners\SendAppointmentCreatedMail;
use Modules\APPOINTMENT\Listeners\SendBookingCencelSMS;
use Modules\APPOINTMENT\Listeners\SendBookingConfirmSMS;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        AppointmentCreated::class => [
            SendAppointmentCreatedMail::class,
        ],
        BookingConfirm::class => [SendBookingConfirmSMS::class],
        BookingCancel::class => [SendBookingCencelSMS::class]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }
}