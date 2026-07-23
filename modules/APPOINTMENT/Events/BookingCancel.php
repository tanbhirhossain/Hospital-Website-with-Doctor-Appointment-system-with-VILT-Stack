<?php 

namespace Modules\APPOINTMENT\Events;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\APPOINTMENT\Models\Appointment;

class BookingCancel
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Appointment $appointment
    ){}
}