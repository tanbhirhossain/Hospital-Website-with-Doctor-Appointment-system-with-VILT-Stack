<?php

namespace Modules\APPOINTMENT\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\APPOINTMENT\Interfaces\AppointmentRepositoryInterface;
use Modules\APPOINTMENT\Interfaces\AppointmentServiceInterface;
use Modules\APPOINTMENT\Interfaces\TimeSlotServiceInterface;
use Modules\APPOINTMENT\Models\Appointment;
use Modules\APPOINTMENT\Support\AppointmentStatus;

class AppointmentService implements AppointmentServiceInterface
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointmentRepository,
        private readonly TimeSlotServiceInterface       $timeSlotService,
    ) {}

    public function createAppointment(array $data)
    {
        if (!$this->timeSlotService->isWorkingDay($data['doctor_id'], $data['appointment_date'])) {
            throw new \InvalidArgumentException('The selected doctor does not work on this day.');
        }

        if ($this->timeSlotService->isRestDay($data['doctor_id'], $data['appointment_date'])) {
            throw new \InvalidArgumentException('The selected date is a rest day for this doctor.');
        }

        if ($this->appointmentRepository->isSlotBooked(
            (int) $data['doctor_id'],
            $data['appointment_date'],
            $data['start_time'],
            $data['end_time'],
        )) {
            throw new \InvalidArgumentException('This time slot is already booked. Please select another slot.');
        }

        return $this->appointmentRepository->create($data);
    }

    public function updateAppointment(Appointment $appointment, array $data)
    {
        $doctorId = (int) ($data['doctor_id'] ?? $appointment->doctor_id);
        $date     = $data['appointment_date'] ?? $appointment->appointment_date;
        $start    = $data['start_time'] ?? $appointment->start_time;
        $end      = $data['end_time'] ?? $appointment->end_time;

        if (!$this->timeSlotService->isWorkingDay($doctorId, $date)) {
            throw new \InvalidArgumentException('The selected doctor does not work on this day.');
        }

        if ($this->timeSlotService->isRestDay($doctorId, $date)) {
            throw new \InvalidArgumentException('The selected date is a rest day for this doctor.');
        }

        if ($this->appointmentRepository->isSlotBooked(
            $doctorId,
            $date,
            $start,
            $end,
            $appointment->id,
        )) {
            throw new \InvalidArgumentException('This time slot is already booked. Please select another slot.');
        }

        return $this->appointmentRepository->update($appointment, $data);
    }

    public function deleteAppointment(Appointment $appointment): void
    {
        $this->appointmentRepository->delete($appointment);
    }

    public function getAppointment(int $id): ?Appointment
    {
        return $this->appointmentRepository->findById($id);
    }

    public function listAppointments(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->appointmentRepository->paginate($perPage, $filters);
    }

    public function confirmAppointment(Appointment $appointment, ?int $serialNumber = null)
    {
        if ($appointment->status !== AppointmentStatus::PENDING) {
            throw new \InvalidArgumentException('Only pending appointments can be confirmed.');
        }

        return DB::transaction(function () use ($appointment, $serialNumber) {
            $appointment = Appointment::query()
                ->whereKey($appointment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($appointment->status !== AppointmentStatus::PENDING) {
                throw new \InvalidArgumentException('Only pending appointments can be confirmed.');
            }

            if ($serialNumber !== null) {
                if ($serialNumber < 1) {
                    throw new \InvalidArgumentException('Serial number must be greater than 0.');
                }

                $exists = Appointment::query()
                    ->where('doctor_id', $appointment->doctor_id)
                    ->whereDate('appointment_date', $appointment->appointment_date)
                    ->where('serial_number', $serialNumber)
                    ->where('id', '!=', $appointment->id)
                    ->exists();

                if ($exists) {
                    throw new \InvalidArgumentException('This serial number is already used.');
                }

                $finalSerialNumber = $serialNumber;
                $serialMode = 'manual';
            } else {
                $lastSerial = Appointment::query()
                    ->where('doctor_id', $appointment->doctor_id)
                    ->whereDate('appointment_date', $appointment->appointment_date)
                    ->whereNotNull('serial_number')
                    ->lockForUpdate()
                    ->max('serial_number');

                $finalSerialNumber = ((int) $lastSerial) + 1;
                $serialMode = 'auto';
            }

            return $this->appointmentRepository->update($appointment, [
                'status'        => AppointmentStatus::CONFIRMED,
                'serial_number' => $finalSerialNumber,
                'serial_mode'   => $serialMode,
                'confirmed_at'  => now(),
            ]);
        });
    }
        public function cancelAppointment(Appointment $appointment, ?string $reason = null)
    {
        if (!in_array($appointment->status, [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED])) {
            throw new \InvalidArgumentException('This appointment cannot be cancelled.');
        }

        return $this->appointmentRepository->update($appointment, [
            'status'              => AppointmentStatus::CANCELLED,
            'cancellation_reason' => $reason,
        ]);
    }

    public function completeAppointment(Appointment $appointment)
    {
        if ($appointment->status !== AppointmentStatus::CONFIRMED) {
            throw new \InvalidArgumentException('Only confirmed appointments can be marked as completed.');
        }

        return $this->appointmentRepository->update($appointment, [
            'status' => AppointmentStatus::COMPLETED,
        ]);
    }

    public function markNoShow(Appointment $appointment)
    {
        if ($appointment->status !== AppointmentStatus::CONFIRMED) {
            throw new \InvalidArgumentException('Only confirmed appointments can be marked as no-show.');
        }

        return $this->appointmentRepository->update($appointment, [
            'status' => AppointmentStatus::NO_SHOW,
        ]);
    }

    public function getDashboardStats(): array
    {
        return [
            'total'     => $this->appointmentRepository->countByStatus(null),
            'pending'   => $this->appointmentRepository->countByStatus(AppointmentStatus::PENDING),
            'confirmed' => $this->appointmentRepository->countByStatus(AppointmentStatus::CONFIRMED),
            'completed' => $this->appointmentRepository->countByStatus(AppointmentStatus::COMPLETED),
            'cancelled' => $this->appointmentRepository->countByStatus(AppointmentStatus::CANCELLED),
            'no_show'   => $this->appointmentRepository->countByStatus(AppointmentStatus::NO_SHOW),
        ];
    }

    
}
