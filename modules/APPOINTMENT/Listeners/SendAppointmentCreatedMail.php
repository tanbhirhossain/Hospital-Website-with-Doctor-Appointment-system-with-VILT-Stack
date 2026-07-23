<?php 

namespace Modules\APPOINTMENT\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\APPOINTMENT\Events\AppointmentCreated;
use Modules\APPOINTMENT\Mail\AppointmentCreatedMail;
use Modules\WEBSITE\Interfaces\DoctorRepositoryInterface;

class SendAppointmentCreatedMail implements ShouldQueue
{

    public function __construct(
        private readonly DoctorRepositoryInterface $doctorRepository
    ){}

    public function handle(AppointmentCreated $event)
    {
        try {
            $appointment = $event->appointment;
            $doctor = $this->doctorRepository->findByid($appointment->doctor_id);
            $appointment->setRelation('doctor', $doctor); 
            
            Log::info('Processing email for appointment: ' . $appointment->id);
            Log::info('Doctor data: ' . ($appointment->doctor ? 'Loaded' : 'NULL'));

            Mail::to($appointment->patient_email)->send(new AppointmentCreatedMail($appointment));
            
        } catch (\Exception $e) {
            Log::error('Mail Listener Failed: ' . $e->getMessage());
            throw $e;
        }
    }
}