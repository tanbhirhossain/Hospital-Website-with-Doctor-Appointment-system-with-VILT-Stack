<?php

namespace Modules\APPOINTMENT\Listeners;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Modules\APPOINTMENT\Events\BookingConfirm;
use Modules\APPOINTMENT\Notifications\SmsNotification;
use Modules\WEBSITE\Interfaces\DoctorRepositoryInterface;

class SendBookingConfirmSMS implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Queue Configuration
     */
    public int $tries = 3;

    public int $timeout = 30;

    public int $backoff = 60;

    public function __construct(
        private readonly DoctorRepositoryInterface $doctorRepo
    ) {}

    /**
     * Handle the event.
     */
    public function handle(BookingConfirm $event): void
    {
        $booking = $event->appointment;

        // Appointment must be confirmed
        if ($booking->status !== 'confirmed') {
            return;
        }

        // Patient phone required
        if (empty($booking->patient_phone)) {
            return;
        }

        // Get doctor
        $doctor = $this->doctorRepo->findById($booking->doctor_id);

        $doctorName = $doctor?->name ?? 'Doctor';

        // Format Date
        $appointmentDate = Carbon::parse($booking->appointment_date)
            ->format('d M Y');

        // Format Time
        $appointmentTime = Carbon::parse($booking->start_time)
            ->format('h:i A');

        // Normalize Phone Number
        $phone = preg_replace('/\D/', '', $booking->patient_phone);

        if (str_starts_with($phone, '01')) {
            $phone = '88' . $phone;
        }

        // SMS Message
        $message = "Dear {$booking->patient_name},

        Your appointment has been confirmed.

        Doctor: {$doctorName}
        Date: {$appointmentDate}
        Time: {$appointmentTime}

        Thank you for choosing AMZ Hospital Ltd.";

        // Dispatch SMS Queue
        SmsNotification::dispatch(
            $message,
            $phone
        );
    }

    /**
     * Handle failed job.
     */
    public function failed(BookingConfirm $event, \Throwable $exception): void
    {
        Log::error('Booking SMS Listener Failed', [
            'appointment_id' => $event->appointment->id,
            'error' => $exception->getMessage(),

        ]);
    }
}