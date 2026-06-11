<?php

namespace Modules\APPOINTMENT\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\APPOINTMENT\Interfaces\AppointmentRepositoryInterface;
use Modules\APPOINTMENT\Models\Appointment;
use Modules\APPOINTMENT\Support\AppointmentStatus;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function __construct(
        private readonly Appointment $model,
    ) {}

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(Appointment $appointment, array $data)
    {
        $appointment->update($data);
        return $appointment->fresh();
    }

    public function delete(Appointment $appointment): void
    {
        $appointment->delete();
    }

    public function findById(int $id): ?Appointment
    {
        return $this->model->with('doctor')->findOrFail($id);
    }

    public function all(): Collection
    {
        return $this->model->with('doctor')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with('doctor')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'asc');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        if (!empty($filters['date'])) {
            $query->where('appointment_date', $filters['date']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('appointment_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('appointment_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'LIKE', "%{$search}%")
                  ->orWhere('patient_phone', 'LIKE', "%{$search}%")
                  ->orWhere('patient_email', 'LIKE', "%{$search}%");
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getBookedSlots(int $doctorId, string $date): Collection
    {
        return $this->model->where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->where('status', '!=', AppointmentStatus::CANCELLED)
            ->get(['start_time', 'end_time'])
            ->map(fn ($item) => [
                'start_time' => $item->start_time instanceof \DateTimeInterface
                    ? $item->start_time->format('H:i')
                    : $item->start_time,
                'end_time' => $item->end_time instanceof \DateTimeInterface
                    ? $item->end_time->format('H:i')
                    : $item->end_time,
            ]);
    }

public function isSlotBooked(
    int $doctorId,
    string $date,
    string $startTime,
    string $endTime,
    ?int $ignoreAppointmentId = null
): bool {
    $startTime = strlen($startTime) === 5 ? $startTime . ':00' : $startTime;
    $endTime   = strlen($endTime) === 5 ? $endTime . ':00' : $endTime;

    return $this->model
        ->where('doctor_id', $doctorId)
        ->whereDate('appointment_date', $date)
        ->where('status', '!=', AppointmentStatus::CANCELLED)
        ->when($ignoreAppointmentId, function ($query) use ($ignoreAppointmentId) {
            $query->where('id', '!=', $ignoreAppointmentId);
        })
        ->where(function ($query) use ($startTime, $endTime) {
            $query
                ->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                });
        })
        ->exists();
}

    public function countByStatus(?string $status = null): int
    {
        if ($status === null) {
            return $this->model->count();
        }

        return $this->model->where('status', $status)->count();
    }
}
