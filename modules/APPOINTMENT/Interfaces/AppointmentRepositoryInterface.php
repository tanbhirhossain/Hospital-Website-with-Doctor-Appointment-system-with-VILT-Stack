<?php

namespace Modules\APPOINTMENT\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\APPOINTMENT\Models\Appointment;

interface AppointmentRepositoryInterface
{
    public function create(array $data);
    public function update(Appointment $appointment, array $data);
    public function delete(Appointment $appointment): void;
    public function findById(int $id): ?Appointment;
    public function all(): Collection;
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function getBookedSlots(int $doctorId, string $date): Collection;
    // public function isSlotBooked(int $doctorId, string $date, string $startTime, string $endTime): bool;
    public function isSlotBooked(int $doctorId, string $date, string $startTime, string $endTime, ?int $ignoreAppointmentId = null):bool;

    public function countByStatus(?string $status = null): int;
}
