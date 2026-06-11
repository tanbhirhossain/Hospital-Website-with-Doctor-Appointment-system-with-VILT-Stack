<?php

namespace Modules\APPOINTMENT\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\APPOINTMENT\Models\Appointment;

interface AppointmentServiceInterface
{
    public function createAppointment(array $data);
    public function updateAppointment(Appointment $appointment, array $data);
    public function deleteAppointment(Appointment $appointment): void;
    public function getAppointment(int $id);
    public function listAppointments(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function confirmAppointment(Appointment $appointment, ?int $serialNumber = null);
    public function cancelAppointment(Appointment $appointment, ?string $reason = null);
    public function completeAppointment(Appointment $appointment);
    public function markNoShow(Appointment $appointment);
    public function getDashboardStats(): array;
}
