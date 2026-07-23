<?php

namespace Modules\APPOINTMENT\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\APPOINTMENT\Models\Appointment;

class BookingConfirm
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Appointment $appointment
    ) {}
}