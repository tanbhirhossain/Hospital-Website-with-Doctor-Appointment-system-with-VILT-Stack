<?php

namespace Modules\APPOINTMENT\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Modules\APPOINTMENT\Events\BookingCancel;
use Modules\APPOINTMENT\Notifications\SmsNotification;
use Modules\WEBSITE\Interfaces\DoctorRepositoryInterface;

class SendBookingCencelSMS implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private readonly DoctorRepositoryInterface $doctorRepo
    ) {}

    public function handle(BookingCancel $event): void
    {
        $booking = $event->appointment;

        if (empty($booking->patient_phone)) {
            return;
        }

        $doctor = $this->doctorRepo->findById($booking->doctor_id);

        $doctorName = $doctor?->name ?? 'Doctor';

        $message = "Your appointment with {$doctorName} scheduled on {$booking->appointment_date} has been cancelled.";

        SmsNotification::dispatch(
            $message,
            $booking->patient_phone
        );
    }

    public function failed(BookingCancel $event, \Throwable $exception): void
    {
        Log::error('Booking Cancel SMS Listener Failed', [
            'appointment_id' => $event->appointment->id,
            'error' => $exception->getMessage(),
        ]);
    }
}