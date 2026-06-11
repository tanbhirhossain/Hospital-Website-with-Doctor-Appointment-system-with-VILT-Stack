<?php

namespace Modules\APPOINTMENT\Interfaces;

use Illuminate\Support\Collection;

interface TimeSlotServiceInterface
{
    public function getAvailableSlots(int $doctorId, string $date): Collection;
    public function isWorkingDay(int $doctorId, string $date): bool;
    public function isRestDay(int $doctorId, string $date): bool;
}
