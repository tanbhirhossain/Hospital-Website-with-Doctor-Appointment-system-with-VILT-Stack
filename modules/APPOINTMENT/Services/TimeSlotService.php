<?php

namespace Modules\APPOINTMENT\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\APPOINTMENT\Interfaces\AppointmentRepositoryInterface;
use Modules\APPOINTMENT\Interfaces\TimeSlotServiceInterface;
use Modules\WEBSITE\Interfaces\DoctorRestRepositoryInterface;
use Modules\WEBSITE\Interfaces\DoctorTimetableRepositoryInterface;

class TimeSlotService implements TimeSlotServiceInterface
{
    public function __construct(
        private readonly DoctorTimetableRepositoryInterface $timetableRepository,
        private readonly DoctorRestRepositoryInterface      $restRepository,
        private readonly AppointmentRepositoryInterface     $appointmentRepository,
    ) {}

    /**
     * Generate available time slots for a doctor on a given date.
     *
     * Algorithm:
     *  1. Find the DoctorTimetable for the given day-of-week.
     *  2. If none → doctor doesn't work that day → return empty.
     *  3. Check DoctorRest for the date → if full-day rest → return empty.
     *  4. Generate slots from start_time to end_time, step = slot_duration.
     *  5. Mark unavailable: past slots, rest periods, already-booked.
     *
     * @return Collection<int, array{start_time: string, end_time: string, available: bool}>
     */
    public function getAvailableSlots(int $doctorId, string $date): Collection
    {
        $dateCarbon = Carbon::parse($date);
        $dayOfWeek  = $dateCarbon->dayOfWeek; // 0 (Sun) – 6 (Sat)

        // 1. Find timetable for this day
        $timetables = $this->timetableRepository->findByDrId($doctorId);
        $timetable  = $timetables->first(fn ($t) => (int) $t->day_of_week === $dayOfWeek);

        if (! $timetable) {
            return new Collection();
        }

        // 2. Check rest day
        if ($this->isRestDay($doctorId, $date)) {
            return new Collection();
        }

        // 3. Get partial-day rest periods (safe try/catch — no model imports)
        $rests = null;
        try {
            $rests = $this->restRepository->findByDrIdAndDate($doctorId, $dateCarbon);
        } catch (\Throwable) {
            $rests = null;
        }

        // 4. Generate all slots
        $slotDuration = (int) $timetable->slot_duration;
        $startTime    = Carbon::parse($timetable->start_time);
        $endTime      = Carbon::parse($timetable->end_time);

        $slots = new Collection();

        while ($startTime->lt($endTime)) {
            $slotEnd = $startTime->copy()->addMinutes($slotDuration);

            if ($slotEnd->gt($endTime)) {
                break;
            }

            $slotStartStr = $startTime->format('H:i');
            $slotEndStr   = $slotEnd->format('H:i');

            $available = $this->isSlotAvailable(
                $doctorId,
                $dateCarbon,
                $slotStartStr,
                $slotEndStr,
                $rests,
            );

            $slots->push([
                'start_time' => $slotStartStr,
                'end_time'   => $slotEndStr,
                'available'  => $available,
            ]);

            $startTime->addMinutes($slotDuration);
        }

        return $slots;
    }

    public function isWorkingDay(int $doctorId, string $date): bool
    {
        $dateCarbon = Carbon::parse($date);
        $dayOfWeek  = $dateCarbon->dayOfWeek;

        $timetables = $this->timetableRepository->findByDrId($doctorId);

        return $timetables->contains(fn ($t) => (int) $t->day_of_week === $dayOfWeek);
    }

    public function isRestDay(int $doctorId, string $date): bool
    {
        try {
            $dateCarbon = Carbon::parse($date);
            $rest       = $this->restRepository->findByDrIdAndDate($doctorId, $dateCarbon);

            if ($rest) {
                return true;
            }
        } catch (\Throwable) {
            // No rest record found — that's fine
        }

        return false;
    }

    /**
     * Check if a single slot is available.
     */
    private function isSlotAvailable(
        int $doctorId,
        Carbon $date,
        string $slotStart,
        string $slotEnd,
        $rests,
    ): bool {
        // 1. Past date
        if ($date->isPast() && !$date->isToday()) {
            return false;
        }

        // 2. Past time for today
        if ($date->isToday() && Carbon::parse($slotStart)->lte(Carbon::now())) {
            return false;
        }

        // 3. Overlaps rest period
        if ($rests && $this->slotOverlapsRest($slotStart, $slotEnd, $rests)) {
            return false;
        }

        // 4. Already booked
        if ($this->appointmentRepository->isSlotBooked(
            $doctorId,
            $date->format('Y-m-d'),
            $slotStart,
            $slotEnd,
        )) {
            return false;
        }

        return true;
    }

    /**
     * Check if a slot overlaps with any rest period.
     * Works with a single model OR a Collection — no direct Model imports.
     */
    private function slotOverlapsRest(string $slotStart, string $slotEnd, $rests): bool
    {
        // Normalize to array of objects with start_time/end_time
        $restList = [];

        if ($rests instanceof Collection) {
            $restList = $rests->all();
        } elseif (is_iterable($rests)) {
            $restList = iterator_to_array($rests);
        } elseif (is_object($rests)) {
            $restList = [$rests];
        }

        foreach ($restList as $rest) {
            if (!is_object($rest)) {
                continue;
            }

            $restStart = data_get($rest, 'start_time');
            $restEnd   = data_get($rest, 'end_time');

            if ($restStart && $restEnd) {
                $rStart = $restStart instanceof \DateTimeInterface
                    ? $restStart->format('H:i')
                    : (string) $restStart;
                $rEnd = $restEnd instanceof \DateTimeInterface
                    ? $restEnd->format('H:i')
                    : (string) $restEnd;

                if ($this->timeRangesOverlap($slotStart, $slotEnd, $rStart, $rEnd)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if two time ranges overlap: (s1, e1) vs (s2, e2).
     */
    private function timeRangesOverlap(string $s1, string $e1, string $s2, string $e2): bool
    {
        return $s1 < $e2 && $s2 < $e1;
    }
}
