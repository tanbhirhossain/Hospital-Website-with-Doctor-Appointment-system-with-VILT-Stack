<?php 

namespace Modules\APPOINTMENT\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\APPOINTMENT\Models\Appointment;

class AppointmentCreated
{
    use Dispatchable, SerializesModels;

    public $appointment;
    
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }
}