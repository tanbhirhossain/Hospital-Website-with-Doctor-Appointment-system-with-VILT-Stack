<?php 

namespace Modules\APPOINTMENT\Mail;

use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\APPOINTMENT\Models\Appointment;

class AppointmentCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function build()
    {
        return $this->subject('Your Appointment is Created')
                    ->view('appointment::emails.appointment_created')
                    ->with([
                        'appointment' => $this->appointment,
                        'doctor' => $this->appointment->doctor, // ডাক্তার রিলেশনশিপটি পাস করা জরুরি
                    ]);
    }
}
