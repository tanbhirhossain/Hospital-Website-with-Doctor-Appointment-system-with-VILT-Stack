<?php 

namespace Modules\APPOINTMENT\Services;

use Exception;
use Modules\APPOINTMENT\Events\AppointmentCreated;
use Modules\APPOINTMENT\Events\BookingCancel;
use Modules\APPOINTMENT\Events\BookingConfirm;
use Modules\APPOINTMENT\Interfaces\AppointmentRepositoryInterface;
use Modules\APPOINTMENT\Models\Appointment;
use Modules\APPOINTMENT\Services\TimeSlotService;

class AppointmentService {
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepo,
        private ?TimeSlotService $timeSlotService = null
    ){}

    public function getAvailableSlots(int $doctorId, string $date): array {
        return $this->timeSlotService ? $this->timeSlotService->getAvailableSlots($doctorId, $date) : [];
    }


    public function createAppointment(array $data): Appointment
    {
        $isAvailable = $this->appointmentRepo->checkTimeSlotAvailability(
            $data['doctor_id'], 
            $data['appointment_date'], 
            $data['start_time']
        );

        if (!$isAvailable) {
            throw new Exception('দুঃখিত, এই টাইম স্লটটি ইতিমধ্যে বুকড হয়ে গেছে!');
        }

        $lastAppointment = Appointment::where('doctor_id', $data['doctor_id'])
            ->whereDate('appointment_date', $data['appointment_date'])
            ->orderBy('serial_number', 'desc')
            ->first();

        $data['serial_number'] = $lastAppointment ? ($lastAppointment->serial_number + 1) : 1;
        $data['serial_mode'] = $data['serial_mode'] ?? 'auto'; 

        $appointment = $this->appointmentRepo->create($data);

        if (!empty($appointment->patient_email)) {
            event(new AppointmentCreated($appointment));
        }

        return $appointment;
    }


    public function updateAppointment(int $id, array $data): Appointment
    {
        $appointment = $this->appointmentRepo->findById($id);
        
        if (!$appointment) {
            throw new Exception('অ্যাপয়েন্টments খুঁজে পাওয়া যায়নি!');
        }

        return $this->appointmentRepo->update($appointment, $data);
    }


    public function deleteAppointment(int $id): void
    {
        $appointment = $this->appointmentRepo->findById($id);
        
        if (!$appointment) {
            throw new Exception('অ্যাপয়েন্টments খুঁজে পাওয়া যায়নি!');
        }

        $this->appointmentRepo->delete($appointment);
    }


    public function changeStatus(int $id, string $status): Appointment
    {
        $appointment = $this->appointmentRepo->findById($id);
        
        if (!$appointment) {
            throw new Exception('অ্যাপয়েন্টments খুঁজে পাওয়া যায়নি!');
        }

        return $this->appointmentRepo->changeStaus($appointment, $status);
    }


    public function confirmBooking(int $id): Appointment
    {
        $appointment = $this->appointmentRepo->findById($id);

        if (! $appointment) {
            throw new \Exception('Appointment not found.');
        }

        if ($appointment->status === 'confirmed') {
            return $appointment;
        }

        $confirmed = $this->appointmentRepo->confirmBooking($appointment);

        if (! empty($confirmed->patient_phone)) {
            event(new BookingConfirm($confirmed));
        }

        return $confirmed;
    }


    public function cancelBooking(int $id, ?string $reason = null): Appointment
    {
        $appointment = $this->appointmentRepo->findById($id);
        
        if (!$appointment) {
            throw new Exception('অ্যাপয়েন্টments খুঁজে পাওয়া যায়নি!');
        }

        if ($reason) {
            $appointment->cancellation_reason = $reason;
        }

        $cancel = $this->appointmentRepo->cancelBooking($appointment);

        event(new BookingCancel($cancel));

        return  $cancel;
    }
}
