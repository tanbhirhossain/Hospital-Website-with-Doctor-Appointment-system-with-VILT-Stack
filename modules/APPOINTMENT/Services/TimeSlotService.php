<?php 

namespace Modules\APPOINTMENT\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Modules\APPOINTMENT\Interfaces\AppointmentRepositoryInterface;
use Modules\WEBSITE\Interfaces\DoctorRestRepositoryInterface;
use Modules\WEBSITE\Interfaces\DoctorTimetableRepositoryInterface;

class TimeSlotService {
    // কনস্ট্রাক্টরে সব ইন্টারফেস ইনজেক্ট করা হলো (Loosely Coupled)
    public function __construct(
        private DoctorTimetableRepositoryInterface $timeRepo,
        private DoctorRestRepositoryInterface $restRepo,
        private AppointmentRepositoryInterface $appointmentRepo 
    ){}

    public function getTimeSlotDayWise(int $day, int $doctorId) {
        $shifts = $this->timeRepo->findByDayAndDrId($day, $doctorId);
        $duration = 15;

        return $this->generateSlotsFromShifts($shifts, $duration);
    }

    public function getTimeSlotDateWise(int $doctorId, string $start_date, string $end_date) {
        $duration = 15;
        
        // ১. ডাক্তারের শিফট এবং ছুটির দিন আনা
        $shiftsCollection = $this->timeRepo->findByDrId($doctorId);
        if ($shiftsCollection->isEmpty()) {
            return [];
        }

        $timetables = [];
        foreach ($shiftsCollection as $shift) {
            $dayKey = (int) $shift->day_of_week;
            $timetables[$dayKey][] = $shift;
        }
        
        $restDays = $this->restRepo->findByDrIdAndDateRange(
            $doctorId, 
            Carbon::parse($start_date), 
            Carbon::parse($end_date)
        )->pluck('date')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->flip()->toArray();

        // ২. SOLID মেনে রিপোজিটরি ইন্টারফেস থেকে বুকড ডেটা আনা (No Direct Model Call)
        $bookedAppointments = $this->appointmentRepo->getBookedSlotsByDateRange($doctorId, $start_date, $end_date);

        // ওয়ান-টাইম ফাস্ট হ্যাশ ম্যাপ তৈরি
        $bookedMap = [];
        foreach ($bookedAppointments as $appointment) {
            $dateKey = Carbon::parse($appointment->appointment_date)->format('Y-m-d');
            $timeKey = Carbon::parse($appointment->start_time)->format('H:i');
            $bookedMap[$dateKey][$timeKey] = true;
        }

        // ৩. লুপ চালিয়ে ফাইনাল স্লট জেনারেট করা
        $periods = CarbonPeriod::create($start_date, $end_date);
        $dateWiseSlots = [];

        foreach ($periods as $period) {
            $currentDate = $period->format('Y-m-d');

            if (isset($restDays[$currentDate])) {
                continue;
            }

            $dayOfWeek = $period->dayOfWeek;
            if (!isset($timetables[$dayOfWeek])) {
                continue;
            }

            $todayBookedSlots = $bookedMap[$currentDate] ?? [];
            $slots = $this->generateSlotsFromShifts($timetables[$dayOfWeek], $duration, $todayBookedSlots);
            
            if (!empty($slots)) {
                $dateWiseSlots[$currentDate] = $slots;
            }
        }

        return $dateWiseSlots;
    }

    private function generateSlotsFromShifts($shifts, int $duration, array $todayBookedSlots = []): array {
        $slots = [];

        foreach ($shifts as $shift) {
            if (empty($shift->start_time) || empty($shift->end_time)) {
                continue;
            }

            $startParts = explode(':', $shift->start_time);
            $endParts = explode(':', $shift->end_time);

            $startMinutes = ((int)$startParts[0] * 60) + (int)($startParts[1] ?? 0);
            $endMinutes = ((int)$endParts[0] * 60) + (int)($endParts[1] ?? 0);

            for ($time = $startMinutes; $time + $duration <= $endMinutes; $time += $duration) {
                $currentStart = sprintf('%02d:%02d', intdiv($time, 60), $time % 60);
                $currentEnd = sprintf('%02d:%02d', intdiv($time + $duration, 60), ($time + $duration) % 60);

                $isBooked = isset($todayBookedSlots[$currentStart]);

                $slots[] = [
                    'start_time' => $currentStart,
                    'end_time'   => $currentEnd,
                    'is_booked'  => $isBooked
                ];
            }
        }

        return $slots;
    }
}
